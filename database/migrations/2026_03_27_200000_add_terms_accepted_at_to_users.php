<?php

use Illuminate\Database\Migrations\Migration;

class AddTermsAcceptedAtToUsers extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS terms_accepted_at TIMESTAMP NULL");
    }

    public function down()
    {
        \DB::statement("ALTER TABLE users DROP COLUMN IF EXISTS terms_accepted_at");
    }
}
