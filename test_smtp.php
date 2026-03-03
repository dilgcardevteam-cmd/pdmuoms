<?php
/**
 * SMTP Test Script
 * Run: php artisan tinker < test_smtp.php
 */

use Illuminate\Support\Facades\Mail;

echo "Testing SMTP Configuration...\n\n";

// Test 1: Check configuration
echo "=== Configuration Check ===\n";
echo "Mail Driver: " . config('mail.default') . "\n";
echo "SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
echo "SMTP Port: " . config('mail.mailers.smtp.port') . "\n";
echo "SMTP Username: " . config('mail.mailers.smtp.username') . "\n";
echo "From Address: " . config('mail.from.address') . "\n";
echo "From Name: " . config('mail.from.name') . "\n\n";

// Test 2: Send a test email
echo "=== Sending Test Email ===\n";
try {
    Mail::raw('This is a test email from your PDMU PDMUOMS system. If you received this, your SMTP configuration is working correctly!', function ($message) {
        $message->to('test@example.com') // Change this to your test email
                ->subject('PDMU PDMUOMS - SMTP Test');
    });
    echo "✓ Email sent successfully!\n";
    echo "Check your email for the test message.\n";
} catch (\Exception $e) {
    echo "✗ Email sending failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
}
