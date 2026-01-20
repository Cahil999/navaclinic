<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$namesToDelete = [
    "Test User",
    "chinnakit uparakul",
    "CABER",
    "Doctor Fah"
];

foreach ($namesToDelete as $name) {
    $user = \App\Models\User::where('name', $name)->first();
    if ($user) {
        echo "Found user: {$user->name} (ID: {$user->id})\n";

        // Unlink bookings
        $count = \App\Models\Booking::where('user_id', $user->id)->update(['user_id' => null]);
        echo "  Unlinked {$count} bookings.\n";

        // Delete user
        $user->delete();
        echo "  Deleted user.\n";
    } else {
        echo "User {$name} not found.\n";
    }
}
