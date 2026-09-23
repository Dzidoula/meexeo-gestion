<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_stays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_room_id')->constrained()->restrictOnDelete();
            $table->string('guest_name');
            $table->string('guest_phone', 30);
            $table->date('arrival_date');
            $table->date('departure_date');
            // Le franc CFA n'a pas de décimales : entiers obligatoires.
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('deposit_amount')->default(0);
            $table->string('status')->default('reserve');
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_stays');
    }
};
