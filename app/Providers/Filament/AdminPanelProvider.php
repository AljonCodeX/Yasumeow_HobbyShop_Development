<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\HtmlString;

use App\Filament\Pages\EventDashboard;
use App\Filament\Pages\InventoryReport;
use App\Filament\Pages\ShopDashboard;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Games\GameResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Registrations\RegistrationResource;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Filament\Resources\Results\ResultResource;
use App\Filament\Resources\Users\UserResource;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->font('Poppins')
            ->topbar(false)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->brandLogo(new HtmlString(
                '<div style="display:flex;align-items:center;gap:.6rem;">'
                . '<img src="' . asset('images/logo.png') . '" alt="Yasumeow" style="height:2.25rem;">'
                . '<span style="font-weight:700;font-size:1.1rem;">Yasumeow Hobby Shop</span>'
                . '</div>'
            ))
            ->favicon(asset('images/logo.png'))
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make()->items([
                        ...ShopDashboard::getNavigationItems(),
                        ...EventDashboard::getNavigationItems(),
                    ]),
                    NavigationGroup::make('Shop')->items([
                        ...ProductResource::getNavigationItems(),
                        ...ReservationResource::getNavigationItems(),
                    ]),
                    NavigationGroup::make('Events')->items([
                        ...EventResource::getNavigationItems(),
                        ...RegistrationResource::getNavigationItems(),
                        ...ResultResource::getNavigationItems(),
                    ]),
                    NavigationGroup::make('Reports')->items([
                        ...InventoryReport::getNavigationItems(),
                    ]),
                    NavigationGroup::make()->items([
                        ...UserResource::getNavigationItems(),
                        ...CategoryResource::getNavigationItems(),
                        ...GameResource::getNavigationItems(),
                    ]),
                ]);
            })
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
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
