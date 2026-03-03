<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';
    protected int $maxLoginAttempts = 5;
    protected int $loginDecaySeconds = 900;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Handle an authentication attempt with explicit rate limiting.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);
        $lockoutResponse = $this->ensureIsNotRateLimited($request);
        if ($lockoutResponse) {
            return $lockoutResponse;
        }

        if ($this->attemptLogin($request)) {
            RateLimiter::clear($this->throttleKey($request));
            $request->session()->regenerate();

            return $this->authenticated($request, Auth::user())
                ?: redirect()->intended($this->redirectPath());
        }

        RateLimiter::hit($this->throttleKey($request), $this->decaySeconds());

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        // Get credentials
        $username = $request->input('username');
        $password = $request->input('password');
        
        // Find user by username
        $user = User::where('username', $username)->first();
        
        // Check all conditions
        if (!$user) {
            return false;
        }
        
        if (strtolower($user->status) !== 'active') {
            return false;
        }
        
        if (!Hash::check($password, $user->password)) {
            return false;
        }
        
        // All checks passed - login the user
        Auth::login($user, $request->filled('remember'));
        return true;
    }

    /**
     * Validate incoming login request.
     */
    protected function validateLogin(Request $request): void
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);
    }

    /**
     * Block requests when too many failed attempts were recorded.
     */
    protected function ensureIsNotRateLimited(Request $request): ?RedirectResponse
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), $this->maxAttempts())) {
            return null;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));
        $minutes = (int) ceil($seconds / 60);
        $unit = $minutes === 1 ? 'minute' : 'minutes';

        return redirect('/login')->withErrors([
            'login_error' => 'Too many login attempts. Please try again in '.$minutes.' '.$unit.'.',
        ])->withInput($request->only('username', 'remember'))
          ->with('lockout_seconds', $seconds);
    }

    /**
     * Build a unique throttle key using username and client IP.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower((string) $request->input('username')).'|'.$request->ip();
    }

    protected function maxAttempts(): int
    {
        return $this->maxLoginAttempts;
    }

    protected function decaySeconds(): int
    {
        return $this->loginDecaySeconds;
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect('/dashboard');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where('username', $request->input('username'))->first();
        
        if ($user && strtolower($user->status) !== 'active') {
            return redirect('/login')->withErrors([
                'login_error' => 'Your account is inactive. Please contact an administrator.',
            ])->withInput($request->only('username', 'remember'));
        }
        
        return redirect('/login')->withErrors([
            'login_error' => 'The username or password is incorrect.',
        ])->withInput($request->only('username', 'remember'));
    }
}
