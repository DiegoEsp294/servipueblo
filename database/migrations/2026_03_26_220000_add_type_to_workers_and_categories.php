<?php

use Illuminate\Database\Migrations\Migration;

class AddTypeToWorkersAndCategories extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        // workers.type: 'worker' (oficio) | 'entrepreneur' (emprendimiento)
        \DB::statement("ALTER TABLE workers ADD COLUMN IF NOT EXISTS type VARCHAR(20) NOT NULL DEFAULT 'worker'");

        // categories.for_type: 'worker' | 'entrepreneur' | 'all'
        \DB::statement("ALTER TABLE categories ADD COLUMN IF NOT EXISTS for_type VARCHAR(20) NOT NULL DEFAULT 'worker'");
    }

    public function down()
    {
        \DB::statement('ALTER TABLE workers DROP COLUMN IF EXISTS type');
        \DB::statement('ALTER TABLE categories DROP COLUMN IF EXISTS for_type');
    }
}
