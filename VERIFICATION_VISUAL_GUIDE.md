# Email Verification System - Visual Guide

## Complete Verification Flow Diagram

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                     EMAIL VERIFICATION COMPLETE FLOW                         │
└──────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────┐
│     1. USER REGISTRATION        │
├─────────────────────────────────┤
│ User fills registration form:   │
│ - First Name: John              │
│ - Last Name: Doe                │
│ - Email: john@example.com       │
│ - Password: ••••••••            │
│ - Other details...              │
│                                 │
│ Clicks: [Register]              │
└────────────┬────────────────────┘
             │
             ▼ POST /register
┌─────────────────────────────────┐
│  RegisterController::register() │
├─────────────────────────────────┤
│ ✓ Validate input                │
│ ✓ Create user record            │
│ ✓ Set status = 'inactive'       │
│ ✓ Trigger Registered event      │
└────────────┬────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────┐
│  2. VERIFICATION EMAIL SENT                 │
├─────────────────────────────────────────────┤
│ Event: User::Registered triggered           │
│                                             │
│ System calls:                               │
│ User::sendEmailVerificationNotification()   │
│                                             │
│ What happens:                               │
│ ✓ Generate token: Str::random(64)           │
│   Token = 'Kv0EY8hhj5rK33JJuVoqu...' (64)  │
│ ✓ Save token to database                    │
│   UPDATE tbusers SET                        │
│   verification_token = 'Kv0EY8...'          │
│ ✓ Create email message                      │
│ ✓ Send email to john@example.com            │
└────────────┬────────────────────────────────┘
             │
             ▼
┌──────────────────────────────────────────────────────────┐
│  3. EMAIL RECEIVED BY USER                              │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  From: noreply@pdmu-poms.local                          │
│  To: john@example.com                                   │
│  Subject: Verify Your Email Address - PDMU PDMUOMS        │
│                                                          │
│  ┌───────────────────────────────────────────────────┐  │
│  │ PDMU PDMUOMS - Email Verification                    │  │
│  ├───────────────────────────────────────────────────┤  │
│  │                                                   │  │
│  │ Hello John Doe!                                   │  │
│  │                                                   │  │
│  │ Thank you for registering with PDMU PDMUOMS.        │  │
│  │                                                   │  │
│  │ To activate your account, please verify your     │  │
│  │ email address by clicking the button below:      │  │
│  │                                                   │  │
│  │ ┌─────────────────────────────────────────────┐  │  │
│  │ │  [VERIFY EMAIL ADDRESS]                     │  │  │
│  │ └─────────────────────────────────────────────┘  │  │
│  │                                                   │  │
│  │ Link: http://localhost:8000/email/verify/token/  │  │
│  │ Kv0EY8hhj5rK33JJuVoquTfW4dUjqBj7bHs3X1BUsz4...  │  │
│  │                                                   │  │
│  │ This verification link will expire in 60 min.    │  │
│  │                                                   │  │
│  │ If you did not create an account, no further     │  │
│  │ action is required.                              │  │
│  │                                                   │  │
│  │ Best regards,                                     │  │
│  │ PDMU Operations Management System                 │  │
│  │                                                   │  │
│  └───────────────────────────────────────────────────┘  │
│                                                          │
└──────────────┬───────────────────────────────────────────┘
               │
               ▼ User clicks [VERIFY EMAIL ADDRESS]
