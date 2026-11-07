<?php
namespace Tests\Feature;
use App\Models\Comercio;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CatalogoIsolationTest extends TestCase {
    use RefreshDatabase;
    public function test_owner_cannot_change_another_commerces_product(): void {
        $user = User::create(['name'=>'Dueño','email'=>'dueno@example.test','password'=>'password-test-123']);
        $own = Comercio::create(['nombre'=>'Propia','slug'=>'propia']);
        $other = Comercio::create(['nombre'=>'Ajena','slug'=>'ajena']);
        foreach ([$own,$other] as $c) { $c->estado='habilitado'; $c->save(); }
        DB::table('comercio_usuarios')->insert(['comercio_id'=>$own->id,'user_id'=>$user->id,'rol'=>'propietario','created_at'=>now(),'updated_at'=>now()]);
        $product = new Producto(['nombre'=>'Pizza ajena','precio'=>10000,'disponible'=>true]);
        $product->comercio_id = $other->id; $product->save();
        $this->actingAs($user)->patch("/comerciante/{$own->id}/productos/{$product->id}", [
            'nombre'=>'Hack','precio'=>1,'disponible'=>1,
        ])->assertNotFound();
        $this->assertDatabaseHas('productos',['id'=>$product->id,'nombre'=>'Pizza ajena']);
        $this->actingAs($user)->get("/comerciante/{$other->id}/productos")->assertForbidden();
    }
    public function test_suspended_commerce_catalog_is_not_public(): void {
        $c = Comercio::create(['nombre'=>'Suspendida','slug'=>'suspendida']);
        $this->getJson('/api/comercios/suspendida/catalogo')->assertNotFound();
        $c->estado='habilitado';$c->save();
        $this->getJson('/api/comercios/suspendida/catalogo')->assertOk();
    }
}
