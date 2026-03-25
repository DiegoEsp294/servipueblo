<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkerEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worker_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            // view | whatsapp_click | share_click
            $table->string('type', 20)->index();
            // SHA-256 del IP — privacidad sin perder unicidad
            $table->string('ip_hash', 64)->nullable();
            // De dónde vino el visitante
            $table->string('referrer', 300)->nullable();
            // Solo created_at, sin updated_at
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('worker_events');
    }
}
