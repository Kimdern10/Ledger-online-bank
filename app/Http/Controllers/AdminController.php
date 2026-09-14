<?php

namespace App\Http\Controllers;

use App\Mail\TransactionMail;
use App\Models\AppNotification;
use App\Models\Bill;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * The only feature slugs toggleFeature() will ever act on, each mapped
     * to its real column on users. The route itself is also constrained to
     * these six slugs (see web.php), so this is really a second, belt-and-
     * suspenders guarantee that nothing else can ever be flipped through
     * this endpoint.
     */
    private const FEATURE_FLAGS = [
        'send' => 'can_send',
        'pay-bills' => 'can_pay_bills',
        'link-account' => 'can_link_account',
        'withdraw' => 'can_withdraw',
        'top-up' => 'can_top_up',
        'cards' => 'can_manage_cards',
        'receive' => 'can_receive',
        'scan' => 'can_scan',
    ];

    /**
     * A quick top-level look — how many users, how many are active vs.
     * frozen/suspended/disabled, how many bills across all of them, and the
     * combined available balance on the platform. is_admin accounts are
     * excluded from these counts so an admin account itself never skews the
     * "how many real customers do we have" numbers.
     */
    public function dashboard(): View
    {
        $customers = User::where('is_admin', false);

        return view('admin.dashboard', [
            'userCount' => (clone $customers)->count(),
            'activeCount' => (clone $customers)->where('account_status', 'active')->count(),
            'inactiveCount' => (clone $customers)->where('account_status', '!=', 'active')->count(),
            'billCount' => Bill::count(),
            'totalBalance' => (float) (clone $customers)->sum('balance'),
        ]);
    }

    public function users(): View
    {
        return view('admin.users', [
            'users' => User::where('is_admin', false)->orderBy('created_at', 'desc')->paginate(20),
        ]);
    }

    /**
     * The per-user page: verification info, account status controls, and
     * the Credit/Debit panel. Scoped away from admin accounts themselves —
     * there's nothing here an admin needs to do to another admin, and it
     * keeps the account-status buttons from ever being pointed at yourself
     * by mistake.
     */
    public function show(User $user): View
    {
        abort_if($user->isAdmin(), 404);

        // Loaded up front so the Employment & finances section on the page
        // can read $user->profile without a second query, and so it isn't
        // null just because Eloquent hasn't touched the relation yet.
        $user->load('profile');

        return view('admin.users-show', [
            'user' => $user,
            'adjustments' => $user->adminBalanceAdjustments()->latest()->take(5)->get(),
            // Read-only for admins — linking, withdrawing, and topping up are
            // all things only the user themselves does (see
            // LinkedAccountController/WithdrawalController/TopUpController);
            // this just lets support staff see what's actually on file.
            'linkedAccounts' => $user->linkedAccounts()->latest()->get(),
            'withdrawals' => $user->withdrawals()->latest()->take(5)->get(),
            'topUps' => $user->topUps()->latest()->take(5)->get(),
        ]);
    }

    /**
     * Freeze / Suspend / Disable / Reactivate. Real enforcement lives in
     * User::canSignIn(), checked at login (see FortifyServiceProvider) —
     * this endpoint just flips the flag it checks.
     */
    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        $data = $request->validate([
            'account_status' => ['required', 'in:active,frozen,suspended,disabled'],
        ]);

        $user->account_status = $data['account_status'];
        $user->save();

        return back()->with('status', $user->name.' is now '.strtolower($user->accountStatusLabel()).'.');
    }

    /**
     * Credits or debits the user's real balance — the same `balance` column
     * the dashboard, Send Money, Withdraw, Top Up, and History all read
     * from. There used to be a second "Account balance" pot here too (see
     * the 2026_09_06_090000 migration for why it originally existed), but
     * it never showed up anywhere in the consumer app, so crediting it
     * looked like nothing happened. Removed — this only ever touches the
     * one real balance now. Debiting is floored at 0. Every adjustment is
     * logged to admin_balance_adjustments, including the optional "sender"
     * fields, so a credit that's meant to represent an incoming transfer
     * leaves a real record instead of just changing a number with no
     * explanation.
     */
    public function adjustBalance(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        $data = $request->validate([
            'direction' => ['required', 'in:credit,debit'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'sender_name' => ['nullable', 'string', 'max:150'],
            'sender_account_name' => ['nullable', 'string', 'max:150'],
            'sender_account_number' => ['nullable', 'string', 'max:50'],
            'sender_bank_name' => ['nullable', 'string', 'max:150'],
            'bank_address' => ['nullable', 'string', 'max:255'],
        ]);

        $amount = (float) $data['amount'];
        $current = (float) $user->balance;

        $user->balance = $data['direction'] === 'credit'
            ? $current + $amount
            : max(0, $current - $amount);

        $user->save();

        $adjustment = $user->adminBalanceAdjustments()->create([
            'admin_id' => $request->user()->id,
            'direction' => $data['direction'],
            // Always 'available' now that there's only one pot — kept on
            // the row (rather than dropped from the table) so older
            // adjustments made back when "Account balance" existed still
            // display correctly below.
            'balance_pot' => 'available',
            'amount' => $amount,
            'sender_name' => $data['sender_name'] ?? null,
            'sender_account_name' => $data['sender_account_name'] ?? null,
            'sender_account_number' => $data['sender_account_number'] ?? null,
            'sender_bank_name' => $data['sender_bank_name'] ?? null,
            'bank_address' => $data['bank_address'] ?? null,
        ]);

        // See TransferController::storeLedgerTransfer() for why this is
        // gated by notify_transactions_email and wrapped in try/catch — the
        // balance above already moved and already saved. AdminBalanceAdjustment
        // rows don't have their own reference code the way a Transfer/
        // Withdrawal/TopUp does, so "ADJ" plus the row's own id stands in
        // for one here — display-only, never stored.
        // Hoisted above the try block below (rather than declared inside
        // its notify_transactions_email check) since the bell notification
        // after it needs this too, and that one always fires regardless of
        // the user's email preference.
        $isCredit = $data['direction'] === 'credit';

        try {
            // !== false (not just a truthy check): a missing/never-set
            // column reads back as null, and null should still mean "send
            // it" since the preference defaults to on — only an explicit
            // false (the user actually switched it off) should skip this.
            if ($user->notify_transactions_email !== false) {
                $rows = [];
                if ($isCredit && ! empty($data['sender_name'])) {
                    $rows[] = ['label' => 'From', 'value' => $data['sender_name']];
                }
                $rows[] = ['label' => 'Date', 'value' => $adjustment->created_at->format('M j, Y \a\t g:i A')];

                Mail::to($user->email)->send(new TransactionMail(
                    user: $user,
                    title: $isCredit ? 'Deposit to your account' : 'Balance adjustment',
                    amountSign: $isCredit ? '+' : '-',
                    amount: $amount,
                    reference: 'ADJ'.$adjustment->id,
                    rows: $rows,
                    newBalance: (float) $user->balance,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send balance adjustment notification email: '.$e->getMessage());
        }

        AppNotification::notify(
            $user,
            $isCredit ? 'Deposit to your account' : 'Balance adjustment',
            ($isCredit ? 'A deposit of $' : 'A balance adjustment of $').number_format($amount, 2).' was made to your account.',
            route('history'),
        );

        return back()->with(
            'status',
            ucfirst($data['direction']).'ed $'.number_format($amount, 2)." to {$user->name}'s balance."
        );
    }

    /**
     * Flips is_restricted on or off — separate from account_status/
     * updateStatus() above. This never signs the user out or stops them
     * from reaching their dashboard; it only blocks Send, Withdraw, Top
     * Up, Pay Bills, and Scan (see EnsureAccountIsNotRestricted), showing
     * them a popup pointing to Support instead of the form. Freeze/
     * Suspend/Disable are still the right tool for "this person shouldn't
     * be signed in at all" — this is for "let them see their account, but
     * don't let them move money."
     */
    public function toggleRestriction(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        $user->is_restricted = ! $user->is_restricted;
        $user->save();

        $state = $user->is_restricted ? 'restricted' : 'unrestricted';

        return redirect()->route('admin.users.show', $user)
            ->with('status', "{$user->name}'s account is now {$state}.");
    }

    /**
     * Flips email_verified_at on or off. This is the same column Fortify's
     * normal "click the link/enter the code we emailed you" flow sets —
     * toggling it here just lets an admin do that manually (e.g. someone
     * genuinely verified but their email never arrived).
     */
    public function toggleEmailVerification(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        $user->forceFill([
            'email_verified_at' => $user->email_verified_at ? null : now(),
        ])->save();

        $state = $user->email_verified_at ? 'verified' : 'unverified';

        return back()->with('status', "{$user->name}'s email is now marked {$state}.");
    }

    /**
     * Clears a user's two-factor setup entirely. Admins can't turn 2FA ON
     * for someone — that needs their authenticator app to scan a real QR
     * code — but clearing it is the standard way to get a locked-out user
     * (lost their phone, lost their recovery codes) back into their
     * account: they sign in with just their password again afterward, and
     * can set 2FA back up themselves if they want it.
     */
    public function resetTwoFactor(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return back()->with('status', "Two-factor authentication reset for {$user->name}.");
    }

    /**
     * Sends the same "reset your password" email Fortify's own
     * forgot-password page sends — this just lets an admin trigger it on a
     * user's behalf instead of the user requesting it themselves. Nothing
     * about the password changes until the user actually clicks the link
     * and picks a new one.
     */
    public function sendPasswordReset(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        Password::sendResetLink(['email' => $user->email]);

        return back()->with('status', "Password reset email sent to {$user->email}.");
    }

    /**
     * Flips one of the six "Feature access" toggles — see
     * EnsureFeatureEnabled.php for what actually enforces this on the
     * consumer side. $feature arrives as one of the six slugs the route is
     * constrained to (web.php); FEATURE_FLAGS maps it to the real column.
     */
    public function toggleFeature(User $user, string $feature): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);
        abort_unless(array_key_exists($feature, self::FEATURE_FLAGS), 404);

        $column = self::FEATURE_FLAGS[$feature];
        $user->{$column} = ! $user->{$column};
        $user->save();

        $label = str_replace('-', ' ', $feature);
        $state = $user->{$column} ? 'enabled' : 'disabled';

        // Redirects to a fixed, known route rather than back() on purpose —
        // back() depends on the browser having sent a Referer header for
        // this POST, and lands wherever that happens to point. Going
        // straight to this same user's page every time means the toggle
        // you just clicked is guaranteed to be back on screen, showing its
        // real saved state, regardless of browser/referrer-policy quirks.
        return redirect()->route('admin.users.show', $user)
            ->with('status', ucfirst("{$label} {$state} for {$user->name}."));
    }

    /**
     * The "Edit information" form: Account fields that live on the User
     * itself (first_name/last_name/middle_name/email/phone — all already
     * fillable, see User.php) plus the Employment & finances answers that
     * live on the related Profile (same columns onboarding's account
     * wizard writes to — see the profiles table migration). Both are saved
     * together since they're one form, but each goes to its own model.
     *
     * Changing someone's email here does NOT reset email_verified_at —
     * that's a deliberate simplification for now, not an oversight; if you
     * want re-verification enforced after an admin-driven email change,
     * that's a follow-up, not something this endpoint does today.
     */
    public function updateProfile(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'employment_status' => ['nullable', 'string', 'max:100'],
            'occupation' => ['nullable', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:150'],
            'annual_income' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'expected_annual_income' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'main_source_of_income' => ['nullable', 'string', 'max:150'],
            'net_worth' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
        ]);

        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        $user->profile()->updateOrCreate([], [
            'employment_status' => $data['employment_status'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'industry' => $data['industry'] ?? null,
            'annual_income' => $data['annual_income'] ?? null,
            'expected_annual_income' => $data['expected_annual_income'] ?? null,
            'main_source_of_income' => $data['main_source_of_income'] ?? null,
            'net_worth' => $data['net_worth'] ?? null,
        ]);

        return back()->with('status', "{$user->name}'s information was updated.");
    }

    /**
     * Every bill added across every account — not scoped to one user, the
     * way BillController's version is for the regular Pay Bills page.
     */
    public function bills(): View
    {
        return view('admin.bills', [
            'bills' => Bill::with('user')->orderBy('due_date')->paginate(20),
        ]);
    }

    /**
     * Removes any user's bill. Deliberately separate from
     * BillController::destroy(), which only lets a user delete their own
     * bill — an admin isn't the bill's owner, so that ownership check
     * would always fail here.
     */
    public function destroyBill(Bill $bill): RedirectResponse
    {
        $bill->delete();

        return back()->with('status', 'Bill removed.');
    }

    /**
     * "Trash" — every account that chose Delete Account (see
     * AccountDeletionController::destroy()) and hasn't been purged yet.
     * Ordered soonest-to-be-purged first so nothing slips past its 30-day
     * window unnoticed. Once PurgeDeletedAccounts (or "Delete now" below)
     * sets permanently_deleted_at, an account drops off this list for good
     * — there's nothing left on it worth showing an admin.
     */
    public function trash(): View
    {
        $users = User::whereNotNull('account_deleted_at')
            ->whereNull('permanently_deleted_at')
            ->orderBy('account_deleted_at')
            ->paginate(20);

        return view('admin-trash', ['users' => $users]);
    }

    /**
     * Undoes a self-delete — see User::restoreFromTrash(). Checks
     * permanently_deleted_at itself rather than trusting that the button
     * was only ever reachable from the Trash list, since an admin could
     * still hit this route directly for an account already purged.
     */
    public function restoreAccount(User $user): RedirectResponse
    {
        if ($user->permanently_deleted_at) {
            return back()->with('adminError', 'This account was already permanently deleted and can\'t be restored.');
        }

        $user->restoreFromTrash();

        return redirect()->route('admin.trash')->with('status', $user->name.'\'s account was restored.');
    }

    /**
     * Skips the rest of the 30-day wait and scrubs this account right now —
     * the same anonymization PurgeDeletedAccounts runs automatically, just
     * triggered by an admin instead of the schedule. See
     * User::anonymizeForPermanentDeletion() for exactly what this does (and
     * deliberately doesn't do — the row itself is never dropped).
     */
    public function purgeAccountNow(User $user): RedirectResponse
    {
        if (! $user->account_deleted_at) {
            return back()->with('adminError', 'This account was never deleted, so there\'s nothing to purge.');
        }

        if ($user->permanently_deleted_at) {
            return back()->with('adminError', 'This account was already permanently deleted.');
        }

        $user->anonymizeForPermanentDeletion();

        return redirect()->route('admin.trash')->with('status', 'Account #'.$user->id.' was permanently deleted.');
    }
}
