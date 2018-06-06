<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("/../resources/jsondata/countries.json");
        $data = json_decode($json);
        //$array1 = $data->toArray();
        foreach ($data as $obj) {
            DB::table('countries')->insert(array(
                'name' => $obj->name,
                'nice_name' => $obj->nice_name,
                'iso' => $obj->iso,
                'iso3' => $obj->iso3,
                'phonecode' => $obj->phonecode
            ));
        }
    }
}
