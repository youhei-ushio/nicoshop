<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order\Mock;

use App\Contexts\Sales\Domain\Entity\IdFactory;

final class IdFactoryImpl implements IdFactory
{
    public function create(): string
    {
        return uniqid(more_entropy: true);
    }
}
