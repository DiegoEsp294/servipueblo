<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WorkerPost extends Model
{
    protected $fillable = ['worker_id', 'content', 'photo_path', 'is_sold_out', 'expires_at'];

    protected $casts = [
        'is_sold_out' => 'boolean',
        'expires_at'  => 'datetime',
    ];

    public static array $durations = [
        'today'  => 'Hoy (expira a medianoche)',
        '3days'  => '3 días',
        '7days'  => '7 días',
        'none'   => 'Sin vencimiento',
    ];

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getExpiryLabelAttribute(): ?string
    {
        if (!$this->expires_at) return null;
        if ($this->is_expired) return 'Vencida';
        return 'Vence ' . $this->expires_at->diffForHumans();
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo_path) return null;

        if (config('filesystems.default') === 's3') {
            return Storage::url($this->photo_path);
        }
        return asset('storage/' . $this->photo_path);
    }
}
