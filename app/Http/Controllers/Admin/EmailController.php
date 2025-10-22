<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{

    public function index(){
        $emails = Email::where('user_id', Auth::id())
            ->whereIn('status', ['unread','read'])
            ->latest()
            ->paginate(10);

        $unread_mails_count = Email::where('user_id', Auth::id())
            ->where('status', 'unread')
            ->count();

        return view('admin.emails.inbox', compact('emails', 'unread_mails_count'));
    }
    // In your EmailController.php

    public function inbox()
    {
        $emails = Email::where('user_id', Auth::id())
            ->whereIn('status', ['unread','read'])
            ->latest()
            ->paginate(10);

        $unread_mails_count = Email::where('user_id', Auth::id())
        ->where('status', 'unread')
        ->count();

        return view('admin.emails.inbox', compact('emails', 'unread_mails_count'));
    }


    public function compose()
    {
        return view('admin.emails.compose');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email_id' => 'required|email',
            'subject' => 'nullable|string|max:255',
            'email_message' => 'nullable|string',
            'action' => 'required|in:send,draft',
        ]);

        $email = Email::create([
            'user_id' => Auth::id(),
            'email_id' => $request->email_id,
            'subject' => $request->subject,
            'email_message' => $request->email_message,
            'status' => $request->action === 'send' ? 'sent' : 'draft',
        ]);

        if ($request->action === 'send') {
            Mail::send([], [], function ($message) use ($email) {
                $message->from('info@play2earn.com', 'Play2Earn')
                        ->to($email->email_id)
                        ->subject($email->subject ?? '(No Subject)')
                        ->html($email->email_message ?? '');
            });
        }

        return redirect()->route('admin.email.inbox')->with('success', 'Email ' . ($request->action === 'send' ? 'sent' : 'saved as draft') . ' successfully.');
    }

    public function show($id)
    {
        $email = Email::findOrFail($id);

        if ($email->status === 'unread') {
            $email->update(['status' => 'read']);
        }

        return view('admin.emails.show', compact('email'));
    }

    public function sent()
    {
        $emails = Email::where('user_id', Auth::id())
            ->where('status', 'sent')
            ->latest()
            ->paginate(10);

        return view('admin.emails.sent', ['emails' => $emails]);
    }

    public function drafts()
    {
        $emails = Email::where('user_id', Auth::id())
            ->where('status', 'draft')
            ->latest()
            ->paginate(10);

        return view('admin.emails.drafts', ['emails' => $emails]);
    }

    public function destroy($id)
    {
        $email = Email::findOrFail($id);
        $email->delete();

        return back()->with('success', 'Email deleted successfully.');
    }

    // Show trashed emails
public function trash()
{
    $emails = Email::onlyTrashed()
        ->where('user_id', Auth::id())
        ->latest()
        ->paginate(20);

    return view('admin.emails.trash', compact('emails'));
}

// Permanently delete selected emails
public function bulkDelete(Request $request)
{
    $request->validate([
        'selected_emails' => 'required|array',
        'selected_emails.*' => 'exists:emails,id',
    ]);

    Email::onlyTrashed()
        ->whereIn('id', $request->selected_emails)
        ->where('user_id', Auth::id())
        ->forceDelete();

    return back()->with('success', 'Selected emails permanently deleted.');
}

// Move multiple emails to trash (from Inbox)
public function bulkMoveToTrash(Request $request)
{
    $request->validate([
        'selected_emails' => 'required|array',
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
