<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountryCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_currency', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('country_id');
            $table->foreign('country_id')
                ->references('id')->on('countries')
                ->onDelete('cascade');
            $table->unsignedInteger('currency_id');
            $table->foreign('currency_id')
                ->references('id')->on('currencies')
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
        Schema::dropIfExists('country_currency');
    }
}
