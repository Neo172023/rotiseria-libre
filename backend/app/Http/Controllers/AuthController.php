<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class AuthController extends Controller {
    public function login(Request $request) {
        $data = $request->validate(['email' => ['required','email'], 'password' => ['required','string']]);
        if (!Auth::attempt($data)) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        return redirect()->intended('/admin/comercios');
    }
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/ingresar');
    }
}
