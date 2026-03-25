<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['worker_id', 'path', 'order'];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function getUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::url($this->path);
    }
}
