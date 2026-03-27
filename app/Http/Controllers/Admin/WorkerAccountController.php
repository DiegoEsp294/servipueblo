<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WorkerAccountController extends Controller
{
    /** Crear cuenta para un trabajador */
    public function store(Worker $worker)
    {
        if ($worker->user) {
            return back()->with('error', 'Este trabajador ya tiene una cuenta.');
        }

        if (!$worker->email) {
            return back()->with('error', 'El trabajador no tiene email. Agregá uno primero.');
        }

        if (User::where('email', $worker->email)->exists()) {
            return back()->with('error', 'Ya existe un usuario con el email ' . $worker->email . '.');
        }

        $password = Str::random(10);

        User::create([
            'name'      => $worker->name,
            'email'     => $worker->email,
            'password'  => Hash::make($password),
            'role'      => 'worker',
            'worker_id' => $worker->id,
        ]);

        return back()->with('account_created', [
            'email'    => $worker->email,
            'password' => $password,
        ]);
    }

    /** Resetear contraseña */
    public function resetPassword(Worker $worker)
    {
        if (!$worker->user) {
            return back()->with('error', 'Este trabajador no tiene cuenta todavía.');
        }

        $password = Str::random(10);
        $worker->user->update(['password' => Hash::make($password)]);

        return back()->with('account_created', [
            'email'    => $worker->user->email,
            'password' => $password,
        ]);
    }

    /** Eliminar cuenta */
    public function destroy(Worker $worker)
    {
        optional($worker->user)->delete();
        return back()->with('success', 'Cuenta eliminada.');
    }
}
