<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // No envolver en transacción para poder usar IF NOT EXISTS
    public $withinTransaction = false;

    public function up()
    {
        // Columna
        \DB::statement('ALTER TABLE ratings ADD COLUMN IF NOT EXISTS user_id BIGINT NULL');

        // Foreign key
        \DB::statement('
            DO $$ BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_constraint
                    WHERE conname = \'ratings_user_id_foreign\'
                ) THEN
                    ALTER TABLE ratings ADD CONSTRAINT ratings_user_id_foreign
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
                END IF;
            END $$;
        ');

        // Unique
        \DB::statement('
            DO $$ BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_indexes
                    WHERE indexname = \'ratings_worker_user_unique\'
                ) THEN
                    CREATE UNIQUE INDEX ratings_worker_user_unique ON ratings(worker_id, user_id);
                END IF;
            END $$;
        ');
    }

    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique('ratings_worker_user_unique');
            $table->dropConstrainedForeignId('user_id');
        });
    }
}
