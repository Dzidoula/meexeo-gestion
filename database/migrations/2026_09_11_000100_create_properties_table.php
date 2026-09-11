<?php
// database/migrations/2026_09_11_000100_create_properties_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type', 20);
            $table->string('status', 20)->default('vacant');

            // Localisation ivoirienne : ville, commune, quartier, lot, îlot
            $table->string('city');
            $table->string('commune')->nullable();
            $table->string('district')->nullable();
            $table->string('lot_number', 30)->nullable();
            $table->string('block_number', 30)->nullable();

            $table->unsignedSmallInteger('rooms')->nullable();
            $table->unsignedInteger('area_sqm')->nullable();

            // Francs CFA, sans décimales
            $table->unsignedBigInteger('monthly_rent')->default(0);
            $table->unsignedBigInteger('deposit')->default(0);

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['city', 'commune']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
