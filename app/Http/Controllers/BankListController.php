<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The read-only "Banks" page under Settings — shows every active entry
 * from the admin-managed bank directory (see App\Models\Bank), the same
 * list Send Money's "Another bank" and "International bank" tabs pick
 * from. Customers can't add or edit anything here; that's admin-only
 * (see AdminBankController).
 */
class BankListController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');

        $query = Bank::query()->active()->orderBy('name');

        if (in_array($type, ['external', 'international'], true)) {
            $query->where('type', $type);
        }

        $banks = $query->paginate(15)->withQueryString();

        return view('setting-banks', ['banks' => $banks, 'type' => $type]);
    }
}
