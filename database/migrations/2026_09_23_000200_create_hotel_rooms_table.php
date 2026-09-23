<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_room_type_id')->constrained()->restrictOnDelete();
            $table->string('number')->unique();
            // Le franc CFA n'a pas de décimales : entier obligatoire.
            $table->unsignedBigInteger('nightly_rate');
            $table->text('amenities')->nullable();
            $table->string('status')->default('disponible_chambre');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