┌──────────────────────────────────────────────────────────┐
│  4. USER CLICKS VERIFICATION LINK                        │
├──────────────────────────────────────────────────────────┤
│                                                          │
│ Browser Request:                                        │
│ GET /email/verify/token/                                │
│     Kv0EY8hhj5rK33JJuVoquTfW4dUjqBj7bHs3X1BUsz4...      │
│                                                          │
│ Status: 200 OK                                          │
│ Processing verification...                              │
│                                                          │
└──────────────┬───────────────────────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────────────────────┐
│  5. VERIFICATION CONTROLLER PROCESSES                    │
├──────────────────────────────────────────────────────────┤
│  VerificationController::verifyWithToken($token)        │
│                                                          │
│  Step 1: Find user by token                             │
│  ┌────────────────────────────────────────┐             │
│  │ SELECT * FROM tbusers                  │             │
│  │ WHERE verification_token =             │             │
│  │ 'Kv0EY8hhj5rK33JJuVoquTfW4dUjqBj...'; │             │
│  └────────────────────────────────────────┘             │
│  Result: ✓ User found (ID: 1, john@example.com)        │
│                                                          │
│  Step 2: Check if already verified                      │
│  ┌────────────────────────────────────────┐             │
│  │ if (user->hasVerifiedEmail()) {        │             │
│  │   // email_verified_at IS NOT NULL     │             │
│  │ }                                      │             │
│  └────────────────────────────────────────┘             │
│  Result: ✓ Not yet verified (email_verified_at = NULL) │
│                                                          │
│  Step 3: Mark email as verified                         │
│  ┌────────────────────────────────────────┐             │
│  │ $user->markEmailAsVerified();           │             │
│  │ // Sets email_verified_at = NOW()      │             │
│  └────────────────────────────────────────┘             │
│  Result: ✓ email_verified_at = '2026-01-25 07:24:02'   │
│                                                          │
│  Step 4: Set status to 'active'                         │
│  ┌────────────────────────────────────────┐             │
│  │ $user->status = 'active';               │             │
│  └────────────────────────────────────────┘             │
│  Result: ✓ status = 'active'                           │
│                                                          │
│  Step 5: Clear verification token                       │
│  ┌────────────────────────────────────────┐             │
│  │ $user->verification_token = null;       │             │
│  └────────────────────────────────────────┘             │
│  Result: ✓ verification_token = NULL                    │
│                                                          │
│  Step 6: Save all changes to database                   │
│  ┌────────────────────────────────────────┐             │
│  │ UPDATE tbusers SET                     │             │
│  │   status = 'active',                   │             │
│  │   verification_token = NULL,           │             │
│  │   email_verified_at = '2026-01-25...'  │             │
│  │ WHERE idno = 1;                        │             │
│  └────────────────────────────────────────┘             │
│  Result: ✓ Database updated successfully                │
│                                                          │
└──────────────┬───────────────────────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────────────────────┐
│  6. REDIRECT TO LOGIN                                    │
├──────────────────────────────────────────────────────────┤
│  HTTP 302 Redirect to: /login                           │
│                                                          │
│  With Message:                                          │
│  "Email verified successfully! You can now login."      │
│                                                          │
│  ┌───────────────────────────────────────────────────┐  │
│  │ ✓ Email Verification Successful                   │  │
│  ├───────────────────────────────────────────────────┤  │
│  │                                                   │  │
│  │ Your email has been verified and your account     │  │
│  │ is now active.                                    │  │
│  │                                                   │  │
│  │ You can now login with your credentials:          │  │
│  │ Email: john@example.com                           │  │
│  │ Password: ••••••••                                │  │
│  │                                                   │  │
│  │ [Login]                                           │  │
│  │                                                   │  │
│  └───────────────────────────────────────────────────┘  │
│                                                          │
└──────────────┬───────────────────────────────────────────┘
               │
               ▼ User enters credentials and clicks [Login]
┌──────────────────────────────────────────────────────────┐
│  7. USER LOGS IN                                         │
├──────────────────────────────────────────────────────────┤
│  ✓ Email verified: YES                                  │
│  ✓ Status: ACTIVE                                       │
│  ✓ Can access: /home and protected routes               │
│                                                          │
│  User is now FULLY REGISTERED AND ACTIVE                │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

## Database State Changes

