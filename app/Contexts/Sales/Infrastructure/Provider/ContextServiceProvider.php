<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Provider;

use App\Contexts\Sales\Application\Persistence\OrderQuery;
use App\Contexts\Sales\Application\Persistence\ProductQuery;
use App\Contexts\Sales\Domain\Entity\IdFactory;
use App\Contexts\Sales\Domain\Event\OrderCreated;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;
use App\Contexts\Sales\Infrastructure\Factory\IdFactoryImpl;
use App\Contexts\Sales\Infrastructure\Notification\OrderCreatedNotification;
use App\Contexts\Sales\Infrastructure\Persistence\EventChannelImpl;
use App\Contexts\Sales\Infrastructure\Persistence\OrderQueryImpl;
use App\Contexts\Sales\Infrastructure\Persistence\OrderRepositoryImpl;
use App\Contexts\Sales\Infrastructure\Persistence\ProductQueryImpl;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

final class ContextServiceProvider extends ServiceProvider
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
            [OrderCreatedNotification::class, 'handle']
        );
    }

    public function register(): void
    {
        $this->app->bind(
            abstract: ProductQuery::class,
            concrete: ProductQueryImpl::class,
        );
        $this->app->bind(
            abstract: IdFactory::class,
            concrete: IdFactoryImpl::class,
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
            abstract: OrderQuery::class,
            concrete: OrderQueryImpl::class,
        );
    }
}
