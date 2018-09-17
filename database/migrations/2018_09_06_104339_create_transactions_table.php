<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {

            $table->increments('id');
            $table->string('type', 15);
            $table->string('from_exchange', 15);
            $table->string('to_exchange', 15);
            $table->string('from_currency', 15);
            $table->string('to_currency', 15);
            $table->string('price', 10);
            $table->string('amount', 10);
            $table->jsonb('response')->default("{}");
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
        Schema::dropIfExists('transactions');
    }
}
