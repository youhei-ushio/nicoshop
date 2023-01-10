<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

use Iterator;

/**
 * アプリケーションで利用する注文クエリ
 */
interface OrderPaginator extends Iterator
{
    public function current(): OrderQueryResult;

    public function total(): int;

    public function perPage(): int;

    public function currentPage(): int;
}
