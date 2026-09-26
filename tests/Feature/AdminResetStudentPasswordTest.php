<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminResetStudentPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reset_student_password_to_default(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'nisn' => null]);
        School::create([
            'name' => 'Sekolah Test',
            'address' => 'Alamat Test',
            'student_default_password' => 'PASSWORDBARU',
        ]);
        $student = User::factory()->create([
            'password' => 'custom-password',
            'must_change_password' => false,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.reset-password', $student))
            ->assertRedirect()
            ->assertSessionHas('status');

        $student->refresh();

        $this->assertTrue(Hash::check('PASSWORDBARU', $student->password));
        $this->assertTrue($student->must_change_password);
    }

    public function test_admin_cannot_reset_non_student_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'nisn' => null]);
        $teacher = User::factory()->create([
            'role' => 'guru',
            'nisn' => null,
            'password' => 'custom-password',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.reset-password', $teacher))
            ->assertRedirect()
            ->assertSessionHasErrors('user');

        $this->assertTrue(Hash::check('custom-password', $teacher->fresh()->password));
    }

    public function test_student_cannot_reset_another_student_password(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create(['password' => 'custom-password']);

        $this->actingAs($student)
            ->patch(route('admin.users.reset-password', $otherStudent))
            ->assertForbidden();

        $this->assertTrue(Hash::check('custom-password', $otherStudent->fresh()->password));
    }
}
