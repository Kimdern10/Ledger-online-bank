<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nullable, no default on purpose — null means "hasn't set one",
            // and User::monthlyBudget() is what turns that into the $3,000
            // fallback thae dashboard used to hardcode. That way a user who
            // never visits Set Budget still sees the same number they always
            // have, while anyone who does set one gets their real number.
            $table->decimal('monthly_budget', 10, 2)->nullable()->after('notify_promotions_email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('monthly_budget');
        });
    }
};
