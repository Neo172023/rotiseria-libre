<?php
namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperadmin extends Command {
    protected $signature = 'rotiseria:crear-superadmin';
    protected $description = 'Crea la primera cuenta superadministradora de forma interactiva';
    public function handle(): int {
        if (User::where('is_superadmin', true)->exists()) {
            $this->error('Ya existe un superadministrador. No se crearán más por este comando.');
            return self::FAILURE;
        }
        $name = trim((string)$this->ask('Nombre'));
        $email = trim((string)$this->ask('Correo electrónico'));
        $password = (string)$this->secret('Contraseña (mínimo 12 caracteres)');
        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12 || User::where('email', $email)->exists()) {
            $this->error('Datos inválidos o correo ya registrado.');
            return self::FAILURE;
        }
        User::create(['name'=>$name,'email'=>$email,'password'=>Hash::make($password)])->forceFill(['is_superadmin'=>true])->save();
        $this->info('Cuenta superadministradora creada.');
        return self::SUCCESS;
    }
}
