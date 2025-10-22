<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['player', 'club', 'processedBy']);
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('player', function($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%") ;
                })
                ->orWhereHas('club', function($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%") ;
                });
            });
        }
        $payments = $query->orderByDesc('created_at')->paginate(20);
        $totalPlayerPayments = Payment::where('type', 'player')->sum('amount');
        $totalClubPayouts = Payment::where('type', 'club_payout')->sum('amount');
        $totalDonations = Payment::where('type', 'donation')->sum('amount');
        $clubs = Club::orderBy('name')->get();
        return view('admin.payments.index', compact('payments', 'totalPlayerPayments', 'totalClubPayouts', 'totalDonations', 'clubs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);
        $data['type'] = 'club_payout';
        $data['currency'] = 'usd';
        $data['processed_by'] = Auth::id();
        $data['player_id'] = null;
        $data['stripe_session_id'] = null;
        Payment::create($data);
        return redirect()->route('admin.payments.index')->with('success', 'Club payout recorded successfully.');
    }
} 