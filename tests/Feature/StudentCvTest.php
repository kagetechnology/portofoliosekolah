<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCvTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_view_generated_student_cv_without_login(): void
    {
        $student = User::factory()->create();
        $approved = Portfolio::factory()->create([
            'user_id' => $student->id,
            'approval_status' => 'approved',
        ]);
        $pending = Portfolio::factory()->create([
            'user_id' => $student->id,
            'approval_status' => 'pending',
        ]);

        $this->get(route('students.cv', $student))
            ->assertOk()
            ->assertSee('Print / Simpan PDF')
            ->assertSee($approved->title)
            ->assertDontSee($pending->title);
    }

    public function test_accepted_team_project_appears_on_member_cv(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $portfolio = Portfolio::factory()->create([
            'user_id' => $owner->id,
            'project_type' => 'team',
            'approval_status' => 'approved',
        ]);
        $portfolio->contributors()->attach($member, ['status' => 'accepted']);

        $this->get(route('students.cv', $member))
            ->assertOk()
            ->assertSee($portfolio->title);
    }
}
