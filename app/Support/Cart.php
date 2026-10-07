<?php

namespace App\Support;

use App\Models\Menu;

class Cart
{
    /**
     * @return array{items: array<int, array{menu_id:int, menu:?Menu, qty:int, price:int, subtotal:int, available:bool}>, total:int, count:int, isEmpty:bool, hasUnavailable:bool}
     */
    public static function detailed(): array
    {
        /** @var array<int, int> $raw */
        $raw = session('cart', []);
        $items = [];
        $total = 0;
        $count = 0;
        $hasUnavailable = false;

        if (! empty($raw)) {
            $menus = Menu::whereIn('id', array_keys($raw))->get()->keyBy('id');

            foreach ($raw as $menuId => $qty) {
                $menu = $menus->get((int) $menuId);
                $qty = max(1, (int) $qty);
                $available = $menu !== null && (bool) $menu->is_available;

                if (! $available) {
                    $hasUnavailable = true;
                }

                $price = $menu ? (int) $menu->price : 0;
                $subtotal = $available ? $price * $qty : 0;
                $total += $subtotal;
                $count += $qty;

                $items[] = [
                    'menu_id' => (int) $menuId,
                    'menu' => $menu,
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'available' => $available,
                ];
            }
        }

        return [
            'items' => $items,
            'total' => $total,
            'count' => $count,
            'isEmpty' => empty($items),
            'hasUnavailable' => $hasUnavailable,
        ];
    }

    public static function clear(): void
    {
        session()->forget('cart');
    }
}
