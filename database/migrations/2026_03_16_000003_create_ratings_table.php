<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('score');
            $table->text('comment')->nullable();
            $table->string('reviewer_name', 80)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index('worker_id');
            $table->index(['ip_address', 'worker_id']);
        });

        // CHECK constraint para score 1-5
        DB::statement('ALTER TABLE ratings ADD CONSTRAINT ratings_score_check CHECK (score BETWEEN 1 AND 5)');
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
