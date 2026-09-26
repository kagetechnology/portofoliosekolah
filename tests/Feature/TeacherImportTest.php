<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use ZipArchive;

class TeacherImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_import_teachers_with_default_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'nisn' => null]);
        School::create([
            'name' => 'Sekolah Test',
            'address' => 'Alamat Test',
            'student_default_password' => 'PASSWORDGURU',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.import-teachers.store'), [
                'file' => new UploadedFile($this->teacherXlsx(), 'daftar-guru.xlsx', null, null, true),
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status');

        $teacher = User::where('email', 'guru.baru@sekolah.test')->firstOrFail();

        $this->assertSame('Guru Baru', $teacher->name);
        $this->assertSame('guru', $teacher->role);
        $this->assertSame('active', $teacher->status);
        $this->assertSame('081234567890', $teacher->phone);
        $this->assertTrue($teacher->must_change_password);
        $this->assertTrue(Hash::check('PASSWORDGURU', $teacher->password));
    }

    public function test_import_updates_existing_teacher_without_resetting_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'nisn' => null]);
        $teacher = User::factory()->create([
            'role' => 'guru',
            'nisn' => null,
            'email' => 'guru.baru@sekolah.test',
            'password' => 'password-lama',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.import-teachers.store'), [
                'file' => new UploadedFile($this->teacherXlsx(), 'daftar-guru.xlsx', null, null, true),
            ])
            ->assertRedirect(route('admin.users.index'));

        $teacher->refresh();
        $this->assertSame('Guru Baru', $teacher->name);
        $this->assertSame('active', $teacher->status);
        $this->assertTrue(Hash::check('password-lama', $teacher->password));
    }

    private function teacherXlsx(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'teacher-import-').'.xlsx';
        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $zip->addFromString('xl/worksheets/sheet1.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>
    <row r="1">
      <c r="A1" t="inlineStr"><is><t>Nama</t></is></c>
      <c r="B1" t="inlineStr"><is><t>Email</t></is></c>
      <c r="C1" t="inlineStr"><is><t>Jenis PTK</t></is></c>
      <c r="D1" t="inlineStr"><is><t>HP</t></is></c>
    </row>
    <row r="2">
      <c r="A2" t="inlineStr"><is><t>Guru Baru</t></is></c>
      <c r="B2" t="inlineStr"><is><t>guru.baru@sekolah.test</t></is></c>
      <c r="C2" t="inlineStr"><is><t>Guru</t></is></c>
      <c r="D2" t="inlineStr"><is><t>081234567890</t></is></c>
    </row>
  </sheetData>
</worksheet>
XML);
        $zip->close();

        return $path;
    }
}
