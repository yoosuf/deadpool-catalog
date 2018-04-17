<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuySellFieldsOnCryptExchange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crypt_exchange', function (Blueprint $table) {
            $table->string('current_rate')->default("");
            $table->string('asking_rate')->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crypt_exchange', function (Blueprint $table) {
            $table->dropColumn(['current_rate', 'asking_rate']);
        });
    }
}
