<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function checkout(): View
    {
        $cart = Cart::detailed();

        if ($cart['isEmpty'] || $cart['hasUnavailable']) {
            return view('orders.checkout', [
                'cart' => $cart,
                'blocked' => true,
            ]);
        }

        return view('orders.checkout', [
            'cart' => $cart,
            'blocked' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:30',
            // Tetap required: checkout manual (tanpa map) harus jalan seperti dulu.
            // Saat map dipakai, JS mengisi textarea ini dari hasil reverse geocoding.
            'customer_address' => 'required|string|max:2000',
            // Titik map: opsional, tapi jika salah satu diisi pasangannya wajib + harus numerik valid.
            'latitude' => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude' => 'nullable|numeric|between:-180,180|required_with:latitude',
            // Alamat hasil reverse geocoding (boleh null bila geocoding gagal; lat/lng tetap disimpan).
            'address' => 'nullable|string|max:2000',
            // Detail manual customer (boleh kosong).
            'address_note' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
        ]);

        /** @var array<int, int> $raw */
        $raw = session('cart', []);

        if (empty($raw)) {
            return redirect()->route('cart.index')
                ->withErrors(['cart' => 'Keranjang masih kosong. Pilih menu dulu ya.']);
        }

        $menus = Menu::whereIn('id', array_keys($raw))->lockForUpdate()->get()->keyBy('id');

        foreach ($raw as $menuId => $qty) {
            $menu = $menus->get((int) $menuId);
            if (! $menu || ! $menu->is_available) {
                return redirect()->route('cart.index')
                    ->withErrors(['cart' => 'Ada menu yang sudah tidak tersedia. Periksa kembali keranjangmu.']);
            }
            if ((int) $qty < 1) {
                return redirect()->route('cart.index')
                    ->withErrors(['cart' => 'Jumlah pesanan tidak valid.']);
            }
        }

        $order = DB::transaction(function () use ($raw, $menus, $validated) {
            $code = $this->generateOrderCode();

            $total = 0;
            $lines = [];
            foreach ($raw as $menuId => $qty) {
                $menu = $menus->get((int) $menuId);
                $qty = max(1, min(99, (int) $qty));
                $price = (int) $menu->price;
                $subtotal = $price * $qty;
                $total += $subtotal;
                $lines[] = [
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'price' => $price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ];
            }

            $order = Order::create([
                'order_code' => $code,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'address' => $validated['address'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'address_note' => $validated['address_note'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $total,
                'status' => 'pending',
            ]);

            $order->items()->createMany($lines);

            return $order;
        });

        Cart::clear();

        return redirect()->route('orders.success', ['order_code' => $order->order_code]);
    }

    private function generateOrderCode(): string
    {
        // KAIRO-0001 dst. Loop aman dari race: kolom unique + retry.
        for ($i = 0; $i < 10; $i++) {
            $max = (int) Order::where('order_code', 'like', 'KAIRO-%')
                ->get(['order_code'])
                ->map(fn ($o) => (int) preg_replace('/\D/', '', (string) $o->order_code))
                ->max();
            $code = 'KAIRO-'.str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);

            if (! Order::where('order_code', $code)->exists()) {
                return $code;
            }
        }

        return 'KAIRO-'.now()->format('YmdHis').'-'.random_int(100, 999);
    }

    public function success(string $order_code): View
    {
        $order = Order::where('order_code', $order_code)->firstOrFail();

        return view('orders.success', compact('order'));
    }

    public function trackForm(): View
    {
        return view('orders.track');
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'order_code' => 'required|string|max:50',
        ]);

        $code = strtoupper(trim($validated['order_code']));

        $order = Order::with('items')->where('order_code', $code)->first();

        if (! $order) {
            return back()->withErrors(['order_code' => 'Nomor pesanan tidak ditemukan. Cek lagi ya.'])->withInput();
        }

        return redirect()->route('orders.show', ['order_code' => $order->order_code]);
    }

    public function show(string $order_code): View
    {
        $order = Order::with('items')->where('order_code', $order_code)->firstOrFail();

        return view('orders.show', compact('order'));
    }

    public function confirm(Request $request, string $order_code): RedirectResponse
    {
        $order = Order::where('order_code', $order_code)->firstOrFail();

        if ($order->status !== 'delivered') {
            return back()->withErrors(['order' => 'Pesanan ini belum bisa dikonfirmasi.']);
        }

        $order->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Terima kasih! Pesanan selesai.');
    }
}
