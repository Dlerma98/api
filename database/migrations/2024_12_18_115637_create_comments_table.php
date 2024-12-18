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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Relación con productos
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade'); // Relación con el comentario principal (si es una respuesta)
            $table->text('content'); // Contenido del comentario
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relación con el usuario que hizo el comentario
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
