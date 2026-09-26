<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_contributors', function (Blueprint $table) {
            $table->string('status', 20)->default('accepted')->index()->after('user_id');
            $table->timestamp('responded_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_contributors', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'responded_at']);
        });
    }
};
