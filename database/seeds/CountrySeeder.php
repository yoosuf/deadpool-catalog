<?php

use App\Entities\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file_path = realpath(__DIR__ . '/../../storage/data/countries.json');
        $json = json_decode(file_get_contents($file_path), true);

        $this->command->info('Truncating countries table...');
        // DB::table('countries')->truncate();
        $this->command->info('Seed starting for countries...');
        foreach ($json as $obj) {
            $this->command->info('Creating  ' . $obj['nice_name']);
            Country::create([
                'name' => $obj['name'],
                'nice_name' => $obj['nice_name'],
                'iso' => $obj['iso'],
                'iso3' => $obj['iso3'],
                'phone_code' => $obj['phonecode'],
                'is_active' => true
            ]);
            $this->command->info('created  ' . $obj['nice_name']);
        }
    }
}
