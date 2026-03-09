<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LocallyFundedProject;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\SystemManagementController;

Auth::routes(['reset' => false, 'register' => false]); // Disable default register routes

// Custom register routes
Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Email verification routes - Token route FIRST (more specific)
Route::get('/email/verify/token/{token}', [App\Http\Controllers\Auth\VerificationController::class, 'verifyWithToken'])->name('verification.verify.token');
// Then general verification routes
Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
Route::post('/email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->middleware(['throttle:6,1'])->name('verification.resend');

Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('forgot-password');
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendOtp'])->name('forgot-password.send-otp');
Route::get('/verify-otp', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showVerifyOtpForm'])->name('forgot-password.verify');
Route::post('/verify-otp', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyOtp'])->name('forgot-password.verify-otp');
Route::get('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('forgot-password.reset');
Route::post('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])->name('forgot-password.reset-submit');

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Public API endpoint for municipality projects
Route::get('/api/municipality-projects', function () {
    try {
        if (!Schema::hasTable('subay_project_profiles')) {
            return response()->json(['features' => [], 'maxCount' => 0]);
        }

        $municipalityCoords = [
            // Abra
            'Bangued' => ['lat' => 17.0667, 'lng' => 120.6167],
            'Boliney' => ['lat' => 17.0833, 'lng' => 120.65],
            'Bucay' => ['lat' => 16.85, 'lng' => 120.6],
            'Daguioman' => ['lat' => 16.9, 'lng' => 120.7167],
            'Danglas' => ['lat' => 16.9667, 'lng' => 120.8],
            'Dolores' => ['lat' => 17.1667, 'lng' => 120.75],
            'La Paz' => ['lat' => 17.0, 'lng' => 120.5],
            'Lacub' => ['lat' => 17.05, 'lng' => 120.8],
            'Lagangilang' => ['lat' => 16.8667, 'lng' => 120.75],
            'Lagayan' => ['lat' => 16.9167, 'lng' => 120.65],
            'Langiden' => ['lat' => 17.0833, 'lng' => 120.9],
            'Licuan-Baay' => ['lat' => 17.0, 'lng' => 120.85],
            'Malibcong' => ['lat' => 16.95, 'lng' => 120.6333],
            'Manabo' => ['lat' => 16.8833, 'lng' => 120.65],
            'Peñarrubia' => ['lat' => 16.9333, 'lng' => 120.5333],
            'Pidcal' => ['lat' => 17.0167, 'lng' => 120.7333],
            'Pilar' => ['lat' => 16.9667, 'lng' => 120.5667],
            'Sallapadan' => ['lat' => 16.95, 'lng' => 120.85],
            'San Isidro' => ['lat' => 17.0667, 'lng' => 120.6833],
            'San Juan' => ['lat' => 17.1333, 'lng' => 120.7833],
            'San Quintin' => ['lat' => 17.1167, 'lng' => 120.6667],
            'Tayum' => ['lat' => 17.1083, 'lng' => 120.8333],
            // Apayao
            'Calanasan' => ['lat' => 17.95, 'lng' => 121.1667],
            'Conner' => ['lat' => 17.75, 'lng' => 121.05],
            'Flora' => ['lat' => 17.95, 'lng' => 121.0],
            'Kabugao' => ['lat' => 17.8333, 'lng' => 120.95],
            'Pudtol' => ['lat' => 17.85, 'lng' => 121.1667],
            'Santa Marcela' => ['lat' => 18.0333, 'lng' => 121.2167],
            // Benguet
            'Atok' => ['lat' => 16.4167, 'lng' => 120.6],
            'Bakun' => ['lat' => 16.35, 'lng' => 120.9],
            'Bokod' => ['lat' => 16.3833, 'lng' => 120.8167],
            'Buguias' => ['lat' => 16.5167, 'lng' => 120.8],
            'City Of Baguio' => ['lat' => 16.408, 'lng' => 120.594],
            'Itogon' => ['lat' => 16.4833, 'lng' => 120.85],
            'Kabayan' => ['lat' => 16.3833, 'lng' => 120.7],
            'Kapangan' => ['lat' => 16.45, 'lng' => 120.7667],
            'Kibungan' => ['lat' => 16.4833, 'lng' => 120.7667],
            'La Trinidad' => ['lat' => 16.3667, 'lng' => 120.55],
            'Mankayan' => ['lat' => 16.5333, 'lng' => 120.65],
            'Sablan' => ['lat' => 16.35, 'lng' => 120.65],
            'Tuba' => ['lat' => 16.3333, 'lng' => 120.5333],
            'Tublay' => ['lat' => 16.3667, 'lng' => 120.6667],
            'Tubo' => ['lat' => 16.4, 'lng' => 120.6],
            // Ifugao
            'Aguinaldo' => ['lat' => 16.95, 'lng' => 121.3],
            'Alfonso Lista' => ['lat' => 17.0333, 'lng' => 121.3833],
            'Asipulo' => ['lat' => 16.85, 'lng' => 121.2667],
            'Banaue' => ['lat' => 16.95, 'lng' => 121.1667],
            'Hingyon' => ['lat' => 16.8667, 'lng' => 121.3333],
            'Hungduan' => ['lat' => 16.8333, 'lng' => 121.2],
            'Kiangan' => ['lat' => 16.9833, 'lng' => 121.2667],
            'Lagawe' => ['lat' => 16.9333, 'lng' => 121.2167],
            'Mayoyao' => ['lat' => 16.85, 'lng' => 121.45],
            'Tinoc' => ['lat' => 16.9, 'lng' => 121.4167],
            // Kalinga
            'Balbalan' => ['lat' => 17.4333, 'lng' => 121.45],
            'City Of Tabuk' => ['lat' => 17.3667, 'lng' => 121.4167],
            'Dagupagsan' => ['lat' => 17.35, 'lng' => 121.3833],
            'Lubuagan' => ['lat' => 17.3833, 'lng' => 121.3167],
            'Luna' => ['lat' => 17.2, 'lng' => 121.2167],
            'Mabunguran' => ['lat' => 17.3667, 'lng' => 121.5167],
            'Pasil' => ['lat' => 17.2333, 'lng' => 121.3667],
            'Pinukpuk' => ['lat' => 17.2667, 'lng' => 121.45],
            'Rizal' => ['lat' => 17.2833, 'lng' => 121.2333],
            'Tanudan' => ['lat' => 17.2333, 'lng' => 121.2667],
            'Tinglayan' => ['lat' => 17.2167, 'lng' => 121.5333],
            // Mountain Province
            'Amlang' => ['lat' => 16.65, 'lng' => 121.3333],
            'Amtan' => ['lat' => 16.7, 'lng' => 121.2667],
            'Barlig' => ['lat' => 16.75, 'lng' => 121.1667],
            'Bauko' => ['lat' => 16.8, 'lng' => 121.1833],
            'Besao' => ['lat' => 16.7167, 'lng' => 121.2667],
            'Bontoc' => ['lat' => 16.7667, 'lng' => 121.3],
            'Bucloc' => ['lat' => 16.6667, 'lng' => 121.4],
            'Cervantes' => ['lat' => 16.7667, 'lng' => 121.35],
            'Luba' => ['lat' => 16.8333, 'lng' => 121.25],
            'Natonin' => ['lat' => 16.6667, 'lng' => 121.2667],
            'Paracelis' => ['lat' => 16.7833, 'lng' => 121.4833],
            'Sabangan' => ['lat' => 16.8167, 'lng' => 121.35],
            'Sadanga' => ['lat' => 16.7, 'lng' => 121.3167],
            'Sagada' => ['lat' => 16.7333, 'lng' => 121.2167],
            'Tadian' => ['lat' => 16.7667, 'lng' => 121.3167],
            'Tineg' => ['lat' => 16.7333, 'lng' => 121.35]
        ];

        // Get all municipalities with subay project counts
        $municipalityProjects = DB::table('subay_project_profiles')
            ->select(
                DB::raw('UPPER(TRIM(city_municipality)) as municipality'),
                DB::raw('count(*) as project_count')
            )
            ->whereNotNull('city_municipality')
            ->where('city_municipality', '<>', '')
            ->groupBy(DB::raw('UPPER(TRIM(city_municipality))'))
            ->get();

        $maxCount = 0;
        $features = [];
        
        // Create a lowercase version of coords for matching
        $coordsLowercase = [];
        foreach ($municipalityCoords as $name => $coord) {
            $coordsLowercase[strtolower($name)] = ['name' => $name, 'coord' => $coord];
        }

        foreach ($municipalityProjects as $row) {
            $municipality = $row->municipality;
            // Remove anything in parentheses and lowercase for matching
            $municipalityKey = strtolower(trim(preg_replace('/\s*\([^)]*\)\s*/', '', $municipality)));
            $count = $row->project_count;
            $maxCount = max($maxCount, $count);

            if (isset($coordsLowercase[$municipalityKey])) {
                $matchedData = $coordsLowercase[$municipalityKey];
                $coords = $matchedData['coord'];
                $displayName = $matchedData['name'];
                $features[] = [
                    'type' => 'Feature',
                    'properties' => [
                        'name' => $displayName,
                        'project_count' => $count
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$coords['lng'], $coords['lat']]
                    ]
                ];
            }
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
            'maxCount' => $maxCount
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('api.municipality-projects');

Route::middleware(['auth'])->group(function () {
    // PAGASA time endpoint for live clock display
    Route::get('/api/pagasa-time/current', [App\Http\Controllers\PagasaTimeController::class, 'current'])->name('pagasa-time.current');

    Route::get('/notifications/{id}/read', function ($id) {
        $notification = \Illuminate\Support\Facades\DB::table('tbnotifications')
            ->where('id', $id)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->first();

        if (!$notification) {
            return redirect()->back();
        }

        \Illuminate\Support\Facades\DB::table('tbnotifications')
            ->where('id', $id)
            ->update(['read_at' => now(), 'updated_at' => now()]);

        return redirect($notification->url ?: route('fund-utilization.index'));
    })->name('notifications.read');

    Route::post('/notifications/clear', function () {
        \Illuminate\Support\Facades\DB::table('tbnotifications')
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereNotNull('read_at')
            ->delete();

        return redirect()->back();
    })->name('notifications.clear');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/dashboard', function () {
        try {
            $subayUploadDateLabel = 'No SubayBAYAN upload yet';
            if (Schema::hasTable('subay_project_profiles') && Schema::hasColumn('subay_project_profiles', 'created_at')) {
                $latestSubayUploadAt = DB::table('subay_project_profiles')->max('created_at');
                if ($latestSubayUploadAt) {
                    try {
                        $subayUploadDateLabel = \Illuminate\Support\Carbon::parse($latestSubayUploadAt)->format('F d, Y h:i A');
                    } catch (\Throwable $error) {
                        $subayUploadDateLabel = (string) $latestSubayUploadAt;
                    }
                }
            }

            $user = Auth::user();
            $agency = strtoupper(trim((string) $user->agency));
            $province = trim((string) $user->province);
            $office = trim((string) $user->office);
            $region = trim((string) $user->region);
            $provinceLower = strtolower($province);
            $officeLower = strtolower($office);
            $regionLower = strtolower($region);
            $officeBaseLower = trim((string) preg_replace('/,.*$/', '', $officeLower));
            $officeComparableLower = trim((string) preg_replace('/^(municipality|city)\s+of\s+/i', '', $officeBaseLower));
            $isRegionalOfficeUser = $agency === 'DILG'
                && (
                    str_contains($provinceLower, 'regional office')
                    || str_contains($officeLower, 'regional office')
                );

            $requestedPrograms = request()->input('program', []);
            if (!is_array($requestedPrograms)) {
                $requestedPrograms = $requestedPrograms === null ? [] : [$requestedPrograms];
            }
            $selectedPrograms = collect($requestedPrograms)
                ->map(function ($value) {
                    return trim((string) $value);
                })
                ->filter(function ($value) {
                    return $value !== '';
                })
                ->unique()
                ->values()
                ->all();

            $filters = [
                'province' => trim((string) request('province', '')),
                'city_municipality' => trim((string) request('city_municipality', '')),
                'barangay' => trim((string) request('barangay', '')),
                'programs' => $selectedPrograms,
                'funding_year' => trim((string) request('funding_year', '')),
                'project_type' => trim((string) request('project_type', '')),
                'project_status' => trim((string) request('project_status', '')),
            ];

            $filterOptions = [
                'provinces' => collect(),
                'cities' => collect(),
                'barangays' => collect(),
                'programs' => collect(),
                'funding_years' => collect(),
                'project_types' => collect(),
                'project_statuses' => collect(),
            ];

            $subayCityComparableExpression = "TRIM(REPLACE(REPLACE(LOWER(SUBSTRING_INDEX(COALESCE(spp.city_municipality, ''), ',', 1)), 'municipality of ', ''), 'city of ', ''))";
            $applyOfficeScopeToSubay = function ($query) use ($officeLower, $officeComparableLower, $subayCityComparableExpression) {
                if ($officeLower === '') {
                    return;
                }

                $officeNeedle = $officeComparableLower !== '' ? $officeComparableLower : $officeLower;

                $query->where(function ($subQuery) use ($officeLower, $officeNeedle, $subayCityComparableExpression) {
                    $subQuery->whereRaw('LOWER(TRIM(COALESCE(spp.city_municipality, ""))) = ?', [$officeLower])
                        ->orWhereRaw("{$subayCityComparableExpression} = ?", [$officeNeedle]);
                });
            };

            $applyRoleScopeToSubay = function ($query) use (
                $agency,
                $province,
                $office,
                $region,
                $provinceLower,
                $regionLower,
                $isRegionalOfficeUser,
                $applyOfficeScopeToSubay
            ) {
                if ($agency === 'LGU') {
                    if ($office !== '') {
                        if ($province !== '') {
                            $query->whereRaw('LOWER(TRIM(COALESCE(spp.province, ""))) = ?', [$provinceLower]);
                            $applyOfficeScopeToSubay($query);
                        } else {
                            $applyOfficeScopeToSubay($query);
                        }
                    } elseif ($province !== '') {
                        $query->whereRaw('LOWER(TRIM(COALESCE(spp.province, ""))) = ?', [$provinceLower]);
                    }
                } elseif ($agency === 'DILG') {
                    if ($isRegionalOfficeUser) {
                        // Regional Office users can see all projects.
                    } elseif ($province !== '') {
                        $query->whereRaw('LOWER(TRIM(COALESCE(spp.province, ""))) = ?', [$provinceLower]);
                    } elseif ($region !== '') {
                        $query->whereRaw('LOWER(TRIM(COALESCE(spp.region, ""))) = ?', [$regionLower]);
                    }
                }
            };

            $applyExactFilterToSubay = function ($query, string $column, string $value) {
                $normalized = trim($value);
                if ($normalized === '') {
                    return;
                }

                $query->whereRaw("LOWER(TRIM(COALESCE({$column}, ''))) = ?", [strtolower($normalized)]);
            };

            $applyExactMultiFilterToSubay = function ($query, string $column, array $values) {
                $normalizedValues = collect($values)
                    ->map(function ($value) {
                        return strtolower(trim((string) $value));
                    })
                    ->filter(function ($value) {
                        return $value !== '';
                    })
                    ->unique()
                    ->values()
                    ->all();

                if (empty($normalizedValues)) {
                    return;
                }

                $placeholders = implode(', ', array_fill(0, count($normalizedValues), '?'));
                $query->whereRaw("LOWER(TRIM(COALESCE({$column}, ''))) IN ({$placeholders})", $normalizedValues);
            };

            $applyDashboardFiltersToSubay = function ($query) use ($filters, $applyExactFilterToSubay, $applyExactMultiFilterToSubay) {
                $applyExactFilterToSubay($query, 'spp.province', $filters['province']);
                $applyExactFilterToSubay($query, 'spp.city_municipality', $filters['city_municipality']);
                $applyExactFilterToSubay($query, 'spp.barangay', $filters['barangay']);
                $applyExactMultiFilterToSubay($query, 'spp.program', $filters['programs']);
                $applyExactFilterToSubay($query, 'spp.funding_year', $filters['funding_year']);
                $applyExactFilterToSubay($query, 'spp.type_of_project', $filters['project_type']);
                $applyExactFilterToSubay($query, 'spp.status', $filters['project_status']);
            };

            $totalProjects = 0;
            $fundSourceOptions = ['SBDP', 'FALGU', 'CMGP', 'GEF', 'SAFPB'];
            $fundSourceCountsMap = [];
            $fundSourceProjectsMap = [];
            $totalObligationAmount = 0.0;
            $totalDisbursementAmount = 0.0;
            $totalBalanceAmount = 0.0;
            $totalLgsfAllocationAmount = 0.0;
            $projectsWithBalance = collect();
            $financialStatusProjects = collect();
            $utilizationPercentage = 0.0;
            $expectedCompletionMonthLabel = now()->format('F Y');
            $currentYear = now()->year;
            $currentMonth = now()->month;
            $currentMonthStart = now()->copy()->startOfMonth()->toDateString();
            $currentMonthEnd = now()->copy()->endOfMonth()->toDateString();
            $projectsExpectedCompletionThisMonth = collect();
            $projectAtRiskOrder = ['Ahead', 'No Risk', 'On Schedule', 'High Risk', 'Moderate Risk', 'Low Risk'];
            $projectAtRiskAgingOrder = ['High Risk', 'Low Risk', 'No Risk'];
            $projectUpdateStatusOrder = ['High Risk', 'Low Risk', 'No Risk'];
            $projectAtRiskCounts = array_fill_keys($projectAtRiskOrder, 0);
            $projectAtRiskAgingCounts = array_fill_keys($projectAtRiskAgingOrder, 0);
            $projectUpdateStatusCounts = array_fill_keys($projectUpdateStatusOrder, 0);
            $projectUpdateRiskProjects = [
                'High Risk' => collect(),
                'Low Risk' => collect(),
                'No Risk' => collect(),
            ];
            $projectAtRiskAgingProjects = [
                'High Risk' => collect(),
                'Low Risk' => collect(),
                'No Risk' => collect(),
            ];

            $statusLabels = [
                'COMPLETED' => 'Completed',
                'ONGOING' => 'On-going',
                'BID EVALUATION/OPENING' => 'Bid Evaluation/Opening',
                'NOA ISSUANCE' => 'NOA Issuance',
                'DED PREPARATION' => 'DED Preparation',
                'NOT YET STARTED' => 'Not Yet Started',
                'ITB/AD POSTED' => 'ITB/AD Posted',
                'TERMINATED' => 'Terminated',
                'CANCELLED' => 'Cancelled',
            ];
            $statusAliases = [
                'ON-GOING' => 'ONGOING',
                'NOT STARTED' => 'NOT YET STARTED',
            ];

            $normalizeStatus = function ($status) use ($statusLabels, $statusAliases) {
                $raw = trim((string) $status);
                if ($raw === '') {
                    return null;
                }

                $upper = strtoupper($raw);
                if (array_key_exists($upper, $statusAliases)) {
                    $upper = $statusAliases[$upper];
                }
                if (array_key_exists($upper, $statusLabels)) {
                    return $upper;
                }

                return null;
            };

            $labelForStatus = function ($normalized) use ($statusLabels) {
                if ($normalized && array_key_exists($normalized, $statusLabels)) {
                    return $statusLabels[$normalized];
                }
                return null;
            };

            $statusDisplayOrder = array_values($statusLabels);
            $statusActualCounts = array_fill_keys($statusDisplayOrder, 0);
            $statusSubaybayanCounts = array_fill_keys($statusDisplayOrder, 0);
            $statusSubaybayanProjectsMap = [];
            $statusSubaybayanLocationReport = [];
            foreach ($statusDisplayOrder as $statusLabel) {
                $statusSubaybayanProjectsMap[$statusLabel] = [];
            }

            $carProvinceDisplayOrder = [
                'Abra',
                'Apayao',
                'Benguet',
                'Ifugao',
                'Kalinga',
                'Mountain Province',
            ];
            $carProvinceProjectCounts = array_fill_keys($carProvinceDisplayOrder, 0);
            $carProvinceProjectMaxCount = 0;

            $normalizeCarProvince = function ($province) {
                $raw = trim((string) $province);
                if ($raw === '') {
                    return null;
                }

                $compact = preg_replace('/[^A-Z]/', '', strtoupper($raw)) ?? '';
                if ($compact === '') {
                    return null;
                }

                $provinceAliases = [
                    'ABRA' => 'Abra',
                    'APAYAO' => 'Apayao',
                    'BENGUET' => 'Benguet',
                    'IFUGAO' => 'Ifugao',
                    'KALINGA' => 'Kalinga',
                    'MOUNTAINPROVINCE' => 'Mountain Province',
                    'MOUNTAINPROV' => 'Mountain Province',
                    'MTPROVINCE' => 'Mountain Province',
                ];

                if (array_key_exists($compact, $provinceAliases)) {
                    return $provinceAliases[$compact];
                }

                return null;
            };

            $normalizeRiskLevel = function ($riskLevel) {
                $raw = strtoupper(trim((string) $riskLevel));
                if ($raw === '') {
                    return null;
                }

                $compact = preg_replace('/[^A-Z]/', '', $raw) ?? '';
                if ($compact === '') {
                    return null;
                }

                if (str_contains($compact, 'AHEAD')) {
                    return 'Ahead';
                }
                if (str_contains($compact, 'ONSCHEDULE')) {
                    return 'On Schedule';
                }
                if (str_contains($compact, 'NORISK')) {
                    return 'No Risk';
                }
                if (str_contains($compact, 'HIGHRISK')) {
                    return 'High Risk';
                }
                if (str_contains($compact, 'MODERATERISK')) {
                    return 'Moderate Risk';
                }
                if (str_contains($compact, 'LOWRISK')) {
                    return 'Low Risk';
                }

                return null;
            };

            $computeProjectAtRiskCounts = function ($projectCodesQuery, string $riskColumn, array &$targetCounts) use ($normalizeRiskLevel) {
                if (!Schema::hasTable('project_at_risks')) {
                    return;
                }

                if (!in_array($riskColumn, ['risk_level'], true)) {
                    return;
                }

                $riskBaseQuery = DB::table('project_at_risks as par')
                    ->joinSub($projectCodesQuery, 'filtered_codes', function ($join) {
                        $join->on(DB::raw('UPPER(TRIM(par.project_code))'), '=', 'filtered_codes.project_code');
                    })
                    ->selectRaw('UPPER(TRIM(par.project_code)) as project_code')
                    ->selectRaw("TRIM(COALESCE(par.{$riskColumn}, \"\")) as risk_level_value")
                    ->selectRaw("COALESCE(par.date_of_extraction, '1900-01-01') as extraction_date")
                    ->addSelect('par.id')
                    ->whereNotNull('par.project_code')
                    ->whereRaw('TRIM(par.project_code) <> ""');

                $latestExtractionByProject = DB::query()
                    ->fromSub($riskBaseQuery, 'risk_base')
                    ->selectRaw('risk_base.project_code')
                    ->selectRaw('MAX(risk_base.extraction_date) as latest_extraction')
                    ->groupBy('risk_base.project_code');

                $latestRowsByExtraction = DB::query()
                    ->fromSub($riskBaseQuery, 'risk_base')
                    ->joinSub($latestExtractionByProject, 'risk_latest', function ($join) {
                        $join->on('risk_base.project_code', '=', 'risk_latest.project_code')
                            ->on('risk_base.extraction_date', '=', 'risk_latest.latest_extraction');
                    })
                    ->select('risk_base.project_code', 'risk_base.id', 'risk_base.risk_level_value');

                $latestIdByProject = DB::query()
                    ->fromSub($latestRowsByExtraction, 'risk_rows')
                    ->selectRaw('risk_rows.project_code')
                    ->selectRaw('MAX(risk_rows.id) as latest_id')
                    ->groupBy('risk_rows.project_code');

                $finalRiskRows = DB::query()
                    ->fromSub($riskBaseQuery, 'risk_base')
                    ->joinSub($latestIdByProject, 'risk_latest_id', function ($join) {
                        $join->on('risk_base.project_code', '=', 'risk_latest_id.project_code')
                            ->on('risk_base.id', '=', 'risk_latest_id.latest_id');
                    })
                    ->select('risk_base.risk_level_value')
                    ->get();

                foreach ($finalRiskRows as $row) {
                    $riskLabel = $normalizeRiskLevel($row->risk_level_value ?? null);
                    if ($riskLabel !== null && array_key_exists($riskLabel, $targetCounts)) {
                        $targetCounts[$riskLabel] += 1;
                    }
                }
            };

            $computeProjectAtRiskAgingCounts = function ($projectCodesQuery, array &$targetCounts) {
                if (!Schema::hasTable('project_at_risks')) {
                    return;
                }

                $riskBaseQuery = DB::table('project_at_risks as par')
                    ->joinSub($projectCodesQuery, 'filtered_codes', function ($join) {
                        $join->on(DB::raw('UPPER(TRIM(par.project_code))'), '=', 'filtered_codes.project_code');
                    })
                    ->selectRaw('UPPER(TRIM(par.project_code)) as project_code')
                    ->selectRaw('TRIM(COALESCE(par.aging, "")) as aging_value')
                    ->selectRaw("COALESCE(par.date_of_extraction, '1900-01-01') as extraction_date")
                    ->addSelect('par.id')
                    ->whereNotNull('par.project_code')
                    ->whereRaw('TRIM(par.project_code) <> ""');

                $latestExtractionByProject = DB::query()
                    ->fromSub($riskBaseQuery, 'risk_base')
                    ->selectRaw('risk_base.project_code')
                    ->selectRaw('MAX(risk_base.extraction_date) as latest_extraction')
                    ->groupBy('risk_base.project_code');

                $latestRowsByExtraction = DB::query()
                    ->fromSub($riskBaseQuery, 'risk_base')
                    ->joinSub($latestExtractionByProject, 'risk_latest', function ($join) {
                        $join->on('risk_base.project_code', '=', 'risk_latest.project_code')
                            ->on('risk_base.extraction_date', '=', 'risk_latest.latest_extraction');
                    })
                    ->select('risk_base.project_code', 'risk_base.id', 'risk_base.aging_value');

                $latestIdByProject = DB::query()
                    ->fromSub($latestRowsByExtraction, 'risk_rows')
                    ->selectRaw('risk_rows.project_code')
                    ->selectRaw('MAX(risk_rows.id) as latest_id')
                    ->groupBy('risk_rows.project_code');

                $finalAgingRows = DB::query()
                    ->fromSub($riskBaseQuery, 'risk_base')
                    ->joinSub($latestIdByProject, 'risk_latest_id', function ($join) {
                        $join->on('risk_base.project_code', '=', 'risk_latest_id.project_code')
                            ->on('risk_base.id', '=', 'risk_latest_id.latest_id');
                    })
                    ->select('risk_base.aging_value')
                    ->get();

                foreach ($finalAgingRows as $row) {
                    $rawAging = trim((string) ($row->aging_value ?? ''));
                    if ($rawAging === '') {
                        continue;
                    }

                    if (is_numeric($rawAging)) {
                        $agingValue = (float) $rawAging;
                    } else {
                        $cleanedAging = preg_replace('/[^0-9\.\-]/', '', $rawAging);
                        if ($cleanedAging === null || $cleanedAging === '' || !is_numeric($cleanedAging)) {
                            continue;
                        }
                        $agingValue = (float) $cleanedAging;
                    }

                    if ($agingValue >= 60) {
                        $riskLabel = 'High Risk';
                    } elseif ($agingValue > 30 && $agingValue < 60) {
                        $riskLabel = 'Low Risk';
                    } else {
                        $riskLabel = 'No Risk';
                    }

                    if (array_key_exists($riskLabel, $targetCounts)) {
                        $targetCounts[$riskLabel] += 1;
                    }
                }
            };

            $fetchProjectAtRiskAgingProjects = function ($projectCodesQuery) {
                $projectsByRisk = [
                    'High Risk' => collect(),
                    'Low Risk' => collect(),
                    'No Risk' => collect(),
                ];

                if (!Schema::hasTable('project_at_risks')) {
                    return $projectsByRisk;
                }

                $riskBaseQuery = DB::table('project_at_risks as par')
                    ->joinSub($projectCodesQuery, 'filtered_codes', function ($join) {
                        $join->on(DB::raw('UPPER(TRIM(par.project_code))'), '=', 'filtered_codes.project_code');
                    })
                    ->selectRaw('UPPER(TRIM(par.project_code)) as project_code')
                    ->selectRaw('TRIM(COALESCE(par.project_title, "")) as project_title')
                    ->selectRaw('TRIM(COALESCE(par.province, "")) as province')
                    ->selectRaw('TRIM(COALESCE(par.city_municipality, "")) as city_municipality')
                    ->selectRaw('TRIM(COALESCE(par.aging, "")) as aging_value')
                    ->selectRaw("COALESCE(par.date_of_extraction, '1900-01-01') as extraction_date")
                    ->addSelect('par.id')
                    ->whereNotNull('par.project_code')
                    ->whereRaw('TRIM(par.project_code) <> ""');

                $latestExtractionByProject = DB::query()
                    ->fromSub($riskBaseQuery, 'risk_base')
                    ->selectRaw('risk_base.project_code')
                    ->selectRaw('MAX(risk_base.extraction_date) as latest_extraction')
                    ->groupBy('risk_base.project_code');

                $latestRowsByExtraction = DB::query()
                    ->fromSub($riskBaseQuery, 'risk_base')
                    ->joinSub($latestExtractionByProject, 'risk_latest', function ($join) {
                        $join->on('risk_base.project_code', '=', 'risk_latest.project_code')
                            ->on('risk_base.extraction_date', '=', 'risk_latest.latest_extraction');
                    })
                    ->select(
                        'risk_base.project_code',
                        'risk_base.project_title',
                        'risk_base.province',
                        'risk_base.city_municipality',
                        'risk_base.aging_value',
                        'risk_base.extraction_date',
                        'risk_base.id'
                    );

                $latestIdByProject = DB::query()
                    ->fromSub($latestRowsByExtraction, 'risk_rows')
                    ->selectRaw('risk_rows.project_code')
                    ->selectRaw('MAX(risk_rows.id) as latest_id')
                    ->groupBy('risk_rows.project_code');

                $finalAgingRows = DB::query()
                    ->fromSub($riskBaseQuery, 'risk_base')
                    ->joinSub($latestIdByProject, 'risk_latest_id', function ($join) {
                        $join->on('risk_base.project_code', '=', 'risk_latest_id.project_code')
                            ->on('risk_base.id', '=', 'risk_latest_id.latest_id');
                    })
                    ->select(
                        'risk_base.project_code',
                        'risk_base.project_title',
                        'risk_base.province',
                        'risk_base.city_municipality',
                        'risk_base.aging_value',
                        'risk_base.extraction_date'
                    )
                    ->get();

                $rowsByRisk = [
                    'High Risk' => [],
                    'Low Risk' => [],
                    'No Risk' => [],
                ];

                foreach ($finalAgingRows as $row) {
                    $rawAging = trim((string) ($row->aging_value ?? ''));
                    if ($rawAging === '') {
                        continue;
                    }

                    if (is_numeric($rawAging)) {
                        $agingValue = (float) $rawAging;
                    } else {
                        $cleanedAging = preg_replace('/[^0-9\.\-]/', '', $rawAging);
                        if ($cleanedAging === null || $cleanedAging === '' || !is_numeric($cleanedAging)) {
                            continue;
                        }
                        $agingValue = (float) $cleanedAging;
                    }

                    if ($agingValue >= 60) {
                        $riskLabel = 'High Risk';
                    } elseif ($agingValue > 30 && $agingValue < 60) {
                        $riskLabel = 'Low Risk';
                    } else {
                        $riskLabel = 'No Risk';
                    }

                    $rowsByRisk[$riskLabel][] = (object) [
                        'project_code' => $row->project_code ?? null,
                        'project_title' => $row->project_title ?? null,
                        'province' => $row->province ?? null,
                        'city_municipality' => $row->city_municipality ?? null,
                        'latest_update_date' => $row->extraction_date ?? null,
                        'aging_days' => (fmod($agingValue, 1.0) === 0.0) ? (int) $agingValue : round($agingValue, 2),
                    ];
                }

                foreach (array_keys($rowsByRisk) as $riskLabel) {
                    $projectsByRisk[$riskLabel] = collect($rowsByRisk[$riskLabel])
                        ->sortByDesc('aging_days')
                        ->values();
                }

                return $projectsByRisk;
            };

            $projectUpdateStatusParsedDateExpression = "
                COALESCE(
                    IF(
                        TRIM(COALESCE(spp.date, '')) REGEXP '^[0-9]+(\\.[0-9]+)?$',
                        DATE_ADD('1899-12-30', INTERVAL FLOOR(CAST(TRIM(COALESCE(spp.date, '')) AS DECIMAL(12,4))) DAY),
                        NULL
                    ),
                    STR_TO_DATE(TRIM(COALESCE(spp.date, '')), '%Y-%m-%d'),
                    STR_TO_DATE(TRIM(COALESCE(spp.date, '')), '%Y-%m-%d %H:%i:%s'),
                    STR_TO_DATE(TRIM(COALESCE(spp.date, '')), '%m/%d/%Y'),
                    STR_TO_DATE(TRIM(COALESCE(spp.date, '')), '%m/%d/%Y %H:%i'),
                    STR_TO_DATE(TRIM(COALESCE(spp.date, '')), '%m/%d/%Y %H:%i:%s'),
                    STR_TO_DATE(TRIM(COALESCE(spp.date, '')), '%m/%d/%Y %h:%i:%s %p'),
                    STR_TO_DATE(TRIM(COALESCE(spp.date, '')), '%m/%d/%y'),
                    STR_TO_DATE(TRIM(COALESCE(spp.date, '')), '%d/%m/%Y'),
                    STR_TO_DATE(TRIM(COALESCE(spp.date, '')), '%d-%m-%Y'),
                    STR_TO_DATE(TRIM(COALESCE(spp.date, '')), '%d-%b-%Y'),
                    STR_TO_DATE(TRIM(COALESCE(spp.date, '')), '%b %e, %Y'),
                    STR_TO_DATE(TRIM(COALESCE(spp.date, '')), '%M %e, %Y')
                )
            ";

            $expectedCompletionParsedDateExpression = "
                COALESCE(
                    IF(
                        TRIM(COALESCE(spp.intended_completion_date_2, '')) REGEXP '^[0-9]+(\\.[0-9]+)?$',
                        DATE_ADD('1899-12-30', INTERVAL FLOOR(CAST(TRIM(COALESCE(spp.intended_completion_date_2, '')) AS DECIMAL(12,4))) DAY),
                        NULL
                    ),
                    STR_TO_DATE(TRIM(COALESCE(spp.intended_completion_date_2, '')), '%Y-%m-%d'),
                    STR_TO_DATE(TRIM(COALESCE(spp.intended_completion_date_2, '')), '%Y-%m-%d %H:%i:%s'),
                    STR_TO_DATE(TRIM(COALESCE(spp.intended_completion_date_2, '')), '%m/%d/%Y'),
                    STR_TO_DATE(TRIM(COALESCE(spp.intended_completion_date_2, '')), '%m/%d/%Y %H:%i'),
                    STR_TO_DATE(TRIM(COALESCE(spp.intended_completion_date_2, '')), '%m/%d/%Y %H:%i:%s'),
                    STR_TO_DATE(TRIM(COALESCE(spp.intended_completion_date_2, '')), '%m/%d/%Y %h:%i:%s %p'),
                    STR_TO_DATE(TRIM(COALESCE(spp.intended_completion_date_2, '')), '%m/%d/%y'),
                    STR_TO_DATE(TRIM(COALESCE(spp.intended_completion_date_2, '')), '%d/%m/%Y'),
                    STR_TO_DATE(TRIM(COALESCE(spp.intended_completion_date_2, '')), '%d-%m-%Y'),
                    STR_TO_DATE(TRIM(COALESCE(spp.intended_completion_date_2, '')), '%d-%b-%Y'),
                    STR_TO_DATE(TRIM(COALESCE(spp.intended_completion_date_2, '')), '%b %e, %Y'),
                    STR_TO_DATE(TRIM(COALESCE(spp.intended_completion_date_2, '')), '%M %e, %Y')
                )
            ";

            $computeProjectUpdateStatusCountsFromSubay = function ($subayQuery, array &$targetCounts) use ($projectUpdateStatusParsedDateExpression) {

                $latestProjectDatesQuery = (clone $subayQuery)
                    ->selectRaw('UPPER(TRIM(spp.project_code)) as project_code')
                    ->selectRaw("MAX(CASE WHEN LOWER(TRIM(COALESCE(spp.status, ''))) = 'completed' THEN 1 ELSE 0 END) as has_completed_status")
                    ->selectRaw("MAX({$projectUpdateStatusParsedDateExpression}) as latest_update_date")
                    ->groupBy(DB::raw('UPPER(TRIM(spp.project_code))'));

                $counts = DB::query()
                    ->fromSub($latestProjectDatesQuery, 'project_updates')
                    ->where('project_updates.has_completed_status', '=', 0)
                    ->selectRaw('SUM(CASE WHEN project_updates.latest_update_date IS NOT NULL AND DATEDIFF(CURDATE(), project_updates.latest_update_date) >= 60 THEN 1 ELSE 0 END) as high_risk_total')
                    ->selectRaw('SUM(CASE WHEN project_updates.latest_update_date IS NOT NULL AND DATEDIFF(CURDATE(), project_updates.latest_update_date) > 30 AND DATEDIFF(CURDATE(), project_updates.latest_update_date) < 60 THEN 1 ELSE 0 END) as low_risk_total')
                    ->selectRaw('SUM(CASE WHEN project_updates.latest_update_date IS NOT NULL AND DATEDIFF(CURDATE(), project_updates.latest_update_date) <= 30 THEN 1 ELSE 0 END) as no_risk_total')
                    ->first();

                $targetCounts['High Risk'] = (int) ($counts->high_risk_total ?? 0);
                $targetCounts['Low Risk'] = (int) ($counts->low_risk_total ?? 0);
                $targetCounts['No Risk'] = (int) ($counts->no_risk_total ?? 0);
            };

            $fetchProjectUpdateProjectsFromSubay = function ($subayQuery, string $riskLabel) use ($projectUpdateStatusParsedDateExpression) {
                $latestProjectDatesQuery = (clone $subayQuery)
                    ->selectRaw('UPPER(TRIM(spp.project_code)) as project_code')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.project_title, ""))) as project_title')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.province, ""))) as province')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.city_municipality, ""))) as city_municipality')
                    ->selectRaw("MAX(CASE WHEN LOWER(TRIM(COALESCE(spp.status, ''))) = 'completed' THEN 1 ELSE 0 END) as has_completed_status")
                    ->selectRaw("MAX({$projectUpdateStatusParsedDateExpression}) as latest_update_date")
                    ->groupBy(DB::raw('UPPER(TRIM(spp.project_code))'));

                $statusRowsQuery = DB::query()
                    ->fromSub($latestProjectDatesQuery, 'project_updates')
                    ->select(
                        'project_updates.project_code',
                        'project_updates.project_title',
                        'project_updates.province',
                        'project_updates.city_municipality',
                        'project_updates.latest_update_date'
                    )
                    ->selectRaw('DATEDIFF(CURDATE(), project_updates.latest_update_date) as aging_days')
                    ->where('project_updates.has_completed_status', '=', 0)
                    ->whereNotNull('project_updates.latest_update_date');

                if ($riskLabel === 'High Risk') {
                    $statusRowsQuery->whereRaw('DATEDIFF(CURDATE(), project_updates.latest_update_date) >= 60');
                } elseif ($riskLabel === 'Low Risk') {
                    $statusRowsQuery->whereRaw('DATEDIFF(CURDATE(), project_updates.latest_update_date) > 30 AND DATEDIFF(CURDATE(), project_updates.latest_update_date) < 60');
                } elseif ($riskLabel === 'No Risk') {
                    $statusRowsQuery->whereRaw('DATEDIFF(CURDATE(), project_updates.latest_update_date) <= 30');
                } else {
                    return collect();
                }

                return $statusRowsQuery
                    ->orderByDesc('aging_days')
                    ->orderBy('project_updates.project_code')
                    ->get();
            };

            if (Schema::hasTable('subay_project_profiles')) {
                $subayBaseQuery = DB::table('subay_project_profiles as spp')
                    ->whereNotNull('spp.project_code')
                    ->whereRaw('TRIM(spp.project_code) <> ""');

                $applyRoleScopeToSubay($subayBaseQuery);

                $filterOptions['provinces'] = (clone $subayBaseQuery)
                    ->select('spp.province')
                    ->whereNotNull('spp.province')
                    ->whereRaw('TRIM(spp.province) <> ""')
                    ->distinct()
                    ->orderBy('spp.province')
                    ->pluck('spp.province');

                $cityOptionsQuery = clone $subayBaseQuery;
                $applyExactFilterToSubay($cityOptionsQuery, 'spp.province', $filters['province']);
                $filterOptions['cities'] = $cityOptionsQuery
                    ->select('spp.city_municipality')
                    ->whereNotNull('spp.city_municipality')
                    ->whereRaw('TRIM(spp.city_municipality) <> ""')
                    ->distinct()
                    ->orderBy('spp.city_municipality')
                    ->pluck('spp.city_municipality');

                $barangayOptionsQuery = clone $subayBaseQuery;
                $applyExactFilterToSubay($barangayOptionsQuery, 'spp.province', $filters['province']);
                $applyExactFilterToSubay($barangayOptionsQuery, 'spp.city_municipality', $filters['city_municipality']);
                $filterOptions['barangays'] = $barangayOptionsQuery
                    ->select('spp.barangay')
                    ->whereNotNull('spp.barangay')
                    ->whereRaw('TRIM(spp.barangay) <> ""')
                    ->distinct()
                    ->orderBy('spp.barangay')
                    ->pluck('spp.barangay');

                $programOptionsQuery = clone $subayBaseQuery;
                $applyExactFilterToSubay($programOptionsQuery, 'spp.province', $filters['province']);
                $applyExactFilterToSubay($programOptionsQuery, 'spp.city_municipality', $filters['city_municipality']);
                $applyExactFilterToSubay($programOptionsQuery, 'spp.barangay', $filters['barangay']);
                $filterOptions['programs'] = $programOptionsQuery
                    ->select('spp.program')
                    ->whereNotNull('spp.program')
                    ->whereRaw('TRIM(spp.program) <> ""')
                    ->distinct()
                    ->orderBy('spp.program')
                    ->pluck('spp.program');

                $filterOptions['funding_years'] = (clone $subayBaseQuery)
                    ->select('spp.funding_year')
                    ->whereNotNull('spp.funding_year')
                    ->whereRaw('TRIM(spp.funding_year) <> ""')
                    ->distinct()
                    ->orderByRaw('CAST(spp.funding_year AS UNSIGNED) DESC')
                    ->pluck('spp.funding_year');

                $filterOptions['project_types'] = (clone $subayBaseQuery)
                    ->select('spp.type_of_project')
                    ->whereNotNull('spp.type_of_project')
                    ->whereRaw('TRIM(spp.type_of_project) <> ""')
                    ->distinct()
                    ->orderBy('spp.type_of_project')
                    ->pluck('spp.type_of_project');

                $filterOptions['project_statuses'] = (clone $subayBaseQuery)
                    ->select('spp.status')
                    ->whereNotNull('spp.status')
                    ->whereRaw('TRIM(spp.status) <> ""')
                    ->distinct()
                    ->orderBy('spp.status')
                    ->pluck('spp.status');

                $subayDashboardQuery = clone $subayBaseQuery;
                $applyDashboardFiltersToSubay($subayDashboardQuery);

                $totalProjects = (int) ((clone $subayDashboardQuery)
                    ->selectRaw('COUNT(DISTINCT UPPER(TRIM(spp.project_code))) as total_projects')
                    ->value('total_projects') ?? 0);

                $subayProvinceProjectRows = (clone $subayDashboardQuery)
                    ->selectRaw('TRIM(COALESCE(spp.province, "")) as province')
                    ->selectRaw('COUNT(DISTINCT UPPER(TRIM(spp.project_code))) as total')
                    ->groupBy(DB::raw('TRIM(COALESCE(spp.province, ""))'))
                    ->get();

                foreach ($subayProvinceProjectRows as $row) {
                    $normalizedProvince = $normalizeCarProvince($row->province ?? null);
                    if ($normalizedProvince === null || !array_key_exists($normalizedProvince, $carProvinceProjectCounts)) {
                        continue;
                    }

                    $carProvinceProjectCounts[$normalizedProvince] += (int) ($row->total ?? 0);
                }

                $balanceByProjectQuery = (clone $subayDashboardQuery)
                    ->selectRaw('UPPER(TRIM(spp.project_code)) as project_code')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.project_title, ""))) as project_title')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.status, ""))) as status')
                    ->selectRaw("MAX(CAST(NULLIF(REPLACE(REPLACE(TRIM(COALESCE(spp.national_subsidy_original_allocation, '')), ',', ''), ' ', ''), '') AS DECIMAL(20,2))) as original_allocation")
                    ->selectRaw("MAX(CAST(NULLIF(REPLACE(REPLACE(TRIM(COALESCE(spp.lgu_counterpart_original_allocation, '')), ',', ''), ' ', ''), '') AS DECIMAL(20,2))) as lgu_counterpart")
                    ->selectRaw("MAX(CAST(NULLIF(REPLACE(REPLACE(TRIM(COALESCE(spp.obligation, '')), ',', ''), ' ', ''), '') AS DECIMAL(20,2))) as obligation")
                    ->selectRaw("MAX(CAST(NULLIF(REPLACE(REPLACE(TRIM(COALESCE(spp.disbursement, '')), ',', ''), ' ', ''), '') AS DECIMAL(20,2))) as disbursement")
                    ->selectRaw("MAX(CAST(NULLIF(REPLACE(REPLACE(TRIM(COALESCE(spp.national_subsidy_reverted_amount, '')), ',', ''), ' ', ''), '') AS DECIMAL(20,2))) as reverted_allocation")
                    ->groupBy(DB::raw('UPPER(TRIM(spp.project_code))'));

                $balanceFormulaExpression = 'COALESCE(balance_projects.original_allocation, 0) - (COALESCE(balance_projects.disbursement, 0) + COALESCE(balance_projects.reverted_allocation, 0))';
                $revertedAllocationExpression = 'COALESCE(balance_projects.reverted_allocation, 0)';

                $financialStatusProjectsBaseQuery = DB::query()
                    ->fromSub($balanceByProjectQuery, 'balance_projects')
                    ->select(
                        'balance_projects.project_code',
                        'balance_projects.project_title',
                        'balance_projects.status',
                        'balance_projects.original_allocation',
                        'balance_projects.lgu_counterpart',
                        'balance_projects.obligation',
                        'balance_projects.disbursement'
                    )
                    ->selectRaw("{$revertedAllocationExpression} as reverted_allocation")
                    ->selectRaw("{$balanceFormulaExpression} as balance");

                $financialStatusProjects = (clone $financialStatusProjectsBaseQuery)
                    ->orderByRaw("CASE WHEN LOWER(TRIM(COALESCE(balance_projects.status, ''))) = 'completed' THEN 1 ELSE 0 END")
                    ->orderBy('balance_projects.project_code')
                    ->get();

                $projectsWithBalance = (clone $financialStatusProjectsBaseQuery)
                    ->whereRaw("{$balanceFormulaExpression} > 0")
                    ->orderByRaw("{$balanceFormulaExpression} DESC")
                    ->orderBy('balance_projects.project_code')
                    ->get();

                $financialTotals = DB::query()
                    ->fromSub($balanceByProjectQuery, 'balance_projects')
                    ->selectRaw('COALESCE(SUM(COALESCE(balance_projects.original_allocation, 0)), 0) as total_lgsf_allocation')
                    ->selectRaw('COALESCE(SUM(COALESCE(balance_projects.obligation, 0)), 0) as total_obligation')
                    ->selectRaw('COALESCE(SUM(COALESCE(balance_projects.disbursement, 0)), 0) as total_disbursement')
                    ->selectRaw("COALESCE(SUM({$balanceFormulaExpression}), 0) as total_balance")
                    ->first();

                $totalLgsfAllocationAmount = (float) ($financialTotals->total_lgsf_allocation ?? 0);
                $totalObligationAmount = (float) ($financialTotals->total_obligation ?? 0);
                $totalDisbursementAmount = (float) ($financialTotals->total_disbursement ?? 0);
                $totalBalanceAmount = (float) ($financialTotals->total_balance ?? 0);

                $utilizationPercentage = $totalObligationAmount > 0
                    ? (($totalDisbursementAmount / $totalObligationAmount) * 100)
                    : 0.0;

                $projectsExpectedCompletionBaseQuery = (clone $subayDashboardQuery)
                    ->selectRaw('UPPER(TRIM(spp.project_code)) as project_code')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.project_title, ""))) as project_title')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.province, ""))) as province')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.city_municipality, ""))) as city_municipality')
                    ->selectRaw("MAX({$expectedCompletionParsedDateExpression}) as expected_completion_date")
                    ->groupBy(DB::raw('UPPER(TRIM(spp.project_code))'));

                $projectsExpectedCompletionThisMonth = DB::query()
                    ->fromSub($projectsExpectedCompletionBaseQuery, 'due_projects')
                    ->whereNotNull('due_projects.expected_completion_date')
                    ->whereYear('due_projects.expected_completion_date', $currentYear)
                    ->whereMonth('due_projects.expected_completion_date', $currentMonth)
                    ->orderBy('due_projects.expected_completion_date')
                    ->orderBy('due_projects.project_code')
                    ->get();

                $fundSourceFromProjectCodeExpr = "
                    CASE
                        WHEN UPPER(TRIM(spp.project_code)) LIKE 'SBDP%' THEN 'SBDP'
                        WHEN UPPER(TRIM(spp.project_code)) LIKE 'FA-%' THEN 'FALGU'
                        WHEN UPPER(TRIM(spp.project_code)) LIKE 'FALGU%' THEN 'FALGU'
                        WHEN UPPER(TRIM(spp.project_code)) LIKE 'CMGP%' THEN 'CMGP'
                        WHEN UPPER(TRIM(spp.project_code)) LIKE 'GEF%' THEN 'GEF'
                        WHEN UPPER(TRIM(spp.project_code)) LIKE 'SAFPB%' THEN 'SAFPB'
                        WHEN UPPER(TRIM(spp.project_code)) LIKE 'SGLGIF%' THEN 'SGLGIF'
                        ELSE 'UNSPECIFIED'
                    END
                ";

                $fundSourceCountsMap = (clone $subayDashboardQuery)
                    ->selectRaw("{$fundSourceFromProjectCodeExpr} as fund_source")
                    ->selectRaw('COUNT(DISTINCT UPPER(TRIM(spp.project_code))) as total')
                    ->groupBy(DB::raw($fundSourceFromProjectCodeExpr))
                    ->get()
                    ->reduce(function ($carry, $row) {
                        $label = strtoupper(trim((string) $row->fund_source));
                        $label = $label !== '' && $label !== 'UNSPECIFIED' ? $label : 'Unspecified';
                        $carry[$label] = (int) $row->total;
                        return $carry;
                    }, []);

                $fundSourceProjectRows = (clone $subayDashboardQuery)
                    ->selectRaw("{$fundSourceFromProjectCodeExpr} as fund_source")
                    ->selectRaw('UPPER(TRIM(spp.project_code)) as project_code')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.project_title, ""))) as project_title')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.province, ""))) as province')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.city_municipality, ""))) as city_municipality')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.status, ""))) as status')
                    ->groupBy(DB::raw($fundSourceFromProjectCodeExpr), DB::raw('UPPER(TRIM(spp.project_code))'))
                    ->orderByRaw("{$fundSourceFromProjectCodeExpr}")
                    ->orderByRaw('UPPER(TRIM(spp.project_code))')
                    ->get();

                foreach ($fundSourceProjectRows as $row) {
                    $label = strtoupper(trim((string) ($row->fund_source ?? '')));
                    $label = $label !== '' && $label !== 'UNSPECIFIED' ? $label : 'Unspecified';

                    if (!array_key_exists($label, $fundSourceProjectsMap)) {
                        $fundSourceProjectsMap[$label] = [];
                    }

                    $fundSourceProjectsMap[$label][] = (object) [
                        'project_code' => $row->project_code ?? null,
                        'project_title' => $row->project_title ?? null,
                        'province' => $row->province ?? null,
                        'city_municipality' => $row->city_municipality ?? null,
                        'status' => $row->status ?? null,
                    ];
                }

                foreach (array_keys($fundSourceProjectsMap) as $sourceLabel) {
                    $fundSourceProjectsMap[$sourceLabel] = collect($fundSourceProjectsMap[$sourceLabel])
                        ->sort(function ($leftRow, $rightRow) {
                            $leftIsCompleted = strtolower(trim((string) ($leftRow->status ?? ''))) === 'completed' ? 1 : 0;
                            $rightIsCompleted = strtolower(trim((string) ($rightRow->status ?? ''))) === 'completed' ? 1 : 0;

                            if ($leftIsCompleted !== $rightIsCompleted) {
                                return $leftIsCompleted <=> $rightIsCompleted;
                            }

                            $leftCode = strtoupper(trim((string) ($leftRow->project_code ?? '')));
                            $rightCode = strtoupper(trim((string) ($rightRow->project_code ?? '')));

                            if ($leftCode === $rightCode) {
                                return 0;
                            }

                            return $leftCode < $rightCode ? -1 : 1;
                        })
                        ->values();
                }

                $subayStatusRows = (clone $subayDashboardQuery)
                    ->selectRaw('UPPER(TRIM(COALESCE(spp.status, ""))) as status_raw')
                    ->selectRaw('COUNT(DISTINCT UPPER(TRIM(spp.project_code))) as total')
                    ->groupBy(DB::raw('UPPER(TRIM(COALESCE(spp.status, "")))'))
                    ->get();

                foreach ($subayStatusRows as $row) {
                    $statusLabel = $labelForStatus($normalizeStatus($row->status_raw));
                    if ($statusLabel !== null) {
                        $statusSubaybayanCounts[$statusLabel] += (int) $row->total;
                    }
                }

                $subayStatusLocationRows = (clone $subayDashboardQuery)
                    ->selectRaw('TRIM(COALESCE(spp.province, "")) as province')
                    ->selectRaw('TRIM(COALESCE(spp.city_municipality, "")) as city_municipality')
                    ->selectRaw('UPPER(TRIM(COALESCE(spp.status, ""))) as status_raw')
                    ->selectRaw('COUNT(DISTINCT UPPER(TRIM(spp.project_code))) as total')
                    ->groupBy(
                        DB::raw('TRIM(COALESCE(spp.province, ""))'),
                        DB::raw('TRIM(COALESCE(spp.city_municipality, ""))'),
                        DB::raw('UPPER(TRIM(COALESCE(spp.status, "")))')
                    )
                    ->get();

                $statusByProvince = [];
                foreach ($subayStatusLocationRows as $row) {
                    $statusLabel = $labelForStatus($normalizeStatus($row->status_raw));
                    if ($statusLabel === null) {
                        continue;
                    }

                    $provinceLabel = trim((string) ($row->province ?? ''));
                    $provinceLabel = $provinceLabel !== '' ? $provinceLabel : 'Unspecified Province';
                    $cityLabel = trim((string) ($row->city_municipality ?? ''));
                    $cityLabel = $cityLabel !== '' ? $cityLabel : 'Unspecified City/Municipality';
                    $countValue = (int) ($row->total ?? 0);

                    if (!array_key_exists($provinceLabel, $statusByProvince)) {
                        $statusByProvince[$provinceLabel] = [
                            'province_totals' => array_fill_keys($statusDisplayOrder, 0),
                            'cities' => [],
                        ];
                    }

                    if (!array_key_exists($cityLabel, $statusByProvince[$provinceLabel]['cities'])) {
                        $statusByProvince[$provinceLabel]['cities'][$cityLabel] = array_fill_keys($statusDisplayOrder, 0);
                    }

                    $statusByProvince[$provinceLabel]['province_totals'][$statusLabel] += $countValue;
                    $statusByProvince[$provinceLabel]['cities'][$cityLabel][$statusLabel] += $countValue;
                }

                if (!empty($statusByProvince)) {
                    $provinceLabels = array_keys($statusByProvince);
                    natcasesort($provinceLabels);

                    foreach ($provinceLabels as $provinceLabel) {
                        $provinceData = $statusByProvince[$provinceLabel];

                        $statusSubaybayanLocationReport[] = [
                            'row_type' => 'province',
                            'province' => $provinceLabel,
                            'city_municipality' => '',
                            'counts' => $provinceData['province_totals'],
                        ];

                        $cityLabels = array_keys($provinceData['cities']);
                        natcasesort($cityLabels);

                        foreach ($cityLabels as $cityLabel) {
                            $statusSubaybayanLocationReport[] = [
                                'row_type' => 'city',
                                'province' => $provinceLabel,
                                'city_municipality' => $cityLabel,
                                'counts' => $provinceData['cities'][$cityLabel],
                            ];
                        }
                    }
                }

                $subayStatusProjectRows = (clone $subayDashboardQuery)
                    ->selectRaw('UPPER(TRIM(COALESCE(spp.status, ""))) as status_raw')
                    ->selectRaw('UPPER(TRIM(spp.project_code)) as project_code')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.project_title, ""))) as project_title')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.province, ""))) as province')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.city_municipality, ""))) as city_municipality')
                    ->selectRaw('MAX(TRIM(COALESCE(spp.funding_year, ""))) as funding_year')
                    ->groupBy(
                        DB::raw('UPPER(TRIM(COALESCE(spp.status, "")))'),
                        DB::raw('UPPER(TRIM(spp.project_code))')
                    )
                    ->orderByRaw('UPPER(TRIM(COALESCE(spp.status, "")))')
                    ->orderByRaw('UPPER(TRIM(spp.project_code))')
                    ->get();

                foreach ($subayStatusProjectRows as $row) {
                    $statusLabel = $labelForStatus($normalizeStatus($row->status_raw));
                    if ($statusLabel === null) {
                        continue;
                    }

                    if (!array_key_exists($statusLabel, $statusSubaybayanProjectsMap)) {
                        $statusSubaybayanProjectsMap[$statusLabel] = [];
                    }

                    $statusSubaybayanProjectsMap[$statusLabel][] = (object) [
                        'project_code' => $row->project_code ?? null,
                        'project_title' => $row->project_title ?? null,
                        'province' => $row->province ?? null,
                        'city_municipality' => $row->city_municipality ?? null,
                        'funding_year' => $row->funding_year ?? null,
                        'status' => $statusLabel,
                    ];
                }

                $filteredProjectCodesQuery = (clone $subayDashboardQuery)
                    ->selectRaw('DISTINCT UPPER(TRIM(spp.project_code)) as project_code');
                $computeProjectAtRiskCounts(clone $filteredProjectCodesQuery, 'risk_level', $projectAtRiskCounts);
                $computeProjectAtRiskAgingCounts(clone $filteredProjectCodesQuery, $projectAtRiskAgingCounts);
                $projectAtRiskAgingProjects = $fetchProjectAtRiskAgingProjects(clone $filteredProjectCodesQuery);
                $computeProjectUpdateStatusCountsFromSubay(clone $subayDashboardQuery, $projectUpdateStatusCounts);
                foreach ($projectUpdateStatusOrder as $riskLabel) {
                    $projectUpdateRiskProjects[$riskLabel] = $fetchProjectUpdateProjectsFromSubay(clone $subayDashboardQuery, $riskLabel);
                }
            } else {
                $fallbackQuery = LocallyFundedProject::query();
                $fallbackCityComparableExpression = "TRIM(REPLACE(REPLACE(LOWER(SUBSTRING_INDEX(COALESCE(city_municipality, ''), ',', 1)), 'municipality of ', ''), 'city of ', ''))";
                $fallbackOfficeComparableExpression = "TRIM(REPLACE(REPLACE(LOWER(SUBSTRING_INDEX(COALESCE(office, ''), ',', 1)), 'municipality of ', ''), 'city of ', ''))";
                $applyOfficeScopeToFallback = function ($query) use (
                    $officeLower,
                    $officeComparableLower,
                    $fallbackCityComparableExpression,
                    $fallbackOfficeComparableExpression
                ) {
                    if ($officeLower === '') {
                        return;
                    }

                    $officeNeedle = $officeComparableLower !== '' ? $officeComparableLower : $officeLower;

                    $query->where(function ($subQuery) use (
                        $officeLower,
                        $officeNeedle,
                        $fallbackCityComparableExpression,
                        $fallbackOfficeComparableExpression
                    ) {
                        $subQuery->whereRaw('LOWER(TRIM(COALESCE(office, ""))) = ?', [$officeLower])
                            ->orWhereRaw('LOWER(TRIM(COALESCE(city_municipality, ""))) = ?', [$officeLower])
                            ->orWhereRaw("{$fallbackOfficeComparableExpression} = ?", [$officeNeedle])
                            ->orWhereRaw("{$fallbackCityComparableExpression} = ?", [$officeNeedle]);
                    });
                };

                if ($agency === 'LGU') {
                    if ($office !== '') {
                        if ($province !== '') {
                            $fallbackQuery->whereRaw('LOWER(TRIM(COALESCE(province, ""))) = ?', [$provinceLower]);
                            $applyOfficeScopeToFallback($fallbackQuery);
                        } else {
                            $applyOfficeScopeToFallback($fallbackQuery);
                        }
                    } elseif ($province !== '') {
                        $fallbackQuery->whereRaw('LOWER(TRIM(COALESCE(province, ""))) = ?', [$provinceLower]);
                    }
                } elseif ($agency === 'DILG') {
                    if ($isRegionalOfficeUser) {
                        // Regional Office users can see all projects
                    } elseif ($province !== '') {
                        $fallbackQuery->whereRaw('LOWER(TRIM(COALESCE(province, ""))) = ?', [$provinceLower]);
                    } elseif ($region !== '') {
                        $fallbackQuery->whereRaw('LOWER(TRIM(COALESCE(region, ""))) = ?', [$regionLower]);
                    }
                }

                if ($filters['province'] !== '') {
                    $fallbackQuery->whereRaw('LOWER(TRIM(COALESCE(province, ""))) = ?', [strtolower($filters['province'])]);
                }
                if ($filters['city_municipality'] !== '') {
                    $fallbackQuery->whereRaw('LOWER(TRIM(COALESCE(city_municipality, ""))) = ?', [strtolower($filters['city_municipality'])]);
                }
                if ($filters['barangay'] !== '') {
                    $fallbackQuery->whereRaw('LOWER(TRIM(COALESCE(barangay, ""))) = ?', [strtolower($filters['barangay'])]);
                }
                if (!empty($filters['programs'])) {
                    $normalizedPrograms = collect($filters['programs'])
                        ->map(function ($value) {
                            return strtolower(trim((string) $value));
                        })
                        ->filter(function ($value) {
                            return $value !== '';
                        })
                        ->unique()
                        ->values()
                        ->all();

                    if (!empty($normalizedPrograms)) {
                        $placeholders = implode(', ', array_fill(0, count($normalizedPrograms), '?'));
                        $fallbackQuery->whereRaw("LOWER(TRIM(COALESCE(fund_source, \"\"))) IN ({$placeholders})", $normalizedPrograms);
                    }
                }
                if ($filters['funding_year'] !== '') {
                    $fallbackQuery->whereRaw('LOWER(TRIM(COALESCE(funding_year, ""))) = ?', [strtolower($filters['funding_year'])]);
                }
                if ($filters['project_type'] !== '') {
                    $fallbackQuery->whereRaw('LOWER(TRIM(COALESCE(project_type, ""))) = ?', [strtolower($filters['project_type'])]);
                }

                $totalProjects = (int) (clone $fallbackQuery)->count();

                $fallbackProvinceProjectRows = (clone $fallbackQuery)
                    ->selectRaw('TRIM(COALESCE(province, "")) as province')
                    ->selectRaw('COUNT(DISTINCT NULLIF(UPPER(TRIM(COALESCE(subaybayan_project_code, ""))), "")) as code_total')
                    ->selectRaw('COUNT(*) as row_total')
                    ->groupBy(DB::raw('TRIM(COALESCE(province, ""))'))
                    ->get();

                foreach ($fallbackProvinceProjectRows as $row) {
                    $normalizedProvince = $normalizeCarProvince($row->province ?? null);
                    if ($normalizedProvince === null || !array_key_exists($normalizedProvince, $carProvinceProjectCounts)) {
                        continue;
                    }

                    $countValue = (int) ($row->code_total ?? 0);
                    if ($countValue < 1) {
                        $countValue = (int) ($row->row_total ?? 0);
                    }

                    $carProvinceProjectCounts[$normalizedProvince] += $countValue;
                }

                $financialTotals = (clone $fallbackQuery)
                    ->selectRaw('COALESCE(SUM(COALESCE(obligation, 0)), 0) as total_obligation')
                    ->selectRaw('COALESCE(SUM(COALESCE(disbursed_amount, 0)), 0) as total_disbursement')
                    ->selectRaw('COALESCE(SUM(COALESCE(lgsf_allocation, 0)), 0) as total_lgsf_allocation')
                    ->selectRaw('COALESCE(SUM(COALESCE(reverted_amount, 0)), 0) as total_reverted_amount')
                    ->first();

                $totalObligationAmount = (float) ($financialTotals->total_obligation ?? 0);
                $totalDisbursementAmount = (float) ($financialTotals->total_disbursement ?? 0);
                $totalLgsfAllocationAmount = (float) ($financialTotals->total_lgsf_allocation ?? 0);
                $totalBalanceAmount = (float) ($financialTotals->total_lgsf_allocation ?? 0)
                    - (
                        (float) ($financialTotals->total_disbursement ?? 0)
                        + (float) ($financialTotals->total_reverted_amount ?? 0)
                    );
                $utilizationPercentage = $totalObligationAmount > 0
                    ? (($totalDisbursementAmount / $totalObligationAmount) * 100)
                    : 0.0;

                $projectsExpectedCompletionThisMonth = (clone $fallbackQuery)
                    ->selectRaw('UPPER(TRIM(COALESCE(subaybayan_project_code, ""))) as project_code')
                    ->selectRaw('MAX(TRIM(COALESCE(project_name, ""))) as project_title')
                    ->selectRaw('MAX(TRIM(COALESCE(province, ""))) as province')
                    ->selectRaw('MAX(TRIM(COALESCE(city_municipality, ""))) as city_municipality')
                    ->selectRaw('MAX(target_date_completion) as expected_completion_date')
                    ->groupBy(DB::raw('UPPER(TRIM(COALESCE(subaybayan_project_code, "")))'))
                    ->havingRaw('MAX(target_date_completion) BETWEEN ? AND ?', [$currentMonthStart, $currentMonthEnd])
                    ->orderByRaw('MAX(target_date_completion) ASC')
                    ->orderByRaw('UPPER(TRIM(COALESCE(subaybayan_project_code, "")))')
                    ->get();

                $financialStatusProjects = (clone $fallbackQuery)
                    ->selectRaw('UPPER(TRIM(COALESCE(subaybayan_project_code, ""))) as project_code')
                    ->selectRaw('TRIM(COALESCE(project_name, "")) as project_title')
                    ->selectRaw('TRIM(COALESCE(status, "")) as status')
                    ->selectRaw('COALESCE(lgsf_allocation, 0) as original_allocation')
                    ->selectRaw('COALESCE(lgu_counterpart, 0) as lgu_counterpart')
                    ->selectRaw('COALESCE(obligation, 0) as obligation')
                    ->selectRaw('COALESCE(disbursed_amount, 0) as disbursement')
                    ->selectRaw('COALESCE(reverted_amount, 0) as reverted_allocation')
                    ->selectRaw('COALESCE(lgsf_allocation, 0) - (COALESCE(disbursed_amount, 0) + COALESCE(reverted_amount, 0)) as balance')
                    ->orderByRaw("CASE WHEN LOWER(TRIM(COALESCE(status, ''))) = 'completed' THEN 1 ELSE 0 END")
                    ->orderByRaw('UPPER(TRIM(COALESCE(subaybayan_project_code, "")))')
                    ->get();

                $fundSourceCountsMap = (clone $fallbackQuery)
                    ->select('fund_source', DB::raw('COUNT(*) as total'))
                    ->groupBy('fund_source')
                    ->get()
                    ->reduce(function ($carry, $row) {
                        $label = strtoupper(trim((string) $row->fund_source));
                        $label = $label !== '' ? $label : 'Unspecified';
                        $carry[$label] = ($carry[$label] ?? 0) + (int) $row->total;
                        return $carry;
                    }, collect())
                    ->toArray();

                $fundSourceProjectRows = (clone $fallbackQuery)
                    ->selectRaw('COALESCE(NULLIF(TRIM(fund_source), ""), "Unspecified") as fund_source')
                    ->selectRaw('UPPER(TRIM(COALESCE(subaybayan_project_code, ""))) as project_code')
                    ->selectRaw('MAX(TRIM(COALESCE(project_name, ""))) as project_title')
                    ->selectRaw('MAX(TRIM(COALESCE(province, ""))) as province')
                    ->selectRaw('MAX(TRIM(COALESCE(city_municipality, ""))) as city_municipality')
                    ->groupBy(
                        DB::raw('COALESCE(NULLIF(TRIM(fund_source), ""), "Unspecified")'),
                        DB::raw('UPPER(TRIM(COALESCE(subaybayan_project_code, "")))')
                    )
                    ->orderBy('fund_source')
                    ->orderBy('project_code')
                    ->get();

                foreach ($fundSourceProjectRows as $row) {
                    $label = strtoupper(trim((string) ($row->fund_source ?? '')));
                    $label = $label !== '' ? $label : 'Unspecified';

                    if (!array_key_exists($label, $fundSourceProjectsMap)) {
                        $fundSourceProjectsMap[$label] = [];
                    }

                    $fundSourceProjectsMap[$label][] = (object) [
                        'project_code' => $row->project_code ?? null,
                        'project_title' => $row->project_title ?? null,
                        'province' => $row->province ?? null,
                        'city_municipality' => $row->city_municipality ?? null,
                        'status' => null,
                    ];
                }

                foreach (array_keys($fundSourceProjectsMap) as $sourceLabel) {
                    $fundSourceProjectsMap[$sourceLabel] = collect($fundSourceProjectsMap[$sourceLabel])
                        ->sort(function ($leftRow, $rightRow) {
                            $leftIsCompleted = strtolower(trim((string) ($leftRow->status ?? ''))) === 'completed' ? 1 : 0;
                            $rightIsCompleted = strtolower(trim((string) ($rightRow->status ?? ''))) === 'completed' ? 1 : 0;

                            if ($leftIsCompleted !== $rightIsCompleted) {
                                return $leftIsCompleted <=> $rightIsCompleted;
                            }

                            $leftCode = strtoupper(trim((string) ($leftRow->project_code ?? '')));
                            $rightCode = strtoupper(trim((string) ($rightRow->project_code ?? '')));

                            if ($leftCode === $rightCode) {
                                return 0;
                            }

                            return $leftCode < $rightCode ? -1 : 1;
                        })
                        ->values();
                }

                $fallbackProjectCodesQuery = (clone $fallbackQuery)
                    ->whereNotNull('subaybayan_project_code')
                    ->whereRaw('TRIM(subaybayan_project_code) <> ""')
                    ->selectRaw('DISTINCT UPPER(TRIM(subaybayan_project_code)) as project_code');
                $computeProjectAtRiskCounts(clone $fallbackProjectCodesQuery, 'risk_level', $projectAtRiskCounts);
                $computeProjectAtRiskAgingCounts(clone $fallbackProjectCodesQuery, $projectAtRiskAgingCounts);
                $projectAtRiskAgingProjects = $fetchProjectAtRiskAgingProjects(clone $fallbackProjectCodesQuery);
            }

            $fundSourceCounts = collect();
            foreach ($fundSourceOptions as $source) {
                $fundSourceCounts[$source] = (int) ($fundSourceCountsMap[$source] ?? 0);
            }

            foreach ($fundSourceCountsMap as $source => $count) {
                if (!in_array($source, $fundSourceOptions, true)) {
                    $fundSourceCounts[$source] = (int) $count;
                }
            }

            foreach (array_keys($statusSubaybayanProjectsMap) as $statusLabel) {
                $statusSubaybayanProjectsMap[$statusLabel] = collect($statusSubaybayanProjectsMap[$statusLabel])
                    ->sort(function ($leftRow, $rightRow) {
                        $leftCode = strtoupper(trim((string) ($leftRow->project_code ?? '')));
                        $rightCode = strtoupper(trim((string) ($rightRow->project_code ?? '')));

                        if ($leftCode === $rightCode) {
                            return 0;
                        }

                        return $leftCode < $rightCode ? -1 : 1;
                    })
                    ->values();
            }

            $carProvinceProjectMaxCount = !empty($carProvinceProjectCounts)
                ? (int) max($carProvinceProjectCounts)
                : 0;

            return view('dashboard.index', compact(
                'totalProjects',
                'statusActualCounts',
                'statusSubaybayanCounts',
                'statusSubaybayanProjectsMap',
                'statusSubaybayanLocationReport',
                'statusDisplayOrder',
                'subayUploadDateLabel',
                'fundSourceCounts',
                'filters',
                'filterOptions',
                'totalLgsfAllocationAmount',
                'totalObligationAmount',
                'totalDisbursementAmount',
                'totalBalanceAmount',
                'utilizationPercentage',
                'expectedCompletionMonthLabel',
                'projectsExpectedCompletionThisMonth',
                'projectAtRiskCounts',
                'projectAtRiskAgingCounts',
                'projectAtRiskAgingProjects',
                'projectUpdateStatusCounts',
                'projectUpdateRiskProjects',
                'projectsWithBalance',
                'financialStatusProjects',
                'fundSourceProjectsMap',
                'carProvinceProjectCounts',
                'carProvinceProjectMaxCount'
            ));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Dashboard view error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    })->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    // Change password routes
    Route::get('/change-password', [App\Http\Controllers\ChangePasswordController::class, 'show'])->name('password.show');
    Route::put('/change-password', [App\Http\Controllers\ChangePasswordController::class, 'update'])->name('password.update');
    
    // User Management routes (superadmin only)
    Route::middleware('superadmin')->group(function () {
        Route::resource('users', App\Http\Controllers\UserManagementController::class);
    });

    // Fund Utilization Report routes
    Route::prefix('fund-utilization')->group(function () {
        Route::get('/', [App\Http\Controllers\FundUtilizationReportController::class, 'index'])->name('fund-utilization.index');
        Route::get('/export', [App\Http\Controllers\FundUtilizationReportController::class, 'export'])->name('fund-utilization.export');
        Route::get('/create', [App\Http\Controllers\FundUtilizationReportController::class, 'create'])->name('fund-utilization.create');
        Route::get('/get-municipalities/{province}', [App\Http\Controllers\FundUtilizationReportController::class, 'getMunicipalities'])->name('fund-utilization.get-municipalities');
        Route::post('/', [App\Http\Controllers\FundUtilizationReportController::class, 'store'])->name('fund-utilization.store');
        Route::get('/{projectCode}', [App\Http\Controllers\FundUtilizationReportController::class, 'show'])->name('fund-utilization.show');
        Route::get('/{projectCode}/edit', [App\Http\Controllers\FundUtilizationReportController::class, 'edit'])->name('fund-utilization.edit');
        Route::put('/{projectCode}', [App\Http\Controllers\FundUtilizationReportController::class, 'update'])->name('fund-utilization.update');
        Route::delete('/{projectCode}', [App\Http\Controllers\FundUtilizationReportController::class, 'deleteProject'])->name('fund-utilization.delete-project');
        Route::post('/{projectCode}/upload-mov', [App\Http\Controllers\FundUtilizationReportController::class, 'uploadMOV'])->name('fund-utilization.upload-mov');
        Route::post('/{projectCode}/upload-written-notice', [App\Http\Controllers\FundUtilizationReportController::class, 'uploadWrittenNotice'])->name('fund-utilization.upload-written-notice');
        Route::post('/{projectCode}/upload-fdp', [App\Http\Controllers\FundUtilizationReportController::class, 'uploadFDP'])->name('fund-utilization.upload-fdp');
        Route::post('/{projectCode}/save-posting-link', [App\Http\Controllers\FundUtilizationReportController::class, 'savePostingLink'])->name('fund-utilization.save-posting-link');
        Route::post('/{projectCode}/add-remark', [App\Http\Controllers\FundUtilizationReportController::class, 'addRemark'])->name('fund-utilization.add-remark');
        Route::post('/{projectCode}/approve/{uploadType}/{quarter}', [App\Http\Controllers\FundUtilizationReportController::class, 'approveUpload'])->name('fund-utilization.approve-upload');
        Route::post('/{projectCode}/save-remarks/{uploadType}/{quarter}', [App\Http\Controllers\FundUtilizationReportController::class, 'saveUserRemarks'])->name('fund-utilization.save-remarks');
        Route::get('/{projectCode}/view-document/{docType}/{quarter}', [App\Http\Controllers\FundUtilizationReportController::class, 'viewDocument'])->name('fund-utilization.view-document');
        Route::post('/{projectCode}/delete-document/{docType}/{quarter}', [App\Http\Controllers\FundUtilizationReportController::class, 'deleteDocument'])->name('fund-utilization.delete-document');
    });

    Route::get('/pre-implementation-documents/sbdp-projects', [App\Http\Controllers\PreImplementationDocumentController::class, 'index'])
        ->name('pre-implementation-documents.sbdp');
    Route::get('/pre-implementation-documents/sbdp-projects/{projectCode}', [App\Http\Controllers\PreImplementationDocumentController::class, 'show'])
        ->name('pre-implementation-documents.sbdp.show');
    Route::post('/pre-implementation-documents/sbdp-projects/{projectCode}', [App\Http\Controllers\PreImplementationDocumentController::class, 'save'])
        ->name('pre-implementation-documents.sbdp.save');
    Route::post('/pre-implementation-documents/sbdp-projects/{projectCode}/validate/{documentType}', [App\Http\Controllers\PreImplementationDocumentController::class, 'validateDocument'])
        ->name('pre-implementation-documents.sbdp.validate');

    // Projects routes
    Route::get('/projects/locally-funded', [App\Http\Controllers\LocallyFundedProjectController::class, 'index'])->name('projects.locally-funded');
    Route::get('/projects/locally-funded/subay/{projectCode}', [App\Http\Controllers\LocallyFundedProjectController::class, 'showSubaybayan'])->name('locally-funded-project.subay-show');
    Route::get('/projects/locally-funded/create', [App\Http\Controllers\LocallyFundedProjectController::class, 'create'])->name('locally-funded-project.create');
    Route::get('/projects/locally-funded/ensure/{projectCode}', [App\Http\Controllers\LocallyFundedProjectController::class, 'ensureFromSubay'])
        ->name('locally-funded-project.ensure');
    Route::post('/projects/locally-funded', [App\Http\Controllers\LocallyFundedProjectController::class, 'store'])->name('locally-funded-project.store');
    Route::get('/projects/locally-funded/{project}', [App\Http\Controllers\LocallyFundedProjectController::class, 'show'])->name('locally-funded-project.show');
    Route::get('/projects/locally-funded/{project}/pcr-mov', [App\Http\Controllers\LocallyFundedProjectController::class, 'viewPcrMov'])->name('locally-funded-project.view-pcr-mov');
    Route::get('/projects/locally-funded/{project}/edit', [App\Http\Controllers\LocallyFundedProjectController::class, 'edit'])->name('locally-funded-project.edit');
    Route::put('/projects/locally-funded/{project}', [App\Http\Controllers\LocallyFundedProjectController::class, 'update'])->name('locally-funded-project.update');
    Route::delete('/projects/locally-funded/{project}', [App\Http\Controllers\LocallyFundedProjectController::class, 'destroy'])->name('locally-funded-project.destroy');
    
    // API routes for location data

    Route::get('/project-at-risk', [App\Http\Controllers\ProjectAtRiskController::class, 'index'])
        ->name('projects.at-risk');
    Route::get('/project-at-risk/export', [App\Http\Controllers\ProjectAtRiskController::class, 'export'])
        ->name('projects.at-risk.export');
    Route::post('/project-at-risk/import', [App\Http\Controllers\ProjectAtRiskController::class, 'import'])
        ->name('projects.at-risk.import');

    Route::get('/projects/rlip-lime', function () {
        return view('projects.rlip-lime');
    })->name('projects.rlip-lime');

    Route::middleware('regional_dilg')->group(function () {
        Route::get('/system-management', function () {
            return view('system-management.index');
        })->name('system-management.index');
        Route::get('/system-management/upload-subaybayan', [SystemManagementController::class, 'uploadSubaybayan'])
            ->name('system-management.upload-subaybayan');
        Route::post('/system-management/upload-subaybayan/import', [SystemManagementController::class, 'importSubaybayan'])
            ->name('system-management.upload-subaybayan.import');
        Route::post('/system-management/upload-subaybayan/import/{importId}/load', [SystemManagementController::class, 'loadSubaybayanImport'])
            ->name('system-management.upload-subaybayan.load');
        Route::get('/system-management/upload-subaybayan/import/{importId}/download', [SystemManagementController::class, 'downloadSubaybayanImport'])
            ->name('system-management.upload-subaybayan.download');
        Route::delete('/system-management/upload-subaybayan/import/{importId}', [SystemManagementController::class, 'deleteSubaybayanImport'])
            ->name('system-management.upload-subaybayan.delete');
    });

    // Local Project Monitoring Committee routes
    Route::post('local-project-monitoring-committee/{lpmc}/upload', [App\Http\Controllers\LocalProjectMonitoringCommitteeController::class, 'upload'])
        ->name('local-project-monitoring-committee.upload');
    Route::post('local-project-monitoring-committee/{lpmc}/approve/{docId}', [App\Http\Controllers\LocalProjectMonitoringCommitteeController::class, 'approveDocument'])
        ->name('local-project-monitoring-committee.approve');
    Route::get('local-project-monitoring-committee/{lpmc}/document/{docId}', [App\Http\Controllers\LocalProjectMonitoringCommitteeController::class, 'viewDocument'])
        ->name('local-project-monitoring-committee.document');
    Route::resource('local-project-monitoring-committee', App\Http\Controllers\LocalProjectMonitoringCommitteeController::class)
        ->parameters(['local-project-monitoring-committee' => 'lpmc']);

    // Road Maintenance Status Report routes
    Route::post('road-maintenance-status/{roadMaintenance}/upload', [App\Http\Controllers\RoadMaintenanceStatusReportController::class, 'upload'])
        ->name('road-maintenance-status.upload');
    Route::post('road-maintenance-status/{roadMaintenance}/approve/{docId}', [App\Http\Controllers\RoadMaintenanceStatusReportController::class, 'approveDocument'])
        ->name('road-maintenance-status.approve');
    Route::get('road-maintenance-status/{roadMaintenance}/document/{docId}', [App\Http\Controllers\RoadMaintenanceStatusReportController::class, 'viewDocument'])
        ->name('road-maintenance-status.document');
    Route::resource('road-maintenance-status', App\Http\Controllers\RoadMaintenanceStatusReportController::class)
        ->parameters(['road-maintenance-status' => 'roadMaintenance']);

    Route::get('/reports/monthly/pd-no-pbbm-2025-1572-1573', [App\Http\Controllers\PdNoPbbmMonthlyReportController::class, 'index'])
        ->name('reports.monthly.pd-no-pbbm-2025-1572-1573');
    Route::get('/reports/monthly/pd-no-pbbm-2025-1572-1573/{office}/edit', [App\Http\Controllers\PdNoPbbmMonthlyReportController::class, 'edit'])
        ->name('reports.monthly.pd-no-pbbm-2025-1572-1573.edit');
    Route::post('/reports/monthly/pd-no-pbbm-2025-1572-1573/{office}/upload', [App\Http\Controllers\PdNoPbbmMonthlyReportController::class, 'upload'])
        ->name('reports.monthly.pd-no-pbbm-2025-1572-1573.upload');
    Route::post('/reports/monthly/pd-no-pbbm-2025-1572-1573/{office}/approve/{docId}', [App\Http\Controllers\PdNoPbbmMonthlyReportController::class, 'approveDocument'])
        ->name('reports.monthly.pd-no-pbbm-2025-1572-1573.approve');
    Route::get('/reports/monthly/pd-no-pbbm-2025-1572-1573/{office}/document/{docId}', [App\Http\Controllers\PdNoPbbmMonthlyReportController::class, 'viewDocument'])
        ->name('reports.monthly.pd-no-pbbm-2025-1572-1573.document');

    Route::get('/reports/rbis-annual-certification', [App\Http\Controllers\RbisAnnualCertificationController::class, 'index'])
        ->name('rbis-annual-certification.index');
    Route::get('/reports/rbis-annual-certification/{office}/edit', [App\Http\Controllers\RbisAnnualCertificationController::class, 'edit'])
        ->name('rbis-annual-certification.edit');
    Route::post('/reports/rbis-annual-certification/{office}/upload', [App\Http\Controllers\RbisAnnualCertificationController::class, 'upload'])
        ->name('rbis-annual-certification.upload');
    Route::post('/reports/rbis-annual-certification/{office}/approve/{docId}', [App\Http\Controllers\RbisAnnualCertificationController::class, 'approveDocument'])
        ->name('rbis-annual-certification.approve');
    Route::get('/reports/rbis-annual-certification/{office}/document/{docId}', [App\Http\Controllers\RbisAnnualCertificationController::class, 'viewDocument'])
        ->name('rbis-annual-certification.document');
});
