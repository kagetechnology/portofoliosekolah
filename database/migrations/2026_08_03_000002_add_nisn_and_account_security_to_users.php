<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nisn', 10)->nullable()->unique()->after('id');
            $table->boolean('must_change_password')->default(false)->after('password');
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nisn']);
            $table->dropColumn(['nisn', 'must_change_password']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
