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
  DB::table('pedidos')->where('comercio_id',$comercio->id)->where('id',$pedido)->update(['estado'=>$d['estado'],'updated_at'=>now()]);
  return back()->with('ok','Estado actualizado.');
 }
}
