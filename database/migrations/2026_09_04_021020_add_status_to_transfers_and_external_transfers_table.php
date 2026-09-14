<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A real status field for the receipt page to show, instead of every
     * transfer just being implicitly "done" the moment its row exists.
     * Right now every transfer this app creates — Ledger-to-Ledger or
     * Another bank — is written to the database only AFTER the balance has
     * already moved, inside one atomic DB::transaction() (see
     * TransferController::storeLedgerTransfer()/storeExternalTransfer()),
     * so as of today every real row genuinely IS 'completed' the instant
     * it's created — there's no queue or async step that would leave one
     * sitting at 'initiated' or 'in_progress'. The column (and the other
     * two values) exist so the receipt page has a real field to read
     * instead of a hardcoded label, and so a future async transfer type
     * (a real external ACH/wire rail, say) has somewhere real to record
     * its actual in-flight status rather than pretending to be done early.
     */
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('amount');
        });

        Schema::table('external_transfers', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('external_transfers', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
