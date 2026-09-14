<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The admin-managed bank directory — see App\Models\Bank. Replaces the
     * idea of calling a live third-party "list every bank" API (there isn't
     * a free/feasible one that actually covers this) with a list an admin
     * curates by hand, for both the existing domestic "Another bank" picker
     * (type=external) and the new International transfer tab (type=international).
     */
    public function up(): void
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['external', 'international'])->default('external');
            $table->string('country')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('currency', 3)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
