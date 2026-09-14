<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * address_status is the Tier 3 half of User::tier() (see that method) —
     * defaults to 'not_submitted' for the same reason kyc_status does (see
     * that column's migration): every existing account, and every brand
     * new one, reads as not-yet-verified rather than silently at the top
     * tier. Tier 3 needs Tier 2 (kyc_status === 'approved') first, so this
     * column only ever starts mattering after that's already true.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('address_status', 15)->default('not_submitted')->after('profile_picture_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('address_status');
        });
    }
};
