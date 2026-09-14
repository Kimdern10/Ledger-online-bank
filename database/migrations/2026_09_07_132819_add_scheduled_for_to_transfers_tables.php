<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Backs the "Schedule" option under When on send.blade.php. Nullable —
     * only ever set on a row created with status = 'scheduled' (see
     * TransferController::scheduleLedgerTransfer()/scheduleExternalTransfer()).
     * A regular "send now" transfer never touches this column at all.
     *
     * The `status` column already existed for exactly this kind of future
     * use (see its own migration's comment) — this adds 'scheduled',
     * 'cancelled', and 'failed' as real values alongside the existing
     * 'completed', rather than introducing a second status system.
     */
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->date('scheduled_for')->nullable()->after('status');
        });

        Schema::table('external_transfers', function (Blueprint $table) {
            $table->date('scheduled_for')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn('scheduled_for');
        });

        Schema::table('external_transfers', function (Blueprint $table) {
            $table->dropColumn('scheduled_for');
        });
    }
};
