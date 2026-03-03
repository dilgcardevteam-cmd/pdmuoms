# Email Verification - Implementation Guide

## Current Setup

The email verification system is now fully implemented with the following flow:

### Registration Process
1. User fills registration form
2. User data is validated
3. User account created with status = `inactive`
4. `Registered` event is fired
5. Verification email is sent
6. Success message shown to user
7. User redirected to login

### Email Sending
- **Current Mail Driver**: `log` (for testing/development)
- **Default Configuration**: Gmail SMTP (ready to use)
- **Queue**: Database (can be changed to `sync`)

### Verification Process
1. User receives verification email with a unique link
2. Email link expires in 60 minutes
3. User clicks link in email
4. System verifies the signature of the link
5. User status is updated from `inactive` to `active`
6. Email is marked as verified
7. User can now access the system

## Switching Email Drivers

### Option 1: Use Log Driver (Current - For Development/Testing)
The email content is logged to `storage/logs/laravel.log`

**View the emails sent in logs:**
```bash
# On Windows with PowerShell:
Get-Content storage/logs/laravel.log -Tail 50 -Wait

# Or view the last emails:
Get-Content storage/logs/laravel.log -Tail 100
```

### Option 2: Use Gmail SMTP (Production)

1. **Update `.env` file:**
   ```
   MAIL_MAILER=smtp
   ```

2. **Ensure your Gmail account has:**
   - Two-factor authentication enabled
   - App password generated (NOT your regular password)
   - Less secure app access enabled (for older Gmail)

3. **Get your Gmail App Password:**
   - Go to: https://myaccount.google.com/apppasswords
   - Select Mail and Windows Computer
   - Copy the 16-character password
   - Paste it in `.env` as `MAIL_PASSWORD`

4. **Clear config cache:**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

## Testing Email Verification

### Method 1: Automatic Testing via Registration Form
1. Go to `http://localhost/register`
2. Fill in all fields:
   - Email: Use a real email you have access to
   - All other fields with valid data
3. Click "Register"
4. See "User successfully registered in the system" message
5. Check your email for verification link
6. Click link to verify (or check logs if using log driver)

### Method 2: Manual User Verification (Admin)
If email doesn't arrive or for testing purposes:

```bash
php artisan user:verify-email {username}
```

Example:
```bash
php artisan user:verify-email johndoe
```

This will:
- ✓ Mark user's email as verified
- ✓ Set user status to `active`
- ✓ User can immediately login

### Method 3: Resend Verification Email
After registration, user is redirected to email verification notice page. They can click "Send Verification Email Again" to request a new email.

Rate limited to: **6 requests per minute**

## Email Structure

The verification email includes:

```
Subject: Verify Your Email Address - PDMU PDMUOMS

Hello [First Name] [Last Name]!

Thank you for registering with the PDMU Operations Management System (PDMUOMS).
Your account has been successfully created. 
To activate your account, please verify your email address by clicking the button below.

[Verify Email Address Button]

This verification link will expire in 60 minutes.

If you did not create an account, no further action is required.

If the button above does not work, copy and paste this URL into your web browser:
[Verification Link URL]

Best regards,
PDMU Operations Management System (PDMUOMS)
```

## User Status Changes

### Registration
- Status: `inactive`
- Email Verified: `null`
- Can Access: `/register`, `/login` only

### After Email Verification
- Status: `active`
- Email Verified: `[timestamp]`
- Can Access: `/home` and protected routes

## Database Columns

User (`tbusers`) table columns involved in verification:

```sql
email_verified_at    -- Timestamp when email was verified (NULL = not verified)
emailaddress         -- Email used for verification
status               -- 'inactive' or 'active'
```

## Troubleshooting

### Email Not Received

1. **Check logs if using log driver:**
   ```bash
   Get-Content storage/logs/laravel.log -Tail 100
   ```

2. **Verify user was created:**
   ```bash
   php artisan tinker
   >>> App\Models\User::where('username', 'johndoe')->first()
   ```

3. **Check email in spam folder** (if using real email)

4. **Resend verification email:**
   - After login attempt, click "Send Verification Email Again"

### User Can't Login After Verification

1. Check if `status` is `active`:
   ```bash
   php artisan tinker
   >>> App\Models\User::where('username', 'johndoe')->first()->status
   ```

2. Check if `email_verified_at` is set:
   ```bash
   php artisan tinker
   >>> App\Models\User::where('username', 'johndoe')->first()->email_verified_at
   ```

3. Manually verify user:
   ```bash
   php artisan user:verify-email johndoe
   ```

### Verification Link Expired

- User can request new link via "Send Verification Email Again"
- New links are valid for 60 minutes
- Resend is rate-limited to 6 per minute

## Configuration Files Modified

- ✅ `app/Notifications/VerifyEmailNotification.php` - Custom email template
- ✅ `app/Models/User.php` - Uses custom notification, implements MustVerifyEmail
- ✅ `app/Http/Controllers/Auth/RegisterController.php` - Handles registration
- ✅ `app/Http/Controllers/Auth/VerificationController.php` - Handles verification, sets status to active
- ✅ `routes/web.php` - Email verification routes
- ✅ `resources/views/auth/verify.blade.php` - Verification notice view
- ✅ `app/Console/Commands/VerifyUserEmail.php` - Command to manually verify users
- ✅ `.env` - Mail driver and SMTP settings

## Production Checklist

- [ ] Change `MAIL_MAILER` from `log` to `smtp` in `.env`
- [ ] Use Gmail App Password (NOT regular password)
- [ ] Test email sending with test account
- [ ] Monitor email delivery rates
- [ ] Set up email logging/monitoring
- [ ] Document email verification process for users
- [ ] Test with real email addresses
- [ ] Verify SMTP credentials are correct
- [ ] Test link expiration (wait 60+ minutes)
- [ ] Test resend functionality

## Commands

```bash
# Clear configuration cache
php artisan config:clear
php artisan config:cache

# Manually verify user email
php artisan user:verify-email {username}

# View recent logs
Get-Content storage/logs/laravel.log -Tail 100

# Access Laravel tinker
php artisan tinker

# Check user in tinker:
>>> App\Models\User::where('username', 'johndoe')->first()
>>> App\Models\User::where('username', 'johndoe')->first()->status
```

## Next Steps

1. For development: Use `log` driver (emails logged to file)
2. For production: Switch to `smtp` driver with Gmail App Password
3. Monitor email delivery and user verification rates
4. Consider adding email bouncing/invalid email handling
5. Add admin panel to manage user verifications
