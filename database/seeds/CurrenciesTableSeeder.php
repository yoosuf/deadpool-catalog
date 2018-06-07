<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Currency;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file_path = realpath(__DIR__ . '/../../storage/data/currencies.json');
        $data = json_decode(file_get_contents($file_path), true);

        foreach ($data as $obj) {

            Currency::create([
                'name' => $obj['name'],
                'iso' => $obj['code'],
                'iso3' => $obj['code'],
                'symbol' => $obj['symbol']
            ]);
        }
    }
}
