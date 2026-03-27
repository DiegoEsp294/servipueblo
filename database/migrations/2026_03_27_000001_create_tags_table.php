<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        \DB::statement("
            CREATE TABLE IF NOT EXISTS tags (
                id SERIAL PRIMARY KEY,
                name VARCHAR(60) NOT NULL,
                slug VARCHAR(60) NOT NULL,
                group_name VARCHAR(40) NOT NULL DEFAULT 'general',
                icon VARCHAR(10) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )
        ");

        \DB::statement("
            CREATE TABLE IF NOT EXISTS tag_worker (
                tag_id INTEGER NOT NULL REFERENCES tags(id) ON DELETE CASCADE,
                worker_id INTEGER NOT NULL REFERENCES workers(id) ON DELETE CASCADE,
                PRIMARY KEY (tag_id, worker_id)
            )
        ");
    }

    public function down()
    {
        Schema::dropIfExists('tag_worker');
        Schema::dropIfExists('tags');
    }
}
