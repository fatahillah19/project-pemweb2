<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AcademicDashboardWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Firefly\FilamentBlog\Blog;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
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
            ->login()
            ->brandName('SMA NURUL FIKRI')
            ->brandLogo(fn (): HtmlString => new HtmlString(view('filament.brand-logo')->render()))
            ->brandLogoHeight('3rem')
            ->darkMode(false)
            ->maxContentWidth('full')
            ->resourceCreatePageRedirect('index')
            ->resourceEditPageRedirect('index')
            ->colors([
                'primary' => [
                    50 => '#eef7f4',
                    100 => '#d9ebe5',
                    200 => '#b3d8ca',
                    300 => '#86bfaa',
                    400 => '#5ba386',
                    500 => '#3f8a6a',
                    600 => '#2f6f55',
                    700 => '#295b48',
                    800 => '#244a3c',
                    900 => '#203f35',
                    950 => '#10231d',
                ],
            ])
            ->assets([
                Css::make('siakad-admin-dashboard-theme', public_path('css/filament-admin-dashboard.css'))
                    ->relativePublicPath('css/filament-admin-dashboard.css'),
            ])
            ->plugins([
                Blog::make(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AcademicDashboardWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
