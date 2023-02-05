<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Provider;

use App\Contexts\Sales\Application\Persistence\CartItemQuery;
use App\Contexts\Sales\Application\Persistence\OrderQuery;
use App\Contexts\Sales\Application\Persistence\OrdersQuery;
use App\Contexts\Sales\Application\Persistence\ProductQuery;
use App\Contexts\Sales\Domain\Event\OrderCreated;
use App\Contexts\Sales\Domain\Event\OrderFinished;
use App\Contexts\Sales\Domain\Persistence\CartRepository;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;
use App\Contexts\Sales\Infrastructure\Notification\OrderCreatedSlackNotification;
use App\Contexts\Sales\Infrastructure\Notification\OrderDoneSlackNotification;
use App\Contexts\Sales\Infrastructure\Persistence\CartItemQueryImpl;
use App\Contexts\Sales\Infrastructure\Persistence\CartRepositoryImpl;
use App\Contexts\Sales\Infrastructure\Persistence\OrderQueryImpl;
use App\Contexts\Sales\Infrastructure\Persistence\OrderRepositoryImpl;
use App\Contexts\Sales\Infrastructure\Persistence\OrdersQueryImpl;
use App\Contexts\Sales\Infrastructure\Persistence\ProductQueryImpl;
use App\Contexts\Sales\Presentation\Http\Component\CartIcon;
use App\Contexts\Sales\Presentation\Http\Component\CartItems;
use App\Contexts\Sales\Presentation\Http\Component\Products;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Seasalt\Nicoca\Components\Domain\EventChannel;
use Seasalt\Nicoca\Components\Infrastructure\Persistence\EventChannelImpl;

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
            OrderCreated::class,
            [OrderCreatedSlackNotification::class, 'handle']
        );
        Event::listen(
            OrderFinished::class,
            [OrderDoneSlackNotification::class, 'handle']
        );

        Livewire::component('cart-icon', CartIcon::class);
        Livewire::component('products', Products::class);
        Livewire::component('cart', CartItems::class);
    }

    public function register(): void
    {
        $this->app->bind(
            abstract: ProductQuery::class,
            concrete: ProductQueryImpl::class,
        );
        $this->app->bind(
            abstract: EventChannel::class,
            concrete: EventChannelImpl::class,
        );
        $this->app->bind(
            abstract: OrderRepository::class,
            concrete: OrderRepositoryImpl::class,
        );
        $this->app->bind(
            abstract: OrdersQuery::class,
            concrete: OrdersQueryImpl::class,
        );
        $this->app->bind(
            abstract: CartItemQuery::class,
            concrete: CartItemQueryImpl::class,
        );
        $this->app->bind(
            abstract: CartRepository::class,
            concrete: CartRepositoryImpl::class,
        );
        $this->app->bind(
            abstract: OrderQuery::class,
            concrete: OrderQueryImpl::class,
        );
    }
}
