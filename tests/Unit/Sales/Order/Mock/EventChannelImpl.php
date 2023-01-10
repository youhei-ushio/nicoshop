<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order\Mock;

use App\Contexts\Sales\Domain\Event\OrderAccepted;
use App\Contexts\Sales\Domain\Event\OrderCreated;
use App\Contexts\Sales\Domain\Event\OrderFinished;
use App\Contexts\Sales\Domain\Event\OrderNotYetAccepted;
use App\Contexts\Sales\Domain\Persistence\EventChannel;

final class EventChannelImpl implements EventChannel
{
    private array $subscribers = [];

    public function publish(OrderCreated|OrderAccepted|OrderNotYetAccepted|OrderFinished $event): void
    {
        $eventName = get_class($event);
        foreach ($this->subscribers[$eventName] ?? [] as $subscriber) {
            $subscriber($event);
        }
    }

    public function subscribe(string $eventName, callable $subscriber): void
    {
        $this->subscribers[$eventName][] = $subscriber;
    }
}