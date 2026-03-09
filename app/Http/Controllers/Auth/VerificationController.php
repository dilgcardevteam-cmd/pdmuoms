<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('verifyWithToken');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the authenticated user's email as verified.
     * Also set their status to 'active'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request)
    {
        // Call parent verify method
        $response = parent::verify($request);

        // If verification was successful, set user status to active
        if ($request->user()->hasVerifiedEmail()) {
            $request->user()->update(['status' => 'active']);
        }

        return $response;    }

    /**
     * Verify email using token from database.
     * 
     * When user clicks the verification link in their email:
     * 1. System finds user by verification_token
     * 2. Email is verified by the token ownership (token is unique per user)
     * 3. User status is set to 'active'
     * 4. Verification token is cleared from database
     *
     * @param  string  $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyWithToken($token)
    {
        \Illuminate\Support\Facades\Log::warning('DEBUG: verifyWithToken method CALLED!', ['token' => substr($token, 0, 20)]);
        \Illuminate\Support\Facades\Log::info('Email verification attempt', ['token' => $token]);

        // Find user by verification token
        // The token is unique and tied to one user's email address
        $user = \App\Models\User::where('verification_token', $token)->first();

        // Token not found means invalid token (no user registered with this token)
        if (!$user) {
            \Illuminate\Support\Facades\Log::warning('Invalid verification token', ['token' => $token]);
            return redirect('/login')->with('error', 'Invalid verification token.');
        }

        \Illuminate\Support\Facades\Log::info('User found for verification', [
            'user_id' => $user->idno,
            'email' => $user->emailaddress,
            'current_status' => $user->status,
            'has_verified_email' => $user->hasVerifiedEmail()
        ]);

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            \Illuminate\Support\Facades\Log::info('Email already verified', ['user_id' => $user->idno]);
            return redirect('/login')->with('info', 'Email already verified.');
        }

        // Perform verification:
        // 1. Mark email as verified with timestamp
        $user->markEmailAsVerified();
        
        // 2. Set user status to 'active'
        $user->status = 'active';
        
        // 3. Clear verification token (now that verification is complete)
        $user->verification_token = null;
        
        // 4. Save all changes to database
        $saveResult = $user->save();

        \Illuminate\Support\Facades\Log::info('Email verification completed', [
            'user_id' => $user->idno,
            'email' => $user->emailaddress,
            'new_status' => $user->status,
            'email_verified_at' => $user->email_verified_at,
            'verification_token_cleared' => is_null($user->verification_token),
            'save_result' => $saveResult
        ]);

        return redirect('/login')->with('success', 'Email verified successfully! You can now login.');
    }
}
