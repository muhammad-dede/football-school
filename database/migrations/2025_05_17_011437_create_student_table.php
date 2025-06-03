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
        Schema::create('student', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('national_id_number')->nullable();
            $table->string('photo')->nullable();
            // Informasi Pemain
            $table->enum('dominant_foot', ['RIGHT', 'LEFT', 'BOTH']);
            $table->float('height_cm')->nullable();
            $table->float('weight_kg')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('set null');
        });

        Schema::create('student_enrollment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('period_id')->nullable()->index();
            $table->string('group_code', 20)->nullable()->index();
            $table->string('position_code', 20)->nullable()->index();
            $table->string('alternative_position_code', 20)->nullable()->index();
            $table->integer('jersey_number')->nullable();
            $table->date('join_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('student')->onDelete('cascade');
            $table->foreign('period_id')->references('id')->on('period')->onDelete('set null');
            $table->foreign('group_code')->references('code')->on('group')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('position_code')->references('code')->on('position')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('alternative_position_code')->references('code')->on('position')->onDelete('set null')->onUpdate('cascade');
        });

        Schema::create('billing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->unsignedBigInteger('period_id')->nullable()->index();
            $table->string('billing_type_code')->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->date('due_date')->nullable();
            $table->string('status')->default('UNPAID');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('student')->onDelete('cascade');
            $table->foreign('period_id')->references('id')->on('student')->onDelete('set null');
            $table->foreign('billing_type_code')->references('code')->on('billing_type')->onDelete('set null')->onUpdate('cascade');
        });

        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billing_id')->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date')->nullable();
            $table->enum('method', ['TRANSFER', 'CASH']);
            // Informasi Transfer (jika metode = TRANSFER)
            $table->string('receiver_bank_name')->nullable();
            $table->string('receiver_account_number')->nullable();
            $table->string('receiver_account_holder_name')->nullable();
            $table->string('sender_bank_name')->nullable();
            $table->string('sender_account_number')->nullable();
            $table->string('sender_account_holder_name')->nullable();
            $table->string('proof_file')->nullable();
            $table->string('reference_number')->nullable();
            // Catatan Pembayaran
            $table->text('notes')->nullable();
            $table->string('status')->default('PAID');
            $table->timestamps();

            $table->foreign('billing_id')->references('id')->on('billing')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student');
        Schema::dropIfExists('student_enrollment');
        Schema::dropIfExists('billing');
        Schema::dropIfExists('payment');
    }
};
