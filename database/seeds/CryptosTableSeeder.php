<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Crypto;

class CryptosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file_path = realpath(__DIR__ . '/../../storage/data/cryptos.json');
        $data = json_decode(file_get_contents($file_path), true);
        //$array1 = $data->toArray();
        foreach ($data as $obj) {
            Crypto::create([
                'name' => $obj['name'],
                'code' => $obj['code'],
                'symbol' => $obj['symbol'],
                'is_active' => $obj['is_active'],
            ]);
        }
    }
}
