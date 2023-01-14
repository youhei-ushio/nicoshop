<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Persistence;

use App\Contexts\Sales\Domain\Event\OrderAccepted;
use App\Contexts\Sales\Domain\Event\OrderCreated;
use App\Contexts\Sales\Domain\Event\OrderFinished;
use App\Contexts\Sales\Domain\Event\OrderNotYetAccepted;
use App\Contexts\Sales\Domain\Persistence\EventChannel;

final class EventChannelImpl implements EventChannel
{
    public function publish(OrderCreated|OrderAccepted|OrderNotYetAccepted|OrderFinished $event): void
    {
        event($event);
    }
}
