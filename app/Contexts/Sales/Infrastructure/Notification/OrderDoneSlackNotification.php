<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Notification;

use App\Contexts\Sales\Domain\Event\OrderFinished;
use Spatie\SlackAlerts\Facades\SlackAlert;

final class OrderDoneSlackNotification
{
    public function handle(OrderFinished $event): void
    {
        $env = app()->environment();
        SlackAlert::message("Order is done.\n*Env*: $env\n*ID*: $event->id\n*Date*: {$event->date->format('Y-m-d')}\n*Customer*: $event->customerUserId");
    }
}
