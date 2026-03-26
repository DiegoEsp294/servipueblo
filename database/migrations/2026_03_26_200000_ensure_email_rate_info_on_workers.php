<?php

use Illuminate\Database\Migrations\Migration;

class EnsureEmailRateInfoOnWorkers extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        \DB::statement('ALTER TABLE workers ADD COLUMN IF NOT EXISTS rate_info VARCHAR(100)');
        \DB::statement('ALTER TABLE workers ADD COLUMN IF NOT EXISTS email VARCHAR(150)');
    }

    public function down()
    {
        // intentionally left blank — safe rollback not needed
    }
}
