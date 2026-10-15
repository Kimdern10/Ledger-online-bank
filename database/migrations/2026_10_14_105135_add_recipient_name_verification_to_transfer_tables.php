<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * What a verification provider said about the recipient's name on an
     * outgoing bank transfer, kept separate from the name itself — see
     * App\Services\PayeeVerification\PayeeVerificationManager::outcomeFor()
     * for each value. 'manual' (every existing row) means the sender typed
     * it and nothing checked it.
     */
    public function up(): void
    {
        foreach (['external_transfers', 'international_transfers'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('recipient_name_status')->default('manual')->after('recipient_name');
                $table->string('recipient_name_provider')->nullable()->after('recipient_name_status');
            });
        }
    }

    public function down(): void
    {
        foreach (['external_transfers', 'international_transfers'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['recipient_name_status', 'recipient_name_provider']);
            });
        }
    }
};
