<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ComercianteController extends Controller {
    public function index(Request $request) {
        $comercios = DB::table('comercio_usuarios')
            ->join('comercios', 'comercios.id', '=', 'comercio_usuarios.comercio_id')
            ->where('comercio_usuarios.user_id', $request->user()->id)
            ->where('comercios.estado', 'habilitado')
            ->whereNull('comercios.deleted_at')
            ->select('comercios.id', 'comercios.nombre', 'comercios.slug', 'comercio_usuarios.rol')
            ->orderBy('comercios.nombre')
            ->get();
        return view('comerciante.inicio', ['comercios' => $comercios]);
    }
}
