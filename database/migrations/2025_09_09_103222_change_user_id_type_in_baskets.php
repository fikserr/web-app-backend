<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('baskets', function (Blueprint $table) {
            $table->bigInteger('user_id')->change();
        });
    }

    public function down()
    {
        Schema::table('baskets', function (Blueprint $table) {
            $table->string('user_id')->change();
        });
    }

};
