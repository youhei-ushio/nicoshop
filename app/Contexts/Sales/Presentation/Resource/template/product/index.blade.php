<?php

declare(strict_types=1);

use App\Contexts\Sales\Application\Persistence\ProductPaginator;

/**
 * @var ProductPaginator $products
 */
?>

<h1>商品一覧</h1>

<div>
    @if ($products->currentPage() > 1)
        <span>
            <a href="{{ route('sales.products.index') }}?limit={{ $products->perPage() }}&page={{ $products->currentPage() - 1 }}">
                前へ
            </a>
        </span>
    @endif
    <span>
        <a href="{{ route('sales.products.index') }}?limit={{ $products->perPage() }}&page={{ $products->currentPage() + 1 }}">
            次へ
        </a>
    </span>
</div>
<ul>
    @foreach($products as $product)
        <li>[{{ $product->id }}]
            <a href="">{{ $product->name }}</a>
        </li>
    @endforeach
</ul>
