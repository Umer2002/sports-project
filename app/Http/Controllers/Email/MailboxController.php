<?php
namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MailboxController extends Controller
{
    public function inbox()
    {
        $user = Auth::user();

        // Detect "referee" role robustly
        $isReferee = false;

        // 1) If using Spatie roles:
        if (method_exists($user, 'hasRole')) {
            $isReferee = $user->hasRole('referee');
        }

        // 2) Fallbacks: column-based role/type/enum flags
        if (! $isReferee) {
            $roleValue = strtolower((string) ($user->role ?? $user->type ?? $user->user_type ?? ''));
            $isReferee = $roleValue === 'referee' || (isset($user->is_referee) && (bool) $user->is_referee);
        }

        $layout = $isReferee ? 'layouts.referee' : 'layouts.admin';

        // Your existing queries
        $emails = Email::where('user_id', $user->id)
            ->whereIn('status', ['unread', 'read'])
            ->latest()
            ->paginate(10);

        $unread_mails_count = Email::where('user_id', $user->id)
            ->where('status', 'unread')
            ->count();

        // Use the same Blade file; just pass $layout
        return view('admin.emails.inbox', compact('emails', 'unread_mails_count', 'layout'));
    }

    public function compose()
    {
        return view('admin.emails.compose');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email_id'      => 'required|email',
            'subject'       => 'nullable|string|max:255',
            'email_message' => 'nullable|string',
            'action'        => 'required|in:send,draft',
        ]);

        $email = Email::create([
            'user_id'       => Auth::id(),
            'email_id'      => $request->email_id,
            'subject'       => $request->subject,
            'email_message' => $request->email_message,
            'status'        => $request->action === 'send' ? 'sent' : 'draft',
        ]);

        if ($request->action === 'send') {
            Mail::send([], [], function ($message) use ($email) {
                $message->from('info@play2earn.com', 'Play2Earn')
                    ->to($email->email_id)
                    ->subject($email->subject ?? '(No Subject)')
                    ->html($email->email_message ?? '');
            });
        }

        return redirect()->route('email.inbox')->with('success', 'Email ' . ($request->action === 'send' ? 'sent' : 'saved as draft') . ' successfully.');
    }

    public function show($id)
    {
        $email = Email::where('user_id', Auth::id())->findOrFail($id);
        if ($email->status === 'unread') {
            $email->update(['status' => 'read']);
        }
        return view('admin.emails.show', compact('email'));
    }

    public function sent()
    {
        $emails = Email::where('user_id', Auth::id())
            ->where('status', 'sent')
            ->latest()->paginate(10);
        return view('admin.emails.sent', compact('emails'));
    }

    public function drafts()
    {
        $emails = Email::where('user_id', Auth::id())
            ->where('status', 'draft')
            ->latest()->paginate(10);
        return view('admin.emails.drafts', compact('emails'));
    }

    public function destroy($id)
    {
        $email = Email::where('user_id', Auth::id())->findOrFail($id);
        $email->delete();
        return back()->with('success', 'Email deleted successfully.');
    }

    public function trash()
    {
        $emails = Email::onlyTrashed()
            ->where('user_id', Auth::id())
            ->latest()->paginate(20);
        return view('admin.emails.trash', compact('emails'));
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'selected_emails'   => 'required|array',
            'selected_emails.*' => 'exists:emails,id',
        ]);
        Email::onlyTrashed()->whereIn('id', $request->selected_emails)
            ->where('user_id', Auth::id())->forceDelete();
        return back()->with('success', 'Selected emails permanently deleted.');
    }

    public function bulkMoveToTrash(Request $request)
    {
        $request->validate([
            'selected_emails'   => 'required|array',
            'selected_emails.*' => 'exists:emails,id',
        ]);
        foreach ($request->selected_emails as $id) {
            $email = Email::where('user_id', Auth::id())->find($id);
            if ($email) {
                $email->deleted_userid = Auth::id();
                $email->save();
                $email->delete();
            }
        }
        return back()->with('success', 'Selected emails moved to trash.');
    }
}
