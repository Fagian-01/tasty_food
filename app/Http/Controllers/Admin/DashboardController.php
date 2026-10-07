<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Gallery;
use App\Models\Menu;
use App\Models\News;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'newsCount' => News::count(),
            'galleryCount' => Gallery::count(),
            'menuCount' => Menu::count(),
            'contactCount' => Contact::count(),
            'unreadCount' => Contact::where('status', 'unread')->count(),
            'repliedCount' => Contact::where('status', 'replied')->count(),
            'latestMessages' => Contact::latest()->take(5)->get(),
            'newOrders' => Order::where('status', 'pending')->count(),
            'processingOrders' => Order::whereIn('status', ['approved', 'cooking', 'ready'])->count(),
            'deliveringOrders' => Order::where('status', 'delivering')->count(),
            'completedOrders' => Order::where('status', 'completed')->count(),
        ]);
    }
}
