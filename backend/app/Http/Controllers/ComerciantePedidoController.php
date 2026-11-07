<?php
namespace App\Http\Controllers;
use App\Models\Comercio;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
class ComerciantePedidoController extends Controller {
 private function allowed(Request $r, Comercio $c): void {
  abort_unless($c->estado==='habilitado',404);
  abort_unless(DB::table('comercio_usuarios')->where('comercio_id',$c->id)->where('user_id',$r->user()->id)->exists(),403);
 }
 public function index(Request $r,Comercio $comercio) {
  $this->allowed($r,$comercio);
  $pedidos=DB::table('pedidos')->where('comercio_id',$comercio->id)->orderByDesc('id')->paginate(25);
  return view('comerciante.pedidos',compact('comercio','pedidos'));
 }
 public function estado(Request $r,Comercio $comercio,int $pedido) {
  $this->allowed($r,$comercio);
  $d=$r->validate(['estado'=>'required|in:recibido,preparando,listo,enviado,entregado,cancelado']);
  $found=DB::table('pedidos')->where('comercio_id',$comercio->id)->where('id',$pedido)->first();
  abort_unless($found,404);
  DB::transaction(function () use ($comercio,$pedido,$d) {
   $actual=DB::table('pedidos')->where('comercio_id',$comercio->id)->where('id',$pedido)->lockForUpdate()->first();
   abort_unless($actual,404);
   if($actual->estado==='cancelado' && $d['estado']!=='cancelado')abort(422,'Un pedido cancelado no puede reactivarse automáticamente.');
   if($actual->estado!=='cancelado' && $d['estado']==='cancelado'){
    $items=DB::table('pedido_items')->where('pedido_id',$pedido)->get();
    foreach($items as $item){
     if($item->producto_id===null)continue;
     $producto=DB::table('productos')->where('id',$item->producto_id)->where('comercio_id',$comercio->id)->lockForUpdate()->first();
     if($producto && $producto->stock_disponible!==null)DB::table('productos')->where('id',$producto->id)->increment('stock_disponible',$item->cantidad);
    }
   }
   DB::table('pedidos')->where('comercio_id',$comercio->id)->where('id',$pedido)->update(['estado'=>$d['estado'],'updated_at'=>now()]);
  });
  return back()->with('ok','Estado actualizado.');
 }
}
