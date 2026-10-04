<?php

namespace App\Http\Controllers;

use App\Models\Gallery;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::latest()->get();

        return view('gallery.index', compact('galleries'));
    }

    public function show(Gallery $gallery)
    {
        return view('gallery.show', compact('gallery'));
    }
}
