<?php

namespace App\Models;

use App\Mail\VerificationCodeMail;
use App\Support\AccountTier;
use App\Support\Locale;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property bool $is_admin
 * @property string $account_status
 * @property Carbon|null $account_deleted_at
 * @property Carbon|null $permanently_deleted_at
 * @property bool $is_restricted
 * @property string $account_balance
 * @property string|null $transaction_pin
 * @property string $language
 * @property bool $notify_transactions_email
 * @property bool $notify_security_email
 * @property bool $notify_promotions_email
 * @property string|null $monthly_budget
 * @property string $kyc_status
 * @property string $address_status
 * @property string|null $profile_picture_path
 * @property bool $can_send
 * @property bool $can_pay_bills
 * @property bool $can_link_account
 * @property bool $can_withdraw
 * @property bool $can_top_up
 * @property bool $can_manage_cards
 * @property bool $can_receive
 * @property bool $can_scan
 * @property string|null $account_number
 * @property string|null $routing_number
 * @property string|null $swift_code
 * @property string|null $checking_account_number
 * @property string|null $savings_account_number
 * @property string $first_name
 * @property string $last_name
 * @property string|null $middle_name
 * @property string $email
 * @property string|null $phone
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $verification_code
 * @property Carbon|null $verification_code_expires_at
 * @property string|null $password_reset_code
 * @property Carbon|null $password_reset_code_expires_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['first_name', 'last_name', 'middle_name', 'email', 'phone', 'password'])]
