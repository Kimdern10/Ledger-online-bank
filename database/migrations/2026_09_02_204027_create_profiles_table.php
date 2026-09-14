<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Which step to resume on if the wizard gets interrupted (1-5).
            $table->unsignedTinyInteger('onboarding_step')->default(1);

            // Flips to true only once step 5 is submitted — this is what
            // gates access to the dashboard.
            $table->boolean('onboarding_completed')->default(false);

            // next of kin
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->string('next_of_kin_email')->nullable();
            $table->string('next_of_kin_address')->nullable();

            // profile
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('home_address')->nullable();

            // employment
            $table->string('employment_status')->nullable();
            $table->string('occupation')->nullable();
            $table->string('industry')->nullable();
            $table->decimal('annual_income', 14, 2)->nullable();
            $table->decimal('expected_annual_income', 14, 2)->nullable();
            $table->string('main_source_of_income')->nullable();
            $table->decimal('net_worth', 14, 2)->nullable();

            // account
            $table->string('account_type')->nullable();
            $table->string('currency', 3)->default('USD');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
