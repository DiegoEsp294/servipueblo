<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSessionsTable extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS sessions (
                id           VARCHAR(255) PRIMARY KEY,
                user_id      BIGINT NULL,
                ip_address   VARCHAR(45) NULL,
                user_agent   TEXT NULL,
                payload      TEXT NOT NULL,
                last_activity INTEGER NOT NULL
            )
        ");

        DB::statement('CREATE INDEX IF NOT EXISTS sessions_user_id_index ON sessions (user_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON sessions (last_activity)');
    }

    public function down()
    {
        DB::statement('DROP TABLE IF EXISTS sessions');
    }
}
