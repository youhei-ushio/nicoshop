<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Persistence;

use App\Contexts\Sales\Domain\Event\OrderCreated;
use App\Contexts\Sales\Domain\Event\OrderNotYetAccepted;

interface EventChannel
{
    public function publish(OrderCreated | OrderNotYetAccepted $event): void;

    public function subscribe(string $eventName, callable $subscriber): void;
}
