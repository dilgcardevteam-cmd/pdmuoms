<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

// use RegistersUsers; // Commented out to use custom register method

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'agency' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'office' => ['nullable', 'string', 'max:255'],
            'emailaddress' => ['required', 'string', 'email', 'max:255', 'unique:tbusers'],
            'mobileno' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:tbusers'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'fname' => $data['fname'],
            'lname' => $data['lname'],
            'agency' => $data['agency'],
            'position' => $data['position'],
            'region' => $data['region'],
            'province' => $data['province'],
            'office' => $data['office'] ?? null,
            'emailaddress' => $data['emailaddress'],
            'mobileno' => $data['mobileno'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? User::ROLE_LGU,
            'status' => $data['status'] ?? 'active',
            'access' => $data['access'] ?? 'limited',
        ]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application with AJAX support.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            $validator = $this->validator($request->all());
            
            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $user = $this->create($request->all());
            event(new Registered($user));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful! Please check your email for verification.',
                    'redirect' => route('login')
                ]);
            }

            return $this->registered($request, $user)
                ?: redirect(route('login'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
