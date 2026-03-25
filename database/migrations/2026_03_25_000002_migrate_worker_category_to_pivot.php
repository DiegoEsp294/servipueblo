<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateWorkerCategoryToPivot extends Migration
{
    public function up()
    {
        // Copiar category_id existente al pivot como categoría primaria
        DB::statement('
            INSERT INTO category_worker (worker_id, category_id, is_primary)
            SELECT id, category_id, true
            FROM workers
            WHERE category_id IS NOT NULL
        ');

        // Eliminar la columna category_id de workers
        Schema::table('workers', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    public function down()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
        });

        // Restaurar desde pivot
        DB::statement('
            UPDATE workers w
            SET category_id = cw.category_id
            FROM category_worker cw
            WHERE cw.worker_id = w.id AND cw.is_primary = true
        ');
    }
}
