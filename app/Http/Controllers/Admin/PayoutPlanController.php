<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutPlan;
use Illuminate\Http\Request;

class PayoutPlanController extends Controller
{
    public function index()
    {
        $plans = PayoutPlan::orderBy('player_count')->get();
        return view('admin.payout_plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.payout_plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'player_count' => 'required|integer|unique:payout_plans,player_count',
            'payout_amount' => 'required|numeric|min:0',
        ]);
        PayoutPlan::create($data);
        return redirect()->route('admin.payout_plans.index')->with('success', 'Payout plan created successfully.');
    }

    public function edit(PayoutPlan $payout_plan)
    {
        return view('admin.payout_plans.edit', compact('payout_plan'));
    }

    public function update(Request $request, PayoutPlan $payout_plan)
    {
        $data = $request->validate([
            'player_count' => 'required|integer|unique:payout_plans,player_count,' . $payout_plan->id,
            'payout_amount' => 'required|numeric|min:0',
        ]);
        $payout_plan->update($data);
        return redirect()->route('admin.payout_plans.index')->with('success', 'Payout plan updated successfully.');
    }

    public function destroy(PayoutPlan $payout_plan)
    {
        $payout_plan->delete();
        return redirect()->route('admin.payout_plans.index')->with('success', 'Payout plan deleted successfully.');
    }
} 