<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plant_types', function (Blueprint $table) {
            $table->id();
            $table->string('common_name');
            $table->text('description');
            $table->binary('standard_image');
            $table->string('scientific_name');
            $table->float('ideal_temp');
            $table->float('ideal_moisture');
            $table->float('ideal_light_lux');
            $table->float('ideal_humidity');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE plant_types MODIFY standard_image LONGBLOB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_types');
    }
};
