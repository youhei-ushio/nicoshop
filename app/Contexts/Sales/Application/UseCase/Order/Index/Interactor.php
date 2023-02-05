<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Index;

use App\Contexts\Sales\Application\Persistence\OrderPaginator;
use App\Contexts\Sales\Application\Persistence\OrdersQuery;

/**
 * 注文一覧
 */
final class Interactor
{
    public function __construct(
        private readonly OrdersQuery $ordersQuery,
    )
    {

    }

    public function execute(Input $input): OrderPaginator
    {
        return $this->ordersQuery
            ->withoutFinished()
            ->paginate($input->perPage, $input->currentPage)
            ->get();
    }
}
