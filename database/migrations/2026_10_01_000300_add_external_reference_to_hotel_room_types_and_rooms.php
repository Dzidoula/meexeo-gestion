<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_room_types', function (Blueprint $table) {
            $table->string('external_source')->nullable()->after('amenities');
            $table->unsignedBigInteger('external_id')->nullable()->after('external_source');
            $table->unique(['external_source', 'external_id']);
        });

        Schema::table('hotel_rooms', function (Blueprint $table) {
            $table->string('external_source')->nullable()->after('status');
            $table->unsignedBigInteger('external_id')->nullable()->after('external_source');
            $table->unique(['external_source', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::table('hotel_room_types', function (Blueprint $table) {
            $table->dropUnique(['external_source', 'external_id']);
            $table->dropColumn(['external_source', 'external_id']);
        });

        Schema::table('hotel_rooms', function (Blueprint $table) {
            $table->dropUnique(['external_source', 'external_id']);
            $table->dropColumn(['external_source', 'external_id']);
        });
    }
};
