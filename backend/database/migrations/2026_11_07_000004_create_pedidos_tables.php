<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comercio_id')->constrained('comercios')->restrictOnDelete();
            $table->string('cliente_nombre', 160);
            $table->string('cliente_telefono', 30)->nullable();
            $table->enum('modalidad', ['retiro', 'delivery']);
            $table->text('direccion')->nullable();
            $table->enum('pago', ['efectivo', 'transferencia']);
            $table->enum('estado', ['recibido', 'preparando', 'listo', 'enviado', 'entregado', 'cancelado'])->default('recibido');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('costo_envio', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->index(['comercio_id', 'estado', 'created_at']);
        });
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->restrictOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->string('descripcion', 255);
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('total', 12, 2);
            $table->json('opciones')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pedido_items'); Schema::dropIfExists('pedidos'); }
};
