<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Notification;

use App\Contexts\Sales\Domain\Event\OrderCreated;
use Spatie\SlackAlerts\Facades\SlackAlert;

final class OrderCreatedSlackNotification
{
    public function handle(OrderCreated $event): void
    {
        $env = app()->environment();
        SlackAlert::message("Order created.\n*Env*: $env\n*ID*: $event->id\n*Date*: {$event->date->format('Y-m-d')}\n*Customer*: $event->customerUserId");
    }
}
