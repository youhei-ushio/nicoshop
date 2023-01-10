<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order;

use App\Contexts\Sales\Domain\Event\OrderCreated;
use App\Contexts\Sales\Domain\Event\OrderNotYetAccepted;
use App\Contexts\Sales\Domain\Persistence\EventChannel;

final class EventChannelMock implements EventChannel
{
    private OrderCreated|OrderNotYetAccepted|null $publishedEvent = null;

    private array $subscribers = [];

    public function publish(OrderCreated|OrderNotYetAccepted $event): void
    {
        $this->publishedEvent = $event;
        $eventName = get_class($event);
        foreach ($this->subscribers[$eventName] ?? [] as $subscriber) {
            $subscriber($event);
        }
    }

    public function subscribe(string $eventName, callable $subscriber): void
    {
        $this->subscribers[$eventName][] = $subscriber;
    }

    public function lastPublishedEvent(): OrderCreated|OrderNotYetAccepted
    {
        return $this->publishedEvent;
    }
}