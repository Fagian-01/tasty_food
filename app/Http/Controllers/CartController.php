<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('cart.index', ['cart' => Cart::detailed()]);
    }

    public function add(Request $request, Menu $menu): RedirectResponse
    {
        if (! $menu->is_available) {
            return back()->withErrors(['cart' => 'Maaf, menu "'.$menu->name.'" sedang tidak tersedia.']);
        }

        $qty = (int) $request->input('qty', 1);
        $qty = max(1, min(99, $qty));

        $cart = session('cart', []);
        $cart[$menu->id] = min(99, ($cart[$menu->id] ?? 0) + $qty);
        session(['cart' => $cart]);

        return redirect()->route('cart.index')
            ->with('success', '"'.$menu->name.'" masuk keranjang.');
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:1|max:99',
        ]);

        $cart = session('cart', []);
        if (! isset($cart[$menu->id])) {
            return back()->withErrors(['cart' => 'Item tidak ada di keranjang.']);
        }

        $cart[$menu->id] = $validated['qty'];
        session(['cart' => $cart]);

        return back()->with('success', 'Jumlah diperbarui.');
    }

    public function remove(Menu $menu): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$menu->id]);
        session(['cart' => $cart]);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }
}
