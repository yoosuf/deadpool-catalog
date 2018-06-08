<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Country;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      
        $file_path = realpath(__DIR__ . '/../../storage/data/countries.json');
        $data = json_decode(file_get_contents($file_path), true);

        foreach ($data as $obj) {
           
            Country::create([
                'name' => $obj['name'],
                'nice_name' => $obj['nice_name'],
                'iso' => $obj['iso'],
                'iso3' => $obj['iso3'],
                'phone_code' => $obj['phonecode']
            ]);

        }
    }
}
