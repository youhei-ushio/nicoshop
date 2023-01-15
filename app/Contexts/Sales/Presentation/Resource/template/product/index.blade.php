<?php

declare(strict_types=1);

use App\Contexts\Sales\Application\Persistence\ProductPaginator;

/**
 * @var ProductPaginator $products
 */
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('商品一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
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

                    <ul>
                        @foreach($products as $product)
                            <li>
                                [{{ $product->id }}]
                                <a href="">{{ $product->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
