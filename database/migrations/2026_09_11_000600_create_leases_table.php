<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            $table->date('start_date');
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();

            // Le loyer du bail est figé à la date de signature : celui du bien peut changer ensuite.
            $table->unsignedBigInteger('monthly_rent');
            $table->unsignedBigInteger('deposit_paid')->default(0);

            // Jour d'échéance mensuelle, base des alertes J-5 / J-0 / J+2 / J+5 du plan suivant.
            $table->unsignedTinyInteger('due_day')->default(1);

            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['property_id', 'status']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
