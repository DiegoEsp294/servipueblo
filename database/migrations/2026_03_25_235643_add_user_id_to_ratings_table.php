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
    public function up()
    {
        // Agregar columna si no existe
        if (!Schema::hasColumn('ratings', 'user_id')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('worker_id');
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        // Agregar unique si no existe
        $exists = collect(\DB::select("
            SELECT indexname FROM pg_indexes
            WHERE tablename = 'ratings' AND indexname = 'ratings_worker_user_unique'
        "))->isNotEmpty();

        if (!$exists) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->unique(['worker_id', 'user_id'], 'ratings_worker_user_unique');
            });
        }
    }

    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique('ratings_worker_user_unique');
            $table->dropConstrainedForeignId('user_id');
        });
    }
}
