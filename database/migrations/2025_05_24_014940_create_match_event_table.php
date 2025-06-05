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
        Schema::create('match_event', function (Blueprint $table) {
            $table->id();
            $table->string('group_code', 20)->nullable()->index();
            $table->unsignedBigInteger('period_id')->nullable()->index();
            $table->unsignedBigInteger('coach_id')->nullable()->index();
            $table->string('opponent')->nullable();
            $table->date('match_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('group_score')->nullable();
            $table->integer('opponent_score')->nullable();
            $table->string('location')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('group_code')->references('code')->on('group')->onDelete('cascade');
            $table->foreign('period_id')->references('id')->on('period')->onDelete('cascade');
            $table->foreign('coach_id')->references('id')->on('coach')->onDelete('cascade');
        });

        Schema::create('match_event_participant', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_event_id')->nullable()->index();
            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->string('attendance')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('match_event_id')->references('id')->on('match_event')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('student')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_event');
        Schema::dropIfExists('match_event_participant');
    }
};
