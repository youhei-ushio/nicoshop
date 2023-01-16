<?php

declare(strict_types=1);

use App\Contexts\Sales\Application\Persistence\ProductPaginator;

/**
 * @var ProductPaginator $products
 */
?>
<x-app-layout>
    <x-slot name="header">
        <div class="relative py-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('商品一覧') }}
            </h2>
            <livewire:cart-icon />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <livewire:products />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
