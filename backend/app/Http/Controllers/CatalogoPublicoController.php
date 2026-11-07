<?php
namespace App\Http\Controllers;
use App\Models\Comercio;
use App\Models\Producto;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class CatalogoPublicoController extends Controller {
    public function show(string $slug) {
        $comercio = Comercio::where('slug', $slug)->where('estado', 'habilitado')->firstOrFail();
        $productos = Producto::where('comercio_id', $comercio->id)
            ->where('disponible', true)
            ->where(function ($q) { $q->whereNull('stock_disponible')->orWhere('stock_disponible','>',0); })
            ->orderBy('nombre')
            ->get(['id','nombre','descripcion','precio','precio_oferta','oferta_hasta','imagen','stock_disponible']);
        return response()->json([
            'comercio' => ['nombre'=>$comercio->nombre,'lema'=>$comercio->lema,'descripcion'=>$comercio->descripcion,'color_primario'=>$comercio->color_primario,'color_boton'=>$comercio->color_boton,'slug'=>$comercio->slug,'telefono_whatsapp'=>$comercio->telefono_whatsapp,'logo'=>$comercio->logo ? Storage::disk('public')->url($comercio->logo) : null],
            'productos' => $productos->map(function ($producto) { $producto->imagen = $producto->imagen ? Storage::disk('public')->url($producto->imagen) : null; return $producto; }),
        ]);
    }
}
