<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSchoolSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_change_student_default_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'nisn' => null]);
        $school = School::create([
            'name' => 'Sekolah Test',
            'address' => 'Alamat Lama',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.school.update'), [
                'name' => $school->name,
                'address' => $school->address,
                'student_default_password' => 'PASSWORDBARU',
            ])
            ->assertRedirect(route('admin.school.edit'))
            ->assertSessionHas('status');

        $this->assertSame('PASSWORDBARU', School::studentDefaultPassword());
        $this->assertNotSame('PASSWORDBARU', $school->fresh()->getRawOriginal('student_default_password'));
    }

    public function test_default_password_falls_back_for_existing_school(): void
    {
        School::create([
            'name' => 'Sekolah Test',
            'address' => 'Alamat Test',
        ]);

        $this->assertSame(School::FALLBACK_STUDENT_PASSWORD, School::studentDefaultPassword());
    }
}
