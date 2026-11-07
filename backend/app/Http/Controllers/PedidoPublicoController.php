<?php
namespace App\Http\Controllers;
use App\Models\Comercio;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class PedidoPublicoController extends Controller {
 public function store(Request $request,string $slug) {
  $d=$request->validate(['cliente_nombre'=>'required|string|max:160','modalidad'=>'required|in:retiro,delivery','direccion'=>'required_if:modalidad,delivery|nullable|string|max:500','pago'=>'required|in:efectivo,transferencia','notas'=>'nullable|string|max:1000','items'=>'required|array|min:1|max:30','items.*.producto_id'=>'required|integer|distinct','items.*.cantidad'=>'required|integer|min:1|max:30']);
  return DB::transaction(function() use($d,$slug) {
   $c=Comercio::where('slug',$slug)->where('estado','habilitado')->lockForUpdate()->firstOrFail();
   $ids=collect($d['items'])->pluck('producto_id');
   $products=Producto::where('comercio_id',$c->id)->where('disponible',true)->whereIn('id',$ids)->lockForUpdate()->get()->keyBy('id');
   if($products->count()!==$ids->count())throw ValidationException::withMessages(['items'=>'Producto no disponible.']);
   $sum=0;$lines=[];
   foreach($d['items'] as $item){
    $p=$products->get($item['producto_id']);
    if($p->stock_disponible !== null && $p->stock_disponible < $item['cantidad'])throw ValidationException::withMessages(['items'=>'Stock insuficiente para '.$p->nombre.'.']);
    $price=$p->precio;
    if($p->precio_oferta!==null && ($p->oferta_hasta===null || $p->oferta_hasta->isFuture()) && (float)$p->precio_oferta<(float)$p->precio)$price=$p->precio_oferta;
    $cents=(int)round((float)$price*100);$line=$cents*$item['cantidad'];$sum+=$line;
    $lines[]=['producto_id'=>$p->id,'descripcion'=>$p->nombre,'cantidad'=>$item['cantidad'],'precio_unitario'=>number_format($cents/100,2,'.',''),'total'=>number_format($line/100,2,'.',''),'created_at'=>now(),'updated_at'=>now()];
   }
   foreach($d['items'] as $item){
    $p=$products->get($item['producto_id']);
    if($p->stock_disponible !== null){$p->stock_disponible -= $item['cantidad'];$p->save();}
   }
   $id=DB::table('pedidos')->insertGetId(['comercio_id'=>$c->id,'cliente_nombre'=>$d['cliente_nombre'],'modalidad'=>$d['modalidad'],'direccion'=>$d['modalidad']==='delivery'?($d['direccion']??null):null,'pago'=>$d['pago'],'estado'=>'recibido','subtotal'=>number_format($sum/100,2,'.',''),'costo_envio'=>'0.00','total'=>number_format($sum/100,2,'.',''),'notas'=>$d['notas']??null,'created_at'=>now(),'updated_at'=>now()]);
   foreach($lines as &$line)$line['pedido_id']=$id;
   unset($line);DB::table('pedido_items')->insert($lines);
   return response()->json(['id'=>$id,'estado'=>'recibido','total'=>number_format($sum/100,2,'.',''),'moneda'=>'ARS'],201);
  });
 }
}
