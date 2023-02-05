<?php

declare(strict_types=1);

namespace Tests\Unit\Mock;

use Seasalt\Nicoca\Components\Domain\Persistence\IdGenerator;

final class IdGeneratorImpl implements IdGenerator
{
    public function generate(): string
    {
        return uniqid(more_entropy: true);
    }
}
