<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PortfolioPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_portfolio_shows_shareable_preview_notification(): void
    {
        Storage::fake('public');
        $student = User::factory()->create();

        $this->actingAs($student)
            ->post(route('siswa.portfolios.store'), [
                'title' => 'Project Baru',
                'description' => 'Aplikasi ini dirancang untuk mendokumentasikan karya siswa dengan fitur lengkap dan terstruktur. Sistem dilengkapi dengan antarmuka yang ramah pengguna, validasi input yang aman, serta integrasi multimedia seperti gambar dan video demo. Proyek ini menyelesaikan masalah pencatatan portofolio manual di sekolah sehingga seluruh data karya dapat ditampilkan secara profesional untuk perekrut dan dunia industri.',
                'project_type' => 'personal',
                'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            ])
            ->assertRedirect(route('siswa.portfolios.index'))
            ->assertSessionHas('status')
            ->assertSessionHas('share_url', fn (string $url) => str_contains($url, '/portfolio-preview/project-baru') && str_contains($url, 'signature='));
    }

    public function test_portfolio_description_must_have_at_least_300_characters(): void
    {
        Storage::fake('public');
        $student = User::factory()->create();

        $this->actingAs($student)
            ->post(route('siswa.portfolios.store'), [
                'title' => 'Project Pendek',
                'description' => 'Terlalu pendek.',
                'project_type' => 'personal',
                'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            ])
            ->assertSessionHasErrors('description');
    }

    public function test_guest_must_log_in_to_view_signed_preview(): void
    {
        $portfolio = Portfolio::factory()->create(['approval_status' => 'pending']);
        $url = URL::signedRoute('portfolios.preview', $portfolio);

        $this->get($url)->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_pending_portfolio_with_signed_url(): void
    {
        $viewer = User::factory()->create();
        $portfolio = Portfolio::factory()->create(['approval_status' => 'pending', 'views' => 0]);
        $url = URL::signedRoute('portfolios.preview', $portfolio);

        $this->actingAs($viewer)
            ->get($url)
            ->assertOk()
            ->assertSee('Mode preview')
            ->assertSee($portfolio->title);

        $this->assertSame(0, $portfolio->fresh()->views);
    }

    public function test_authenticated_user_cannot_view_preview_with_invalid_signature(): void
    {
        $viewer = User::factory()->create();
        $portfolio = Portfolio::factory()->create(['approval_status' => 'pending']);

        $this->actingAs($viewer)
            ->get(route('portfolios.preview', $portfolio))
            ->assertForbidden();
    }
}
