<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("/../storage/data/currencies.json");
        $data = json_decode($json);
        //$array1 = $data->toArray();
        foreach ($data as $obj) {
            DB::table('currencies')->insert(array(
                'name' => $obj->name,
                'iso' => $obj->code,
                'iso3' => $obj->code,
                'symbol' => $obj->symbol
            ));
        }
    }
}
