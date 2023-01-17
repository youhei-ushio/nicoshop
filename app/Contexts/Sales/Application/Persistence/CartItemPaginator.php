<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

use Iterator;

/**
 * アプリケーションで利用するカート内商品クエリ結果
 */
interface CartItemPaginator extends Iterator
{
    public function current(): CartItemQueryResult;

    public function total(): int;

    public function perPage(): int;

    public function currentPage(): int;
}
