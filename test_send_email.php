<?php
/**
 * Simple Email Test
 * Run: php artisan tinker < test_send_email.php
 * Or copy-paste into: php artisan tinker
 */

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

echo "Testing Email Configuration...\n\n";

// Test 1: Check configuration
echo "=== Configuration Check ===\n";
echo "Mail Driver: " . config('mail.default') . "\n";
echo "SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
echo "SMTP Port: " . config('mail.mailers.smtp.port') . "\n";
echo "From Address: " . config('mail.from.address') . "\n";
echo "From Name: " . config('mail.from.name') . "\n\n";

// Test 2: Send a test email
echo "=== Sending Test Email ===\n";
try {
    Mail::raw('This is a test email from your PDMU PDMUOMS system. If you received this, your email configuration is working correctly!', function (Message $message) {
        $message->to('subaybayancordillera@gmail.com')
                ->subject('PDMU PDMUOMS - Email Configuration Test');
    });
    echo "✓ Email sent successfully!\n";
    echo "Check your email at: subaybayancordillera@gmail.com\n";
} catch (\Exception $e) {
    echo "✗ Email sending failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
}
