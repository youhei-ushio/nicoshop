<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Detail;

use App\Contexts\Sales\Application\Persistence\OrderQueryResult;
use App\Contexts\Sales\Application\Persistence\OrderQuery;

/**
 * 注文詳細
 */
final class Interactor
{
    public function __construct(
        private readonly OrderQuery $orderQuery,
    )
    {

    }

    public function execute(Input $input): OrderQueryResult
    {
        return $this->orderQuery
            ->get($input->id);
    }
}
