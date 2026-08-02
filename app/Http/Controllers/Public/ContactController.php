<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use App\Mail\ContactReceived;

class ContactController extends Controller
{
    public function create(): View
    {
        $school = School::current();
        return view('public.contact.create', compact('school'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sender_name' => ['required', 'string', 'max:100'],
            'sender_email' => ['required', 'email', 'max:150'],
            'sender_company' => ['nullable', 'string', 'max:150'],
            'sender_phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $school = School::current();
        $data['school_id'] = $school?->id;

        $contact = Contact::create($data);

        if ($school && $school->email) {
            Mail::to($school->email)->send(new ContactReceived($contact));
        }

        return back()->with('status', 'Pesan terkirim. Admin sekolah akan menghubungi Anda lewat email.');
    }
}
