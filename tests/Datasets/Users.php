<?php
use \App\Models\User;
use \Illuminate\Support\Facades\Hash;

dataset('users', function () {
    return fn() => collect([
        User::factory()->create(['email' => 'test@email.com', 'password' => Hash::make('password')]),
        User::factory()->create(['email' => 'test2@email.com', 'password' => Hash::make('password')])
    ]);
});
