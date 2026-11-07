<?php
namespace Tests\Feature;

use App\Models\Comercio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ComercianteIsolationTest extends TestCase {
    use RefreshDatabase;

    public function test_comerciante_only_sees_enabled_assigned_commerce(): void {
        $user = User::create(['name'=>'Dueño','email'=>'dueno@example.test','password'=>'example-password']);
        $own = Comercio::create(['nombre'=>'Propia','slug'=>'propia']);
        $other = Comercio::create(['nombre'=>'Ajena','slug'=>'ajena']);
        $own->estado = 'habilitado'; $own->save();
        $other->estado = 'habilitado'; $other->save();
        DB::table('comercio_usuarios')->insert([
            'comercio_id'=>$own->id,'user_id'=>$user->id,'rol'=>'propietario',
            'created_at'=>now(),'updated_at'=>now()
        ]);
        $this->actingAs($user)->get('/comerciante')
            ->assertOk()->assertSee('Propia')->assertDontSee('Ajena');
        $own->estado = 'suspendido'; $own->save();
        $this->actingAs($user)->get('/comerciante')->assertDontSee('Propia');
    }

    public function test_regular_user_cannot_create_commerce_accounts(): void {
        $user = User::create(['name'=>'Dueño','email'=>'dueno@example.test','password'=>'example-password']);
        $commerce = Comercio::create(['nombre'=>'Propia','slug'=>'propia']);
        $this->actingAs($user)->post('/admin/comercios/'.$commerce->id.'/usuarios', [
            'name'=>'Intruso','email'=>'intruso@example.test',
            'password'=>'secure-password-123','password_confirmation'=>'secure-password-123',
            'rol'=>'propietario',
        ])->assertForbidden();
        $this->assertDatabaseMissing('users',['email'=>'intruso@example.test']);
    }
}
