<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('admin.contact.index', [
            'contacts' => Contact::latest()->get(),
        ]);
    }

    public function show(Contact $contact): View
    {
        return view('admin.contact.show', compact('contact'));
    }

    public function edit(Contact $contact): View
    {
        return view('admin.contact.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact->update($validated);

        return redirect()->route('admin.kontak.index')
            ->with('success', 'Pesan berhasil diperbarui!');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()->route('admin.kontak.index')
            ->with('success', 'Pesan berhasil dihapus!');
    }
}
