<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WorkerFactory extends Factory
{
    protected $model = Worker::class;

    private static $towns = [
        'Los Telares', 'Los Telares', 'Los Telares',
        'Añatuya', 'Tintina', 'Suncho Corral', 'Icaño',
        'Bandera', 'Quimilí', 'Frías', 'Loreto',
    ];

    private static $names = [
        'Carlos Juárez', 'Roberto Paz', 'Miguel Díaz', 'José Coronel',
        'Daniel Leiva', 'Hugo Barrionuevo', 'Ramón Espeche', 'Jorge Figueroa',
        'Pedro Gutiérrez', 'Luis Noriega', 'Oscar Álvarez', 'Héctor Mansilla',
        'Mario Suárez', 'Eduardo Cáceres', 'Fabián Rojas', 'Sergio Luna',
        'Néstor Pereyra', 'Gustavo Ibáñez', 'Marcelo Ríos', 'Fernando Acosta',
        'Ana González', 'María López', 'Sandra Herrera', 'Claudia Ruiz',
        'Patricia Medina', 'Laura Soria', 'Verónica Campos', 'Rosa Vargas',
        'Norma Quiroga', 'Silvia Bustamante',
    ];

    private static $descriptions = [
        'Más de 10 años de experiencia en la zona. Trabajo garantizado y materiales de primera calidad.',
        'Atención rápida y presupuesto sin cargo. Disponible fines de semana.',
        'Trabajo prolijo y puntual. Referencias disponibles. Llame sin compromiso.',
        'Especialista en trabajos de urgencia. Atiendo toda la región.',
        'Precio justo y materiales incluidos. Trabajo garantizado por escrito.',
        'Experiencia en obras nuevas y reparaciones. Consulte por paquetes.',
        'Trabajo a domicilio en toda la zona. Presupuesto gratis.',
        'Técnico matriculado con años de trayectoria en la provincia.',
        null, null,
    ];

    public function definition()
    {
        $name = $this->faker->randomElement(static::$names);
        $slug = Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 9999);

        $ratings_count   = $this->faker->numberBetween(0, 25);
        $recommendations = $ratings_count > 0
            ? $this->faker->numberBetween((int)($ratings_count * 0.5), $ratings_count)
            : 0;
        $average = $ratings_count > 0
            ? round(($recommendations / $ratings_count) * 4 + 1, 2)
            : 0;

        return [
            'name'                  => $name,
            'slug'                  => $slug,
            'description'           => $this->faker->randomElement(static::$descriptions),
            'phone'                 => '+549' . $this->faker->numerify('385#######'),
            'town'                  => $this->faker->randomElement(static::$towns),
            'photo_path'            => null,
            'average_rating'        => $average,
            'ratings_count'         => $ratings_count,
            'recommendations_count' => $recommendations,
            'is_active'             => true,
        ];
    }

    // Configurar categorías múltiples al crear via factory
    public function configure()
    {
        return $this->afterCreating(function (Worker $worker) {
            $allCategories = Category::pluck('id')->toArray();
            if (empty($allCategories)) return;

            // 70% un oficio, 25% dos oficios, 5% tres oficios
            $count = $this->faker->randomElement([1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 3]);
            $count = min($count, count($allCategories));

            $picked = $this->faker->randomElements($allCategories, $count, false);

            $pivot = [];
            foreach ($picked as $i => $catId) {
                $pivot[$catId] = ['is_primary' => ($i === 0)];
            }
            $worker->categories()->sync($pivot);
        });
    }
}
