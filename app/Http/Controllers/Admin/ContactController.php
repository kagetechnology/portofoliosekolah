<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('filter')->toString();
        $contacts = Contact::query()
            ->when($filter === 'unread', fn ($q) => $q->where('is_read', false))
            ->when($filter === 'read', fn ($q) => $q->where('is_read', true))
            ->latest()
            ->paginate(15)
            ->withQueryString();
        return view('admin.contacts.index', compact('contacts', 'filter'));
    }

    public function show(Contact $contact): View
    {
        if (! $contact->is_read) {
            $contact->update(['is_read' => true]);
        }
        return view('admin.contacts.show', compact('contact'));
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')->with('status', 'Pesan dihapus.');
    }
}
