```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Language doesn't actually change anything shown on screen yet
            // — the app has no translation system, every page is hardcoded
            // English text. This just saves the choice for later, rather
            // than pretending a working language switcher exists.
            $table->string('language')->default('en')->after('phone');

            // Same idea — the app doesn't send real transaction/security/
            // promotion emails or push notifications today (Fortify's
            // password-reset and verification-code emails are the only real
            // ones that exist). These three just save the on/off choice so
            // it's ready the moment actual notification-sending gets built.
            $table->boolean('notify_transactions_email')->default(true)->after('language');
            $table->boolean('notify_security_email')->default(true)->after('notify_transactions_email');
            $table->boolean('notify_promotions_email')->default(false)->after('notify_security_email');

            // Set when a user deletes their own account (see
            // AccountDeletionController) — distinguishes "they left" from an
            // admin choosing Disable on the Account status panel, even
            // though both end up setting the same account_status value.
            // Nullable and never touched otherwise.
            $table->timestamp('account_deleted_at')->nullable()->after('account_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'language',
                'notify_transactions_email',
                'notify_security_email',
                'notify_promotions_email',
                'account_deleted_at',
            ]);
        });
    }
};
