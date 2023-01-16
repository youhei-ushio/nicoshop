<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Components;

use App\Models;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class CartIcon extends Component
{
    protected $listeners = [
        'itemAdded' => 'updateCount',
    ];

    public int $itemCount = 0;

    public function mount(): void
    {
        $this->updateCount();
    }

    public function render(): Factory|View|Application
    {
        return view('sales::component.cart-icon');
    }

    public function updateCount(): void
    {
        $this->itemCount = Models\Cart::query()
            ->join('cart_items', 'cart_items.cart_id', '=', 'carts.id')
            ->where('customer_user_id', auth()->id())
            ->count();
    }
}
