<?php

use App\Models\User;
use App\Models\Address;

// Find the first user
$user = User::first();

if (!$user) {
    echo "No users found.\n";
    exit;
}

echo "Testing address creation for user: " . $user->name . " (ID: " . $user->id . ")\n";

try {
    $address = $user->addresses()->create([
        'label' => 'Test Address',
        'full_address' => '123 Test St',
        'city' => 'Test City',
        'province' => 'Test Province',
        'postal_code' => '12345',
        'is_primary' => true,
    ]);

    echo "Address created successfully: ID " . $address->id . "\n";
} catch (\Exception $e) {
    echo "Error creating address: " . $e->getMessage() . "\n";
}
