<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperadminAccessTest extends TestCase {
    use RefreshDatabase;
    public function test_guest_cannot_access_commerces(): void {
        $this->get('/admin/comercios')->assertRedirect('/ingresar');
    }
    public function test_regular_user_cannot_access_commerces(): void {
        $user = User::create(['name'=>'Comerciante','email'=>'comerciante@example.test','password'=>'password-for-test']);
        $this->actingAs($user)->get('/admin/comercios')->assertForbidden();
    }
    public function test_superadmin_can_access_commerces(): void {
        $user = User::create(['name'=>'Admin','email'=>'admin@example.test','password'=>'password-for-test']);
        $user->forceFill(['is_superadmin'=>true])->save();
        $this->actingAs($user)->get('/admin/comercios')->assertOk();
    }
}
