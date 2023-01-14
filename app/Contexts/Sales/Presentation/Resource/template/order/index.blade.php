<?php

declare(strict_types=1);

use App\Contexts\Sales\Application\Persistence\OrderPaginator;

/**
 * @var OrderPaginator $orders
 */
?>

<h1>注文一覧</h1>

@if (session()->has('succeeded'))
    <div class="alert alert-success">{{ session('succeeded') }}</div>
@endif
@if (session()->has('failed'))
    <div class="alert alert-danger">{{ session('failed') }}</div>
@endif

<div>
    <a href="{{ route('sales.orders.create') }}">新規注文</a>
</div>

<div>
    @if ($orders->currentPage() > 1)
        <span>
            <a href="{{ route('sales.orders.index') }}?limit={{ $orders->perPage() }}&page={{ $orders->currentPage() - 1 }}">
                前へ
            </a>
        </span>
    @endif
    <span>
        <a href="{{ route('sales.orders.index') }}?limit={{ $orders->perPage() }}&page={{ $orders->currentPage() + 1 }}">
            次へ
        </a>
    </span>
</div>
<ul>
    @foreach($orders as $order)
        <li>[{{ $order->date->format('Y-m-d') }}]
            <a href="">{{ $order->id }}</a>
            <div>Customer: {{ $order->customerUserId }}</div>
        </li>
    @endforeach
</ul>
