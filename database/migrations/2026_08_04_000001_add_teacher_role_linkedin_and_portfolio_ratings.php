<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('linkedin_url')->nullable()->after('instagram_url');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'guru', 'siswa') NOT NULL DEFAULT 'siswa'");
        }

        Schema::create('portfolio_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->timestamps();
            $table->unique(['portfolio_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_ratings');
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'siswa') NOT NULL DEFAULT 'siswa'");
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('linkedin_url');
        });
    }
};
