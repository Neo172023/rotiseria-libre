<?php
namespace App\Http\Controllers;
use App\Models\Comercio;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
class ComercianteIdentidadController extends Controller {
 public function update(Request $request, Comercio $comercio) {
  abort_unless($comercio->estado === 'habilitado', 404);
  abort_unless(DB::table('comercio_usuarios')->where('comercio_id',$comercio->id)->where('user_id',$request->user()->id)->where('rol','propietario')->exists(),403);
  $data=$request->validate([
   'lema'=>['nullable','string','max:180'],
   'descripcion'=>['nullable','string','max:2000'],
   'color_primario'=>['required','regex:/^#[0-9a-fA-F]{6}$/D'],
   'color_boton'=>['required','regex:/^#[0-9a-fA-F]{6}$/D'],
  ]);
  $comercio->forceFill($data)->save();
  return back()->with('ok','Identidad visual actualizada.');
 }
}
