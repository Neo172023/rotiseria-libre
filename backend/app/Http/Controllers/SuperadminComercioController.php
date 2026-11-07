<?php
namespace App\Http\Controllers;
use App\Models\Comercio;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SuperadminComercioController extends Controller {
    public function index() {
        return view('admin.comercios', ['comercios' => Comercio::orderByDesc('id')->paginate(25)]);
    }
    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => ['required','string','max:160'],
            'slug' => ['required','alpha_dash','max:160',Rule::unique('comercios','slug')],
            'telefono_whatsapp' => ['nullable','string','max:30'],
            'descripcion' => ['nullable','string','max:2000'],
        ]);
        $data['slug'] = Str::lower($data['slug']);
        Comercio::create($data); // El estado por defecto es pendiente.
        return redirect('/admin/comercios')->with('ok', 'Comercio creado como pendiente.');
    }
    public function estado(Request $request, Comercio $comercio) {
        $data = $request->validate(['estado' => ['required',Rule::in(['pendiente','habilitado','suspendido'])]]);
        $comercio->estado = $data['estado'];
        $comercio->save();
        return redirect('/admin/comercios')->with('ok', 'Estado actualizado.');
    }
}
