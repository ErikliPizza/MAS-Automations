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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name');
            $table->string('surname');
            $table->string('email')->unique();
            $table->string('password');

            $table->enum('role', ['additional', 'admin', 'root']);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->string('title')->nullable();
            $table->date('birthday')->nullable();
            $table->bigInteger('id_number')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('phone')->nullable();

            $table->date('start_date_of_work')->nullable();
            $table->date('end_date_of_work')->nullable();
            $table->text('reason_of_leaving')->nullable();
            $table->decimal('salary', 8, 2)->nullable();

            $table->timestamp('email_verified_at')->default(now());
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
