<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TermsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        if (auth()->user()->hasAcceptedTerms()) {
            return redirect()->intended($this->redirectAfterAccept());
        }
        return view('terms.show');
    }

    public function accept()
    {
        auth()->user()->update(['terms_accepted_at' => now()]);
        return redirect($this->redirectAfterAccept());
    }

    private function redirectAfterAccept(): string
    {
        return auth()->user()->isAdmin() ? route('admin.dashboard') : route('worker.profile.edit');
    }
}
