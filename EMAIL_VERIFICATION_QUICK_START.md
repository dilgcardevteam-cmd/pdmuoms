# Email Verification - Quick Start Guide

## System is Ready! ✅

Your PDMU PDMUOMS system is now configured to send verification emails after user registration.

## What Happens When a User Registers

1. User fills out registration form and clicks "Register"
2. Form data is validated on the server
3. User account is created with `status='inactive'`
4. **Email is automatically sent** to the user's registered email address
5. User sees success toast: "User successfully registered in the system."
6. User is redirected to login page with instructions to verify email

## Email Contents

The verification email includes:
- ✅ Personalized greeting with user's name
- ✅ Explanation that account was successfully created
- ✅ Link to verify email address
- ✅ Expiration time (60 minutes)
- ✅ Help text if they didn't receive it
- ✅ Professional PDMUOMS branding

## What Users Need to Do

1. Check their email inbox (or spam folder)
2. Click the "Verify Email Address" button in the email
3. This activates their account and sets `email_verified_at`
4. User can now login to the system

## If User Doesn't Receive Email

1. After login attempt, they'll see email verification notice
2. They can click "Send Verification Email Again"
3. New email with fresh verification link will be sent
4. Rate limited to 6 requests per minute for security

## Protected Routes

The `/home` route now requires:
- ✅ User to be authenticated (`auth` middleware)
- ✅ User email to be verified (`verified` middleware)

If unverified user tries to access `/home`, they're redirected to verification notice page.

## Testing Email Sending

### Method 1: Manual Test with Tinker
```bash
php artisan tinker
include 'test_email_verification.php'
```

### Method 2: Register a Real User
1. Go to `/register`
2. Fill in all fields with real email
3. Submit form
4. Check your email for verification link

## Configuration Details

- **Email Provider**: Gmail SMTP
- **SMTP Host**: smtp.gmail.com
- **SMTP Port**: 587
- **Encryption**: TLS
- **From Address**: subaybayancordillera@gmail.com
- **From Name**: PDMU Operations Management System (PDMUOMS)
- **Verification Link Expires**: 60 minutes
- **Resend Rate Limit**: 6 requests per minute

## Important: Gmail Setup

⚠️ **Your Gmail account needs:**
1. Two-factor authentication enabled
2. App-specific password created (not your regular Gmail password)
3. SMTP access enabled for less secure apps (or use app password)

The password in `.env` should be the **App Password**, not your regular Gmail password!

## Files Modified/Created

✅ Created: `app/Notifications/VerifyEmailNotification.php`
✅ Updated: `app/Models/User.php`
✅ Updated: `routes/web.php`
✅ Updated: `resources/views/auth/verify.blade.php`
✅ Updated: `app/Http/Controllers/Auth/RegisterController.php` (from previous fix)
✅ Config: `.env` (Gmail SMTP credentials)

## Troubleshooting

### Email not sending?
1. Check `.env` file has correct Gmail credentials
2. Verify app-specific password is used (not regular password)
3. Check "From" email matches Gmail account
4. Check logs: `storage/logs/laravel.log`

### Verification link not working?
1. Make sure `/email/verify/{id}/{hash}` route exists
2. Verify `signed` middleware is applied
3. Check link hasn't expired (60 minutes)
4. User can request new link via "Resend" button

### Users can't login after verification?
1. Check `email_verified_at` column is populated
2. Verify `verified` middleware on `/home` route
3. Check user `role`, `status`, and `access` are set correctly

## Next Steps (Optional)

- Send welcome email after successful verification
- Add user approval system (admin verification)
- Custom email templates with logo
- Database logging of sent emails
- Automated email retry on failure
