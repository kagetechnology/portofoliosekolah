<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function show(Certificate $certificate): View
    {
        $certificate->load('user');
        abort_unless($certificate->isApproved() && $certificate->user && $certificate->user->isSiswa() && $certificate->user->isActive(), 404);

        $related = Certificate::with('user')
            ->where('user_id', $certificate->user_id)
            ->where('approval_status', 'approved')
            ->where('id', '!=', $certificate->id)
            ->latest()
            ->limit(3)
            ->get();

        return view('public.certificates.show', compact('certificate', 'related'));
    }
}
