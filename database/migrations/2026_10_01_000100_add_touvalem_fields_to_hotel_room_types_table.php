<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_room_types', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->decimal('base_price', 10, 2)->nullable()->after('description');
            $table->decimal('rating', 3, 1)->nullable()->after('base_price');
            $table->integer('capacity')->nullable()->after('rating');
            $table->integer('bed_count')->nullable()->after('capacity');
            $table->integer('bath_count')->nullable()->after('bed_count');
            $table->integer('area')->nullable()->after('bath_count');
            $table->string('image')->nullable()->after('area');
            $table->json('images')->nullable()->after('image');
            $table->json('amenities')->nullable()->after('images');
        });
    }

    public function down(): void
    {
        Schema::table('hotel_room_types', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'base_price', 'rating', 'capacity', 'bed_count',
                'bath_count', 'area', 'image', 'images', 'amenities',
            ]);
        });
    }
};
