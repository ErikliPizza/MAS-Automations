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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index(); // +
            $table->unsignedBigInteger('service_id')->index(); // +
            $table->unsignedBigInteger('user_id')->index(); // +
            $table->string('slug')->unique()->index(); // +
            $table->string('name'); // +
            $table->string('email')->nullable(); // +
            $table->string('phone')->nullable(); // +
            $table->timestamp('start_time'); // +
            $table->timestamp('end_time'); // +
            $table->enum('status', ['booked', 'completed', 'cancelled', 'missed']); // e.g., booked, completed, cancelled
            $table->text('notes')->nullable(); // +
            $table->decimal('price', 8, 2)->nullable(); // 4
            $table->timestamps(); // +

            // Foreign key constraints
            //$table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
