<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Filled automatically by User::booted()'s "creating" hook — a
            // random, uniquely-generated 9-digit ABA-style number, same
            // pattern as account_number. It deliberately starts with a
            // prefix (33-60) that is never assigned to a real U.S. bank, so
            // it can never collide with — or be mistaken for — someone's
            // actual real-world routing number.
            $table->string('routing_number', 9)->nullable()->unique()->after('account_number');

            // SWIFT/BIC code for international wires — same auto-generation
            // pattern, built from a fictional "LDGR" bank code so it can't
            // collide with a real institution's registered SWIFT/BIC.
            $table->string('swift_code', 11)->nullable()->unique()->after('routing_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['routing_number', 'swift_code']);
        });
    }
};
