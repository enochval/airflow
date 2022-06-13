<?php


namespace App\Actions;

use App\Models\Client;

class CreateClientAction
{
    public function handle($name, $url)
    {
        Client::create([
            'name' => $name,
            'url' => $url,
        ]);
    }
}
