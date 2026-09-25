<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Auth\PhoneVerificationPrompt;
use App\Http\Middleware\SecurityHeaders;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->authGuard('admin')
            // Custom OTP pages (see App\Filament\Auth) replace the package's
            // own — no `->registration()` call, so no registration route is
            // ever registered: self-signup is not possible for this panel.
            ->login(Login::class)
            ->routes(function (Panel $panel): void {
                Route::name('auth.')->group(function (): void {
                    Route::get('phone-verification-prompt', PhoneVerificationPrompt::class)
                        ->name('phone-verification-prompt');
                });
            })
            ->brandName('Venndor')
            ->brandLogo(fn () => asset('logo.svg'))
            ->darkModeBrandLogo(fn () => new HtmlString(
                '<img src="'.e(asset('logo.svg')).'" alt="Venndor" class="h-6 w-auto brightness-0 invert" />',
            ))
            ->favicon(asset('assets/img/favicon/favicon.ico'))
            ->font('Creato Display')
            ->colors([
                'primary' => Color::hex('#006b5e'),
                'success' => Color::hex('#16b364'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SecurityHeaders::class,
                ThrottleRequests::class.':120,1',
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
