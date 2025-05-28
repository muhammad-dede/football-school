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
        Schema::create('position', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
        });

        Schema::create('group', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->integer('age_min')->nullable();
            $table->integer('age_max')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('stage', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // contoh: FISIK, TEKNIK, PSIKOLOGI, EVALUASI
            $table->string('name');
            $table->decimal('percentage')->default(0);
            $table->unsignedInteger('order');
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position');
        Schema::dropIfExists('group');
        Schema::dropIfExists('stage');
    }
};
