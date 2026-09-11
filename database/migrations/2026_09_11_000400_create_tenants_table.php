<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('last_name');
            $table->string('first_names');
            $table->date('birth_date')->nullable();
            $table->string('id_number', 60)->nullable();
            $table->string('marital_status', 20)->nullable();
            $table->string('occupation')->nullable();
            $table->string('workplace')->nullable();

            $table->string('phone1', 30);
            $table->string('phone2', 30)->nullable();
            $table->string('email')->nullable();

            $table->string('emergency_name')->nullable();
            $table->string('emergency_phone', 30)->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('spouse_phone', 30)->nullable();

            $table->string('status', 20)->default('pending');
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('last_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
