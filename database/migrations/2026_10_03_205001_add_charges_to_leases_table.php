<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            // monthly_rent reste le montant total dû, pour ne rien casser de l'existant.
            // charges en est la part récupérable, affichée séparément dans le portail.
            $table->unsignedBigInteger('charges')->default(0)->after('monthly_rent');
        });
    }

    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn('charges');
        });
    }
};
