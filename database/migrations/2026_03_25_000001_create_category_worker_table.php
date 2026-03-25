<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryWorkerTable extends Migration
{
    public function up()
    {
        Schema::create('category_worker', function (Blueprint $table) {
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->primary(['worker_id', 'category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_worker');
    }
}
