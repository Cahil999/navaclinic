<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::where('is_admin', false)->get();
echo "Total Users: " . $users->count() . "\n";
foreach ($users as $user) {
    echo "ID: {$user->id} | Name: {$user->name} | Phone: {$user->phone_number} | Email: {$user->email}\n";
}
