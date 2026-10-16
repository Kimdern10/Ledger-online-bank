<?php

namespace App\Models;

use Database\Factories\AdminActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One change an admin made — recorded automatically by
 * App\Http\Middleware\RecordAdminActivity, shown on Admin > Activity log.
 *
 * $action is the admin route's name (e.g. "admin.users.status"); see
 * actionLabel() for how it reads on screen. $description is the same
 * confirmation message the admin saw (e.g. "Ada Okafor is now frozen.").
 */
class AdminActivity extends Model
{
    /** @use HasFactory<AdminActivityFactory> */
    use HasFactory;

    /**
     * What each recorded admin action is called on the Activity log.
     */
    public const ACTION_LABELS = [
        'admin.users.status' => 'Account status',
        'admin.users.balance' => 'Balance adjustment',
        'admin.users.email-verification' => 'Email verification',
        'admin.users.two-factor.reset' => 'Two-factor reset',
        'admin.users.password-reset' => 'Password reset email',
        'admin.users.features.toggle' => 'Feature access',
        'admin.users.profile.update' => 'Profile edit',
        'admin.bills.destroy' => 'Bill removed',
        'admin.banks.store' => 'Bank added',
        'admin.banks.import' => 'Bank imported',
        'admin.banks.update' => 'Bank edited',
        'admin.banks.destroy' => 'Bank deleted',
        'admin.banks.toggle' => 'Bank on/off',
        'admin.card-requests.approve' => 'Card request approved',
        'admin.card-requests.decline' => 'Card request declined',
        'admin.kyc.approve' => 'Identity approved',
        'admin.kyc.decline' => 'Identity declined',
        'admin.address.approve' => 'Address approved',
        'admin.address.decline' => 'Address declined',
        'admin.support.end' => 'Support chat ended',
        'admin.support.priority' => 'Support priority',
        'admin.support.timer' => 'Support auto-close',
        'admin.support.guests.end' => 'Guest chat ended',
        'admin.trash.restore' => 'Account restored',
        'admin.trash.purge' => 'Account deleted for good',
    ];

    protected $fillable = [
        'admin_id',
        'user_id',
        'action',
        'description',
        'ip_address',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * The customer the action was about, if any.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actionLabel(): string
    {
        return self::ACTION_LABELS[$this->action] ?? $this->action;
    }
}
