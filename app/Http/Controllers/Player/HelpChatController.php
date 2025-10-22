<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\HelpChatMessage;
use App\Models\HelpChatSession;
use App\Models\HelpChatTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class HelpChatController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'sometimes|array',
            'history.*.role' => 'required_with:history|string|in:user,assistant',
            'history.*.content' => 'required_with:history|string|max:800',
            'diagnostics' => 'sometimes|boolean',
            'wizard_context' => 'sometimes|array',
            'wizard_context.role' => 'nullable|string|max:60',
            'wizard_context.role_label' => 'nullable|string|max:120',
            'wizard_context.intent' => 'nullable|string|max:80',
            'wizard_context.intent_label' => 'nullable|string|max:160',
            'wizard_context.intent_group' => 'nullable|string|max:60',
            'wizard_context.stage' => 'nullable|string|max:240',
        ]);

        $message = trim($validated['message']);
        $history = collect($validated['history'] ?? [])
            ->take(10)
            ->map(function (array $entry) {
                return [
                    'role' => $entry['role'] === 'assistant' ? 'assistant' : 'user',
                    'content' => (string) Str::of($entry['content'])->limit(800, ''),
                ];
            })
            ->values()
            ->all();

        $wizardContext = $this->normalizeWizardContext($validated['wizard_context'] ?? []);
        $session = $this->resolveSession($request, $wizardContext);
        $suggestions = $this->suggestLinks($message, $wizardContext);
        $this->recordMessage($session, 'user', $message, [
            'wizard_context' => $wizardContext,
        ]);
        $this->maybeCreateTicket($session, $message, $wizardContext);

        if (!empty($validated['diagnostics'])) {
            Log::info('Help chat diagnostics requested', [
                'user_id' => $request->user()?->id,
                'history_count' => count($history),
            ]);
        }

        $apiKey = config('services.openai.key');
        $endpoint = config('services.openai.endpoint', 'https://api.openai.com/v1/chat/completions');
        $model = config('services.openai.model', 'gpt-4o-mini');

        if (empty($apiKey)) {
            Log::warning('Help chat assistant missing OpenAI key; returning fallback reply.', [
                'user_id' => $request->user()?->id,
            ]);

            $fallback = $this->fallbackReply($message, $wizardContext);
            $this->recordMessage($session, 'assistant', $fallback);

            return response()->json([
                'reply' => $fallback,
                'suggestions' => $suggestions,
            ]);
        }

        $knowledgeBase = <<<'KB'
Key Play2Earn workflows you can reference:
- Update a player profile photo: open the avatar menu and choose "My Account," proceed to Step 3 "Capture Your Image," allow the camera, align your face inside the frame, press Capture, then Next and Finish to store the new photo. Any previously saved photo appears beneath the frame so players can confirm the change.
- Create a club tournament: from the Club Dashboard choose Tournaments -> Create Tournament, complete the form with name, format, dates, registration window, fees, venue, and team eligibility, then submit. After saving, use the Schedule tab to auto-generate fixtures or adjust matchups manually.
- Build a new club team: Club Dashboard -> Teams -> Add Team to launch the wizard, Step 1 records basics (name, sport, season), Step 2 sets eligibility and defaults, Step 3 selects the roster, and Step 4 confirms the formation before publishing.
- Update broader player profile details: Player Dashboard -> My Account shortcut, review Steps 1-5 to adjust contact info, bio, social links, and payment details before finishing.
KB;

        $baseSystemPrompt = <<<'PROMPT'
You are the official AI assistant for Play2Earn Sports - a multi-role sports ecosystem where clubs, players, and referees connect, compete, and earn rewards through tournaments, videos, and rankings.
Always maintain a friendly, motivational, and professional tone - like a sports mentor or event coordinator.
Be concise (2-4 sentences), action-oriented, and refer to actual dashboard features when possible.
If the user asks about something unrelated to Play2Earn Sports, politely respond: "I can only assist with Play2Earn Sports features and dashboards."
Now, respond according to the user's role and their question.
PROMPT;

        $playerPrompt = <<<'PROMPT'
You are assisting a Player on Play2Earn Sports.

Core Goals:
1. Guide players to register, join clubs, and complete their profile.
2. Help them upload videos, join tournaments, and check rankings.
3. Explain the Play2Earn points system, achievements, and badges.
4. Motivate them to improve performance and share progress.

