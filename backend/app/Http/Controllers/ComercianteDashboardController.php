<?php
namespace App\Http\Controllers;

use App\Models\Comercio;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ComercianteDashboardController extends Controller
{
    public function index(Request $request, Comercio $comercio)
    {
        abort_unless($comercio->estado === 'habilitado', 404);
        abort_unless(
            DB::table('comercio_usuarios')
                ->where('comercio_id', $comercio->id)
                ->where('user_id', $request->user()->id)
                ->exists(),
            403
        );

        $pedidos = DB::table('pedidos')->where('comercio_id', $comercio->id);
        $hoy = now()->startOfDay();
        $resumen = [
            'nuevos' => (clone $pedidos)->where('estado', 'recibido')->count(),
            'preparando' => (clone $pedidos)->where('estado', 'preparando')->count(),
            'listos' => (clone $pedidos)->where('estado', 'listo')->count(),
            'pedidos_hoy' => (clone $pedidos)->where('created_at', '>=', $hoy)->count(),
            'ventas_hoy' => (clone $pedidos)
                ->where('estado', 'entregado')
                ->where('created_at', '>=', $hoy)
                ->sum('total'),
        ];
        $recientes = (clone $pedidos)->orderByDesc('id')->limit(5)->get();
        $productos = DB::table('productos')
            ->where('comercio_id', $comercio->id)
            ->whereNull('deleted_at')
            ->count();

        return view('comerciante.dashboard', compact('comercio', 'resumen', 'recientes', 'productos'));
    }
}
