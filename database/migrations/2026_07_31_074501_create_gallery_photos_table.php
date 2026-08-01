<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('image');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_photos');
    }
};
