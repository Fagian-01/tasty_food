<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Gallery;
use App\Models\News;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'newsCount' => News::count(),
            'galleryCount' => Gallery::count(),
            'contactCount' => Contact::count(),
            'latestMessages' => Contact::latest()->take(5)->get(),
        ]);
    }
}
