<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateBusinessHoursTable extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS business_hours (
                id           BIGSERIAL PRIMARY KEY,
                worker_id    INTEGER NOT NULL REFERENCES workers(id) ON DELETE CASCADE,
                day_of_week  SMALLINT NOT NULL, -- 1=Lunes, 2=Martes, ..., 7=Domingo
                is_closed    BOOLEAN NOT NULL DEFAULT FALSE,
                open_time    VARCHAR(5) NULL,
                close_time   VARCHAR(5) NULL,
                created_at   TIMESTAMP NOT NULL DEFAULT NOW(),
                updated_at   TIMESTAMP NOT NULL DEFAULT NOW(),
                UNIQUE(worker_id, day_of_week)
            )
        ");
    }

    public function down()
    {
        DB::statement('DROP TABLE IF EXISTS business_hours');
    }
}
