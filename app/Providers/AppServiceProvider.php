<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureSmartsuppLogout();
        $this->configureRememberedEmail();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Smartsupp identifies a visitor by browser (cookie/local storage), not
     * by our own session — so on a shared device, logging out of the app
     * does nothing to Smartsupp's side, and the next person to open the
     * widget still sees the previous person's identified conversation.
     * Flashing this flag lets the very next page (guest layout) fire
     * smartsupp('logout') once, which ends that identification and starts
     * a fresh, anonymous conversation for whoever uses the browser next.
     */
    protected function configureSmartsuppLogout(): void
    {
        Event::listen(Logout::class, function (): void {
            session()->flash('smartsupp_logout', true);
        });
    }

    /**
     * "Remember me" on the login page also remembers the email address: it
     * is kept in a year-long cookie and filled in on the next visit (see
     * pages/auth/login.blade.php). Signing in without the box ticked
     * forgets it again.
     */
    protected function configureRememberedEmail(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            if ($event->remember && $event->user instanceof User) {
                Cookie::queue('remembered_email', $event->user->email, 60 * 24 * 365);
            } else {
                Cookie::queue(Cookie::forget('remembered_email'));
            }
        });
    }
}
