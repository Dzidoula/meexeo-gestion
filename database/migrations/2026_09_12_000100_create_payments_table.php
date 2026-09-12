<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();

            // Premier jour du mois concerné (ex. 2026-05-01 pour "Loyer Mai 2026").
            $table->date('month');
            $table->date('paid_on');

            // Franc CFA, sans décimales.
            $table->unsignedBigInteger('amount');

            $table->string('method', 20);
            $table->string('reference')->nullable();
            $table->string('proof_path');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['lease_id', 'month']);
            $table->index('paid_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
