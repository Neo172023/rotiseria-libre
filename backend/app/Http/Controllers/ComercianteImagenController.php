<?php
namespace App\Http\Controllers;
use App\Models\Comercio;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class ComercianteImagenController extends Controller {
 public function logo(Request $request, Comercio $comercio) {
  abort_unless($comercio->estado === 'habilitado', 404);
  abort_unless(DB::table('comercio_usuarios')->where('comercio_id',$comercio->id)->where('user_id',$request->user()->id)->where('rol','propietario')->exists(),403);
  $data=$request->validate(['logo'=>['required','image','mimes:jpg,jpeg,png,webp','max:2048']]);
  $path=$data['logo']->store('comercios/'.$comercio->id.'/logos','public');
  $old=$comercio->logo;
  $comercio->logo=$path;
  $comercio->save();
  if($old) Storage::disk('public')->delete($old);
  return back()->with('ok','Logo actualizado.');
 }
}
