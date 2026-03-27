<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedPlaceholderEmailsToWorkers extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        DB::statement("
            UPDATE workers
            SET email = 'trabajador' || id || '@servipueblo.com'
            WHERE email IS NULL OR email = ''
        ");
    }

    public function down()
    {
        DB::statement("
            UPDATE workers
            SET email = NULL
            WHERE email LIKE '%@servipueblo.com'
        ");
    }
}