#[Hidden(['password', 'transaction_pin', 'verification_code', 'verification_code_expires_at', 'password_reset_code', 'password_reset_code_expires_at', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
            'password_reset_code_expires_at' => 'datetime',
            'account_deleted_at' => 'datetime',
            'permanently_deleted_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
            'account_balance' => 'decimal:2',
            'is_admin' => 'boolean',
            'is_restricted' => 'boolean',
            'can_send' => 'boolean',
            'can_pay_bills' => 'boolean',
            'can_link_account' => 'boolean',
            'can_withdraw' => 'boolean',
            'can_top_up' => 'boolean',
            'can_manage_cards' => 'boolean',
            'can_receive' => 'boolean',
            'can_scan' => 'boolean',
            'notify_transactions_email' => 'boolean',
            'notify_security_email' => 'boolean',
            'notify_promotions_email' => 'boolean',
            'monthly_budget' => 'decimal:2',
        ];
    }

    /**
     * is_admin, account_status, account_deleted_at, is_restricted,
     * account_balance, and the six can_* feature flags are all deliberately
     * left out of #[Fillable(...)] above, so nothing — not registration,
     * not any form on the site, not the user themselves — can ever touch
     * them through mass assignment. The only way to change them is through
     * the admin dashboard (AdminController) or, for account_deleted_at,
     * AccountDeletionController — both use forceFill()/direct property
     * assignment on purpose.
     *
     * language, the three notify_*_email columns, and monthly_budget are
     * genuinely self-service (see LanguageSettingController/
     * NotificationSettingController/BudgetSettingController) but are set the
     * same direct-assignment way rather than being added to Fillable — same
     * reasoning TransactionPinController already uses for transaction_pin:
     * one consistent way to change anything on this model outside of
     * registration, instead of two.
     *
     * kyc_status and profile_picture_path join that same never-Fillable
     * list for the opposite reason — they're never the user's own choice
     * at all. kyc_status only ever moves via KycController (submitting
     * sets it to 'pending') or AdminKycController (an admin approves or
     * declines it), and profile_picture_path is only ever set by
     * AdminKycController::approve(), which copies the just-approved
     * selfie's path onto it. address_status follows the exact same rule,
     * moved by AddressVerificationController/AdminAddressController
     * instead.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * The one place all the account-status logic lives, so a login check
     * and a "why is this button disabled" UI check never drift apart.
     * Frozen/suspended/disabled all block sign-in for now — Ledger doesn't
     * have real money-movement endpoints yet (Send/Withdraw/Top Up are
     * still front-end only), so there's nothing finer-grained to gate like
     * "can view but can't transact" until those exist for real.
     */
    public function canSignIn(): bool
    {
        return $this->account_status === 'active';
    }

    public function accountStatusLabel(): string
    {
        return match ($this->account_status) {
            'frozen' => 'Frozen',
            'suspended' => 'Suspended',
            'disabled' => 'Disabled',
            default => 'Active',
        };
    }

    /**
     * Separate from account_status on purpose — a restricted account can
     * still sign in and see its dashboard (canSignIn() doesn't check this
     * at all), it just can't move money. See EnsureAccountIsNotRestricted,
     * which is what actually blocks Send/Withdraw/Top Up/Pay Bills/Scan for
     * an account this returns true for.
     */
    public function isRestricted(): bool
    {
        return (bool) $this->is_restricted;
    }

    /**
     * Matches the option list LanguageSettingController validates against
     * (both now read from App\Support\Locale::LANGUAGES) — see that class
     * for why picking one doesn't actually translate anything yet.
     */
    public function languageLabel(): string
    {
        return Locale::LANGUAGES[$this->language] ?? 'English';
    }

    /**
     * The only thing EnsureKycApproved actually checks before letting
     * someone use Send Money — see that middleware and KycController/
     * AdminKycController for how kyc_status gets here.
     */
    public function isKycApproved(): bool
    {
        return $this->kyc_status === 'approved';
    }

    /**
     * The Tier 3 counterpart to isKycApproved() — see
     * AddressVerificationController/AdminAddressController for how
     * address_status gets here. Nothing currently gates a whole page on
     * this the way EnsureKycApproved gates Send Money on KYC; it only ever
     * feeds into tier()/tierDailyLimit() below.
     */
    public function isAddressVerified(): bool
    {
        return $this->address_status === 'approved';
    }

    /**
     * The account's overall verification tier — see App\Support\AccountTier
     * for what each number actually unlocks (currently just a daily
     * outbound limit, enforced by Concerns\EnforcesTierLimits). Tier 3
     * needs Tier 2 first: an approved address on its own, with identity
     * never verified, still reads as Tier 1 — the address step is only ever
     * reachable after KYC is approved in the first place (see
     * AddressVerificationController::create()), so this is mostly a
     * defensive floor rather than something that happens in practice.
     */
    public function tier(): int
    {
        if (! $this->isKycApproved()) {
            return 1;
        }

        return $this->isAddressVerified() ? 3 : 2;
    }

    public function tierLabel(): string
    {
        return AccountTier::label($this->tier());
    }

    public function tierDescription(): string
    {
        return AccountTier::description($this->tier());
    }

    /**
     * The number Concerns\EnforcesTierLimits actually compares today's
     * Send Money + Withdraw total against before letting one more of
     * either through.
     */
    public function tierDailyLimit(): float
    {
        return AccountTier::dailyLimit($this->tier());
    }

    /**
     * Same wording FortifyServiceProvider shows when a blocked account
     * tries to log in — reused by EnsureAccountIsActive so a status
     * message never drifts into two different wordings depending on
     * whether it was caught at sign-in or mid-session.
     */
    public function accountBlockedMessage(): string
    {
        return match ($this->account_status) {
            'frozen' => __('authpage.account_frozen'),
            'suspended' => __('authpage.account_suspended'),
            'disabled' => __('authpage.account_disabled'),
            default => __('authpage.account_blocked_default'),
        };
    }

    /**
     * The number the dashboard's spending ring compares against — see
     * BudgetSettingController. Nullable in the database (nobody's set one
     * yet by default), so this is the one place that turns "hasn't set one"
     * into the same $3,000 default the dashboard always used to hardcode,
     * instead of every caller needing to know that fallback itself.
     */
    public function monthlyBudget(): float
    {
        return $this->monthly_budget !== null ? (float) $this->monthly_budget : 3000.0;
    }

    public function adminBalanceAdjustments(): HasMany
    {
        return $this->hasMany(AdminBalanceAdjustment::class);
    }

    /**
     * Every bell notification ever created for this user — see
     * AppNotification::notify() and NotificationController. Deliberately
     * named appNotifications(), not notifications(): Notifiable (already
     * used by this class, below) defines its own notifications() reading a
     * completely different, polymorphic "notifications" table that this
     * app has never actually migrated or used — reusing that name would
     * silently shadow it instead of adding something new. Newest-first
     * isn't enforced here on purpose; callers (NotificationController::
     * poll()) order it themselves so this relation stays reusable either
     * way.
     */
    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    /**
     * Whether this user has ever created a transaction PIN — see
     * TransactionPinController. Send Money, Withdraw, and Top Up all check
     * this before moving any real money (see RequiresTransactionPin) and
     * ask the user to create one first if it's still false.
     */
    public function hasTransactionPin(): bool
    {
        return ! empty($this->transaction_pin);
    }

    /**
     * How many days are left before PurgeDeletedAccounts (see
     * routes/console.php) scrubs this account for good. Only meaningful for
     * an account actually sitting in Trash — returns null for one that was
     * never deleted, and null again once it's already been purged, instead
     * of a stray number either way. Used only by the admin Trash page.
     */
    public function daysUntilPurge(): ?int
    {
        if (! $this->account_deleted_at || $this->permanently_deleted_at) {
            return null;
        }

        return max(0, 30 - (int) $this->account_deleted_at->diffInDays(now()));
    }

    /**
     * Reverses AccountDeletionController::destroy() — the admin Trash
     * page's "Restore" button. Only makes sense before
     * anonymizeForPermanentDeletion() below has run; once that's happened
     * there's no personal data left to restore.
     */
    public function restoreFromTrash(): void
    {
        $this->account_status = 'active';
        $this->account_deleted_at = null;
        $this->save();
    }

    /**
     * The actual "permanently delete" — run automatically by
     * PurgeDeletedAccounts 30 days after account_deleted_at, or immediately
     * by an admin via the Trash page's "Delete now" button. Deliberately
     * does NOT delete the row itself: every transfer, card, bill, and
     * support message this account ever touched uses ON DELETE CASCADE back
     * to users (see the migrations), so actually deleting the row would
     * silently wipe those records too — including the OTHER side of any
     * transfer, e.g. erasing a transfer from a recipient's history just
     * because the sender's account got purged. Scrubbing every personal and
     * financial field in place keeps all of that history intact while
     * making sure nothing identifying is left on the account itself.
     */
    public function anonymizeForPermanentDeletion(): void
    {
        $this->first_name = 'Deleted';
        $this->last_name = 'User';
        $this->middle_name = null;
        $this->email = 'deleted-user-'.$this->id.'@ledger.invalid';
        $this->phone = null;
        $this->password = Str::random(40);
        $this->transaction_pin = null;
        $this->verification_code = null;
        $this->verification_code_expires_at = null;
        $this->password_reset_code = null;
        $this->password_reset_code_expires_at = null;
        $this->two_factor_secret = null;
        $this->two_factor_recovery_codes = null;
        $this->two_factor_confirmed_at = null;
        $this->remember_token = null;
        $this->account_number = null;
        $this->routing_number = null;
        $this->swift_code = null;
        $this->checking_account_number = null;
        $this->savings_account_number = null;
        $this->permanently_deleted_at = now();
        $this->save();

        if ($this->profile) {
            $this->profile->update([
                'next_of_kin_name' => null,
                'next_of_kin_phone' => null,
                'next_of_kin_email' => null,
                'next_of_kin_address' => null,
                'gender' => null,
                'date_of_birth' => null,
                'country' => null,
                'state' => null,
                'city' => null,
                'zip_code' => null,
                'home_address' => null,
                'employment_status' => null,
                'occupation' => null,
                'industry' => null,
                'annual_income' => null,
                'expected_annual_income' => null,
                'main_source_of_income' => null,
                'net_worth' => null,
            ]);
        }
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials(trim("{$this->first_name} {$this->last_name}"), true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * A computed "name" for anything still expecting a single name field
     * (Flux components, any leftover Blade referencing $user->name). There's
     * no "name" column anymore, but this makes $user->name keep working.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->first_name} {$this->last_name}"),
        );
    }

    /**
     * Every place that already shows a round avatar (dashboard topbar,
     * Settings profile card, the admin per-user page) reads this instead of
     * profile_picture_path directly — it turns "no picture yet" into a
     * clean null (so those views can keep falling back to initials) and
     * turns a real one into a URL through AvatarController, never the raw
     * storage path.
     */
    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->profile_picture_path ? route('avatar.show', $this) : null,
        );
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * This user's one KYC submission — see KycVerification's own docblock
     * for why it's a single row that gets overwritten on resubmission
     * rather than a history of every attempt.
     */
    public function kycVerification(): HasOne
    {
        return $this->hasOne(KycVerification::class);
    }

    /**
     * This user's one address-verification submission — same
     * one-row-per-user reasoning as kycVerification() above, see
     * AddressVerification's own docblock.
     */
    public function addressVerification(): HasOne
    {
        return $this->hasOne(AddressVerification::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Transfers this user sent (see TransferController::store() — the
     * only kind that exists right now is a Ledger-to-Ledger send).
     */
    public function sentTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'sender_id');
    }

    /**
     * Transfers this user received.
     */
    public function receivedTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'recipient_id');
    }

    /**
     * Transfers this user sent to an external bank (see ExternalTransfer —
     * there's no "received" side here, since the recipient isn't a Ledger
     * user at all).
     */
    public function externalTransfers(): HasMany
    {
        return $this->hasMany(ExternalTransfer::class, 'sender_id');
    }

    /**
     * Transfers this user sent through the "International bank" tab (see
     * InternationalTransfer) — same "no received side" reasoning as
     * externalTransfers() above.
     */
    public function internationalTransfers(): HasMany
    {
        return $this->hasMany(InternationalTransfer::class, 'sender_id');
    }

    /**
     * This user's own support conversation with the bank — every message
     * in it, whether they wrote it themselves or an admin replied. See
     * the support_messages migration for why user_id always means "whose
     * conversation this is", not "who sent this one message".
     */
    public function supportMessages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'user_id');
    }

    /**
     * Every bank account or card this user has linked from the Link Account
     * page — see LinkedAccountController and LinkedAccount.php. Feeds the
     * "Already linked" list there, the destination/source chips on Withdraw
     * and Top Up, and the read-only card on the admin per-user page.
     */
    public function linkedAccounts(): HasMany
    {
        return $this->hasMany(LinkedAccount::class);
    }

    /**
     * Real withdrawals this user has made — see WithdrawalController.
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Real top-ups this user has made — see TopUpController.
     */
    public function topUps(): HasMany
    {
        return $this->hasMany(TopUp::class);
    }

    /**
     * Auto-generates, for every new user at registration time: a unique
     * fake-but-valid-format routing number and a unique fake SWIFT/BIC
     * code. The generic account_number column still exists (older users
     * may still have one from before), but nothing generates it anymore —
     * checking_account_number / savings_account_number, below, are the
     * only account numbers Ledger creates now.
     *
     * Those two aren't generated here, on purpose — at registration time
     * the app doesn't know yet whether this person wants a checking
     * account, a savings account, or both. That choice isn't made until
     * step 5 of the onboarding wizard (onboarding.blade.php's "Account
     * Type" field). See assignAccountNumbersForType() below, which
     * Profile's own booted() hook calls the moment that choice is saved.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->routing_number)) {
                do {
                    $number = static::generateFakeRoutingNumber();
                } while (static::where('routing_number', $number)->exists());

                $user->routing_number = $number;
            }

            if (empty($user->swift_code)) {
                do {
                    $code = static::generateFakeSwiftCode();
                } while (static::where('swift_code', $code)->exists());

                $user->swift_code = $code;
            }
        });
    }

    /**
     * Fills in whichever account number(s) match the account type chosen
     * on step 5 of onboarding — "checking", "savings", or "both" — and
     * only the one(s) that don't already exist yet, so calling this twice
     * (e.g. someone clicks back to step 5 and re-submits) never overwrites
     * a number that's already been generated and shown to them.
     */
    public function assignAccountNumbersForType(string $accountType): void
    {
        $accountType = strtolower($accountType);
        $dirty = false;

        if (in_array($accountType, ['checking', 'both'], true) && empty($this->checking_account_number)) {
            do {
                $number = static::generatePrefixedAccountNumber('74');
            } while (static::where('checking_account_number', $number)->exists());

            $this->checking_account_number = $number;
            $dirty = true;
        }

        if (in_array($accountType, ['savings', 'both'], true) && empty($this->savings_account_number)) {
            do {
                $number = static::generatePrefixedAccountNumber('50');
            } while (static::where('savings_account_number', $number)->exists());

            $this->savings_account_number = $number;
            $dirty = true;
        }

        if ($dirty) {
            $this->save();
        }
    }

    /**
     * Builds a 9-digit number that LOOKS like a real ABA routing number
     * (it passes the standard bank checksum) but can never actually BE one.
     *
     * Real routing numbers start with a 2-digit Federal Reserve prefix:
     * 00-32 (banks/thrifts), 61-72 (electronic institutions), or 80
     * (traveler's checks). Prefixes 33-60 have never been assigned to any
     * real institution, so picking one there guarantees this number can't
     * collide with — or be mistaken for — someone's actual bank.
     *
     * Note for later: in real banking a routing number identifies the BANK
     * (or branch), not the individual customer — every customer of the same
     * bank shares one routing number, and only the account number differs
     * per person. Ledger gives each user their own purely because that's
     * what was asked for; it isn't how real routing numbers behave.
     */
    protected static function generateFakeRoutingNumber(): string
    {
        $prefix = (string) random_int(33, 60);
        $middle = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $digits = array_map('intval', str_split($prefix.$middle));

        // Standard ABA checksum: 3*(d1+d4+d7) + 7*(d2+d5+d8) + 1*(d3+d6+d9) ≡ 0 (mod 10)
        $weights = [3, 7, 1, 3, 7, 1, 3, 7];
        $sum = 0;
        foreach ($digits as $i => $d) {
            $sum += $d * $weights[$i];
        }
        $checkDigit = (10 - ($sum % 10)) % 10;

        return $prefix.$middle.$checkDigit;
    }

    /**
     * Builds an 11-character fake SWIFT/BIC code: a fictional 4-letter
     * "bank code" (LDGR, for Ledger — not a real registered SWIFT bank
     * code), the US country code, then a random 2-character location code
     * and 3-character branch code so every user's is unique. Same caveat
     * as the routing number above: real SWIFT/BIC codes identify an
     * institution/branch, not a person.
     */
    protected static function generateFakeSwiftCode(): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $random = fn (int $length) => collect(range(1, $length))
            ->map(fn () => $alphabet[random_int(0, strlen($alphabet) - 1)])
            ->implode('');

        return 'LDGRUS'.$random(2).$random(3);
    }

    /**
     * Builds a 10-digit account number that always starts with the given
     * prefix (e.g. "74" for checking, "50" for savings), with the
     * remaining digits random. Same idea as generateFakeRoutingNumber()'s
     * safe prefix trick, but here the "prefix" is just a made-up house
     * style — account numbers aren't nationally standardized in the US the
     * way routing numbers are, so there's no real-world number this could
     * ever collide with or be mistaken for.
     */
    protected static function generatePrefixedAccountNumber(string $prefix): string
    {
        $remainingLength = 10 - strlen($prefix);
        $rest = str_pad((string) random_int(0, (10 ** $remainingLength) - 1), $remainingLength, '0', STR_PAD_LEFT);

        return $prefix.$rest;
    }

    /**
     * Sends a 6-digit code instead of Laravel's default verification link.
     * Fortify calls this automatically both right after registration and
     * whenever someone clicks "resend" on the verify-email page.
     */
    public function sendEmailVerificationNotification(): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(10),
        ])->save();

        Mail::to($this->email)->send(new VerificationCodeMail($code));
    }
}
