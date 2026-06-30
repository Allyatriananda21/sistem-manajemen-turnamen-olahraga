<?php

namespace App\Providers;

use App\Models\GameMatch;
use App\Observers\GameMatchObserver;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\LoginResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(LoginResponse::class, new class implements LoginResponse
        {
            public function toResponse($request)
            {
                $role = $request->user()->role;

                return match ($role) {
                    'admin' => redirect()->route('admin.dashboard'),
                    // TODO: Fase 5 — ganti ke route khusus wasit saat modul live score dibuat
                    'wasit' => redirect()->route('admin.dashboard'),
                    // TODO: Fase 7 — ganti ke route('pos.index') saat modul POS Livewire selesai dibuat
                    'kasir' => redirect()->route('admin.pos'),
                    default => redirect()->route('admin.dashboard'),
                };
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        GameMatch::observe(GameMatchObserver::class);

        // Redirect already-authenticated users away from /login to admin dashboard
        RedirectIfAuthenticated::redirectUsing(fn () => route('admin.dashboard'));

        $this->configureDefaults();
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
}
