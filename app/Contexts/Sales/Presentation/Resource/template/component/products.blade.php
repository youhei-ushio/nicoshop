<?php

declare(strict_types=1);

use App\Models;
use Illuminate\Contracts\Pagination\Paginator;

/**
 * @var Paginator $products
 * @var Models\Product $product
 * @see Products
 */
?>
<div>
    <ul>
        @foreach($products as $product)
            <li>
                [{{ $product->id }}]
                <span class="hover:underline cursor-pointer" wire:click="add({{ $product->id }})">{{ $product->name }}</span>
            </li>
        @endforeach
    </ul>

    {{ $products->links() }}
</div>
