<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Component;

use App\Models;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class CartIcon extends Component
{
    protected $listeners = [
        'itemAdded' => 'updateCount',
        'itemCleared' => 'updateCount',
    ];

    private int $itemCount = 0;

    public function mount(): void
    {
        $this->updateCount();
    }

    public function render(): Factory|View|Application
    {
        return view('sales::component.cart-icon')
            ->with('itemCount', $this->itemCount);
    }

    public function updateCount(): void
    {
        // Eloquent直呼び出しパターン
        $this->itemCount = Models\Cart::query()
            ->join('cart_items', 'cart_items.cart_id', '=', 'carts.id')
            ->where('customer_user_id', auth()->id())
            ->count();
    }
}
