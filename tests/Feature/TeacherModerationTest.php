<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_approve_and_rate_a_student_project(): void
    {
        $teacher = User::factory()->create(['role' => 'guru', 'nisn' => null]);
        $portfolio = Portfolio::factory()->create(['approval_status' => 'pending']);

        $this->actingAs($teacher)
            ->patch(route('guru.portfolios.approve', $portfolio))
            ->assertRedirect();

        $this->actingAs($teacher)
            ->put(route('guru.portfolios.rate', $portfolio), ['rating' => 5])
            ->assertRedirect();

        $this->assertDatabaseHas('portfolios', ['id' => $portfolio->id, 'approval_status' => 'approved']);
        $this->assertDatabaseHas('portfolio_ratings', ['portfolio_id' => $portfolio->id, 'user_id' => $teacher->id, 'rating' => 5]);
    }

    public function test_student_cannot_access_teacher_moderation(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->get(route('guru.dashboard'))->assertForbidden();
    }

    public function test_teacher_must_include_note_when_rejecting_portfolio(): void
    {
        $teacher = User::factory()->create(['role' => 'guru', 'nisn' => null]);
        $portfolio = Portfolio::factory()->create(['approval_status' => 'pending']);

        $this->actingAs($teacher)
            ->patch(route('guru.portfolios.reject', $portfolio))
            ->assertSessionHasErrors('rejection_note');

        $this->actingAs($teacher)
            ->patch(route('guru.portfolios.reject', $portfolio), [
                'rejection_note' => 'Link demo belum dapat dibuka. Mohon diperbaiki.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('portfolios', [
            'id' => $portfolio->id,
            'approval_status' => 'rejected',
            'rejection_note' => 'Link demo belum dapat dibuka. Mohon diperbaiki.',
        ]);
    }

    public function test_teacher_can_edit_portfolio_and_assign_skills(): void
    {
        $teacher = User::factory()->create(['role' => 'guru', 'nisn' => null]);
        $portfolio = Portfolio::factory()->create();
        $skill = Skill::create(['name' => 'Laravel']);

        $this->actingAs($teacher)
            ->put(route('guru.portfolios.update', $portfolio), [
                'title' => 'Project yang Diperbaiki Guru',
                'description' => 'Deskripsi project setelah diperbaiki oleh guru pembimbing untuk memastikan karya memenuhi standar penilaian kompetensi sekolah. Mencakup latar belakang pembuatan aplikasi, arsitektur basis data, integrasi antarmuka, serta hasil pengujian fungsionalitas yang telah diverifikasi kelayakannya sesuai standar industri terkini.',
                'project_type' => 'personal',
                'skill_ids' => [$skill->id],
                'skill_levels' => [$skill->id => 4],
            ])
            ->assertRedirect(route('guru.portfolios.show', $portfolio));

        $this->assertDatabaseHas('portfolios', [
            'id' => $portfolio->id,
            'title' => 'Project yang Diperbaiki Guru',
        ]);
        $this->assertDatabaseHas('portfolio_skill', [
            'portfolio_id' => $portfolio->id,
            'skill_id' => $skill->id,
            'level' => 4,
        ]);
    }

    public function test_student_revision_returns_rejected_portfolio_to_review_without_changing_skills(): void
    {
        $student = User::factory()->create();
        $portfolio = Portfolio::factory()->create([
            'user_id' => $student->id,
            'approval_status' => 'rejected',
            'rejection_note' => 'Deskripsi belum menjelaskan hasil project.',
        ]);
        $skill = Skill::create(['name' => 'PHP']);
        $portfolio->skills()->attach($skill, ['level' => 3]);

        $this->actingAs($student)
            ->patch(route('siswa.portfolios.update', $portfolio), [
                'title' => $portfolio->title,
                'description' => 'Deskripsi sudah diperbaiki secara menyeluruh oleh siswa dengan menjelaskan latar belakang masalah, fitur-fitur kunci yang dikembangkan, peran teknis yang dikerjakan, serta tantangan yang berhasil diatasi selama proses implementasi. Dokumentasi ini disusun agar karya siap ditinjau kembali oleh guru pembimbing.',
                'project_type' => 'personal',
                'skill_ids' => [],
            ])
            ->assertRedirect(route('siswa.portfolios.index'));

        $this->assertDatabaseHas('portfolios', [
            'id' => $portfolio->id,
            'approval_status' => 'pending',
            'rejection_note' => null,
        ]);
        $this->assertDatabaseHas('portfolio_skill', [
            'portfolio_id' => $portfolio->id,
            'skill_id' => $skill->id,
            'level' => 3,
        ]);
    }
}
