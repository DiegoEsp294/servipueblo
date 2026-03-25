<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerEvent extends Model
{
    use HasFactory;

    public $timestamps  = false;
    public $updatedAt   = false;

    protected $fillable = ['worker_id', 'type', 'ip_hash', 'referrer', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    const TYPE_VIEW      = 'view';
    const TYPE_WHATSAPP  = 'whatsapp_click';
    const TYPE_SHARE     = 'share_click';

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    // Registra un evento evitando duplicados por IP en ventana de tiempo
    public static function record(Worker $worker, string $type, int $dedupeMinutes = 60): void
    {
        $ipHash = hash('sha256', request()->ip());

        $exists = static::where('worker_id', $worker->id)
            ->where('type', $type)
            ->where('ip_hash', $ipHash)
            ->where('created_at', '>=', now()->subMinutes($dedupeMinutes))
            ->exists();

        if (!$exists) {
            static::create([
                'worker_id'  => $worker->id,
                'type'       => $type,
                'ip_hash'    => $ipHash,
                'referrer'   => substr(request()->headers->get('referer', ''), 0, 300),
                'created_at' => now(),
            ]);
        }
    }
}
