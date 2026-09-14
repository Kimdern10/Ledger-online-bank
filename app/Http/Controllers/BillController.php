<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillController extends Controller
{
    /**
     * Adds one bill for the logged-in user — called via fetch() from the
     * "Add bill" sheet on pay-bills.blade.php, which inserts the new row
     * into the page itself on success rather than doing a full reload.
     */
    public function store(Request $request): JsonResponse
    {
        // Bills only make sense against a checking account — this blocks a
        // savings-only user from adding one even by calling this endpoint
        // directly (the UI already hides the "+ Add bill" button for them,
        // but that's not something a server can trust on its own).
        if (empty($request->user()->checking_account_number)) {
            return response()->json([
                'message' => 'Bills can only be added to a checking account.',
            ], 403);
        }

        $data = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'biller' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'due_date' => ['required', 'date'],
        ]);

        $bill = $request->user()->bills()->create($data);

        return response()->json([
            'bill' => [
                'id' => $bill->id,
                'category' => $bill->category,
                'biller' => $bill->biller,
                'amount' => number_format($bill->amount, 2),
                'due_date_label' => 'Due '.$bill->due_date->format('M j'),
            ],
        ], 201);
    }

    /**
     * Removes one bill — called via fetch() from the trash icon on each row
     * in pay-bills.blade.php. {bill} is resolved by Laravel's route-model
     * binding straight from the URL segment; the ownership check below is
     * what stops user A from deleting user B's bill just by guessing an id.
     */
    public function destroy(Request $request, Bill $bill): JsonResponse
    {
        abort_unless($bill->user_id === $request->user()->id, 403);

        $bill->delete();

        return response()->json(['deleted' => true]);
    }
}
