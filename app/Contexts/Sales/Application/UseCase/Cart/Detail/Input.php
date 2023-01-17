<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Cart\Detail;

final class Input
{
    public function __construct(
        public readonly int $customerUserId,
        public readonly int $currentPage = 1,
    )
    {

    }
}
