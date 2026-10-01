<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hotel_stays', function (Blueprint $table) {
            $table->string('confirmation_status')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('hotel_stays', function (Blueprint $table) {
            $table->dropColumn('confirmation_status');
        });
    }
};
