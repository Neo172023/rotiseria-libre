<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comercio_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comercio_id')->constrained('comercios')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('rol', ['propietario', 'empleado'])->default('propietario');
            $table->timestamps();
            $table->unique(['comercio_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('comercio_usuarios'); }
};
