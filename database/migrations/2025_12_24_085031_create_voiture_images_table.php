<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('voiture_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voiture_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->enum('type', ['interieur', 'exterieur', 'composant', 'autre'])->default('autre');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voiture_images');
    }
};
