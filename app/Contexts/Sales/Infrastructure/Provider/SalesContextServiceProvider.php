<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Provider;

use App\Contexts\Sales\Application;
use App\Contexts\Sales\Domain;
use App\Contexts\Sales\Infrastructure;
use App\Contexts\Sales\Presentation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;

final class SalesContextServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $context = 'Sales';
        $this->loadTranslationsFrom(
            path: app_path("Contexts/$context/Presentation/Resource/language"),
            namespace: Str::lower($context));
        $this->loadViewsFrom(
            path: app_path("Contexts/$context/Presentation/Resource/template"),
            namespace: Str::lower($context));

        Event::listen(
            Domain\Event\OrderCreated::class,
            [Infrastructure\Notification\OrderCreatedSlackNotification::class, 'handle']
        );
        Event::listen(
            Domain\Event\OrderFinished::class,
            [Infrastructure\Notification\OrderDoneSlackNotification::class, 'handle']
        );

        Livewire::component('cart-icon', Presentation\Http\Component\CartIcon::class);
        Livewire::component('products', Presentation\Http\Component\Products::class);
        Livewire::component('cart', Presentation\Http\Component\CartItems::class);
    }

    public function register(): void
    {
        $this->app->bind(
            abstract: Application\Persistence\ProductQuery::class,
            concrete: Infrastructure\Persistence\ProductQueryImpl::class,
        );
        $this->app->bind(
            abstract: Domain\Persistence\OrderRepository::class,
            concrete: Infrastructure\Persistence\OrderRepositoryImpl::class,
        );
        $this->app->bind(
            abstract: Application\Persistence\OrdersQuery::class,
            concrete: Infrastructure\Persistence\OrdersQueryImpl::class,
        );
        $this->app->bind(
            abstract: Application\Persistence\CartItemQuery::class,
            concrete: Infrastructure\Persistence\CartItemQueryImpl::class,
        );
        $this->app->bind(
            abstract: Domain\Persistence\CartRepository::class,
            concrete: Infrastructure\Persistence\CartRepositoryImpl::class,
        );
        $this->app->bind(
            abstract: Application\Persistence\OrderQuery::class,
            concrete: Infrastructure\Persistence\OrderQueryImpl::class,
        );
    }
}
