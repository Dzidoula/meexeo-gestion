<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            // Qui a écrit : 'tenant' ou 'manager'. L'expéditeur gestionnaire peut être
            // nul (message automatique du système, par exemple un rappel d'échéance).
            $table->string('sender', 10);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Fil de discussion : nul pour un message racine, sinon l'id de la racine.
            $table->foreignId('parent_id')->nullable()->constrained('tenant_messages')->cascadeOnDelete();

            $table->string('subject')->nullable();
            $table->text('body');
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'read_at']);
            $table->index(['parent_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_messages');
    }
};
