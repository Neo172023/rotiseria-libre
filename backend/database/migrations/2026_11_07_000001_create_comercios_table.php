<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comercios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 160);
            $table->string('slug', 160)->unique();
            $table->enum('estado', ['pendiente', 'habilitado', 'suspendido'])->default('pendiente');
            $table->string('telefono_whatsapp', 30)->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('estado');
        });
    }
    public function down(): void { Schema::dropIfExists('comercios'); }
};
