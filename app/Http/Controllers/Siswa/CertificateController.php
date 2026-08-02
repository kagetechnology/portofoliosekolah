<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $certificates = $request->user()->certificates()->latest()->paginate(12);

        return view('siswa.certificates.index', compact('certificates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'certificate_number' => ['nullable', 'string', 'max:100'],
            'issuer' => ['nullable', 'string', 'max:150'],
            'issue_date' => ['nullable', 'date'],
            'file' => ['nullable', 'file', 'max:4096', 'mimes:pdf,jpg,jpeg,png,webp'],
        ]);

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('certificates', 'public');
        }
        $data['user_id'] = $request->user()->id;
        $data['approval_status'] = 'pending';

        Certificate::create($data);

        return redirect()->route('siswa.certificates.index')->with('status', 'Sertifikat dikirim. Tunggu persetujuan admin.');
    }

    public function destroy(Request $request, Certificate $certificate): RedirectResponse
    {
        if ($certificate->user_id !== $request->user()->id) {
            abort(403);
        }
        if ($certificate->file) {
            Storage::disk('public')->delete($certificate->file);
        }
        $certificate->delete();

        return redirect()->route('siswa.certificates.index')->with('status', 'Sertifikat dihapus.');
    }
}
