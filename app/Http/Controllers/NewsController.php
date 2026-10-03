<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->get();

        return view('news.index', compact('news'));
    }

    public function create()
    {
        return view('news.create');
    }

    public function store(Request $request)
    {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:news,slug',
        'image' => 'nullable|image|max:2048',
        'content' => 'required|string',
    ]);

    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('news', 'public');
    }

    News::create($validated);

    return redirect('/berita')->with('success', 'Berita berhasil ditambahkan!');
    }

    public function show(News $news)
    {
        return view('news.show', compact('news'));
    }

    public function edit(News $news)
    {
        return view('news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:news,slug,' . $news->id,
        'image' => 'nullable|image|max:2048',
        'content' => 'required|string',
    ]);

    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('news', 'public');
    }

    $news->update($validated);

    return redirect('/berita')->with('success', 'Berita berhasil diperbarui!');
    }

    public function destroy(News $news)
    {
    $news->delete();

    return redirect('/berita')->with('success', 'Berita berhasil dihapus!');
    }
}