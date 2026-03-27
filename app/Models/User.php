<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
        'worker_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'  => 'datetime',
        'terms_accepted_at'  => 'datetime',
    ];

    // ── Relación ─────────────────────────────────────────────────────────────

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    // ── Helpers de rol ───────────────────────────────────────────────────────

    public function hasAcceptedTerms(): bool
    {
        return $this->terms_accepted_at !== null;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isWorker(): bool
    {
        return $this->role === 'worker';
    }
}
