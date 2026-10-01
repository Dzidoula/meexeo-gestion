<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hotel_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('author_name');
            $table->string('author_subtitle')->nullable();
            $table->string('author_image')->nullable();
            $table->decimal('rating', 2, 1)->default(5.0);
            $table->string('title');
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->string('external_source')->nullable();
            $table->unsignedBigInteger('external_id')->nullable();
            $table->unique(['external_source', 'external_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_testimonials');
    }
};
