<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tradings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 15);
            $table->string('from_exchange', 15);
            $table->string('to_exchange', 15);
            $table->string('from_currency', 15);
            $table->string('to_currency', 15);
            $table->string('from_crypto', 15);
            $table->string('to_crypto', 15);
            $table->string('start_order_price', 10);
            $table->string('end_order_price', 10);
            $table->string('amount', 10);
            $table->jsonb('start_response')->default("{}");
            $table->jsonb('end_response')->default("{}");
            $table->tinyInteger('status');

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
        Schema::dropIfExists('tradings');
    }
}
