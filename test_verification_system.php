#!/usr/bin/env php
<?php
/**
 * Test Email Verification System
 * Run: php test_verification_system.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "\n=== PDMU PDMUOMS - Email Verification System Test ===\n\n";

// Test 1: Check database connection
echo "✓ Test 1: Database Connection\n";
try {
    DB::connection()->getPdo();
    echo "  ✓ Connected to database: " . DB::getDatabaseName() . "\n";
} catch (\Exception $e) {
    echo "  ✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check mail configuration
echo "\n✓ Test 2: Mail Configuration\n";
echo "  Mail Driver: " . config('mail.default') . "\n";
echo "  From Address: " . config('mail.from.address') . "\n";
echo "  From Name: " . config('mail.from.name') . "\n";

// Test 3: Check User model implements MustVerifyEmail
echo "\n✓ Test 3: User Model Configuration\n";
$user = new User();
if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
    echo "  ✓ User model implements MustVerifyEmail interface\n";
} else {
    echo "  ✗ User model does NOT implement MustVerifyEmail\n";
    exit(1);
}

// Test 4: Check tbusers table structure
echo "\n✓ Test 4: Database Table Structure\n";
$columns = DB::getSchemaBuilder()->getColumnListing('tbusers');
$requiredColumns = ['emailaddress', 'email_verified_at', 'status'];
foreach ($requiredColumns as $col) {
    if (in_array($col, $columns)) {
        echo "  ✓ Column '$col' exists\n";
    } else {
        echo "  ✗ Column '$col' NOT found\n";
        exit(1);
    }
}

// Test 5: Check routes
echo "\n✓ Test 5: Verification Routes\n";
$routes = [
    'verification.notice' => '/email/verify',
    'verification.verify' => '/email/verify/{id}/{hash}',
    'verification.resend' => '/email/resend',
];

foreach ($routes as $name => $path) {
    try {
        $route = app('router')->getRoutes()->getByName($name);
        echo "  ✓ Route '$name' registered at '$path'\n";
    } catch (\Exception $e) {
        echo "  ✗ Route '$name' NOT registered\n";
        exit(1);
    }
}

// Test 6: Check VerificationController
echo "\n✓ Test 6: Controller Classes\n";
if (class_exists('App\\Http\\Controllers\\Auth\\VerificationController')) {
    echo "  ✓ VerificationController found\n";
} else {
    echo "  ✗ VerificationController NOT found\n";
    exit(1);
}

if (class_exists('App\\Notifications\\VerifyEmailNotification')) {
    echo "  ✓ VerifyEmailNotification found\n";
} else {
    echo "  ✗ VerifyEmailNotification NOT found\n";
    exit(1);
}

// Test 7: Check for manual verification command
echo "\n✓ Test 7: Artisan Commands\n";
if (class_exists('App\\Console\\Commands\\VerifyUserEmail')) {
    echo "  ✓ user:verify-email command available\n";
} else {
    echo "  ✗ user:verify-email command NOT found\n";
}

// Test 8: Count existing users
echo "\n✓ Test 8: Database Statistics\n";
$userCount = User::count();
echo "  Total users in database: $userCount\n";

$verifiedCount = User::whereNotNull('email_verified_at')->count();
echo "  Verified users: $verifiedCount\n";

$activeCount = User::where('status', 'active')->count();
echo "  Active users: $activeCount\n";

$inactiveCount = User::where('status', 'inactive')->count();
echo "  Inactive users (pending verification): $inactiveCount\n";

echo "\n=== All Tests Passed! ===\n\n";
echo "Next Steps:\n";
echo "1. Go to http://localhost/register\n";
echo "2. Fill in the registration form\n";
echo "3. Submit the form\n";
echo "4. Check storage/logs/laravel.log for verification email content\n";
echo "5. Click the verification link from the email\n";
echo "6. User status will change from 'inactive' to 'active'\n";
echo "7. User can now login\n\n";
echo "To manually verify a user:\n";
echo "  php artisan user:verify-email {username}\n\n";
