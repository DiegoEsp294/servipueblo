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
        Schema::table('ratings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('worker_id')->constrained()->nullOnDelete();
            $table->unique(['worker_id', 'user_id'], 'ratings_worker_user_unique');
        });
    }

    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique('ratings_worker_user_unique');
            $table->dropColumn('user_id');
        });
    }
}
