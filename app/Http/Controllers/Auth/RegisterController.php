<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'office' => [
                Rule::requiredIf(static fn () => ($data['agency'] ?? null) === 'LGU'),
                'nullable',
                'string',
                'max:255',
            ],
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
        return view('auth.register', [
            'locationOptions' => $this->registrationLocationOptions(),
        ]);
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

    private function registrationLocationOptions(): array
    {
        $default = [
            'regions' => [],
            'provincesByRegion' => [],
            'officesByProvince' => [],
        ];

        if (
            !Schema::hasTable('location_regions')
            || !Schema::hasTable('location_provinces')
            || !Schema::hasTable('location_city_municipalities')
        ) {
            return $default;
        }

        $regionRows = DB::table('location_regions')
            ->select('id', 'region_code', 'region_name')
            ->whereNotNull('region_name')
            ->orderBy('region_code')
            ->orderBy('region_name')
            ->get();

        if ($regionRows->isEmpty()) {
            return $default;
        }

        $regions = [];
        $currentRegionIds = [];
        $regionIdToName = [];

        foreach ($regionRows as $regionRow) {
            $regionId = (int) ($regionRow->id ?? 0);
            $regionName = trim((string) ($regionRow->region_name ?? ''));
            if ($regionId < 1 || $regionName === '') {
                continue;
            }

            $regions[] = $regionName;
            $currentRegionIds[] = $regionId;
            $regionIdToName[$regionId] = $regionName;
        }

        sort($currentRegionIds);
        $regions = array_values(array_unique($regions));

        $provinceRows = DB::table('location_provinces')
            ->select('id', 'region_id', 'province_name')
            ->whereNotNull('province_name')
            ->orderBy('province_name')
            ->get();

        $currentProvinceIds = [];
        $provinceIdToName = [];
        $provincesByRegion = [];

        foreach ($provinceRows as $provinceRow) {
            $provinceId = (int) ($provinceRow->id ?? 0);
            $provinceName = $this->formatLocationName((string) ($provinceRow->province_name ?? ''));
            if ($provinceId < 1 || $provinceName === '') {
                continue;
            }

            $currentProvinceIds[] = $provinceId;
            $provinceIdToName[$provinceId] = $provinceName;
        }

        sort($currentProvinceIds);

        foreach ($provinceRows as $provinceRow) {
            $provinceName = $this->formatLocationName((string) ($provinceRow->province_name ?? ''));
            if ($provinceName === '') {
                continue;
            }

            $resolvedRegionId = $this->resolveHistoricalLocationId(
                (int) ($provinceRow->region_id ?? 0),
                $currentRegionIds
            );

            if ($resolvedRegionId === null || !isset($regionIdToName[$resolvedRegionId])) {
                continue;
            }

            $regionName = $regionIdToName[$resolvedRegionId];
            $provincesByRegion[$regionName] ??= [];
            $provincesByRegion[$regionName][$provinceName] = $provinceName;
        }

        foreach ($provincesByRegion as $regionName => $provinceNames) {
            $items = array_values($provinceNames);
            sort($items, SORT_NATURAL | SORT_FLAG_CASE);
            $provincesByRegion[$regionName] = $items;
        }

        $cityRows = DB::table('location_city_municipalities')
            ->select('province_id', 'citymun_name')
            ->whereNotNull('citymun_name')
            ->orderBy('citymun_name')
            ->get();

        $officesByProvince = [];

        foreach ($cityRows as $cityRow) {
            $cityName = $this->formatLocationName((string) ($cityRow->citymun_name ?? ''));
            if ($cityName === '') {
                continue;
            }

            $resolvedProvinceId = $this->resolveHistoricalLocationId(
                (int) ($cityRow->province_id ?? 0),
                $currentProvinceIds
            );

            if ($resolvedProvinceId === null || !isset($provinceIdToName[$resolvedProvinceId])) {
                continue;
            }

            $provinceName = $provinceIdToName[$resolvedProvinceId];
            $officesByProvince[$provinceName] ??= [];
            $officesByProvince[$provinceName][$cityName] = $cityName;
        }

        foreach ($officesByProvince as $provinceName => $officeNames) {
            $items = array_values($officeNames);
            sort($items, SORT_NATURAL | SORT_FLAG_CASE);
            $officesByProvince[$provinceName] = $items;
        }

        return [
            'regions' => $regions,
            'provincesByRegion' => $provincesByRegion,
            'officesByProvince' => $officesByProvince,
        ];
    }

    private function resolveHistoricalLocationId(int $candidateId, array $currentIds): ?int
    {
        if ($candidateId < 1 || $currentIds === []) {
            return null;
        }

        sort($currentIds);

        if (in_array($candidateId, $currentIds, true)) {
            return $candidateId;
        }

        $currentBlockStart = $currentIds[0];
        $blockSize = count($currentIds);
        if ($candidateId >= $currentBlockStart) {
            return null;
        }

        $blocksBack = (int) ceil(($currentBlockStart - $candidateId) / $blockSize);
        $historicalBlockStart = $currentBlockStart - ($blocksBack * $blockSize);
        $position = $candidateId - $historicalBlockStart;

        if ($position < 0 || $position >= $blockSize) {
            return null;
        }

        return $currentIds[$position] ?? null;
    }

    private function formatLocationName(string $value): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? $value);
        if ($value === '') {
            return '';
        }

        $formatted = Str::title(Str::lower($value));

        return preg_replace_callback(
            '/\b(Of|De|Del|And|In|Ng)\b/',
            static fn (array $matches): string => Str::lower($matches[0]),
            $formatted
        ) ?? $formatted;
    }
}
