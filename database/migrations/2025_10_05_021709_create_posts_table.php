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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            // Título de la publicación/anuncio
            $table->string('title', 255)->nullable();
            // Contenido principal de la publicación/anuncio
            $table->text('content');
            
            // Tipo: 'post' (foro normal) o 'announcement' (anuncio del gimnasio)
            $table->enum('type', ['post', 'announcement'])->default('post');

            // Relación con el usuario que creó la publicación
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
