<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void {
        $normalizar = static fn (string $nombre): string => trim(preg_replace('/\\s+/u', ' ', Str::ascii(mb_strtolower(trim($nombre), 'UTF-8'))));
        $vistos = [];
        foreach (DB::table('productos')->orderBy('id')->get(['id','comercio_id','nombre','deleted_at']) as $producto) {
            $clave = $producto->comercio_id.'|'.$normalizar($producto->nombre);
            if (isset($vistos[$clave])) {
                throw new \RuntimeException('Productos duplicados detectados (IDs '.$vistos[$clave].' y '.$producto->id.'). Revisá el catálogo antes de migrar; no se modificaron registros.');
            }
            $vistos[$clave] = $producto->id;
        }
        Schema::table('productos', function (Blueprint $table) {
            $table->string('nombre_normalizado', 160)->nullable()->after('nombre');
        });
        foreach (DB::table('productos')->get(['id','nombre']) as $producto) {
            DB::table('productos')->where('id',$producto->id)->update(['nombre_normalizado'=>$normalizar($producto->nombre)]);
        }
        Schema::table('productos', function (Blueprint $table) {
            $table->unique(['comercio_id','nombre_normalizado'], 'productos_comercio_nombre_normalizado_unique');
        });
        Schema::create('movimientos_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comercio_id')->constrained('comercios')->restrictOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            $table->string('tipo', 32);
            $table->integer('cantidad_cambio');
            $table->unsignedInteger('stock_anterior')->nullable();
            $table->unsignedInteger('stock_nuevo')->nullable();
            $table->string('motivo', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['comercio_id','created_at']);
            $table->index(['producto_id','created_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('movimientos_stock');
        Schema::table('productos', function (Blueprint $table) {
            $table->dropUnique('productos_comercio_nombre_normalizado_unique');
            $table->dropColumn('nombre_normalizado');
        });
    }
};
