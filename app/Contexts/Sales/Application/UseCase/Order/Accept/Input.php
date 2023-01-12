<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Accept;

final class Input
{
    private function __construct(
        public readonly string $id,
    )
    {

    }

    public static function fromArray(array $input): self
    {
        return new self(
            id: $input['id'],
        );
    }
}
