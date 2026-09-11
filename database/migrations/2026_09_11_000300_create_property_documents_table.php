<?php
// database/migrations/2026_09_11_000300_create_property_documents_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->string('path');
            $table->string('original_name');
            $table->boolean('verified')->default(false);
            $table->timestamps();

            $table->index(['property_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_documents');
    }
};
