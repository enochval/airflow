<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//         \App\Models\User::factory(10)->create();
        $this->call(CountriesAndStatesTableSeeder::class);
        $this->call(BanksTableSeeder::class);
        $this->call(FirstUserSeeder::class);
        $this->call(SeedPaymentTypes::class);
        $this->call(CurrenciesTableSeeder::class);
    }
}
