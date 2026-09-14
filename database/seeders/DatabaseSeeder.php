<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Guarded with firstOrCreate: on an app that's already been used,
        // this email almost certainly already exists, and an unguarded
        // User::factory()->create() throws on the unique constraint —
        // which previously aborted the whole seeder run before it ever
        // reached BankSeeder below.
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User']
        );

        $this->call(BankSeeder::class);
    }
}
