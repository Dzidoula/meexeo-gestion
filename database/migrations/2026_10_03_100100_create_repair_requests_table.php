<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['plomberie', 'electricite', 'serrure', 'peinture', 'climatisation', 'autre']);
            $table->text('description');
            $table->enum('urgency', ['faible', 'moyenne', 'urgente'])->default('moyenne');
            $table->enum('status', ['recu', 'en_cours', 'repare', 'cloture'])->default('recu');
            $table->string('ticket_no', 20)->unique();
            $table->json('photos')->nullable();
            $table->string('video_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_requests');
    }
};
