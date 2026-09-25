<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_phone', 30);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('venue');
            // Le franc CFA n'a pas de décimales : entiers obligatoires.
            $table->unsignedBigInteger('budget_total');
            $table->unsignedBigInteger('deposit_amount')->default(0);
            $table->string('status')->default('en_attente');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
