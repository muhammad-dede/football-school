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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('training_schedule_id')->nullable()->index();
            $table->unsignedBigInteger('match_schedule_id')->nullable()->index();
            $table->enum('status', ['PRESENT', 'ABSENT', 'SICK', 'PERMISSION']);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('student')->onDelete('cascade');
            $table->foreign('training_schedule_id')->references('id')->on('training_schedule')->onDelete('set null');
            $table->foreign('match_schedule_id')->references('id')->on('match_schedule')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
