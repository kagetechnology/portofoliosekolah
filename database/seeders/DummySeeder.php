<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Portfolio;
use App\Models\RoleChangeLog;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummySeeder extends Seeder
{
    public function run(): void
    {
        // Tambah skills master baru (skip duplicates by name)
        $skillsToAdd = [
            'Node.js', 'Next.js', 'Vue.js', 'Nuxt', 'Angular',
            'TypeScript', 'GraphQL', 'MongoDB', 'PostgreSQL', 'Redis',
            'Docker', 'Kubernetes', 'CI/CD', 'Git', 'Linux',
            'Cybersecurity', 'DevOps', 'AWS', 'Azure', 'GCP',
            'TensorFlow', 'PyTorch', 'scikit-learn', 'Pandas', 'NumPy',
            'Flutter', 'React Native', 'Swift', 'Kotlin', 'Xamarin',
            'Figma', 'Adobe XD', 'Photoshop', 'Illustrator', 'After Effects',
            'Solidity', 'Rust', 'Go', 'Java', 'C++',
            'CodeIgniter', 'Symfony', 'Express', 'Django', 'FastAPI',
            'Bootstrap', 'Bulma', 'Sass', 'LESS', 'Webpack',
            'Vite', 'Vitest', 'Jest', 'Cypress', 'Playwright',
            'Three.js', 'WebGL', 'OpenGL', 'Unity', 'Unreal Engine',
        ];

        foreach ($skillsToAdd as $name) {
            Skill::firstOrCreate(['name' => $name]);
        }

        // Tambah siswa dummy (skip jika sudah ada)
        $existingCount = User::where('role', 'siswa')->count();
        $needed = 30 - $existingCount;
        if ($needed > 0) {
            User::factory()->count($needed)->create([
                'role' => 'siswa',
                'status' => 'active',
            ]);
        }

        $siswaList = User::where('role', 'siswa')->where('status', 'active')->get();

        // Untuk tiap siswa: 1–4 portfolio
        foreach ($siswaList as $siswa) {
            $portfolioCount = fake()->numberBetween(1, 4);
            $portfolios = Portfolio::factory()
                ->count($portfolioCount)
                ->create([
                    'user_id' => $siswa->id,
                    'is_featured' => fake()->boolean(15),
                ]);

            // Tambah skill ke setiap portfolio (2–6 skill random)
            $allSkills = Skill::all()->pluck('id')->all();
            foreach ($portfolios as $p) {
                $skillCount = fake()->numberBetween(2, 6);
                $skillIds = (array) fake()->randomElements($allSkills, $skillCount);
                $sync = [];
                foreach ($skillIds as $sid) {
                    $sync[$sid] = ['level' => fake()->numberBetween(2, 5)];
                }
                $p->skills()->sync($sync);
            }

            // Sertifikat: 0–3
            $certCount = fake()->numberBetween(0, 3);
            for ($i = 0; $i < $certCount; $i++) {
                Certificate::factory()->create(['user_id' => $siswa->id]);
            }
        }

        // Tambah 3 pending siswa (butuh approval)
        User::factory()->count(3)->create([
            'role' => 'siswa',
            'status' => 'pending',
        ]);

        // Tambah dummy contact messages
        $companies = [
            ['PT. Teknologi Maju', 'recruit@tekma.co.id', 'Andi Wijaya', 'Penawaran kerja sama rekrutmen'],
            ['CV. Digital Solusi', 'hr@digital.id', 'Siti Nurhaliza', 'Undangan interview untuk lulusan terbaik'],
            ['Startup Edukasi', 'people@edu.co', 'Budi Santoso', 'Kerja sama program magang'],
            ['Bank Digital Indonesia', 'talent@bdi.co.id', 'Dewi Lestari', 'Permintaan data alumni terbaik'],
            ['PT. Kreatif Indonesia', 'hiring@kreatif.id', 'Randi Pratama', 'Penawaran posisi Web Developer'],
        ];
        foreach ($companies as $c) {
            DB::table('contacts')->insert([
                'sender_name' => $c[2],
                'sender_email' => $c[1],
                'sender_company' => $c[0],
                'subject' => $c[3],
                'message' => 'Halo Admin, kami tertarik dengan lulusan terbaik dari sekolah Anda. Kami ingin mengundang untuk program magang 3-6 bulan dengan kemungkinan kontrak full-time. Mohon informasi lebih lanjut. Terima kasih.',
                'is_read' => fake()->boolean(40),
                'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                'updated_at' => now(),
            ]);
        }
    }
}
