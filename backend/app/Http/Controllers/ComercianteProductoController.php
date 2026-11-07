<?php
namespace App\Http\Controllers;

use App\Models\Comercio;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
        $this->verificarDuplicado($comercio, $data['nombre'], null, $request->boolean('confirmar_similar'));
        $producto = new Producto(collect($data)->except('imagen')->all());
        $producto->comercio_id = $comercio->id;
        $producto->save();
        if ($request->hasFile('imagen')) { $producto->imagen = $request->file('imagen')->store('comercios/'.$comercio->id.'/productos','public'); $producto->save(); }
        return back()->with('ok', 'Producto creado.');
    }
    public function update(Request $request, Comercio $comercio, Producto $producto) {
        $this->comercioAutorizado($request, $comercio);
        abort_unless($producto->comercio_id === $comercio->id, 404);
        $data = $this->validar($request);
        $this->verificarDuplicado($comercio, $data['nombre'], $producto->id, $request->boolean('confirmar_similar'));
        $producto->update(collect($data)->except('imagen')->all());
        if ($request->hasFile('imagen')) { $old = $producto->imagen; $producto->imagen = $request->file('imagen')->store('comercios/'.$comercio->id.'/productos','public'); $producto->save(); if ($old) Storage::disk('public')->delete($old); }
        return back()->with('ok', 'Producto actualizado.');
    }
    public function destroy(Request $request, Comercio $comercio, Producto $producto) {
        $this->comercioAutorizado($request, $comercio);
        abort_unless($producto->comercio_id === $comercio->id, 404);
        $producto->delete();
        return back()->with('ok', 'Producto retirado del catálogo.');
    }
    private function verificarDuplicado(Comercio $comercio, string $nombre, ?int $exceptoId = null, bool $confirmado = false): void {
        $normalizar = static function (string $valor): string {
            $valor = mb_strtolower(trim($valor), 'UTF-8');
            $valor = \Illuminate\Support\Str::ascii($valor);
            return preg_replace('/\\s+/u', ' ', $valor);
        };
        $buscado = $normalizar($nombre);
        $productos = Producto::where('comercio_id', $comercio->id)
            ->when($exceptoId !== null, fn ($query) => $query->where('id', '!=', $exceptoId))
            ->get(['id', 'nombre']);
        $tokens = $this->tokensProducto($nombre);
        foreach ($productos as $existente) {
            if ($normalizar($existente->nombre) === $buscado) {
                throw ValidationException::withMessages([
                    'nombre' => 'Este producto ya existe en tu catálogo. Editá el producto existente para actualizar el precio o el stock.',
                ]);
            }
            $otros = $this->tokensProducto($existente->nombre);
            $union = count(array_unique(array_merge($tokens, $otros)));
            $similitud = $union ? count(array_intersect($tokens, $otros)) / $union : 0;
            if (!$confirmado && count($tokens) >= 2 && $similitud >= 0.8) {
                throw ValidationException::withMessages([
                    'nombre' => 'Posible duplicado: «'.$existente->nombre.'» (ID '.$existente->id.'). Editá el producto existente o marcá «Crear de todos modos» si realmente es diferente.',
                ]);
            }
        }
    }
    private function tokensProducto(string $nombre): array {
        $nombre = \Illuminate\Support\Str::ascii(mb_strtolower(trim($nombre), 'UTF-8'));
        $nombre = preg_replace('/[^a-z0-9]+/', ' ', $nombre);
        $tokens = array_values(array_filter(explode(' ', trim($nombre)),
            static fn ($token) => $token !== '' && !in_array($token, ['de', 'del', 'la', 'el', 'los', 'las', 'con', 'y', 'en'], true)));
        sort($tokens);
        return array_values(array_unique($tokens));
    }
    private function validar(Request $request): array {
        return $request->validate([
            'nombre' => ['required','string','max:160'],
            'descripcion' => ['nullable','string','max:2000'],
            'precio' => ['required','numeric','min:0','max:9999999999'],
            'precio_oferta' => ['nullable','numeric','min:0','lte:precio'],
            'oferta_hasta' => ['nullable','date','after:now'],
            'disponible' => ['required','boolean'],
            'stock_disponible' => ['nullable','integer','min:0','max:1000000'],
            'imagen' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:3072'],
        ]);
    }
}
