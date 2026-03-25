<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = ['worker_id', 'user_id', 'score', 'comment', 'reviewer_name', 'ip_address'];

    protected $casts = [
        'score' => 'integer',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    // Recalcula el promedio del trabajador cada vez que se crea o elimina una calificación
    protected static function booted()
    {
        static::created(function (Rating $rating) {
            $rating->worker->recalculateRating();
        });

        static::deleted(function (Rating $rating) {
            $rating->worker->recalculateRating();
        });
    }
}
