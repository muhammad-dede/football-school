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
        Schema::create('report_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->unsignedBigInteger('period_id')->nullable()->index();
            $table->decimal('training_score', 3, 2);
            $table->decimal('match_event_score', 3, 2);
            $table->decimal('total_score', 3, 2);
            $table->string('status');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('student')->onDelete('cascade');
            $table->foreign('period_id')->references('id')->on('period')->onDelete('set null');
        });

        Schema::create('report_student_stage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_student_id')->nullable()->index();
            $table->string('stage_code', 20)->nullable()->index();
            $table->decimal('score', 3, 2);
            $table->timestamps();

            $table->foreign('report_student_id')->references('id')->on('report_student')->onDelete('cascade');
            $table->foreign('stage_code')->references('code')->on('stage')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_student');
        Schema::dropIfExists('report_student_stage');
    }
};
