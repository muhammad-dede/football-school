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
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('percentage')->default(0);
            $table->unsignedInteger('order');
            $table->boolean('is_active')->default(true);
        });

        Schema::create('billing_type', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name'); // Contoh: Pendaftaran, SPP
        });

        Schema::create('bank_account', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_holder_name');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('billing_type');
        Schema::dropIfExists('bank_account');
    }
};
