<?php

use App\Entities\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file_path = realpath(__DIR__ . '/../../storage/data/currencies.json');
        $json = json_decode(file_get_contents($file_path), true);

        $this->command->info('Truncating currencies table...');
//        DB::table('currencies')->truncate();
        $this->command->info('Seed starting for currencies...');
        foreach ($json as $obj) {
            $this->command->info('Creating  ' . $obj['name']);
            Currency::create([
                'name' => $obj['name'],
                'code' => $obj['code'],
                'symbol' => $obj['symbol'],
                'is_active' => true
            ]);
            $this->command->info('created  ' . $obj['name']);
        }
    }
}
