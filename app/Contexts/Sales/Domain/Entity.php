<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain;

use App\Contexts\Sales\Domain\Persistence\EventChannel;

abstract class Entity
{
    protected function __construct(
        private readonly EventChannel $eventChannel,
    )
    {

    }

    final protected function publish($event): void
    {
        $this->eventChannel->publish($event);
    }

    abstract protected function toPersistenceRecord(): mixed;
}
