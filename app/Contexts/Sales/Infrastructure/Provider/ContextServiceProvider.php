<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Provider;

use App\Contexts\Sales\Application\Persistence\ProductQuery;
use App\Contexts\Sales\Infrastructure\Persistence\ProductQueryImpl;
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
    }

    public function register(): void
    {
        $this->app->bind(
            abstract: ProductQuery::class,
            concrete: ProductQueryImpl::class,
        );
    }
}
