<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateWorkerPostsTable extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS worker_posts (
                id           BIGSERIAL PRIMARY KEY,
                worker_id    INTEGER NOT NULL REFERENCES workers(id) ON DELETE CASCADE,
                content      TEXT NOT NULL,
                photo_path   VARCHAR(500) NULL,
                is_sold_out  BOOLEAN NOT NULL DEFAULT FALSE,
                created_at   TIMESTAMP NOT NULL DEFAULT NOW(),
                updated_at   TIMESTAMP NOT NULL DEFAULT NOW()
            )
        ");
        DB::statement('CREATE INDEX IF NOT EXISTS worker_posts_worker_id_idx ON worker_posts (worker_id, created_at DESC)');
    }

    public function down()
    {
        DB::statement('DROP TABLE IF EXISTS worker_posts');
    }
}
