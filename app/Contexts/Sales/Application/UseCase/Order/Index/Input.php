<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Index;

final class Input
{
    public function __construct(
        public readonly int $perPage = 100,
        public readonly int $currentPage = 1,
    )
    {

    }
}
