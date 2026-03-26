<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Worker extends Model
{
    use HasFactory;

    const AVAILABILITY_AVAILABLE   = 'available';
    const AVAILABILITY_ON_REQUEST  = 'on_request';
    const AVAILABILITY_UNAVAILABLE = 'unavailable';

    static $availabilityLabels = [
        'available'   => ['label' => 'Disponible',              'icon' => '🟢', 'color' => 'green'],
        'on_request'  => ['label' => 'Con aviso previo',        'icon' => '🟡', 'color' => 'yellow'],
        'unavailable' => ['label' => 'No disponible',           'icon' => '🔴', 'color' => 'red'],
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'rate_info',
        'years_experience',
        'availability',
        'phone',
        'email',
        'town',
        'photo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active'             => 'boolean',
        'average_rating'        => 'float',
        'ratings_count'         => 'integer',
        'recommendations_count' => 'integer',
        'years_experience'      => 'integer',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────────

    public function categories()
    {
        return $this->belongsToMany(Category::class)
                    ->withPivot('is_primary')
                    ->orderByPivot('is_primary', 'desc')
                    ->orderBy('name');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function photos()
    {
        return $this->hasMany(WorkerPhoto::class)->orderBy('order');
    }

    public function events()
    {
        return $this->hasMany(WorkerEvent::class);
    }

    // ── Accessor de compatibilidad: $worker->category devuelve la primaria ──

    public function getCategoryAttribute()
    {
        if ($this->relationLoaded('categories')) {
            return $this->categories->firstWhere('pivot.is_primary', true)
                ?? $this->categories->first();
        }
        return $this->categories()->wherePivot('is_primary', true)->first()
            ?? $this->categories()->first();
    }

    // ── Accessors ───────────────────────────────────────────────────────────

    public function getAvailabilityInfoAttribute(): array
    {
        return static::$availabilityLabels[$this->availability] ?? static::$availabilityLabels['available'];
    }

    public function getWhatsappUrlAttribute(): string
    {
        $phone = preg_replace('/[\s\-\(\)]/', '', $this->phone);
        if (strpos($phone, '+') !== 0) {
            $phone = '+' . $phone;
        }
        $phone = ltrim($phone, '+');
        return 'https://wa.me/' . $phone;
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo_path) {
            return Storage::url($this->photo_path);
        }
        return asset('images/placeholder-worker.svg');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Rating recalculation ─────────────────────────────────────────────────

    public function recalculateRating(): void
    {
        $count           = $this->ratings()->count();
        $avg             = $count ? ($this->ratings()->avg('score') ?? 0) : 0;
        $recommendations = $this->ratings()->where('score', '>=', 4)->count();

        $this->update([
            'average_rating'        => round($avg, 2),
            'ratings_count'         => $count,
            'recommendations_count' => $recommendations,
        ]);
    }
}
