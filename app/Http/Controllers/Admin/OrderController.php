<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = Order::withCount('items')->latest();

        if ($status && in_array($status, Order::STATUSES, true)) {
            $query->where('status', $status);
        } else {
            $status = null;
        }

        return view('admin.orders.index', [
            'orders' => $query->paginate(20)->withQueryString(),
            'status' => $status,
            'statuses' => Order::STATUSES,
            'pendingCount' => Order::where('status', 'pending')->count(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load('items.menu');

        return view('admin.orders.show', compact('order'));
    }

    public function approve(Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return back()->withErrors(['order' => 'Hanya pesanan pending yang bisa disetujui.']);
        }

        $order->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', $order->order_code.' disetujui.');
    }

    public function reject(Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return back()->withErrors(['order' => 'Hanya pesanan pending yang bisa ditolak.']);
        }

        $order->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        return back()->with('success', $order->order_code.' ditolak.');
    }

    public function advance(Order $order): RedirectResponse
    {
        $next = $order->nextStatus();

        if ($next === null) {
            return back()->withErrors(['order' => 'Status ini tidak bisa dimajukan lagi.']);
        }

        $timestampField = Order::TIMESTAMP_FOR_STATUS[$next];

        $order->update([
            'status' => $next,
            $timestampField => now(),
        ]);

        return back()->with('success', $order->order_code.' → '.$order->statusLabel().'.');
    }
}
