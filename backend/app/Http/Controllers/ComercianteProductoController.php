<?php
namespace App\Http\Controllers;

use App\Models\Comercio;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ComercianteProductoController extends Controller {
    private function comercioAutorizado(Request $request, Comercio $comercio): void {
        abort_unless($comercio->estado === 'habilitado', 404);
        abort_unless(DB::table('comercio_usuarios')
            ->where('comercio_id', $comercio->id)
            ->where('user_id', $request->user()->id)
            ->where('rol', 'propietario')
            ->exists(), 403);
    }
    public function index(Request $request, Comercio $comercio) {
        $this->comercioAutorizado($request, $comercio);
        $productos = Producto::where('comercio_id', $comercio->id)->orderBy('nombre')->paginate(30);
        return view('comerciante.productos', compact('comercio','productos'));
    }
    public function store(Request $request, Comercio $comercio) {
        $this->comercioAutorizado($request, $comercio);
        $data = $this->validar($request);
        $producto = new Producto($data);
        $producto->comercio_id = $comercio->id;
        $producto->save();
        return back()->with('ok', 'Producto creado.');
    }
    public function update(Request $request, Comercio $comercio, Producto $producto) {
        $this->comercioAutorizado($request, $comercio);
        abort_unless($producto->comercio_id === $comercio->id, 404);
        $producto->update($this->validar($request));
        return back()->with('ok', 'Producto actualizado.');
    }
    public function destroy(Request $request, Comercio $comercio, Producto $producto) {
        $this->comercioAutorizado($request, $comercio);
        abort_unless($producto->comercio_id === $comercio->id, 404);
        $producto->delete();
        return back()->with('ok', 'Producto retirado del catálogo.');
    }
    private function validar(Request $request): array {
        return $request->validate([
            'nombre' => ['required','string','max:160'],
            'descripcion' => ['nullable','string','max:2000'],
            'precio' => ['required','numeric','min:0','max:9999999999'],
            'precio_oferta' => ['nullable','numeric','min:0','lte:precio'],
            'oferta_hasta' => ['nullable','date','after:now'],
            'disponible' => ['required','boolean'],
        ]);
    }
}
