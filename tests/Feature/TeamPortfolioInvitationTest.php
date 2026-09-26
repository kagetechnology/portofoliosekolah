<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamPortfolioInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_accept_team_project_invitation(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $portfolio = Portfolio::factory()->create([
            'user_id' => $owner->id,
            'project_type' => 'team',
            'approval_status' => 'approved',
        ]);
        $portfolio->contributors()->attach($member, ['status' => 'pending']);

        $this->actingAs($member)
            ->patch(route('siswa.team-invitations.accept', $portfolio))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('portfolio_contributors', [
            'portfolio_id' => $portfolio->id,
            'user_id' => $member->id,
            'status' => 'accepted',
        ]);

        $this->get(route('portfolios.user', $member))
            ->assertOk()
            ->assertSee($portfolio->title);
    }

    public function test_rejected_team_project_does_not_appear_as_member_work(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $portfolio = Portfolio::factory()->create([
            'user_id' => $owner->id,
            'project_type' => 'team',
            'approval_status' => 'approved',
        ]);
        $portfolio->contributors()->attach($member, ['status' => 'pending']);

        $this->actingAs($member)
            ->patch(route('siswa.team-invitations.reject', $portfolio))
            ->assertRedirect();

        $this->get(route('portfolios.user', $member))
            ->assertOk()
            ->assertDontSee($portfolio->title);
    }

    public function test_contributor_cannot_edit_project_owned_by_another_student(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $portfolio = Portfolio::factory()->create(['user_id' => $owner->id]);
        $portfolio->contributors()->attach($member, ['status' => 'accepted']);

        $this->actingAs($member)
            ->get(route('siswa.portfolios.edit', $portfolio))
            ->assertForbidden();
    }

    public function test_student_directory_counts_accepted_team_project_as_member_work(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $portfolio = Portfolio::factory()->create([
            'user_id' => $owner->id,
            'project_type' => 'team',
            'approval_status' => 'approved',
        ]);
        $portfolio->contributors()->attach($member, ['status' => 'accepted']);

        $this->get(route('students.index', ['q' => $member->name]))
            ->assertOk()
            ->assertSee($member->name)
            ->assertSee('1 karya');
    }

    public function test_student_can_search_active_contributors_without_loading_all_students(): void
    {
        $student = User::factory()->create();
        $match = User::factory()->create(['name' => 'Anggota Tim Dicari', 'school_class' => 'XI PPLG']);
        User::factory()->create(['name' => 'Siswa Tidak Cocok']);

        $this->actingAs($student)
            ->getJson(route('siswa.contributors.search', ['q' => 'Tim Dicari']))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $match->id,
                'name' => 'Anggota Tim Dicari',
                'school_class' => 'XI PPLG',
            ]);
    }
}
