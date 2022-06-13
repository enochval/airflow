<?php
use \App\Models\User;
use \Illuminate\Support\Facades\Hash;

dataset('user', function () {
    return fn() => User::factory()->create(['email' => 'test@email.com', 'password' => Hash::make('password')]);
});
