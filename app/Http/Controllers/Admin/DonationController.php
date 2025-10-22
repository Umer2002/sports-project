<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Club;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $query = Donation::with(['donor', 'club']);
        
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('donor_name', 'like', "%$search%")
                  ->orWhere('donor_email', 'like', "%$search%")
                  ->orWhereHas('club', function($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%");
                  });
            });
        }
        
        $donations = $query->orderByDesc('created_at')->paginate(20);
        $clubs = Club::orderBy('name')->get();
        
        $stats = [
            'total_donations' => Donation::where('status', 'completed')->sum('amount') / 100,
            'total_count' => Donation::where('status', 'completed')->count(),
            'pending_count' => Donation::where('status', 'pending')->count(),
        ];
        
        return view('admin.donations.index', compact('donations', 'clubs', 'stats'));
    }

    public function show(Donation $donation)
    {
        $donation->load(['donor', 'club']);
        return view('admin.donations.show', compact('donation'));
    }

    public function export(Request $request)
    {
        $query = Donation::with(['donor', 'club'])->where('status', 'completed');
        
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $donations = $query->orderBy('created_at')->get();
        
        $filename = 'donations_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($donations) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'ID', 'Donor Name', 'Donor Email', 'Club', 'Amount', 
                'Currency', 'Status', 'Message', 'Date'
            ]);
            
            // Add data
            foreach ($donations as $donation) {
                fputcsv($file, [
                    $donation->id,
                    $donation->donor_name,
                    $donation->donor_email,
                    $donation->club->name,
                    $donation->amount / 100,
                    $donation->currency,
                    $donation->status,
                    $donation->message,
                    $donation->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
