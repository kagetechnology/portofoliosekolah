<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function edit(Request $request): View
    {
        return view('account.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'email' => [
                $user->isSiswa() ? 'prohibited' : 'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'current_password' => ['required', 'current_password'],
            'password' => [$user->must_change_password ? 'required' : 'nullable', 'confirmed', 'min:8'],
        ]);

        $updates = [];
        if (! $user->isSiswa()) {
            $updates['email'] = $data['email'];
        }
        if (! empty($data['password'])) {
            $updates['password'] = Hash::make($data['password']);
            $updates['must_change_password'] = false;
        }
        $user->update($updates);

        return back()->with('status', 'Pengaturan akun diperbarui.');
    }
}
