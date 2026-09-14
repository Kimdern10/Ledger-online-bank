<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * php artisan admin:create — the only way an admin account gets made.
     * Nothing is stored in .env or in any file: the email/password you
     * type here only exist in your terminal for this one run, then go
     * straight into the database as a bcrypt hash, same as any other
     * user's password.
     */
    protected $signature = 'admin:create';

    protected $description = 'Interactively create or promote a user to admin';

    public function handle(): int
    {
        $this->info('Create an admin account for Ledger.');

        $email = $this->askValid('Admin email', ['required', 'email']);

        $existing = User::where('email', $email)->first();

        if ($existing) {
            if ($existing->isAdmin()) {
                $this->warn("{$existing->email} is already an admin — nothing to do.");

                return self::SUCCESS;
            }

            if (! $this->confirm("A user with {$email} already exists ({$existing->name}). Promote them to admin instead of creating a new account?", true)) {
                $this->info('Cancelled — nothing changed.');

                return self::SUCCESS;
            }

            $existing->forceFill([
                'is_admin' => true,
                'email_verified_at' => $existing->email_verified_at ?? now(),
            ])->save();

            $this->info("{$existing->name} ({$existing->email}) is now an admin.");

            return self::SUCCESS;
        }

        $firstName = $this->ask('First name', 'Ledger');
        $lastName = $this->ask('Last name', 'Admin');
        $password = $this->askForPassword();

        $admin = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
        ]);

        // is_admin isn't fillable (see User::isAdmin()'s docblock), so
        // User::create() above silently ignores it even if it were passed
        // in — forceFill() is the deliberate, explicit exception.
        $admin->forceFill([
            'is_admin' => true,
            'email_verified_at' => now(),
        ])->save();

        $this->info("Admin account created: {$email}");

        return self::SUCCESS;
    }

    /**
     * $this->secret() hides the input as you type — same as a real login
     * prompt — and asks twice to catch typos, since there's no confirm
     * field to check it against like there is on a normal form.
     */
    private function askForPassword(): string
    {
        while (true) {
            $password = $this->secret('Password (min 8 characters, hidden as you type)');
            $confirm = $this->secret('Confirm password');

            if ($password !== $confirm) {
                $this->error('Passwords did not match — try again.');

                continue;
            }

            $validator = Validator::make(['password' => $password], [
                'password' => ['required', 'string', 'min:8'],
            ]);

            if ($validator->fails()) {
                $this->error($validator->errors()->first('password'));

                continue;
            }

            return $password;
        }
    }

    private function askValid(string $question, array $rules): string
    {
        while (true) {
            $value = $this->ask($question);

            $validator = Validator::make(['value' => $value], ['value' => $rules]);

            if ($validator->fails()) {
                $this->error($validator->errors()->first('value'));

                continue;
            }

            return $value;
        }
    }
}
