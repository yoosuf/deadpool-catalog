<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CryptosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("/../storage/data/cryptos.json");
        $data = json_decode($json);
        //$array1 = $data->toArray();
        foreach ($data as $obj) {
            DB::table('cryptos')->insert(array(
                'name' => $obj->name,
                'code' => $obj->code,
                'symbol' => $obj->symbol,
                'is_active' => $obj->is_active
            ));
        }
    }
}
