<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Support\ImageOptimizer;
use App\Support\RichText;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function edit(): View
    {
        $school = School::current() ?? new School;

        return view('admin.school.edit', compact('school'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'mission' => ['nullable', 'string'],
            'kepala_sekolah' => ['nullable', 'string', 'max:150'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'student_default_password' => ['required', 'string', 'min:8', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $school = School::current() ?? new School(['name' => $data['name']]);

        if ($request->hasFile('logo')) {
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }
            $data['logo'] = ImageOptimizer::optimizeAndStore(
                $request->file('logo'),
                'school',
                600,
                600,
                85
            );
        }

        foreach (['description', 'vision', 'mission'] as $field) {
            $data[$field] = RichText::clean($data[$field] ?? null);
        }

        $school->fill($data)->save();

        return redirect()->route('admin.school.edit')->with('status', 'Profil sekolah disimpan.');
    }
}
