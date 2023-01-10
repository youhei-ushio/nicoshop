<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Done;

final class Input
{
    private function __construct(
        public readonly int $id,
    )
    {

    }

    public static function fromArray(array $input): self
    {
        return new self(
            id: intval($input['id']),
        );
    }
}
