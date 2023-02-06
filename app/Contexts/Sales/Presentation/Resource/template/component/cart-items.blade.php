<?php

declare(strict_types=1);

use App\Contexts\Sales\Application\Persistence\CartItemQueryResult;
use App\Models;
use Illuminate\Contracts\Pagination\Paginator;

/**
 * @var Paginator $items
 * @var CartItemQueryResult $item
 * @see CartItems
 */
?>
<div>
    <x-secondary-button class="ml-3 mb-3" wire:click="clear()" onclick="confirm('{{ __('Are you sure you would like to remove these item from the shopping cart') }}') || event.stopImmediatePropagation()">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
        </svg>
        {{ __('Clear') }}
    </x-secondary-button>
    <x-secondary-button class="ml-3 mb-3" wire:click="order()">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path>
        </svg>
        {{ __('Order') }}
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
