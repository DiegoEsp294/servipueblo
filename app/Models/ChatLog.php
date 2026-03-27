<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    protected $fillable = [
        'message',
        'context_words',
        'workers_found',
        'ai_reply',
        'reason',
        'reviewed',
        'admin_note',
    ];

    protected $casts = [
        'reviewed' => 'boolean',
    ];

    public static function reasons(): array
    {
        return [
            'no_workers_found'    => 'Sin trabajadores encontrados',
            'injection_attempt'   => 'Intento de inyección',
            'ai_could_not_answer' => 'IA no pudo responder',
        ];
    }

    public function reasonLabel(): string
    {
        return static::reasons()[$this->reason] ?? $this->reason;
    }
}
