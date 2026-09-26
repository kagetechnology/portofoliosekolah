<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('project_type', 20)->default('personal')->index()->after('category');
        });

        Schema::create('portfolio_contributors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['portfolio_id', 'user_id']);
        });

        $students = DB::table('users')->where('role', 'siswa')->orderBy('id')->get(['id', 'name']);
        $this->bulkUpdateSlugs($students->map(fn ($user) => ['id' => $user->id, 'slug' => 'student-temp-'.$user->id])->all());

        $used = array_fill_keys(
            DB::table('users')->where('role', '!=', 'siswa')->whereNotNull('slug')->pluck('slug')->all(),
            true
        );
        $slugs = [];
        $students->each(function ($user) use (&$used, &$slugs): void {
            $base = Str::slug($user->name) ?: 'siswa';
            $slug = $base;
            $suffix = 2;
            while (isset($used[$slug])) {
                $slug = $base.'-'.$suffix++;
            }
            $used[$slug] = true;
            $slugs[] = ['id' => $user->id, 'slug' => $slug];
        });
        $this->bulkUpdateSlugs($slugs);
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_contributors');
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn('project_type');
        });
    }

    private function bulkUpdateSlugs(array $rows): void
    {
        foreach (array_chunk($rows, 500) as $chunk) {
            $cases = [];
            $bindings = [];
            foreach ($chunk as $row) {
                $cases[] = 'WHEN ? THEN ?';
                $bindings[] = $row['id'];
                $bindings[] = $row['slug'];
            }

            $ids = array_column($chunk, 'id');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            DB::update(
                'UPDATE users SET slug = CASE id '.implode(' ', $cases).' END WHERE id IN ('.$placeholders.')',
                array_merge($bindings, $ids)
            );
        }
    }
};
