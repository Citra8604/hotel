<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $email = 'test_' . time() . '@example.com';
    $user = User::create([
        'name' => 'Test User',
        'email' => $email,
        'password' => Hash::make('password123'),
        'role' => 'guest',
    ]);
    echo "Successfully created user: " . $user->email . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
