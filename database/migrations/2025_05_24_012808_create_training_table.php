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
        Schema::create('training', function (Blueprint $table) {
            $table->id();
            $table->string('team_code', 20)->nullable()->index();
            $table->unsignedBigInteger('period_id')->nullable()->index();
            $table->unsignedBigInteger('coach_id')->nullable()->index();
            $table->date('training_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('training_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_id')->nullable()->index();
            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->string('attendance')->nullable()->default('ABSENCE');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training');
        Schema::dropIfExists('training_attendance');
    }
};
