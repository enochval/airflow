<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class FirstUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::count() == 0) {
            Client::factory()
                ->has(
                    Company::factory()
                        ->state(function (array $attributes, Client $client) {
                            return [
                                'name' => $client->name,
                                'url' => $client->url,
                            ];
                        })
                    ->has(
                        User::factory()
                            ->state(function (array $attributes, Company $company) {
                                return [
                                    'email' => 'johndoe@seamlesshr.com'
                                ];
                            })
                    )
                )
                ->create([
                    'url' => 'https://breeze-test.seamlesshr.com'
                ]);
        }
    }
}
