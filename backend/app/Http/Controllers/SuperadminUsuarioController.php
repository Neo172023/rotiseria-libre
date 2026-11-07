<?php
namespace App\Http\Controllers;

use App\Models\Comercio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SuperadminUsuarioController extends Controller {
    public function store(Request $request, Comercio $comercio) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(12)],
            'rol' => ['required', 'in:propietario,empleado'],
        ]);
        DB::transaction(function () use ($data, $comercio) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            DB::table('comercio_usuarios')->insert([
                'comercio_id' => $comercio->id,
                'user_id' => $user->id,
                'rol' => $data['rol'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
        return redirect('/admin/comercios')->with('ok', 'Usuario asignado al comercio.');
    }
}
