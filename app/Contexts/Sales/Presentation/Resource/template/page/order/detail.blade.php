<?php

declare(strict_types=1);

use App\Contexts\Sales\Application\Persistence\OrderQueryResult;

/**
 * @var OrderQueryResult $order
 */
?>

<h1>注文詳細</h1>

@if (session()->has('succeeded'))
    <div class="alert alert-success">{{ session('succeeded') }}</div>
@endif
@if (session()->has('failed'))
    <div class="alert alert-danger">{{ session('failed') }}</div>
@endif

<div>
    <a href="{{ route('sales.orders.index') }}">一覧へ戻る</a>
</div>

<div>
    ID: {{ $order->id }}
</div>
<div>
    Customer ID: {{ $order->customerUserId }}
</div>
<div>
    Date: {{ $order->date->format('Y-m-d') }}
</div>
<div>
    @if ($order->accepted)
        <div>受付済</div>
    @else
        <form method="post" action="{{ route('sales.orders.accept', ['id' => $order->id]) }}">
            @csrf
            <button>受付</button>
        </form>
    @endif
</div>
