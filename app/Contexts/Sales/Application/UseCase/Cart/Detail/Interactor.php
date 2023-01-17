<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Cart\Detail;

use App\Contexts\Sales\Application\Persistence\CartItemPaginator;
use App\Contexts\Sales\Application\Persistence\CartItemQuery;

/**
 * カート内容
 */
final class Interactor
{
    public function __construct(
        private readonly CartItemQuery $cartItemQuery,
    )
    {

    }

    public function execute(Input $input): CartItemPaginator
    {
        return $this->cartItemQuery
            ->paginate($input->currentPage)
            ->get($input->customerUserId);
    }
}
