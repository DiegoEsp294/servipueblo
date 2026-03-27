<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'slug', 'group_name', 'icon'];

    public function workers()
    {
        return $this->belongsToMany(Worker::class, 'tag_worker');
    }

    public static function allGrouped()
    {
        return static::orderBy('group_name')->orderBy('name')->get()->groupBy('group_name');
    }

    public static $groupLabels = [
        'disponibilidad'  => 'Disponibilidad',
        'servicio'        => 'Servicio',
        'caracteristicas' => 'Características',
        'pagos'           => 'Métodos de pago',
    ];
}
