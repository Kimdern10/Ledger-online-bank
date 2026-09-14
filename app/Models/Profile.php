<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'onboarding_step',
        'onboarding_completed',
        'next_of_kin_name',
        'next_of_kin_phone',
        'next_of_kin_email',
        'next_of_kin_address',
        'gender',
        'date_of_birth',
        'country',
        'state',
        'city',
        'zip_code',
        'home_address',
        'employment_status',
        'occupation',
        'industry',
        'annual_income',
        'expected_annual_income',
        'main_source_of_income',
        'net_worth',
        'account_type',
        'currency',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'annual_income' => 'decimal:2',
        'expected_annual_income' => 'decimal:2',
        'net_worth' => 'decimal:2',
        'onboarding_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The moment account_type gets saved (step 5 of onboarding — see
     * onboarding.blade.php's submit()), tell the related User to generate
     * whichever account number(s) match it. Runs on every save, not just
     * the first, but User::assignAccountNumbersForType() only fills in
     * numbers that don't already exist, so this is safe to fire more than
     * once (e.g. someone goes back to step 5 and re-submits).
     */
    protected static function booted(): void
    {
        static::saved(function (Profile $profile) {
            if (! empty($profile->account_type) && $profile->user) {
                $profile->user->assignAccountNumbersForType($profile->account_type);
            }
        });
    }
}
