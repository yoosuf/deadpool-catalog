<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountryExchangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_exchange', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('country_id');
            $table->foreign('country_id')
                ->references('id')->on('countries')
                ->onDelete('cascade');

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
        Schema::dropIfExists('country_exchange');
    }
}
