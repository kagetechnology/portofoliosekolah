@component('mail::message')
# Pesan Baru: {{ $contact->subject }}

Dari: **{{ $contact->sender_name }}** ({{ $contact->sender_email }})
@if ($contact->sender_company) Perusahaan: {{ $contact->sender_company }} @endif
@if ($contact->sender_phone) Telepon: {{ $contact->sender_phone }} @endif

@component('mail::panel')
{{ $contact->message }}
@endcomponent

@component('mail::button', ['url' => url('/admin/messages/'.$contact->id)])
Buka di Dashboard
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
