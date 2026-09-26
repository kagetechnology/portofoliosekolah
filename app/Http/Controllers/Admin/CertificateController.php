<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $status = $request->string('status')->toString();

        $certificates = Certificate::with('user')
            ->when($q !== '', fn ($qb) => $qb->where(function ($w) use ($q) {
                $w->where('title', 'like', "%$q%")
                    ->orWhere('issuer', 'like', "%$q%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%$q%"));
            }))
            ->when(in_array($status, ['pending', 'approved', 'rejected'], true), fn ($qb) => $qb->where('approval_status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $routePrefix = $request->user()->isGuru() ? 'guru' : 'admin';

        return view('admin.certificates.index', compact('certificates', 'q', 'status', 'routePrefix'));
    }

    public function show(Request $request, Certificate $certificate): View
    {
        $certificate->load('user');

        $routePrefix = $request->user()->isGuru() ? 'guru' : 'admin';

        return view('admin.certificates.show', compact('certificate', 'routePrefix'));
    }

    public function approve(Certificate $certificate): RedirectResponse
    {
        $certificate->update(['approval_status' => 'approved']);

        return back()->with('status', 'Sertifikat disetujui.');
    }

    public function reject(Certificate $certificate): RedirectResponse
    {
        $certificate->update(['approval_status' => 'rejected']);

        return back()->with('status', 'Sertifikat ditolak.');
    }
}
