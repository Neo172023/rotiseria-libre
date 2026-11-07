<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comercio_id')->constrained('comercios')->restrictOnDelete();
            $table->string('nombre', 120);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
            $table->unique(['id', 'comercio_id']);
        });
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comercio_id')->constrained('comercios')->restrictOnDelete();
            $table->foreignId('categoria_id')->nullable();
            $table->string('nombre', 160);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 12, 2);
            $table->decimal('precio_oferta', 12, 2)->nullable();
            $table->timestamp('oferta_hasta')->nullable();
            $table->string('imagen')->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['comercio_id', 'disponible']);
            $table->unique(['id', 'comercio_id']);
            $table->foreign(['categoria_id', 'comercio_id'])->references(['id', 'comercio_id'])->on('categorias')->restrictOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('productos'); Schema::dropIfExists('categorias'); }
};
