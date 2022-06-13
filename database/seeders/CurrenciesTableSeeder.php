<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $json = File::get("database/data/currencies.json");
        $currencies = json_decode($json);

        foreach ($currencies as $currency) {
            $data = Arr::only((array) $currency, [
                'name', 'symbol', 'decimal_digits', 'rounding', 'code'
            ]);

            Currency::firstOrCreate($data);
        }
    }
}
