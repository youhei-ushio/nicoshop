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
    <x-secondary-button class="ml-3 mb-3" wire:click="clear()" onclick="confirm('{{ __('Are you sure you would like to remove these item from the shopping cart') }}') || event.stopImmediatePropagation()">
        {{ __('Clear') }}
    </x-secondary-button>
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