Tone:
- Friendly, energetic, and motivational - like a coach or teammate.
- Encourage fairness, teamwork, and earning through skill.

Rules:
- If the player mentions login, registration, or password reset, direct them to the correct section or link on the Player Dashboard.
- If they ask how to earn or get rewards, explain the Play2Earn points and tournaments system.
- If they ask about matches, stats, or club actions, point them to the relevant dashboard area (for example Match Center, Clubs, Videos, or Rankings).
- If the player sounds confused, suggest a next action such as "Would you like me to walk you through registration?"
- If the request is outside Play2Earn Sports, reply with "I can only assist with Play2Earn Sports features and dashboards."
- If you cannot answer, say "Let me connect you to a support coach for that."
- Use short, helpful answers (2-4 sentences) and mention dashboard labels exactly as they appear.
PROMPT;

        $systemPrompt = $baseSystemPrompt
            . "\n\n"
            . $playerPrompt
            . "\n\nReference information you can rely on when answering:\n"
            . $knowledgeBase;

        $currentQuery = Str::of($message)->replaceMatches('/\s+/', ' ')->trim()->limit(300, '...');
        if ($currentQuery->isNotEmpty()) {
            $systemPrompt .= "\n\nCurrent user query: \"{$currentQuery}\".";
        }

        if (!empty($wizardContext)) {
            $contextLines = [];

            if (!empty($wizardContext['role_label'] ?? $wizardContext['role'])) {
                $contextLines[] = '- User role: ' . ($wizardContext['role_label'] ?? $wizardContext['role']);
            }

            if (!empty($wizardContext['intent_label'] ?? $wizardContext['intent'])) {
                $contextLines[] = '- Focus topic: ' . ($wizardContext['intent_label'] ?? $wizardContext['intent']);
            }

            if (!empty($wizardContext['stage'])) {
                $contextLines[] = '- Reported current screen: ' . $wizardContext['stage'];
            }

            if (!empty($contextLines)) {
                $systemPrompt .= "\n\nConversation setup details:\n" . implode("\n", $contextLines);
            }
        }

        $messages = array_merge([
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
        ], $history, [[
            'role' => 'user',
            'content' => $message,
        ]]);

        try {
            $response = Http::withToken($apiKey)
                ->timeout(15)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.3,
                    'max_tokens' => 400,
                ]);

            if ($response->failed()) {
                Log::warning('Help chat assistant API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                $fallback = $this->fallbackReply($message, $wizardContext);
                $this->recordMessage($session, 'assistant', $fallback);

                return response()->json([
                    'reply' => $fallback,
                    'suggestions' => $suggestions,
                ]);
            }

            $reply = data_get($response->json(), 'choices.0.message.content');

            if (!$reply) {
                Log::info('Help chat assistant returned empty response');

                $fallback = $this->fallbackReply($message, $wizardContext);
                $this->recordMessage($session, 'assistant', $fallback);

                return response()->json([
                    'reply' => $fallback,
                    'suggestions' => $suggestions,
                ]);
            }

            $cleanReply = trim($reply);
            $this->recordMessage($session, 'assistant', $cleanReply);

            return response()->json([
                'reply' => $cleanReply,
                'suggestions' => $suggestions,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Help chat assistant exception', [
                'message' => $exception->getMessage(),
                'user_id' => $request->user()?->id,
            ]);

            $fallback = $this->fallbackReply($message, $wizardContext);
            $this->recordMessage($session, 'assistant', $fallback);

            return response()->json([
                'reply' => $fallback,
                'suggestions' => $suggestions,
            ]);
        }
    }

    protected function fallbackReply(string $message, array $wizardContext = []): string
    {
        $lower = Str::of($message)->lower();
        $normalized = $this->normalizeMessage($message);

        $roleLabel = $wizardContext['role_label'] ?? $wizardContext['role'] ?? 'Play2Earn teammate';
        $intent = $wizardContext['intent'] ?? null;
        $intentLabel = $wizardContext['intent_label'] ?? $intent;
        $stage = $wizardContext['stage'] ?? '';
        $stageLinePrefix = $stage ? "You're currently on {$stage}. " : '';

        if ($lower->contains(['audio chat', 'audio session', 'voice call'])) {
            return $stageLinePrefix . 'Audio chat request logged. Keep this chat open—our support coach will send an audio link shortly. You can continue typing details while we set it up.';
        }

        switch ($intent) {
            case 'player_profile':
                return $stageLinePrefix . 'Open Player Dashboard -> My Account and work through each step until you reach Finish. Tell me which step is giving you trouble and I will break it down.';
            case 'player_videos':
                return $stageLinePrefix . 'Head to Player Dashboard -> Videos to upload your clip, then confirm the sport, title, and visibility. Let me know the error you see if the upload stalls.';
            case 'player_join':
                return $stageLinePrefix . 'Use Clubs -> Browse to request a roster spot or Tournaments -> Explore to send a registration. Share the club or event name and I will guide your next clicks.';
            case 'player_rewards':
                return $stageLinePrefix . 'Open Player Dashboard -> Rewards to review points, badges, and payouts. Ask about any reward and I will explain how to earn or claim it.';
            case 'club_tournaments':
                return $stageLinePrefix . 'From the Club Dashboard, open Tournaments -> Create to build your event, then set the format, schedule, and invites. Tell me which section you need help with.';
            case 'club_roster':
                return $stageLinePrefix . 'Go to Club Dashboard -> Teams to manage rosters, then open the team wizard to add players and positions. Share the team or step you are updating and I will assist.';
            case 'club_registrations':
                return $stageLinePrefix . 'Review pending player registrations from your Club Dashboard notifications, approve the ones that match your roster, and I can help with any stuck request.';
            case 'club_staff':
                return $stageLinePrefix . 'Use Club Dashboard -> Staff or Schedules to assign coaches and manage calendars. Let me know the specific task so I can walk you through the buttons.';
            case 'coach_matches':
                return $stageLinePrefix . 'Coach Dashboard -> Matches lists every assignment with kickoff times. Pick the match you need and I will outline reporting or communication steps.';
            case 'coach_certifications':
                return $stageLinePrefix . 'Update certifications or availability from Coach Dashboard -> Profile/Availability. Tell me what needs updating and I will point to the exact form.';
            case 'coach_reports':
                return $stageLinePrefix . 'Open Coach Dashboard -> Matches, choose the finished game, and submit your post-match report. Share what data you want to capture and I will help.';
            case 'organizer_schedule':
                return $stageLinePrefix . 'Use the Tournament Scheduler to seed brackets and slot matches. Let me know the division or round you are arranging and I will outline the steps.';
            case 'organizer_venue':
                return $stageLinePrefix . 'Manage venues from the tournament settings screen by confirming locations, surfaces, and time windows. Tell me which venue or slot you are editing.';
            case 'organizer_results':
                return $stageLinePrefix . 'Publish results by opening Tournament -> Matches, entering scores, and pushing updates live. Share the match or bracket you are posting and I will guide you.';
            case 'rewards_claim':
                return $stageLinePrefix . 'Visit Player Dashboard -> Rewards -> Claim to request payouts or sponsorship credits. Say which reward you want and I will list the requirements.';
            case 'rewards_tokens':
                return $stageLinePrefix . 'Check the token overview on your Player Dashboard to see how Play2Earn points convert to rewards. Ask about any rule and I will explain it.';
            case 'technical_login':
                return $stageLinePrefix . 'Use Forgot Password to reset your credentials, then sign in again. If the reset email is missing, confirm spam folders or tell me the account email.';
            case 'technical_upload':
                return $stageLinePrefix . 'Refresh the Videos page, confirm the file is under the size limit, and try the upload once more. Describe the error message so I can troubleshoot further.';
            case 'technical_bug':
                return $stageLinePrefix . 'Try refreshing the page or clearing your browser cache, then repeat the action that failed. Share any error text or screenshot and I will escalate if needed.';
            case 'general_other':
                return $stageLinePrefix . "I'm ready to help—share the dashboard task or question you have and I'll map out the next steps.";
        }

        if ($this->containsAll($normalized, ['invite', 'club'])) {
            return 'Open Player Dashboard -> Invite -> Invite a Club, add their email, include a quick note, and send the invite. The club receives a sign-up link that credits you once they join.';
        }

        if (preg_match('/\b(hi|hello|hey|howdy|good (morning|afternoon|evening))\b/i', $message)) {
            return "Hi there! I'm your Play2Earn assistant. Tell me what you're working on and I'll guide you.";
        }

        if (preg_match('/\b(thanks?|thank you)\b/i', $message)) {
            return 'Glad to help! If you need anything else, just let me know.';
        }

        if ($lower->contains('availability')) {
            return $stageLinePrefix . 'Update availability anytime from Player Dashboard -> Availability. Pick the days or slots that fit and I will confirm the save steps.';
        }

        if ($lower->contains(['blog', 'update', 'post'])) {
            return $stageLinePrefix . 'Share a new update from Player Dashboard -> Blogs -> Create. Draft your post, add media if needed, and I will help you publish.';
        }

        if ($lower->contains(['pickup', 'game'])) {
            return $stageLinePrefix . 'Browse pickup games via Player Dashboard -> Pickup Games. Tap a match to view details or join, and ask if you need help confirming a spot.';
        }

        if ($lower->contains(['payout', 'payment'])) {
            return $stageLinePrefix . 'Request payouts from Player Dashboard -> Rewards -> Claim. Tell me which payout you want and I will outline the approval steps.';
        }

        if ($lower->contains(['camera', 'webcam', 'photo capture'])) {
            return $stageLinePrefix . 'Allow camera permissions for Play2Earn in your browser, then retry the capture or use Upload to select an existing image. Let me know if a prompt still blocks you.';
        }

        if ($lower->contains(['microphone', 'audio', 'mic', 'voice'])) {
            return $stageLinePrefix . 'Grant microphone access to Play2Earn, then try the audio feature again. If it fails, tell me which browser you are using and I will troubleshoot.';
        }

        if ($lower->contains(['attach', 'attachment', 'upload', 'screenshot', 'image'])) {
            return $stageLinePrefix . 'Use the paperclip button to attach up to five images. If the preview looks oversized, it will resize after sending—share any issues you see.';
        }

        $intentLine = $intentLabel ? "Let's tackle {$intentLabel}. " : '';
        return $stageLinePrefix . $intentLine . "Describe the exact task or blocker and I'll map your next steps.";
    }

    protected function normalizeMessage(string $message): Stringable
    {
        return Str::of($message)
            ->lower()
            ->replaceMatches('/[^a-z0-9\s]/', ' ')
            ->squish();
    }

    protected function normalizeWizardContext(array $context): array
    {
        $limits = [
            'role' => 60,
            'role_label' => 120,
            'intent' => 80,
            'intent_label' => 160,
            'intent_group' => 60,
            'stage' => 240,
        ];

        $normalized = [];

        foreach ($limits as $key => $limit) {
            if (! isset($context[$key])) {
                continue;
            }

            $value = Str::of($context[$key])->trim();
            if ($value->isEmpty()) {
                continue;
            }

            $normalized[$key] = (string) $value->limit($limit, '');
        }

        return $normalized;
    }

    protected function containsAll(Stringable $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle === '') {
                continue;
            }

            if (! $haystack->contains($needle)) {
                return false;
            }
        }

        return true;
    }

    protected function defaultSuggestions(): array
    {
        return array_values(array_filter([
            $this->makeSuggestion('Player Dashboard', 'player.dashboard'),
            $this->makeSuggestion('Browse Clubs', 'clubs.search'),
            $this->makeSuggestion('Find Tournaments', 'tournaments.search'),
            $this->makeSuggestion('Edit Availability', 'player.availability.index'),
            $this->makeSuggestion('Invite a Club', 'player.invite.club.create'),
        ]));
    }

    protected function suggestLinks(string $message, array $context = []): array
    {
        $suggestions = [];
        $normalizedMessage = $this->normalizeMessage($message);
        $stageContext = isset($context['stage']) ? Str::of($context['stage'])->lower() : null;

        $intent = $context['intent'] ?? null;
        switch ($intent) {
            case 'player_profile':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Open My Account wizard', 'player.profile'));
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Registration checklist', 'player.dashboard'));
                break;
            case 'player_videos':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Video upload workspace', 'player.videos.index'));
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Explore community videos', 'player.videos.explore'));
                break;
            case 'player_join':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Browse clubs', 'clubs.search'));
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Find tournaments', 'tournaments.search'));
                break;
            case 'player_rewards':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Rewards & payouts', 'player.payouts'));
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Player insights', 'player.dashboard'));
                break;
            case 'club_tournaments':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Club dashboard', 'club-dashboard'));
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Launch team wizard', 'club.teams.wizard.step1', [], url('/club/teams/wizard/step1')));
                break;
            case 'club_roster':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Manage club teams', 'club-dashboard'));
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Open club teams', null, [], url('/club/teams')));
                break;
            case 'club_registrations':
            case 'club_staff':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Club dashboard', 'club-dashboard'));
                break;
            case 'coach_matches':
            case 'coach_certifications':
            case 'coach_reports':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Coach dashboard', 'coach.dashboard'));
                break;
            case 'organizer_schedule':
            case 'organizer_venue':
            case 'organizer_results':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Tournament scheduling tools', 'tournaments.search'));
                break;
            case 'rewards_claim':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Claim rewards', 'player.payouts'));
                break;
            case 'rewards_tokens':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Token overview', 'player.dashboard'));
                break;
            case 'technical_login':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Reset password', 'password.request', [], url('/forgot-password')));
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Log in again', 'login'));
                break;
            case 'technical_upload':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Video upload workspace', 'player.videos.index'));
                break;
            case 'technical_bug':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Reload dashboard', 'player.dashboard'));
                break;
            case 'general_other':
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Player dashboard', 'player.dashboard'));
                break;
        }

        $role = $context['role'] ?? null;
        if ($role === 'club') {
            $this->pushSuggestion($suggestions, $this->makeSuggestion('Club dashboard', 'club-dashboard'));
        } elseif ($role === 'coach') {
            $this->pushSuggestion($suggestions, $this->makeSuggestion('Coach dashboard', 'coach.dashboard'));
        } elseif ($role === 'organizer') {
            $this->pushSuggestion($suggestions, $this->makeSuggestion('Tournament tools', 'tournaments.search'));
        } elseif ($role === 'guest') {
            $this->pushSuggestion($suggestions, $this->makeSuggestion('Explore public hubs', 'home'));
        }

        if ($stageContext) {
            if ($stageContext->contains('my account')) {
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Open My Account wizard', 'player.profile'));
            }
            if ($stageContext->contains('tournament')) {
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Tournament tools', 'tournaments.search'));
            }
            if ($stageContext->contains('club dashboard')) {
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Club dashboard', 'club-dashboard'));
            }
            if ($stageContext->contains('pickup')) {
                $this->pushSuggestion($suggestions, $this->makeSuggestion('Explore pickup games', 'player.pickup-games.index'));
            }
        }

        if ($this->containsAll($normalizedMessage, ['invite', 'club'])) {
            $this->pushSuggestion($suggestions, $this->makeSuggestion('Invite a club', 'player.invite.club.create'));
        }

        if ($this->containsAll($normalizedMessage, ['invite', 'player']) || $normalizedMessage->contains('refer')) {
            $this->pushSuggestion($suggestions, $this->makeSuggestion('Invite a player', 'player.invite.create'));
        }

        if ($normalizedMessage->contains('availability') || $normalizedMessage->contains('schedule')) {
            $this->pushSuggestion($suggestions, $this->makeSuggestion('Edit availability', 'player.availability.index'));
        }

        if ($normalizedMessage->contains('blog') || $normalizedMessage->contains('update') || $normalizedMessage->contains('post')) {
            $this->pushSuggestion($suggestions, $this->makeSuggestion('Share an update', 'player.blogs.create'));
        }

        if ($normalizedMessage->contains('pickup')) {
            $this->pushSuggestion($suggestions, $this->makeSuggestion('Explore pickup games', 'player.pickup-games.index'));
        }

        if ($normalizedMessage->contains('payout') || $normalizedMessage->contains('payment')) {
            $this->pushSuggestion($suggestions, $this->makeSuggestion('Claim rewards', 'player.payouts'));
        }

        if ($normalizedMessage->contains('video') || $normalizedMessage->contains('upload')) {
            $this->pushSuggestion($suggestions, $this->makeSuggestion('Video upload workspace', 'player.videos.index'));
        }

        if ($normalizedMessage->contains('login')) {
            $this->pushSuggestion($suggestions, $this->makeSuggestion('Reset password', 'password.request', [], url('/forgot-password')));
        }

        foreach ($this->defaultSuggestions() as $fallbackSuggestion) {
            $this->pushSuggestion($suggestions, $fallbackSuggestion);
            if (count($suggestions) >= 6) {
                break;
            }
        }

        return array_slice($suggestions, 0, 6);
    }

    protected function pushSuggestion(array &$collection, ?array $item): void
    {
        if (! $item) {
            return;
        }

        foreach ($collection as $existing) {
            if ($existing['url'] === $item['url']) {
                return;
            }
        }

        $collection[] = $item;
    }

    protected function makeSuggestion(string $label, ?string $routeName = null, array $routeParameters = [], ?string $fallbackUrl = null): ?array
    {
        $url = null;

        if ($routeName) {
            try {
                $url = route($routeName, $routeParameters);
            } catch (\Throwable $exception) {
                Log::debug('Help chat suggestion route unavailable.', [
                    'route' => $routeName,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if (! $url) {
            $url = $fallbackUrl;
        }

        if (! $url) {
            return null;
        }

        return [
            'label' => $label,
            'url' => $url,
        ];
    }

    protected function resolveSession(Request $request, array $wizardContext): HelpChatSession
    {
        $user = $request->user();
        $playerId = null;

        if ($user) {
            $user->loadMissing('player');
            $playerId = optional($user->player)->id;
        }

        $query = HelpChatSession::query()
            ->when($user, fn ($q) => $q->where('user_id', $user->id))
            ->when(!$user && $playerId, fn ($q) => $q->where('player_id', $playerId))
            ->whereNot('status', 'closed');

        $session = $query->latest('last_interaction_at')
            ->latest('created_at')
            ->first();

        if (! $session) {
            $session = HelpChatSession::create([
                'user_id' => $user?->id,
                'player_id' => $playerId,
                'status' => 'open',
                'last_interaction_at' => Carbon::now(),
            ]);
        }

        $updates = array_filter([
            'role' => $wizardContext['role'] ?? null,
            'role_label' => $wizardContext['role_label'] ?? null,
            'intent' => $wizardContext['intent'] ?? null,
            'intent_label' => $wizardContext['intent_label'] ?? null,
            'intent_group' => $wizardContext['intent_group'] ?? null,
        ], fn ($value) => ! is_null($value) && $value !== '');

        if (array_key_exists('stage', $wizardContext)) {
            $updates['stage'] = $wizardContext['stage'] ?: null;
        }

        if (! empty($updates)) {
            $session->fill($updates);
        }

        $session->last_interaction_at = Carbon::now();
        $session->save();

        return $session;
    }

    protected function recordMessage(HelpChatSession $session, string $sender, string $content, array $metadata = []): void
    {
        $session->messages()->create([
            'sender' => $sender,
            'content' => $content,
            'metadata' => $metadata ?: null,
        ]);

        $session->last_interaction_at = Carbon::now();
        $session->save();
    }

    protected function maybeCreateTicket(HelpChatSession $session, string $message, array $wizardContext = []): void
    {
        $lower = Str::of($message)->lower();

        $needsAudioSupport = $lower->contains(['audio chat', 'audio session', 'voice call']);
        $userUnsatisfied = $lower->contains(['not satisfied', 'unhappy', 'escalate']);
        $technicalIssue = ($wizardContext['intent'] ?? null) === 'technical_bug';

        if (! ($needsAudioSupport || $userUnsatisfied || $technicalIssue)) {
            return;
        }

        $existingOpenTicket = $session->tickets()->where('status', 'open')->first();

        if (! $existingOpenTicket) {
            $reason = $needsAudioSupport
                ? 'Audio chat requested by user.'
                : ($technicalIssue ? 'Technical issue escalated.' : 'User marked the conversation as unsatisfied.');

            $session->tickets()->create([
                'ticket_number' => $this->generateTicketNumber(),
                'status' => 'open',
                'reason' => $reason,
                'created_by' => $session->user_id,
            ]);
        }

        if ($session->status !== 'escalated') {
            $session->status = 'escalated';
            $session->save();
        }
    }

    protected function generateTicketNumber(): string
    {
        do {
            $number = 'HC-' . strtoupper(Str::random(6));
        } while (HelpChatTicket::where('ticket_number', $number)->exists());

        return $number;
    }
}
