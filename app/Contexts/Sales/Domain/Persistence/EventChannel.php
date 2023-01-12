<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Persistence;

use App\Contexts\Sales\Domain\Event\OrderAccepted;
use App\Contexts\Sales\Domain\Event\OrderCreated;
use App\Contexts\Sales\Domain\Event\OrderFinished;
use App\Contexts\Sales\Domain\Event\OrderNotYetAccepted;

interface EventChannel
{
    public function publish(OrderCreated | OrderAccepted | OrderNotYetAccepted | OrderFinished $event): void;

    public function subscribe(string $eventName, callable $subscriber): void;
}