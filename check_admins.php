<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Admin Users Check ===\n";

try {
    $admins = DB::table('admins')->get();
    
    if ($admins->isEmpty()) {
        echo "No admin users found in the database.\n";
        echo "You need to create an admin user first.\n";
        
        // Check if we can create one
        echo "\n=== Creating Default Admin User ===\n";
        $adminId = DB::table('admins')->insertGetId([
            'email' => 'admin@esa.com',
            'password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "Default admin created with ID: $adminId\n";
        echo "Email: admin@esa.com\n";
        echo "Password: admin123\n";
        echo "\nPlease login at: /admin/login\n";
    } else {
        echo "Found admin users:\n";
        foreach ($admins as $admin) {
            echo "ID: {$admin->id}, Email: {$admin->email}\n";
        }
        echo "\nYou can login at: /admin/login\n";
    }
} catch (Exception $e) {
    echo "Error checking admin users: " . $e->getMessage() . "\n";
}

?>
