<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Logout;
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
}
