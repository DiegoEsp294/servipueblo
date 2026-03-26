<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailRateInfoToWorkers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone');
            $table->string('rate_info', 100)->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn(['email', 'rate_info']);
        });
    }
}