```
┌──────────────────────────────────────────────────────────────┐
│            DATABASE STATE BEFORE VERIFICATION               │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  tbusers table (User Registration)                          │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ idno │ emailaddress      │ status   │ verification_.. │ │
│  ├──────┼──────────────────┼──────────┼────────────────┤ │
│  │ 1    │ john@example.com │ inactive │ Kv0EY8hhj5rK3.. │ │
│  │      │                  │          │                │ │
│  └────────────────────────────────────────────────────────┘ │
│  │ email_verified_at │ Other fields ...                     │
│  ├─────────────────┤──────────────────                      │
│  │ NULL            │ password, fname, lname, ...            │
│  │                 │                                        │
│  └────────────────────────────────────────────────────────┘ │
│                                                              │
│  Token in database: ✓ Present (64 characters)              │
│  Email verified: ✗ Not verified (NULL timestamp)           │
│  Status: INACTIVE (awaiting verification)                  │
│                                                              │
└──────────────────────────────────────────────────────────────┘

        ↓ User clicks verification link ↓

┌──────────────────────────────────────────────────────────────┐
│         DATABASE STATE AFTER VERIFICATION                    │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  tbusers table (After Email Verification)                   │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ idno │ emailaddress      │ status  │ verification_.. │ │
│  ├──────┼──────────────────┼─────────┼────────────────┤ │
│  │ 1    │ john@example.com │ active  │ NULL           │ │
│  │      │                  │ (✓)     │ (✓)            │ │
│  │      │                  │ CHANGED │ CLEARED        │ │
│  └────────────────────────────────────────────────────────┘ │
│  │ email_verified_at     │ Other fields ...                 │
│  ├──────────────────────┼──────────────────                 │
│  │ 2026-01-25 07:24:02  │ password, fname, lname, ...      │
│  │ (✓) SET              │                                   │
│  └────────────────────────────────────────────────────────┘ │
│                                                              │
│  Token in database: ✗ Cleared (set to NULL)                │
│  Email verified: ✓ Verified (timestamp: 2026-01-25 07:...) │
│  Status: ACTIVE (fully registered & verified)              │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

## Token Lifecycle

```
┌────────────────────────────────────────────────────────┐
│              VERIFICATION TOKEN LIFECYCLE              │
└────────────────────────────────────────────────────────┘

Step 1: GENERATION (At Registration)
├─ Method: User::generateVerificationToken()
├─ Generator: Str::random(64)
├─ Length: 64 characters
├─ Uniqueness: Random, unique per user
├─ Storage: verification_token column
└─ Status: ACTIVE (can be used)
   │
   ▼
Step 2: STORAGE (In Database)
├─ Column: verification_token
├─ Table: tbusers
├─ Value: 'Kv0EY8hhj5rK33JJuVoquTfW4dUjqBj7bHs3X1BUsz4...'
├─ Indexed: NO (for now, consider adding index for performance)
└─ Status: STORED (ready for verification)
   │
   ▼
