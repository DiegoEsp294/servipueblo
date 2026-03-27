<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateChatLogsTable extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS chat_logs (
                id              BIGSERIAL PRIMARY KEY,
                message         TEXT NOT NULL,
                context_words   TEXT NULL,
                workers_found   INTEGER NOT NULL DEFAULT 0,
                ai_reply        TEXT NULL,
                reason          VARCHAR(50) NOT NULL DEFAULT 'no_workers_found',
                reviewed        BOOLEAN NOT NULL DEFAULT FALSE,
                admin_note      TEXT NULL,
                created_at      TIMESTAMP NOT NULL DEFAULT NOW(),
                updated_at      TIMESTAMP NOT NULL DEFAULT NOW()
            )
        ");
    }

    public function down()
    {
        DB::statement('DROP TABLE IF EXISTS chat_logs');
    }
}
