<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Plomero',          'icon' => '🔧', 'sort_order' => 1],
            ['name' => 'Electricista',     'icon' => '⚡', 'sort_order' => 2],
            ['name' => 'Albañil',          'icon' => '🧱', 'sort_order' => 3],
            ['name' => 'Carpintero',       'icon' => '🪚', 'sort_order' => 4],
            ['name' => 'Pintor',           'icon' => '🖌️', 'sort_order' => 5],
            ['name' => 'Herrero',          'icon' => '⚙️', 'sort_order' => 6],
            ['name' => 'Jardinero',        'icon' => '🌿', 'sort_order' => 7],
            ['name' => 'Mecánico',         'icon' => '🔩', 'sort_order' => 8],
            ['name' => 'Técnico en A/C',   'icon' => '❄️', 'sort_order' => 9],
            ['name' => 'Técnico en TV',    'icon' => '📺', 'sort_order' => 10],
            ['name' => 'Cerrajero',        'icon' => '🔑', 'sort_order' => 11],
            ['name' => 'Limpieza',         'icon' => '🧹', 'sort_order' => 12],
            ['name' => 'Fumigación',       'icon' => '🪲', 'sort_order' => 13],
            ['name' => 'Otro',             'icon' => '🛠️', 'sort_order' => 99],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, ['slug' => Str::slug($data['name'])])
            );
        }
    }
}
