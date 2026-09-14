<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The rate (USD -> currency, see App\Support\CurrencyRates) and the
     * resulting converted amount, both locked in at the moment the
     * transfer is sent or scheduled — so the receipt always shows exactly
     * what was quoted at send time, even if the reference table's figures
     * are updated later. Both nullable: a transfer already sent before
     * this column existed, or one in a currency the table doesn't carry a
     * rate for, simply has nothing to show here instead of a fabricated
     * number.
     */
    public function up(): void
    {
        Schema::table('international_transfers', function (Blueprint $table) {
            $table->decimal('exchange_rate', 18, 6)->nullable()->after('currency');
            $table->decimal('converted_amount', 15, 2)->nullable()->after('exchange_rate');
        });
    }

    public function down(): void
    {
        Schema::table('international_transfers', function (Blueprint $table) {
            $table->dropColumn(['exchange_rate', 'converted_amount']);
        });
    }
};
