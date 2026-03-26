<?php

use Illuminate\Database\Migrations\Migration;

class SeedCategories extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        $categories = [
            // ── Oficios (trabajadores) ────────────────────────────────────────
            ['icon' => '⚡', 'name' => 'Electricista',           'for_type' => 'worker'],
            ['icon' => '🔧', 'name' => 'Plomero',                'for_type' => 'worker'],
            ['icon' => '🏗️', 'name' => 'Albañil',                'for_type' => 'worker'],
            ['icon' => '🖌️', 'name' => 'Pintor',                 'for_type' => 'worker'],
            ['icon' => '🪚', 'name' => 'Carpintero',             'for_type' => 'worker'],
            ['icon' => '🔥', 'name' => 'Gasista',                'for_type' => 'worker'],
            ['icon' => '🔑', 'name' => 'Cerrajero',              'for_type' => 'worker'],
            ['icon' => '🌿', 'name' => 'Jardinero',              'for_type' => 'worker'],
            ['icon' => '🔩', 'name' => 'Mecánico',               'for_type' => 'worker'],
            ['icon' => '🧵', 'name' => 'Modista',                'for_type' => 'worker'],
            ['icon' => '💅', 'name' => 'Estética',               'for_type' => 'worker'],
            ['icon' => '✂️', 'name' => 'Peluquería / Barbería',  'for_type' => 'worker'],
            ['icon' => '🚗', 'name' => 'Lavadero de vehículos',  'for_type' => 'worker'],
            ['icon' => '🧹', 'name' => 'Limpieza',               'for_type' => 'worker'],
            ['icon' => '💻', 'name' => 'Técnico PC / Celular',   'for_type' => 'worker'],
            ['icon' => '⚒️', 'name' => 'Herrero / Soldador',     'for_type' => 'worker'],
            ['icon' => '👶', 'name' => 'Niñera / Cuidados',      'for_type' => 'worker'],
            ['icon' => '🐛', 'name' => 'Fumigador',              'for_type' => 'worker'],
            ['icon' => '📸', 'name' => 'Fotógrafo / Muralista',  'for_type' => 'worker'],
            ['icon' => '🚛', 'name' => 'Fletes / Mudanzas',      'for_type' => 'worker'],
            ['icon' => '🏥', 'name' => 'Enfermero / Cuidador',   'for_type' => 'worker'],

            // ── Emprendimientos ───────────────────────────────────────────────
            ['icon' => '🥖', 'name' => 'Panadería / Repostería', 'for_type' => 'entrepreneur'],
            ['icon' => '🫓', 'name' => 'Comida casera / Viandas','for_type' => 'entrepreneur'],
            ['icon' => '🌽', 'name' => 'Verdulería / Huerta',    'for_type' => 'entrepreneur'],
            ['icon' => '🥩', 'name' => 'Carnicería',             'for_type' => 'entrepreneur'],
            ['icon' => '🛒', 'name' => 'Almacén / Despensa',     'for_type' => 'entrepreneur'],
            ['icon' => '👗', 'name' => 'Tienda de Ropa',         'for_type' => 'entrepreneur'],
            ['icon' => '👕', 'name' => 'Indumentaria',           'for_type' => 'entrepreneur'],
            ['icon' => '🪡', 'name' => 'Manualidades',           'for_type' => 'entrepreneur'],
            ['icon' => '🎨', 'name' => 'Retratadora / Artista',  'for_type' => 'entrepreneur'],
            ['icon' => '🍭', 'name' => 'Kiosco / Golosinas',     'for_type' => 'entrepreneur'],
            ['icon' => '🎉', 'name' => 'Catering / Eventos',     'for_type' => 'entrepreneur'],
            ['icon' => '🌱', 'name' => 'Vivero / Plantas',       'for_type' => 'entrepreneur'],
            ['icon' => '🧴', 'name' => 'Cosméticos / Belleza',   'for_type' => 'entrepreneur'],
            ['icon' => '🖨️', 'name' => 'Fotocopias / Librería',  'for_type' => 'entrepreneur'],
            ['icon' => '🥚', 'name' => 'Huevos / Granja',        'for_type' => 'entrepreneur'],
            ['icon' => '🧃', 'name' => 'Productos naturales',    'for_type' => 'entrepreneur'],
        ];

        $order = \DB::table('categories')->max('sort_order') ?? 0;

        foreach ($categories as $cat) {
            $order++;
            \DB::statement("
                INSERT INTO categories (name, slug, icon, for_type, sort_order, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                ON CONFLICT (name) DO NOTHING
            ", [
                $cat['name'],
                \Illuminate\Support\Str::slug($cat['name']),
                $cat['icon'],
                $cat['for_type'],
                $order,
            ]);
        }
    }

    public function down() {}
}
