<?php

use App\Http\Controllers\AccountDeletionController;
use App\Http\Controllers\AddressVerificationController;
use App\Http\Controllers\AdminAddressController;
use App\Http\Controllers\AdminBankController;
use App\Http\Controllers\AdminCardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminGuestSupportController;
use App\Http\Controllers\AdminKycController;
use App\Http\Controllers\AdminSupportController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\BankDirectoryController;
use App\Http\Controllers\BankListController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BudgetSettingController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\GuestSupportController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\LanguageSettingController;
use App\Http\Controllers\LinkedAccountController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationSettingController;
use App\Http\Controllers\PasswordSettingController;
use App\Http\Controllers\ProfileSettingController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\TransactionPinController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Public route — no middleware
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public — the language selector in the marketing site's header (see
// layouts/partials/headers.blade.php) posts here, for signed-out visitors
// and signed-in users alike. See LanguageSettingController::updateGuest().
Route::post('/language', [LanguageSettingController::class, 'updateGuest'])
    ->name('language.guest.update');

// User middleware routes — auth, not-admin (plus account.active/feature/
// not-restricted on individual routes where it applies)
Route::get('/send', [UserController::class, 'sendout'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_send', 'kyc-approved', 'not-restricted'])
    ->name('send');

Route::post('/send', [TransferController::class, 'store'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_send', 'kyc-approved', 'not-restricted'])
    ->name('send.store');

Route::get('/send/lookup', [TransferController::class, 'lookup'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_send', 'kyc-approved', 'throttle:20,1'])
    ->name('send.lookup');

Route::get('/send/international/convert', [TransferController::class, 'convertPreview'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_send', 'kyc-approved', 'throttle:30,1'])
    ->name('send.international.convert');

Route::get('/send/receipt/{transfer}', [TransferController::class, 'receipt'])
    ->middleware(['auth', 'not-admin'])
    ->name('send.receipt');

Route::get('/send/receipt/external/{externalTransfer}', [TransferController::class, 'externalReceipt'])
    ->middleware(['auth', 'not-admin'])
    ->name('send.receipt.external');

Route::get('/send/receipt/international/{internationalTransfer}', [TransferController::class, 'internationalReceipt'])
    ->middleware(['auth', 'not-admin'])
    ->name('send.receipt.international');

Route::post('/send/scheduled/{transfer}/cancel', [TransferController::class, 'cancelScheduled'])
    ->middleware(['auth', 'not-admin'])
    ->name('send.scheduled.cancel');

Route::post('/send/scheduled/external/{externalTransfer}/cancel', [TransferController::class, 'cancelScheduledExternal'])
    ->middleware(['auth', 'not-admin'])
    ->name('send.scheduled.cancel.external');

Route::post('/send/scheduled/international/{internationalTransfer}/cancel', [TransferController::class, 'cancelScheduledInternational'])
    ->middleware(['auth', 'not-admin'])
    ->name('send.scheduled.cancel.international');

Route::get('/setting', [UserController::class, 'setting'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting');

// Backs the dashboard bell — see NotificationController and
// AppNotification::notify(). throttle:60,1 matches support.messages.poll's
// own limit, same reasoning: a poll every ~20s from one open dashboard tab
// never gets close to it.
Route::get('/notifications/poll', [NotificationController::class, 'poll'])
    ->middleware(['auth', 'not-admin', 'throttle:60,1'])
    ->name('notifications.poll');
Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
    ->middleware(['auth', 'not-admin'])
    ->name('notifications.read');
Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
    ->middleware(['auth', 'not-admin'])
    ->name('notifications.read-all');
Route::post('/notifications/clear-all', [NotificationController::class, 'clearAll'])
    ->middleware(['auth', 'not-admin'])
    ->name('notifications.clear-all');

Route::get('/setting/transaction-pin', [TransactionPinController::class, 'edit'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.transaction-pin');
Route::post('/setting/transaction-pin', [TransactionPinController::class, 'update'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.transaction-pin.update');

Route::get('/setting/profile', [ProfileSettingController::class, 'edit'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.profile');
Route::post('/setting/profile', [ProfileSettingController::class, 'update'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.profile.update');

Route::get('/setting/password', [PasswordSettingController::class, 'edit'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.password');
Route::post('/setting/password', [PasswordSettingController::class, 'update'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.password.update');

Route::get('/setting/notifications', [NotificationSettingController::class, 'edit'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.notifications');
Route::post('/setting/notifications', [NotificationSettingController::class, 'update'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.notifications.update');

// No standalone Settings > Language page — language is chosen from the
// floating switcher that's always on screen inside the app instead (see
// layouts/app.blade.php's .dash-lang-float). This POST is what that
// switcher submits to.
Route::post('/setting/language', [LanguageSettingController::class, 'update'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.language.update');

Route::get('/setting/budget', [BudgetSettingController::class, 'edit'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.budget');
Route::post('/setting/budget', [BudgetSettingController::class, 'update'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.budget.update');

Route::get('/setting/banks', [BankListController::class, 'index'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.banks');

Route::view('/setting/terms', 'terms')
    ->middleware(['auth', 'not-admin'])
    ->name('setting.terms');
Route::view('/setting/app-info', 'app-info')
    ->middleware(['auth', 'not-admin'])
    ->name('setting.app-info');

Route::get('/setting/delete-account', [AccountDeletionController::class, 'edit'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.delete-account');
Route::post('/setting/delete-account', [AccountDeletionController::class, 'destroy'])
    ->middleware(['auth', 'not-admin'])
    ->name('setting.delete-account.destroy');

Route::get('/receive', [UserController::class, 'receive'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_receive'])
    ->name('receive');
Route::get('/link-account', [UserController::class, 'linkaccount'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_link_account'])
    ->name('link-account');

Route::post('/link-account', [LinkedAccountController::class, 'store'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_link_account'])
    ->name('link-account.store');

Route::delete('/link-account/{linkedAccount}', [LinkedAccountController::class, 'destroy'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_link_account'])
    ->name('link-account.destroy');

Route::get('/pay-bills', [UserController::class, 'payBills'])->middleware(['auth', 'not-admin', 'account.active', 'feature:can_pay_bills'])->name('pay-bills');

Route::post('/bills', [BillController::class, 'store'])->middleware(['auth', 'not-admin', 'account.active', 'feature:can_pay_bills'])->name('bills.store');

Route::delete('/bills/{bill}', [BillController::class, 'destroy'])->middleware(['auth', 'not-admin', 'account.active', 'feature:can_pay_bills'])->name('bills.destroy');
Route::get('/withdraw', [UserController::class, 'withdraw'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_withdraw', 'not-restricted'])
    ->name('withdraw');

Route::post('/withdraw', [WithdrawalController::class, 'store'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_withdraw', 'not-restricted'])
    ->name('withdraw.store');

Route::get('/withdraw/receipt/{withdrawal}', [WithdrawalController::class, 'receipt'])
    ->middleware(['auth', 'not-admin'])
    ->name('withdraw.receipt');

Route::get('/top-up', [UserController::class, 'topUp'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_top_up', 'not-restricted'])
    ->name('top-up');

Route::post('/top-up', [TopUpController::class, 'store'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_top_up', 'not-restricted'])
    ->name('top-up.store');

Route::get('/top-up/receipt/{topUp}', [TopUpController::class, 'receipt'])
    ->middleware(['auth', 'not-admin'])
    ->name('top-up.receipt');
Route::get('/history', [HistoryController::class, 'index'])
    ->middleware(['auth', 'not-admin'])
    ->name('history');
Route::get('/history/adjustment/{adjustment}', [HistoryController::class, 'adjustmentReceipt'])
    ->middleware(['auth', 'not-admin'])
    ->name('history.adjustment.receipt');
Route::get('/scan', [UserController::class, 'scan'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_scan', 'not-restricted'])
    ->name('scan');

// URI renamed from the old '/card' (singular felt like an internal/dev
// naming shortcut rather than what a real bank's own app would call a page
// that can show more than one card) to '/cards' — route names below are
// untouched (still 'card', 'card.*'), so this is a URL-bar-only change;
// every route('card...') call elsewhere in the app keeps working as-is.
Route::get('/cards', [CardController::class, 'index'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_manage_cards'])
    ->name('card');

Route::post('/cards/{card}/freeze', [CardController::class, 'toggleFreeze'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_manage_cards'])
    ->name('card.freeze');

Route::post('/cards/{card}/pin', [CardController::class, 'updatePin'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_manage_cards'])
    ->name('card.pin.update');

Route::post('/cards/{card}/limit', [CardController::class, 'updateLimit'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_manage_cards'])
    ->name('card.limit.update');

Route::post('/cards/{card}/contactless', [CardController::class, 'toggleContactless'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_manage_cards'])
    ->name('card.contactless.toggle');

Route::post('/cards/{card}/online-payments', [CardController::class, 'toggleOnlinePayments'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_manage_cards'])
    ->name('card.online-payments.toggle');

Route::post('/cards/{card}/report-lost', [CardController::class, 'reportLostOrStolen'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_manage_cards'])
    ->name('card.report-lost');

Route::post('/cards/{card}/close', [CardController::class, 'close'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_manage_cards'])
    ->name('card.close');

Route::post('/cards/{card}/replace', [CardController::class, 'replace'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_manage_cards'])
    ->name('card.replace');

Route::post('/cards/request', [CardController::class, 'requestCard'])
    ->middleware(['auth', 'not-admin', 'account.active', 'feature:can_manage_cards'])
    ->name('card.request');
Route::get('/support', [SupportController::class, 'index'])
    ->middleware(['auth', 'not-admin'])
    ->name('support');

Route::post('/support/new', [SupportController::class, 'startNew'])
    ->middleware(['auth', 'not-admin'])
    ->name('support.new');

Route::get('/support/history', [SupportController::class, 'history'])
    ->middleware(['auth', 'not-admin'])
    ->name('support.history');

Route::get('/support/history/{conversation}', [SupportController::class, 'showSession'])
    ->middleware(['auth', 'not-admin'])
    ->name('support.history.show');

Route::post('/support/messages', [SupportController::class, 'store'])
    ->middleware(['auth', 'not-admin'])
    ->name('support.messages.store');

Route::get('/support/messages/poll', [SupportController::class, 'poll'])
    ->middleware(['auth', 'not-admin', 'throttle:60,1'])
    ->name('support.messages.poll');

Route::get('/support/stream', [SupportController::class, 'stream'])
    ->middleware(['auth', 'not-admin', 'throttle:20,1'])
    ->name('support.stream');

// Public routes — throttled only, no auth. Both search the admin-managed
// Bank directory (see App\Models\Bank) — search() for the domestic
// "Another bank" tab, searchInternational() for the "International bank"
// tab.
Route::get('/banks/search', [BankDirectoryController::class, 'search'])
    ->middleware('throttle:30,1')
    ->name('banks.search');
Route::get('/banks/search-international', [BankDirectoryController::class, 'searchInternational'])
    ->middleware('throttle:30,1')
    ->name('banks.search.international');

// The floating "chat with us" widget on the public marketing page
// (welcome.blade.php) — public/throttled only, same as banks/search above.
// See GuestSupportController's own doc comment for how a visitor with no
// account gets identified across these requests.
Route::get('/support/guest/init', [GuestSupportController::class, 'init'])
    ->middleware('throttle:30,1')
    ->name('support.guest.init');

Route::post('/support/guest/start', [GuestSupportController::class, 'start'])
    ->middleware('throttle:10,1')
    ->name('support.guest.start');

Route::post('/support/guest/messages', [GuestSupportController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('support.guest.messages.store');

Route::get('/support/guest/messages/poll', [GuestSupportController::class, 'poll'])
    ->middleware('throttle:60,1')
    ->name('support.guest.messages.poll');

Route::post('/support/guest/end', [GuestSupportController::class, 'end'])
    ->middleware('throttle:10,1')
    ->name('support.guest.end');

// A user's own profile picture (the selfie from their approved KYC
// submission — see AvatarController) or, for an admin, any customer's.
// Deliberately not behind 'not-admin': an admin viewing a customer's page
// needs this too.
Route::get('/avatar/{user}', [AvatarController::class, 'show'])
    ->middleware(['auth'])
    ->name('avatar.show');

// User middleware routes — onboarding & dashboard (auth, not-admin, account.active)
Route::middleware(['auth', 'not-admin', 'account.active'])->group(function () {
    // URI renamed from '/onboarding' — "onboarding" read as SaaS/dev jargon
    // rather than something a real bank's app would show a customer. The
    // route NAME stays 'onboarding' (every redirect()->route('onboarding')
    // call elsewhere — KycController, AddressVerificationController — keeps
    // working unchanged), only the address-bar URL changes.
    Volt::route('/account-setup', 'onboarding.account-wizard')->name('onboarding');

    // The step right after the onboarding wizard finishes (see
    // account-wizard.blade.php's submit()) — upload a government ID and a
    // selfie for an admin to review. Reachable again later too (e.g. from
    // Settings), so 'onboarding.complete' isn't applied here — KycController
    // itself redirects back to /account-setup if it isn't finished yet,
    // rather than a middleware silently doing it before the controller can
    // decide what page to actually show.
    // URI renamed from '/kyc' — that's internal compliance shorthand ("Know
    // Your Customer") a customer wouldn't recognize; the app already calls
    // this "Identity verification" everywhere it's shown (see verify.php).
    // Route names stay 'kyc.create'/'kyc.store'.
    Route::get('/identity-verification', [KycController::class, 'create'])->name('kyc.create');
    Route::post('/identity-verification', [KycController::class, 'store'])->name('kyc.store');

    // Tier 3 — reachable any time from Settings once Tier 2 (identity) is
    // approved; AddressVerificationController::create() itself redirects
    // to /kyc if it isn't yet, same "controller decides, not middleware"
    // reasoning the KYC routes above already use.
    Route::get('/address-verification', [AddressVerificationController::class, 'create'])->name('address-verification.create');
    Route::post('/address-verification', [AddressVerificationController::class, 'store'])->name('address-verification.store');
});

Route::middleware(['auth', 'not-admin', 'account.active', 'onboarding.complete', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// Admin middleware routes — auth, admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/status', [AdminController::class, 'updateStatus'])->name('users.status');

    Route::post('/users/{user}/restriction/toggle', [AdminController::class, 'toggleRestriction'])->name('users.restriction.toggle');
    Route::post('/users/{user}/balance', [AdminController::class, 'adjustBalance'])->name('users.balance');
    Route::post('/users/{user}/email-verification', [AdminController::class, 'toggleEmailVerification'])->name('users.email-verification');
    Route::post('/users/{user}/two-factor/reset', [AdminController::class, 'resetTwoFactor'])->name('users.two-factor.reset');
    Route::post('/users/{user}/password-reset', [AdminController::class, 'sendPasswordReset'])->name('users.password-reset');

    Route::post('/users/{user}/features/{feature}', [AdminController::class, 'toggleFeature'])
        ->where('feature', 'send|pay-bills|link-account|withdraw|top-up|cards|receive|scan')
        ->name('users.features.toggle');

    Route::post('/users/{user}/profile', [AdminController::class, 'updateProfile'])->name('users.profile.update');

    Route::get('/bills', [AdminController::class, 'bills'])->name('bills');
    Route::delete('/bills/{bill}', [AdminController::class, 'destroyBill'])->name('bills.destroy');

    // The admin-managed bank directory (see App\Models\Bank) — registered
    // BEFORE /banks/{bank} implicitly needs it, and 'create' is registered
    // before the {bank} routes so it isn't swallowed by route-model binding
    // trying to find a Bank with id "create".
    Route::get('/banks', [AdminBankController::class, 'index'])->name('banks');
    Route::get('/banks/create', [AdminBankController::class, 'create'])->name('banks.create');
    Route::post('/banks', [AdminBankController::class, 'store'])->name('banks.store');
    Route::get('/banks/{bank}/edit', [AdminBankController::class, 'edit'])->name('banks.edit');
    Route::put('/banks/{bank}', [AdminBankController::class, 'update'])->name('banks.update');
    Route::delete('/banks/{bank}', [AdminBankController::class, 'destroy'])->name('banks.destroy');
    Route::post('/banks/{bank}/toggle', [AdminBankController::class, 'toggle'])->name('banks.toggle');

    Route::get('/support', [AdminSupportController::class, 'index'])->name('support');

    // Registered BEFORE /support/{user} below on purpose — that route's
    // {user} segment implicitly binds a User by route key, so if
    // /support/guests came after it, a request for this whole guest inbox
    // would be swallowed by an attempt to find a User with route key
    // "guests" and 404 before ever reaching these.
    Route::get('/support/guests', [AdminGuestSupportController::class, 'index'])->name('support.guests');
    Route::get('/support/guests/{conversation}', [AdminGuestSupportController::class, 'show'])->name('support.guests.show');
    Route::post('/support/guests/{conversation}/messages', [AdminGuestSupportController::class, 'store'])->name('support.guests.messages.store');
    Route::get('/support/guests/{conversation}/messages/poll', [AdminGuestSupportController::class, 'poll'])->name('support.guests.messages.poll');
    Route::post('/support/guests/{conversation}/end', [AdminGuestSupportController::class, 'end'])->name('support.guests.end');
    Route::post('/support/guests/{conversation}/typing', [AdminGuestSupportController::class, 'typing'])->name('support.guests.typing');

    Route::get('/support/{user}', [AdminSupportController::class, 'show'])->name('support.show');

    Route::get('/support/{user}/history', [AdminSupportController::class, 'history'])->name('support.history');
    Route::get('/support/{user}/history/{conversation}', [AdminSupportController::class, 'showSession'])->name('support.session');

    Route::post('/support/{user}/messages', [AdminSupportController::class, 'store'])->name('support.messages.store');
    Route::get('/support/{user}/messages/poll', [AdminSupportController::class, 'poll'])->name('support.messages.poll');

    Route::get('/support/{user}/stream', [AdminSupportController::class, 'stream'])->name('support.stream');

    Route::post('/support/{user}/typing', [AdminSupportController::class, 'typing'])->name('support.typing');

    Route::post('/support/{user}/end', [AdminSupportController::class, 'end'])->name('support.end');
    Route::post('/support/{user}/timer', [AdminSupportController::class, 'setTimer'])->name('support.timer');

    Route::post('/support/{user}/priority', [AdminSupportController::class, 'togglePriority'])->name('support.priority');

    Route::get('/card-requests', [AdminCardController::class, 'requests'])->name('card-requests');
    Route::post('/card-requests/{cardRequest}/approve', [AdminCardController::class, 'approve'])->name('card-requests.approve');
    Route::post('/card-requests/{cardRequest}/decline', [AdminCardController::class, 'decline'])->name('card-requests.decline');

    Route::get('/cards/reported', [AdminCardController::class, 'reported'])->name('cards.reported');

    Route::get('/kyc', [AdminKycController::class, 'requests'])->name('kyc');
    Route::get('/kyc/{kycVerification}/image/{type}', [AdminKycController::class, 'image'])->name('kyc.image');
    Route::post('/kyc/{kycVerification}/approve', [AdminKycController::class, 'approve'])->name('kyc.approve');
    Route::post('/kyc/{kycVerification}/decline', [AdminKycController::class, 'decline'])->name('kyc.decline');

    Route::get('/address', [AdminAddressController::class, 'requests'])->name('address');
    Route::get('/address/{addressVerification}/image', [AdminAddressController::class, 'image'])->name('address.image');
    Route::post('/address/{addressVerification}/approve', [AdminAddressController::class, 'approve'])->name('address.approve');
    Route::post('/address/{addressVerification}/decline', [AdminAddressController::class, 'decline'])->name('address.decline');

    Route::get('/trash', [AdminController::class, 'trash'])->name('trash');
    Route::post('/trash/{user}/restore', [AdminController::class, 'restoreAccount'])->name('trash.restore');
    Route::post('/trash/{user}/purge', [AdminController::class, 'purgeAccountNow'])->name('trash.purge');
});

require __DIR__.'/settings.php';
