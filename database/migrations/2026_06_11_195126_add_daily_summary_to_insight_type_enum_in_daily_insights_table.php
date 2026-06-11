<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE daily_insights MODIFY COLUMN insight_type ENUM('warning', 'recommendation', 'daily_summary')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE daily_insights MODIFY COLUMN insight_type ENUM('warning', 'recommendation')");
    }
};