Step 3: TRANSMISSION (In Email)
├─ Method: sendEmailVerificationNotification()
├─ Format: Embedded in URL
├─ URL: /email/verify/token/{token}
├─ Medium: Email (user's inbox)
├─ Security: Unique, hard to guess
└─ Status: SENT (waiting for user to click)
   │
   ▼
Step 4: VERIFICATION (User Clicks Link)
├─ Lookup: User::where('verification_token', $token)->first()
├─ Validation: Token found in database
├─ Email Match: Implicit (token is user-specific)
├─ Status Check: Ensure not already verified
└─ Status: VALIDATED (confirmed token is correct)
   │
   ▼
Step 5: CLEARANCE (After Verification)
├─ Action: Set verification_token = NULL
├─ Reason: One-time use, no longer needed
├─ Storage: NULL value in database
├─ Security: Can't be reused
└─ Status: CLEARED (no longer exists)

Timeline Example:
┌──────────────────────────────────────────────────────┐
│ 10:00 AM │ Token generated and stored in database   │
├──────────┼─────────────────────────────────────────┤
│ 10:02 AM │ Email sent with token link              │
├──────────┼─────────────────────────────────────────┤
│ 10:05 AM │ User receives and reads email           │
├──────────┼─────────────────────────────────────────┤
│ 10:15 AM │ User clicks verification link           │
├──────────┼─────────────────────────────────────────┤
│ 10:15 AM │ Server verifies token                   │
├──────────┼─────────────────────────────────────────┤
│ 10:15 AM │ Token cleared from database             │
├──────────┼─────────────────────────────────────────┤
│ 10:16 AM │ User can login (email verified)         │
└──────────┴─────────────────────────────────────────┘
```

## Security & Validation Checks

```
┌───────────────────────────────────────────────────────────┐
│         VERIFICATION SECURITY CHECKS                      │
└───────────────────────────────────────────────────────────┘

Request: GET /email/verify/token/{user_token}

┌─ Check 1: TOKEN EXISTENCE ────────────────┐
│ Question: Does token exist in database?   │
│ Query: WHERE verification_token = ?       │
│ Result: User found OR not found           │
│ If NOT found → Reject: "Invalid token"   │
└───────────────────────────────────────────┘
   │ ✓ Pass
   ▼
┌─ Check 2: EMAIL VERIFICATION STATUS ─────┐
│ Question: Already verified?               │
│ Check: if (!user->hasVerifiedEmail())     │
│ Condition: email_verified_at IS NULL      │
│ If verified → Info: "Already verified"   │
└───────────────────────────────────────────┘
   │ ✓ Pass (not yet verified)
   ▼
┌─ Check 3: PERFORM VERIFICATION ──────────┐
│ Action 1: Mark email verified             │
│   → Set email_verified_at = NOW()         │
│ Action 2: Set status to active            │
│   → Set status = 'active'                 │
│ Action 3: Clear verification token        │
│   → Set verification_token = NULL         │
│ Action 4: Save to database                │
│   → UPDATE tbusers SET ...                │
│ Result: All changes persisted             │
└───────────────────────────────────────────┘
   │ ✓ Success
   ▼
┌─ Check 4: PREVENT RE-VERIFICATION ───────┐
│ Question: Already verified before?        │
│ Next time user visits link:               │
│ Check: if (user->hasVerifiedEmail())      │
│ Condition: email_verified_at NOT NULL     │
│ Result: "Email already verified"         │
│ Action: Do NOT process again              │
└───────────────────────────────────────────┘
```

## Code Flow Map

```
Web Request: GET /email/verify/token/{token}
             ↓
Route Definition (routes/web.php)
   Route::get('/email/verify/token/{token}',
      [VerificationController::class, 'verifyWithToken'])
             ↓
VerificationController::verifyWithToken($token)
   ├─ Log attempt
   │  └─ Log::info('Email verification attempt', ['token' => $token])
   │
   ├─ Find user by token
   │  ├─ User::where('verification_token', $token)->first()
   │  └─ Handle if not found → Redirect with error
   │
   ├─ Check if already verified
   │  ├─ hasVerifiedEmail()
   │  └─ Handle if already verified → Redirect with info
   │
   ├─ Mark email verified
   │  └─ User::markEmailAsVerified()
   │     └─ Set email_verified_at = now()
   │        └─ Save to database
   │
   ├─ Set status to active
   │  └─ $user->status = 'active'
   │
   ├─ Clear verification token
   │  └─ $user->verification_token = null
   │
   ├─ Save changes to database
   │  └─ $user->save()
   │
   ├─ Log completion
   │  └─ Log::info('Email verification completed', [...])
   │
   └─ Redirect to login
      └─ redirect('/login')->with('success', '...')
             ↓
User sees: "Email verified successfully! You can now login."
User can: Login with their credentials
```

## Permission & Access Control

```
BEFORE VERIFICATION          AFTER VERIFICATION
┌─────────────────────────┐  ┌─────────────────────────┐
│ Status: inactive        │  │ Status: active          │
│ Email Verified: NO      │  │ Email Verified: YES     │
│ Can Login: NO (block)   │  │ Can Login: YES (allow)  │
│ Access /home: NO        │  │ Access /home: YES       │
│ Access /dashboard: NO   │  │ Access /dashboard: YES  │
│ Access protected routes │  │ Full access granted     │
│           : NO          │  │                         │
└─────────────────────────┘  └─────────────────────────┘

Middleware Check (in routes):
    Route::middleware(['auth', 'verified'])->get('/home', ...)
                                     ↑
                            Only verified users allowed
                            Check: email_verified_at != NULL
```

This complete visual guide shows how the email verification system works from start to finish!
