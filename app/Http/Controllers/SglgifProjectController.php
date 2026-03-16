<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SglgifProjectController extends Controller
{
    public function dashboard(Request $request)
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'province' => trim((string) $request->query('province', '')),
            'city' => trim((string) $request->query('city', '')),
            'funding_year' => trim((string) $request->query('funding_year', '')),
            'level' => trim((string) $request->query('level', '')),
            'type' => trim((string) $request->query('type', '')),
            'status' => trim((string) $request->query('status', '')),
        ];

        if (!Schema::hasTable('subay_project_profiles')) {
            return view('projects.sglgif-dashboard', $this->emptyDashboardPayload($filters));
        }

        $baseQuery = $this->buildScopedBaseQuery();

        $fundingYears = (clone $baseQuery)
            ->select('spp.funding_year')
            ->whereNotNull('spp.funding_year')
            ->whereRaw('TRIM(spp.funding_year) <> ""')
            ->distinct()
            ->orderByRaw("CAST(COALESCE(NULLIF(TRIM(spp.funding_year), ''), '0') AS UNSIGNED) DESC")
            ->pluck('spp.funding_year');

        $provinces = (clone $baseQuery)
            ->select('spp.province')
            ->whereNotNull('spp.province')
            ->whereRaw('TRIM(spp.province) <> ""')
            ->distinct()
            ->orderBy('spp.province')
            ->pluck('spp.province');

        $levelOptions = (clone $baseQuery)
            ->select('spp.sglgif_level')
            ->whereNotNull('spp.sglgif_level')
            ->whereRaw('TRIM(spp.sglgif_level) <> ""')
            ->distinct()
            ->orderBy('spp.sglgif_level')
            ->pluck('spp.sglgif_level');

        $typeOptions = (clone $baseQuery)
            ->select('spp.type_of_project')
            ->whereNotNull('spp.type_of_project')
            ->whereRaw('TRIM(spp.type_of_project) <> ""')
            ->distinct()
            ->orderBy('spp.type_of_project')
            ->pluck('spp.type_of_project');

        $statusOptions = (clone $baseQuery)
            ->select('spp.status')
            ->whereNotNull('spp.status')
            ->whereRaw('TRIM(spp.status) <> ""')
            ->distinct()
            ->orderBy('spp.status')
            ->pluck('spp.status');

        $cityOptionsQuery = clone $baseQuery;
        if ($filters['province'] !== '') {
            $cityOptionsQuery->whereRaw('LOWER(TRIM(COALESCE(spp.province, ""))) = ?', [mb_strtolower($filters['province'])]);
        }

        $cityOptions = $cityOptionsQuery
            ->select('spp.city_municipality')
            ->whereNotNull('spp.city_municipality')
            ->whereRaw('TRIM(spp.city_municipality) <> ""')
            ->distinct()
            ->orderBy('spp.city_municipality')
            ->pluck('spp.city_municipality');

        $query = clone $baseQuery;
        $this->applyFiltersToQuery($query, $filters);

        $rows = $query
            ->select([
                'spp.project_code',
                'spp.project_title',
                'spp.funding_year',
                'spp.region',
                'spp.province',
                'spp.city_municipality',
                'spp.beneficiaries',
                'spp.status',
                'spp.type_of_project',
                'spp.sub_type_of_project',
                'spp.sglgif_level',
                'spp.national_subsidy_original_allocation',
                'spp.total_project_cost',
                'spp.sglgif_financial',
                'spp.total_accomplishment',
                'spp.sglgif_attachment',
                'spp.sglgif_overall',
                'spp.updated_at',
            ])
            ->orderByRaw("CAST(COALESCE(NULLIF(TRIM(spp.funding_year), ''), '0') AS UNSIGNED) DESC")
            ->orderBy('spp.province')
            ->orderBy('spp.city_municipality')
            ->orderBy('spp.project_title')
            ->get()
            ->map(fn ($row) => $this->mapRow($row))
            ->values();

        $totalProjects = $rows->count();
        $uniqueLguCount = $rows->pluck('city_municipality')->filter()->unique()->count();
        $uniqueProvinceCount = $rows->pluck('province')->filter()->unique()->count();
        $uniqueCategoryCount = $rows->pluck('sub_type_of_project')->filter()->unique()->count();

        $totalSubsidyAmount = (float) $rows->sum('subsidy_value');
        $totalProjectCostAmount = (float) $rows->sum('project_cost_value');
        $subsidyUtilizationPercent = $totalSubsidyAmount > 0
            ? round(($totalProjectCostAmount / $totalSubsidyAmount) * 100, 2)
            : 0.0;

        $averageFinancialPercent = round((float) $rows->pluck('financial_pct')->filter(fn ($value) => $value !== null)->avg(), 2);
        $averagePhysicalPercent = round((float) $rows->pluck('physical_pct')->filter(fn ($value) => $value !== null)->avg(), 2);
        $averageAttachmentPercent = round((float) $rows->pluck('attachment_pct')->filter(fn ($value) => $value !== null)->avg(), 2);
        $averageOverallPercent = round((float) $rows->pluck('overall_pct')->filter(fn ($value) => $value !== null)->avg(), 2);

        $completedProjects = $rows->filter(fn (array $row) => $row['status_lc'] === 'completed')->count();
        $ongoingProjects = $rows->filter(fn (array $row) => $row['status_lc'] === 'ongoing')->count();
        $completedRatePercent = $totalProjects > 0
            ? round(($completedProjects / $totalProjects) * 100, 2)
            : 0.0;

        $infrastructureCount = $rows->filter(fn (array $row) => $row['type_lc'] === 'infrastructure')->count();
        $nonInfrastructureCount = $rows->filter(fn (array $row) => $row['type_lc'] === 'non-infrastructure')->count();
        $municipalityCount = $rows->filter(fn (array $row) => $row['level_lc'] === 'municipality')->count();
        $provinceLevelCount = $rows->filter(fn (array $row) => $row['level_lc'] === 'province')->count();
        $cityLevelCount = $rows->filter(fn (array $row) => $row['level_lc'] === 'city')->count();

        $zeroFinancialCount = $rows->filter(fn (array $row) => ($row['financial_pct'] ?? null) !== null && (float) $row['financial_pct'] <= 0)->count();
        $zeroPhysicalCount = $rows->filter(fn (array $row) => ($row['physical_pct'] ?? null) !== null && (float) $row['physical_pct'] <= 0)->count();
        $incompleteAttachmentCount = $rows->filter(fn (array $row) => ($row['attachment_pct'] ?? null) !== null && (float) $row['attachment_pct'] < 100)->count();
        $needsAttentionCount = $rows->filter(fn (array $row) => ($row['overall_pct'] ?? 0) < 50)->count();

        $statusBreakdown = $this->buildCountBreakdown($rows, 'status');
        $provinceBreakdown = $this->buildCountBreakdown($rows, 'province', 6);
        $categoryBreakdown = $this->buildCountBreakdown($rows, 'sub_type_of_project', 6);
        $typeBreakdown = $this->buildCountBreakdown($rows, 'type_of_project');
        $levelBreakdown = $this->buildCountBreakdown($rows, 'sglgif_level');

        $provinceFundingBreakdown = $this->buildFundingBreakdown($rows, 'province', 6);
        $categoryFundingBreakdown = $this->buildFundingBreakdown($rows, 'sub_type_of_project', 6);
        $topLguFundingBreakdown = $this->buildFundingBreakdown($rows, 'city_municipality', 8);

        $fundingYearBreakdown = $rows
            ->groupBy(fn (array $row) => trim((string) ($row['funding_year'] ?? '')) !== '' ? $row['funding_year'] : 'Unspecified')
            ->map(function (Collection $group, string $label) {
                return [
                    'label' => $label,
                    'count' => $group->count(),
                    'amount' => (float) $group->sum('subsidy_value'),
                ];
            })
            ->sortByDesc(function (array $item) {
                return is_numeric($item['label']) ? (int) $item['label'] : -1;
            })
            ->values();

        $progressBandBreakdown = collect([
            [
                'label' => 'Delivered',
                'count' => $rows->filter(fn (array $row) => ($row['overall_pct'] ?? 0) >= 100)->count(),
                'color' => '#16a34a',
                'bg' => '#dcfce7',
                'copy' => 'Projects already at full overall completion.',
            ],
            [
                'label' => 'Near Delivery',
                'count' => $rows->filter(fn (array $row) => ($row['overall_pct'] ?? 0) >= 75 && ($row['overall_pct'] ?? 0) < 100)->count(),
                'color' => '#2563eb',
                'bg' => '#dbeafe',
                'copy' => 'Projects approaching full delivery.',
            ],
            [
                'label' => 'Advancing',
                'count' => $rows->filter(fn (array $row) => ($row['overall_pct'] ?? 0) >= 50 && ($row['overall_pct'] ?? 0) < 75)->count(),
                'color' => '#f59e0b',
                'bg' => '#fef3c7',
                'copy' => 'Projects with material progress but still midstream.',
            ],
            [
                'label' => 'Needs Attention',
                'count' => $rows->filter(fn (array $row) => ($row['overall_pct'] ?? 0) < 50)->count(),
                'color' => '#dc2626',
                'bg' => '#fee2e2',
                'copy' => 'Projects with early-stage or weak overall movement.',
            ],
        ])->values();

        $riskBreakdown = collect([
            [
                'label' => 'Stable',
                'count' => $rows->filter(fn (array $row) => $this->classifyRisk($row) === 'Stable')->count(),
                'color' => '#15803d',
                'bg' => '#dcfce7',
                'copy' => 'Completed or already beyond 90% overall.',
            ],
            [
                'label' => 'Watch',
                'count' => $rows->filter(fn (array $row) => $this->classifyRisk($row) === 'Watch')->count(),
                'color' => '#2563eb',
                'bg' => '#dbeafe',
                'copy' => 'Active projects moving, but not yet near delivery.',
            ],
            [
                'label' => 'Priority',
                'count' => $rows->filter(fn (array $row) => $this->classifyRisk($row) === 'Priority')->count(),
                'color' => '#d97706',
                'bg' => '#ffedd5',
                'copy' => 'Projects in the 50% to 74% range that need closer follow-through.',
            ],
            [
                'label' => 'Critical',
                'count' => $rows->filter(fn (array $row) => $this->classifyRisk($row) === 'Critical')->count(),
                'color' => '#dc2626',
                'bg' => '#fee2e2',
                'copy' => 'Ongoing projects with very low movement or zero financial/physical values.',
            ],
        ])->values();

        $watchlistRows = $rows
            ->filter(fn (array $row) => $row['status_lc'] === 'ongoing')
            ->sort(function (array $left, array $right) {
                $leftOverall = $left['overall_pct'] ?? 999;
                $rightOverall = $right['overall_pct'] ?? 999;
                if ($leftOverall === $rightOverall) {
                    return $right['subsidy_value'] <=> $left['subsidy_value'];
                }

                return $leftOverall <=> $rightOverall;
            })
            ->take(6)
            ->values();

        $statusReviewRows = $rows
            ->filter(function (array $row) {
                $overall = $row['overall_pct'];
                if ($overall === null) {
                    return false;
                }

                return ($row['status_lc'] === 'completed' && $overall < 100)
                    || ($row['status_lc'] === 'ongoing' && $overall >= 90);
            })
            ->sort(function (array $left, array $right) {
                return ($left['overall_pct'] ?? 0) <=> ($right['overall_pct'] ?? 0);
            })
            ->take(6)
            ->values();

        $latestUpdateAt = $rows->pluck('updated_at')->filter()->sortDesc()->first();

        return view('projects.sglgif-dashboard', [
            'activeTab' => 'sglgif',
            'filters' => $filters,
            'fundingYears' => $fundingYears,
            'provinces' => $provinces,
            'cityOptions' => $cityOptions,
            'levelOptions' => $levelOptions,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'totalProjects' => $totalProjects,
            'uniqueLguCount' => $uniqueLguCount,
            'uniqueProvinceCount' => $uniqueProvinceCount,
            'uniqueCategoryCount' => $uniqueCategoryCount,
            'totalSubsidyAmount' => $totalSubsidyAmount,
            'totalProjectCostAmount' => $totalProjectCostAmount,
            'subsidyUtilizationPercent' => $subsidyUtilizationPercent,
            'averageFinancialPercent' => $averageFinancialPercent,
            'averagePhysicalPercent' => $averagePhysicalPercent,
            'averageAttachmentPercent' => $averageAttachmentPercent,
            'averageOverallPercent' => $averageOverallPercent,
            'completedProjects' => $completedProjects,
            'ongoingProjects' => $ongoingProjects,
            'completedRatePercent' => $completedRatePercent,
            'infrastructureCount' => $infrastructureCount,
            'nonInfrastructureCount' => $nonInfrastructureCount,
            'municipalityCount' => $municipalityCount,
            'provinceLevelCount' => $provinceLevelCount,
            'cityLevelCount' => $cityLevelCount,
            'zeroFinancialCount' => $zeroFinancialCount,
            'zeroPhysicalCount' => $zeroPhysicalCount,
            'incompleteAttachmentCount' => $incompleteAttachmentCount,
            'needsAttentionCount' => $needsAttentionCount,
            'statusBreakdown' => $statusBreakdown,
            'provinceBreakdown' => $provinceBreakdown,
            'categoryBreakdown' => $categoryBreakdown,
            'typeBreakdown' => $typeBreakdown,
            'levelBreakdown' => $levelBreakdown,
            'provinceFundingBreakdown' => $provinceFundingBreakdown,
            'categoryFundingBreakdown' => $categoryFundingBreakdown,
            'topLguFundingBreakdown' => $topLguFundingBreakdown,
            'fundingYearBreakdown' => $fundingYearBreakdown,
            'progressBandBreakdown' => $progressBandBreakdown,
            'riskBreakdown' => $riskBreakdown,
            'watchlistRows' => $watchlistRows,
            'statusReviewRows' => $statusReviewRows,
            'latestUpdateAt' => $latestUpdateAt,
        ]);
    }

    private function emptyDashboardPayload(array $filters): array
    {
        return [
            'activeTab' => 'sglgif',
            'filters' => $filters,
            'fundingYears' => collect(),
            'provinces' => collect(),
            'cityOptions' => collect(),
            'levelOptions' => collect(),
            'typeOptions' => collect(),
            'statusOptions' => collect(),
            'totalProjects' => 0,
            'uniqueLguCount' => 0,
            'uniqueProvinceCount' => 0,
            'uniqueCategoryCount' => 0,
            'totalSubsidyAmount' => 0,
            'totalProjectCostAmount' => 0,
            'subsidyUtilizationPercent' => 0,
            'averageFinancialPercent' => 0,
            'averagePhysicalPercent' => 0,
            'averageAttachmentPercent' => 0,
            'averageOverallPercent' => 0,
            'completedProjects' => 0,
            'ongoingProjects' => 0,
            'completedRatePercent' => 0,
            'infrastructureCount' => 0,
            'nonInfrastructureCount' => 0,
            'municipalityCount' => 0,
            'provinceLevelCount' => 0,
            'cityLevelCount' => 0,
            'zeroFinancialCount' => 0,
            'zeroPhysicalCount' => 0,
            'incompleteAttachmentCount' => 0,
            'needsAttentionCount' => 0,
            'statusBreakdown' => collect(),
            'provinceBreakdown' => collect(),
            'categoryBreakdown' => collect(),
            'typeBreakdown' => collect(),
            'levelBreakdown' => collect(),
            'provinceFundingBreakdown' => collect(),
            'categoryFundingBreakdown' => collect(),
            'topLguFundingBreakdown' => collect(),
            'fundingYearBreakdown' => collect(),
            'progressBandBreakdown' => collect(),
            'riskBreakdown' => collect(),
            'watchlistRows' => collect(),
            'statusReviewRows' => collect(),
            'latestUpdateAt' => null,
        ];
    }

    private function buildScopedBaseQuery()
    {
        $query = DB::table('subay_project_profiles as spp')
            ->whereRaw('UPPER(TRIM(COALESCE(spp.program, ""))) = ?', ['SGLGIF']);

        $user = Auth::user();
        if (!$user) {
            return $query;
        }

        $agency = strtoupper(trim((string) $user->agency));
        $province = trim((string) $user->province);
        $office = trim((string) $user->office);
        $region = trim((string) $user->region);

        $provinceLower = mb_strtolower($province);
        $officeLower = mb_strtolower($office);
        $regionLower = mb_strtolower($region);
        $officeBaseLower = trim((string) preg_replace('/,.*$/', '', $officeLower));
        $officeComparableLower = trim((string) preg_replace('/^(municipality|city)\s+of\s+/i', '', $officeBaseLower));
        $cityComparableExpression = "TRIM(REPLACE(REPLACE(LOWER(SUBSTRING_INDEX(COALESCE(spp.city_municipality, ''), ',', 1)), 'municipality of ', ''), 'city of ', ''))";
        $isRegionalOfficeUser = $agency === 'DILG'
            && (
                str_contains($provinceLower, 'regional office')
                || str_contains($officeLower, 'regional office')
            );

        $applyOfficeScope = function ($query) use ($officeLower, $officeComparableLower, $cityComparableExpression) {
            if ($officeLower === '') {
                return;
            }

            $officeNeedle = $officeComparableLower !== '' ? $officeComparableLower : $officeLower;

            $query->where(function ($subQuery) use ($officeLower, $officeNeedle, $cityComparableExpression) {
                $subQuery
                    ->whereRaw('LOWER(TRIM(COALESCE(spp.city_municipality, ""))) = ?', [$officeLower])
                    ->orWhereRaw("{$cityComparableExpression} = ?", [$officeNeedle]);
            });
        };

        if ($agency === 'LGU') {
            if ($office !== '') {
                if ($province !== '') {
                    $query->whereRaw('LOWER(TRIM(COALESCE(spp.province, ""))) = ?', [$provinceLower]);
                    $applyOfficeScope($query);
                } else {
                    $applyOfficeScope($query);
                }
            } elseif ($province !== '') {
                $query->whereRaw('LOWER(TRIM(COALESCE(spp.province, ""))) = ?', [$provinceLower]);
            }

            return $query;
        }

        if ($agency !== 'DILG') {
            return $query;
        }

        if ($isRegionalOfficeUser) {
            return $query;
        }

        if ($province !== '') {
            $query->whereRaw('LOWER(TRIM(COALESCE(spp.province, ""))) = ?', [$provinceLower]);
        } elseif ($region !== '') {
            $query->whereRaw('LOWER(TRIM(COALESCE(spp.region, ""))) = ?', [$regionLower]);
        }

        return $query;
    }

    private function applyFiltersToQuery($query, array $filters): void
    {
        if ($filters['search'] !== '') {
            $keyword = '%' . mb_strtolower($filters['search']) . '%';
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery
                    ->whereRaw('LOWER(COALESCE(spp.project_code, "")) LIKE ?', [$keyword])
                    ->orWhereRaw('LOWER(COALESCE(spp.project_title, "")) LIKE ?', [$keyword])
                    ->orWhereRaw('LOWER(COALESCE(spp.province, "")) LIKE ?', [$keyword])
                    ->orWhereRaw('LOWER(COALESCE(spp.city_municipality, "")) LIKE ?', [$keyword])
                    ->orWhereRaw('LOWER(COALESCE(spp.sub_type_of_project, "")) LIKE ?', [$keyword]);
            });
        }

        $exactFilters = [
            'province' => 'spp.province',
            'city' => 'spp.city_municipality',
            'funding_year' => 'spp.funding_year',
            'level' => 'spp.sglgif_level',
            'type' => 'spp.type_of_project',
            'status' => 'spp.status',
        ];

        foreach ($exactFilters as $filterKey => $column) {
            $value = trim((string) ($filters[$filterKey] ?? ''));
            if ($value === '') {
                continue;
            }

            $query->whereRaw('LOWER(TRIM(COALESCE(' . $column . ', ""))) = ?', [mb_strtolower($value)]);
        }
    }

    private function mapRow(object $row): array
    {
        $subsidy = $this->extractNumeric($row->national_subsidy_original_allocation ?? null);
        $projectCost = $this->extractNumeric($row->total_project_cost ?? null);
        $financial = $this->extractNumeric($row->sglgif_financial ?? null);
        $physical = $this->extractNumeric($row->total_accomplishment ?? null);
        $attachment = $this->extractNumeric($row->sglgif_attachment ?? null);
        $overall = $this->extractNumeric($row->sglgif_overall ?? null);

        return [
            'project_code' => trim((string) ($row->project_code ?? '')),
            'project_title' => trim((string) ($row->project_title ?? '')),
            'funding_year' => trim((string) ($row->funding_year ?? '')),
            'region' => trim((string) ($row->region ?? '')),
            'province' => trim((string) ($row->province ?? '')),
            'city_municipality' => trim((string) ($row->city_municipality ?? '')),
            'beneficiaries' => trim((string) ($row->beneficiaries ?? '')),
            'status' => trim((string) ($row->status ?? '')),
            'status_lc' => mb_strtolower(trim((string) ($row->status ?? ''))),
            'type_of_project' => trim((string) ($row->type_of_project ?? '')),
            'type_lc' => mb_strtolower(trim((string) ($row->type_of_project ?? ''))),
            'sub_type_of_project' => trim((string) ($row->sub_type_of_project ?? '')),
            'sglgif_level' => trim((string) ($row->sglgif_level ?? '')),
            'level_lc' => mb_strtolower(trim((string) ($row->sglgif_level ?? ''))),
            'subsidy_value' => $subsidy ?? 0.0,
            'project_cost_value' => $projectCost ?? 0.0,
            'financial_pct' => $financial,
            'physical_pct' => $physical,
            'attachment_pct' => $attachment,
            'overall_pct' => $overall,
            'updated_at' => $row->updated_at,
        ];
    }

    private function buildCountBreakdown(Collection $rows, string $key, ?int $limit = null): Collection
    {
        $items = $rows
            ->groupBy(function (array $row) use ($key) {
                $label = trim((string) ($row[$key] ?? ''));

                return $label !== '' ? $label : 'Unspecified';
            })
            ->map(fn (Collection $group, string $label) => [
                'label' => $label,
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->values();

        return $limit !== null ? $items->take($limit)->values() : $items->values();
    }

    private function buildFundingBreakdown(Collection $rows, string $key, ?int $limit = null): Collection
    {
        $items = $rows
            ->groupBy(function (array $row) use ($key) {
                $label = trim((string) ($row[$key] ?? ''));

                return $label !== '' ? $label : 'Unspecified';
            })
            ->map(fn (Collection $group, string $label) => [
                'label' => $label,
                'count' => $group->count(),
                'amount' => (float) $group->sum('subsidy_value'),
            ])
            ->sortByDesc('amount')
            ->values();

        return $limit !== null ? $items->take($limit)->values() : $items->values();
    }

    private function classifyRisk(array $row): string
    {
        $status = $row['status_lc'] ?? '';
        $overall = (float) ($row['overall_pct'] ?? 0);
        $financial = (float) ($row['financial_pct'] ?? 0);
        $physical = (float) ($row['physical_pct'] ?? 0);

        if ($status === 'completed' || $overall >= 90) {
            return 'Stable';
        }

        if ($overall < 50 || $financial <= 0 || $physical <= 0) {
            return 'Critical';
        }

        if ($overall < 75) {
            return 'Priority';
        }

        return 'Watch';
    }

    private function extractNumeric(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $clean = preg_replace('/[^0-9\\.-]/', '', trim((string) $value));
        if ($clean === null || $clean === '' || $clean === '-' || $clean === '.' || $clean === '-.') {
            return null;
        }

        return (float) $clean;
    }
}
