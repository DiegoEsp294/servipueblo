<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    protected $fillable = ['worker_id', 'day_of_week', 'is_closed', 'open_time', 'close_time'];

    protected $casts = ['is_closed' => 'boolean'];

    public static array $days = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function getLabelAttribute(): string
    {
        if ($this->is_closed) return 'Cerrado';
        if ($this->open_time && $this->close_time) return "{$this->open_time} – {$this->close_time}";
        return 'Abierto';
    }
}
