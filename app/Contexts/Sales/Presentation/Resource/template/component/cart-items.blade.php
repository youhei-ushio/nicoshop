<?php

declare(strict_types=1);

use App\Contexts\Sales\Application\Persistence\CartItemQueryResult;
use App\Models;
use Illuminate\Contracts\Pagination\Paginator;

/**
 * @var Paginator $items
 * @var CartItemQueryResult $item
 */
?>
<div>
    <ul>
        @foreach($items as $item)
            <li>
                [{{ $item->productId }}]
                {{ $item->productName }}
                x{{ $item->quantity }}
            </li>
        @endforeach
    </ul>

    {{ $items->links() }}
</div>
