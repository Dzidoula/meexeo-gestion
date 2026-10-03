<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Documents publiés au locataire dans son portail (quittances, bail signé).
     * Distinct de `tenant_documents`, qui porte les pièces justificatives
     * collectées par le gestionnaire au moment du dossier.
     */
    public function up(): void
    {
        Schema::create('portal_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lease_id')->nullable()->constrained()->nullOnDelete();

            $table->string('category', 20);
            $table->string('path');
            $table->string('original_name');
            $table->unsignedBigInteger('size')->default(0);

            // Mois couvert par une quittance ; nul pour les autres catégories.
            $table->date('period')->nullable();
            $table->timestamp('issued_at')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'category']);
            $table->index(['lease_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_documents');
    }
};
