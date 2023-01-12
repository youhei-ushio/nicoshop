<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Product\Index;

use App\Contexts\Sales\Application\Persistence\ProductPaginator;
use App\Contexts\Sales\Application\Persistence\ProductQuery;

final class Interactor
{
    public function __construct(
        private readonly ProductQuery $productQuery,
    )
    {

    }

    public function execute(Input $input): ProductPaginator
    {
        return $this->productQuery
            ->paginate($input->perPage, $input->currentPage)
            ->get();
    }
}
