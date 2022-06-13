<?php


namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Arr;

class CreateUserAction
{
    public function __construct() {}

    public function handle(array $data)
    {
        $data['is_active'] = true;

        $payload = Arr::only($data, [
            'first_name', 'last_name', 'email',
            'phone_no', 'company_id', 'is_active'
        ]);

        $payload['password'] = bcrypt($data['password']);

        User::create($payload);
    }
}
