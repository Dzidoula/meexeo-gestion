<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_type_id')->constrained()->restrictOnDelete();
            $table->string('brand');
            $table->string('model');
            $table->string('fuel_type');
            $table->string('transmission');
            $table->unsignedInteger('seats');
            // Le franc CFA n'a pas de décimales : entier obligatoire.
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
