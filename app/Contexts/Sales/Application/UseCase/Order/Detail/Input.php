<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Detail;

final class Input
{
    public function __construct(
        public readonly string $id,
    )
    {

    }
}
