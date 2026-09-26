<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeamInvitationController extends Controller
{
    public function accept(Request $request, Portfolio $portfolio): RedirectResponse
    {
        return $this->respond($request, $portfolio, 'accepted');
    }

    public function reject(Request $request, Portfolio $portfolio): RedirectResponse
    {
        return $this->respond($request, $portfolio, 'rejected');
    }

    private function respond(Request $request, Portfolio $portfolio, string $status): RedirectResponse
    {
        $invitation = $request->user()->contributedPortfolios()
            ->whereKey($portfolio->id)
            ->wherePivot('status', 'pending')
            ->first();

        if (! $invitation) {
            return back()->withErrors(['invitation' => 'Undangan project tidak ditemukan atau sudah ditanggapi.']);
        }

        $request->user()->contributedPortfolios()->updateExistingPivot($portfolio->id, [
            'status' => $status,
            'responded_at' => now(),
        ]);

        $message = $status === 'accepted'
            ? 'Project tim diterima dan ditambahkan ke karya Anda.'
            : 'Undangan project tim ditolak.';

        return back()->with('status', $message);
    }
}
