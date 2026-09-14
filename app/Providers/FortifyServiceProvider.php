<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Same login form and same 'users' table for everyone — this is
        // the only place admin vs. regular user actually branches: where
        // Fortify sends you right after a successful login. Everyone else
        // keeps going to config('fortify.home') (the consumer /dashboard)
        // exactly as before.
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse
            {
                public function toResponse($request)
                {
                    if ($request->user()?->isAdmin()) {
                        return redirect()->intended(route('admin.dashboard'));
                    }

                    return redirect()->intended(config('fortify.home'));
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);

        // Replaces Fortify's default "look up by email, check the hashed
        // password" with the same logic plus one extra check: a
        // frozen/suspended/disabled account (see the admin dashboard's
        // Freeze/Suspend/Disable buttons) never gets past login at all,
        // regardless of how correct the password is. Returning null here
        // is what makes Fortify show its normal "these credentials don't
        // match" error; throwing ValidationException is how we show a more
        // specific one instead.
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return null;
            }

            if (! $user->canSignIn()) {
                throw ValidationException::withMessages([
                    Fortify::username() => $user->accountBlockedMessage(),
                ]);
            }

            return $user;
        });
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn () => view('pages::auth.login'));
        // Was: view('pages::auth.verify-email') — Fortify's default
        // "click the link we emailed you" page. verify-email-page.blade.php
        // is a plain wrapper that properly *mounts* the Volt component with
        // a <livewire:...> tag — pointing this directly at the Volt file
        // (view('livewire.auth.verify-code')) renders it as inert HTML
        // instead of an actual live component, which is why $status/$code
        // came back as undefined variables.
        Fortify::verifyEmailView(fn () => view('auth.verify-email-page'));
        Fortify::twoFactorChallengeView(fn () => view('pages::auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('pages::auth.confirm-password'));
        Fortify::registerView(fn () => view('pages::auth.register'));
        Fortify::resetPasswordView(fn () => view('pages::auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn () => view('pages::auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip(),
            );
        });
    }
}
