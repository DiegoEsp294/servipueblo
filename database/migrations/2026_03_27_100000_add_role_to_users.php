<?php

use Illuminate\Database\Migrations\Migration;

class AddRoleToUsers extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(20) NOT NULL DEFAULT 'admin'");
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS worker_id INTEGER NULL REFERENCES workers(id) ON DELETE SET NULL");
    }

    public function down()
    {
        \DB::statement("ALTER TABLE users DROP COLUMN IF EXISTS worker_id");
        \DB::statement("ALTER TABLE users DROP COLUMN IF EXISTS role");
    }
}
