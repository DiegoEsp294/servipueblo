<?php

use Illuminate\Database\Migrations\Migration;

class SeedTags extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        try { \DB::statement('SELECT 1'); } catch (\Exception $e) { return; }

        $tags = [
            // ── Disponibilidad ─────────────────────────────────────────────────
            ['group_name' => 'disponibilidad', 'icon' => '📅', 'name' => 'Abre domingos'],
            ['group_name' => 'disponibilidad', 'icon' => '🌙', 'name' => 'Atiende de noche'],
            ['group_name' => 'disponibilidad', 'icon' => '⚡', 'name' => 'Urgencias'],
            ['group_name' => 'disponibilidad', 'icon' => '🕐', 'name' => 'Atiende 24hs'],
            ['group_name' => 'disponibilidad', 'icon' => '📆', 'name' => 'Solo con turno'],
            ['group_name' => 'disponibilidad', 'icon' => '🤝', 'name' => 'Bajo pedido'],
            ['group_name' => 'disponibilidad', 'icon' => '🚀', 'name' => 'Disponible hoy'],

            // ── Servicio ───────────────────────────────────────────────────────
            ['group_name' => 'servicio', 'icon' => '🏠', 'name' => 'A domicilio'],
            ['group_name' => 'servicio', 'icon' => '🛵', 'name' => 'Delivery / Envío'],
            ['group_name' => 'servicio', 'icon' => '✅', 'name' => 'Con garantía'],
            ['group_name' => 'servicio', 'icon' => '🔍', 'name' => 'Visita sin cargo'],
            ['group_name' => 'servicio', 'icon' => '📦', 'name' => 'Venta mayorista'],
            ['group_name' => 'servicio', 'icon' => '🎁', 'name' => 'Pedidos especiales'],
            ['group_name' => 'servicio', 'icon' => '♿', 'name' => 'Accesible'],

            // ── Características ────────────────────────────────────────────────
            ['group_name' => 'caracteristicas', 'icon' => '🌾', 'name' => 'Productos caseros'],
            ['group_name' => 'caracteristicas', 'icon' => '🌱', 'name' => 'Productos naturales'],
            ['group_name' => 'caracteristicas', 'icon' => '🚫🌾', 'name' => 'Sin TACC'],
            ['group_name' => 'caracteristicas', 'icon' => '🥗', 'name' => 'Vegetariano / Vegano'],
            ['group_name' => 'caracteristicas', 'icon' => '📜', 'name' => 'Habilitado / Matriculado'],
            ['group_name' => 'caracteristicas', 'icon' => '🧾', 'name' => 'Factura'],

            // ── Pagos ──────────────────────────────────────────────────────────
            ['group_name' => 'pagos', 'icon' => '💵', 'name' => 'Efectivo'],
            ['group_name' => 'pagos', 'icon' => '📲', 'name' => 'Transferencia'],
            ['group_name' => 'pagos', 'icon' => '💳', 'name' => 'Mercado Pago'],
            ['group_name' => 'pagos', 'icon' => '💳', 'name' => 'Tarjeta'],
            ['group_name' => 'pagos', 'icon' => '📅', 'name' => 'Cuotas'],
        ];

        foreach ($tags as $tag) {
            \DB::statement("
                INSERT INTO tags (name, slug, group_name, icon, created_at, updated_at)
                SELECT ?, ?, ?, ?, NOW(), NOW()
                WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = ?)
            ", [
                $tag['name'],
                \Illuminate\Support\Str::slug($tag['name']),
                $tag['group_name'],
                $tag['icon'],
                \Illuminate\Support\Str::slug($tag['name']),
            ]);
        }
    }

    public function down() {}
}
