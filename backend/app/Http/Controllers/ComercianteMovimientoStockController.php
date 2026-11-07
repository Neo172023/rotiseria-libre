<?php
namespace App\Http\Controllers;

use App\Models\Comercio;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ComercianteMovimientoStockController extends Controller {
    public function index(Request $request, Comercio $comercio) {
        abort_unless($comercio->estado === 'habilitado', 404);
        abort_unless(DB::table('comercio_usuarios')->where('comercio_id',$comercio->id)
            ->where('user_id',$request->user()->id)->exists(), 403);
        $movimientos = DB::table('movimientos_stock as m')
            ->join('productos as p', 'p.id', '=', 'm.producto_id')
            ->leftJoin('users as u', 'u.id', '=', 'm.usuario_id')
            ->where('m.comercio_id', $comercio->id)
            ->select('m.*', 'p.nombre as producto_nombre', 'u.name as usuario_nombre')
            ->orderByDesc('m.id')->paginate(30);
        return view('comerciante.movimientos-stock', compact('comercio', 'movimientos'));
    }
}
