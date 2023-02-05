<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain;

use App\Contexts\Sales\Domain\Entity\IdFactory;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use Closure;

class EntityFactory
{
    public function __construct(
        private readonly EventChannel $eventChannel,
        private readonly IdFactory $idFactory,
    )
    {

    }

    final protected function createEntity(string $className, ...$params): mixed
    {
        $eventChannel = $this->eventChannel;
        $id = $this->idFactory->create();
        return Closure::bind(function() use ($className, $eventChannel, $id, $params) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $className::create($eventChannel, $id, ...$params);
        }, null, $className)();
    }
}
