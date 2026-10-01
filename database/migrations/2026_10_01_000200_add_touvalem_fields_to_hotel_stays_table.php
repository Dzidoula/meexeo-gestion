<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_stays', function (Blueprint $table) {
            // A privatisation stay books the whole property, not one physical
            // room, so the room link must become optional.
            $table->foreignId('hotel_room_id')->nullable()->change();
            $table->string('type')->default('chambre')->after('hotel_room_id');
            $table->string('guest_email')->nullable()->after('guest_phone');
            $table->integer('guests')->nullable()->after('departure_date');
            $table->text('special_requests')->nullable()->after('notes');
            // Traceability + idempotent re-import for stays brought in from
            // another system (e.g. the Résidence Touvalem booking site).
            $table->string('external_source')->nullable()->after('special_requests');
            $table->unsignedBigInteger('external_id')->nullable()->after('external_source');
            $table->unique(['external_source', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::table('hotel_stays', function (Blueprint $table) {
            $table->dropUnique(['external_source', 'external_id']);
            $table->dropColumn(['type', 'guest_email', 'guests', 'special_requests', 'external_source', 'external_id']);
            $table->foreignId('hotel_room_id')->nullable(false)->change();
        });
    }
};
