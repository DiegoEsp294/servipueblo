<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Models\EmailVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    // GET /verificar?redirect=...
    public function showEmailForm(Request $request)
    {
        if (session('verified_email')) {
            return redirect($request->query('redirect', '/'));
        }

        return view('verification.email-form', [
            'redirect' => $request->query('redirect', '/'),
        ]);
    }

    // POST /verificar
    public function sendCode(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email', 'max:150'],
            'redirect' => ['nullable', 'string'],
        ]);

        $email    = strtolower(trim($request->email));
        $redirect = $request->input('redirect', '/');

        // Limitar a 3 intentos por email cada 10 minutos
        $recent = EmailVerification::where('email', $email)
            ->where('created_at', '>=', Carbon::now()->subMinutes(10))
            ->count();

        if ($recent >= 3) {
            return back()->withErrors(['email' => 'Demasiados intentos. Esperá 10 minutos e intentá de nuevo.']);
        }

        $code = EmailVerification::generateCode();

        EmailVerification::create([
            'email'      => $email,
            'code'       => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::to($email)->send(new VerificationCodeMail($code));

        return redirect()->route('verification.code-form', [
            'redirect' => $redirect,
            'email'    => $email,
        ]);
    }

    // GET /verificar/codigo
    public function showCodeForm(Request $request)
    {
        return view('verification.code-form', [
            'redirect' => $request->query('redirect', '/'),
            'email'    => session('verification_email', $request->query('email', '')),
        ]);
    }

    // POST /verificar/codigo
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'code'     => ['required', 'string', 'size:6'],
            'redirect' => ['nullable', 'string'],
        ]);

        $email    = strtolower(trim($request->email));
        $redirect = $request->input('redirect', '/');

        $verification = EmailVerification::where('email', $email)
            ->where('code', $request->code)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$verification) {
            return redirect()->route('verification.code-form', [
                'redirect' => $redirect,
                'email'    => $email,
            ])->withErrors(['code' => 'El código es incorrecto.'])->withInput();
        }

        if ($verification->isExpired()) {
            return redirect()->route('verification.code-form', [
                'redirect' => $redirect,
                'email'    => $email,
            ])->withErrors(['code' => 'El código expiró. Solicitá uno nuevo.'])->withInput();
        }

        $verification->update(['used' => true]);

        // Guardar en sesión por 24 horas
        session([
            'verified_email'    => $email,
            'verified_email_at' => now()->timestamp,
        ]);

        return redirect($redirect)->with('success', '¡Correo verificado! Ya podés dejar tu calificación.');
    }

    // POST /verificar/cerrar
    public function logout()
    {
        session()->forget(['verified_email', 'verified_email_at']);
        return back()->with('success', 'Sesión cerrada.');
    }
}
