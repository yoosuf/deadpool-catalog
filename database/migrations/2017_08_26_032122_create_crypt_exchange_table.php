<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCryptExchangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypt_exchange', function (Blueprint $table) {

            $table->integer('exchange_id')->unsigned();
            $table->foreign('exchange_id')
                        ->references('id')->on('exchanges')
                        ->onDelete('cascade');

            $table->integer('crypt_id')->unsigned();
            $table->foreign('crypt_id')
                        ->references('id')->on('crypts')
                        ->onDelete('cascade');

            $table->jsonb('preferences')->default("{}");                            
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->primary(['exchange_id', 'crypt_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crypt_exchange');
    }
}
