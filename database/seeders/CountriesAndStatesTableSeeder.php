<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountriesAndStatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Country::count() == 0) {
            $json = File::get("database/data/countriesandstates.json");
            $data = json_decode($json);

            foreach ($data as $value) {
                $country = Country::firstOrCreate([
                    'name' => $value->name,
                    'country_code' => $value->iso3,
                    'mobile_code' => $value->phone_code,
                ]);

                foreach ($value->states as $state) {
                    $country->states()->firstOrCreate([
                        'name' => $state->name,
                        'abbr' => $state->state_code,
                    ]);
                }
            }
        }

    }
}
