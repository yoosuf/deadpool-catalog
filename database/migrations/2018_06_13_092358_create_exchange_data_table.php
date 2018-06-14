<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_data', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('exchange_id');
            $table->foreign('exchange_id')
                ->references('id')->on('exchanges')
                ->onDelete('cascade');
            $table->jsonb('preference')->default("{}");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchange_data');
    }
}
