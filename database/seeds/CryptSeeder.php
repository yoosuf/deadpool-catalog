<?php

use App\Entities\Crypt;
use Illuminate\Database\Seeder;

class CryptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file_path = realpath(__DIR__ . '/../../storage/data/crypts.json');
        $json = json_decode(file_get_contents($file_path), true);

        $this->command->info('Truncating countries table...');
//        DB::table('crypts')->truncate();
        $this->command->info('Seed starting for crypts...');
        foreach ($json as $obj) {
            $this->command->info('Creating  ' . $obj['name']);
            Crypt::create([
                'name' => $obj['name'],
                'code' => $obj['code'],
                'symbol' => $obj['symbol'],
                'is_active' => true
            ]);
            $this->command->info('created  ' . $obj['name']);
        }
    }
}
