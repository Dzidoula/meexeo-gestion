<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_stay_id')->constrained()->cascadeOnDelete();
            // Le franc CFA n'a pas de décimales : entier obligatoire.
            $table->unsignedBigInteger('amount');
            $table->date('paid_on');
            $table->string('method')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_payments');
    }
};
