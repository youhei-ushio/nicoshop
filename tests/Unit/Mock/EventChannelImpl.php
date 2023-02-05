<?php

declare(strict_types=1);

namespace Tests\Unit\Mock;

use Seasalt\Nicoca\Components\Domain\Event;
use Seasalt\Nicoca\Components\Domain\EventChannel;

final class EventChannelImpl implements EventChannel
{
    private array $subscribers = [];

    public function publish(Event $event): void
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
