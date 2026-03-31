<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddExpiresAtToWorkerPosts extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        DB::statement('ALTER TABLE worker_posts ADD COLUMN IF NOT EXISTS expires_at TIMESTAMP NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE worker_posts DROP COLUMN IF EXISTS expires_at');
    }
}
