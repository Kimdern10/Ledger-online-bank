<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetSettingController extends Controller
{
    public function edit(): View
    {
        return view('budget-setting', ['currentBudget' => auth()->user()->monthlyBudget()]);
    }

    /**
     * Saves a real monthly_budget for the first time (or changes it) — see
     * User::monthlyBudget() for how a user who's never visited this page
     * still gets a sensible $3,000 default, and dashboard.blade.php for the
     * spending ring/percentage this number drives.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'monthly_budget' => ['required', 'numeric', 'min:1', 'max:999999.99'],
        ]);

        $user = $request->user();
        $user->monthly_budget = $data['monthly_budget'];
        $user->save();

        return redirect()->route('setting.budget')->with('status', 'Monthly budget saved.');
    }
}
