<?php

namespace App\Support;

/**
 * Single source of truth for turning a transaction row's raw `status`
 * column ('completed', 'scheduled', 'cancelled', 'failed') into the label
 * shown on a receipt — used identically by Transfer, ExternalTransfer,
 * InternationalTransfer, Withdrawal, and TopUp's own statusLabel() methods,
 * which is why it's pulled out here rather than duplicated five times.
 * Resolved through __() so it follows the viewer's locale — see
 * lang/{locale}/receipt.php's status_* keys. Any status value outside the
 * four known ones (there shouldn't be any — see each model's own docblock)
 * falls back to the same untranslated ucfirst-formatted string this used to
 * return everywhere, rather than showing a raw translation-missing key.
 */
class TransactionStatus
{
    public static function label(?string $status): string
    {
        return match ($status ?? 'completed') {
            'completed' => __('receipt.status_completed'),
            'scheduled' => __('receipt.status_scheduled'),
            'cancelled' => __('receipt.status_cancelled'),
            'failed' => __('receipt.status_failed'),
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }
}
