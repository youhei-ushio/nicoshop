<?php

declare(strict_types=1);

use Illuminate\Support\MessageBag;

/**
 * @var MessageBag $errors
 */
?>

<h1>新規注文</h1>

@if (session()->has('succeeded'))
    <div class="alert alert-success">{{ session('succeeded') }}</div>
@endif
@if (session()->has('failed'))
    <div class="alert alert-danger">{{ session('failed') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger mt-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="post" action="{{ route('sales.orders.store') }}">
    @csrf

    <div>
        <label for="user_id">注文者ID</label>
        <input type="number" name="user_id" id="user_id" required>
    </div>

    @for($i = 1; $i <= 5; $i++)
        <div>
            <label for="product_id{{ $i }}">商品ID {{ $i }}</label>
            <input type="number" name="items[{{ $i }}][product_id]" id="product_id{{ $i }}">
            <input type="number" name="items[{{ $i }}][quantity]" id="quantity{{ $i }}">
            <label for="quantity{{ $i }}">個</label>
        </div>
    @endfor

    <button>登録</button>
</form>
<div>
    <a href="{{ route('sales.orders.index') }}">注文一覧</a>
</div>
<div>
    <a href="{{ route('sales.products.index') }}">商品一覧</a>
</div>
