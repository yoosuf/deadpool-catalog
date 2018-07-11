<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencyValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('currency_id');
            $table->foreign('currency_id')
                ->references('id')->on('currencies')
                ->onDelete('cascade');
            $table->jsonb('other_conversion_values')->default("{}");
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
        Schema::dropIfExists('currency_values');
    }
}
