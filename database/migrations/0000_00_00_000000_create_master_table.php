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

        Schema::create('stage', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // contoh: FISIK, TEKNIK, PSIKOLOGI, EVALUASI
            $table->string('name');
            $table->unsignedInteger('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position');
        Schema::dropIfExists('stage');
    }
};
