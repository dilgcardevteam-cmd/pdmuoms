@extends('layouts.dashboard')

@section('title', 'SGLGIF Dashboard')
@section('page-title', 'SGLGIF Dashboard')

@section('content')
    @php
        $activeFilters = array_merge([
            'search' => '',
            'province' => '',
            'city' => '',
            'funding_year' => '',
            'level' => '',
            'type' => '',
            'status' => '',
        ], $filters ?? []);

        $topCategoryCount = (int) ($categoryBreakdown->first()['count'] ?? 0);
        $topCategoryFundingAmount = (float) ($categoryFundingBreakdown->first()['amount'] ?? 0);
        $topProvinceCount = (int) ($provinceBreakdown->first()['count'] ?? 0);
        $topProvinceFundingAmount = (float) ($provinceFundingBreakdown->first()['amount'] ?? 0);
        $topYearFundingAmount = (float) ($fundingYearBreakdown->max('amount') ?? 0);
        $topLguFundingAmount = (float) ($topLguFundingBreakdown->first()['amount'] ?? 0);
        $progressTotal = (int) (($progressBandBreakdown ?? collect())->sum('count') ?? 0);
        $avgSubsidyPerProject = $totalProjects > 0 ? $totalSubsidyAmount / $totalProjects : 0;

        $progressSegments = [];
        if (($progressBandBreakdown ?? collect())->isNotEmpty() && $progressTotal > 0) {
            $gap = 1.2;
            $available = max(0, 100 - ($progressBandBreakdown->count() * $gap));
            $running = 0.0;

            foreach ($progressBandBreakdown as $item) {
                $count = (int) ($item['count'] ?? 0);
                if ($count < 1) {
                    continue;
                }

                $length = (($count / $progressTotal) * $available);
                $progressSegments[] = [
                    'start' => $running,
                    'length' => $length,
                    'label' => $item['label'],
                    'count' => $count,
                    'color' => $item['color'],
                    'bg' => $item['bg'],
                    'copy' => $item['copy'],
                ];
                $running += $length + $gap;
            }
        }

        $statusStyles = [
            'Completed' => ['color' => '#166534', 'bg' => '#dcfce7', 'border' => '#86efac'],
            'Ongoing' => ['color' => '#1d4ed8', 'bg' => '#dbeafe', 'border' => '#93c5fd'],
        ];
    @endphp

    <div class="content-header">
        <h1>SGLG Incentive Fund Dashboard</h1>
        <p>
            Interactive portfolio infographics from the imported SGLGIF database.
            @if($latestUpdateAt)
                Snapshot refreshed {{ \Illuminate\Support\Carbon::parse($latestUpdateAt)->format('F j, Y g:i A') }}.
            @endif
        </p>
    </div>

    @include('projects.partials.project-section-tabs', ['activeTab' => $activeTab ?? 'sglgif'])

    <div class="dashboard-main-layout sglgif-dashboard-shell">
        <form method="GET" action="{{ route('projects.sglgif') }}" class="dashboard-card project-filter-form dashboard-main-layout-filter collapsed" style="background: #ffffff; padding: 16px 18px; border-radius: 12px; box-shadow: 0 8px 24px rgba(15,23,42,0.08); margin-bottom: 0;">
            <button type="button" class="project-filter-toggle" onclick="toggleProjectFilter(this)" aria-expanded="false" aria-controls="sglgif-filter-body">
                <span class="sglgif-filter-title">
                    <i class="fas fa-filter" aria-hidden="true" style="font-size: 16px;"></i>
                    <span>PROJECT FILTER</span>
                </span>
                <span class="project-filter-chevron">
                    <i class="fas fa-chevron-up"></i>
                </span>
            </button>

            <div id="sglgif-filter-body" class="project-filter-body" style="max-height: 0px;">
                <div class="dashboard-filter-grid sglgif-filter-grid">
                    <div class="sglgif-filter-summary">
                        <strong>{{ number_format($totalProjects) }}</strong> projects in the current SGLGIF scope, covering
                        <strong>{{ number_format($uniqueLguCount) }}</strong> LGUs and
                        <strong>{{ number_format($uniqueProvinceCount) }}</strong> provinces.
                    </div>

                    <div class="sglgif-filter-field sglgif-filter-field--search">
                        <label for="sglgif-search">Search</label>
                        <input id="sglgif-search" type="text" name="search" value="{{ $activeFilters['search'] }}" placeholder="Project code, title, province, LGU, category">
                    </div>

                    <div class="sglgif-filter-field">
                        <label for="sglgif-province">Province</label>
                        <select id="sglgif-province" name="province">
                            <option value="">All</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province }}" @selected((string) $activeFilters['province'] === (string) $province)>{{ $province }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sglgif-filter-field">
                        <label for="sglgif-city">City/Municipality</label>
                        <select id="sglgif-city" name="city">
                            <option value="">All</option>
                            @foreach($cityOptions as $city)
                                <option value="{{ $city }}" @selected((string) $activeFilters['city'] === (string) $city)>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sglgif-filter-field">
                        <label for="sglgif-year">Funding Year</label>
                        <select id="sglgif-year" name="funding_year">
                            <option value="">All</option>
                            @foreach($fundingYears as $year)
                                <option value="{{ $year }}" @selected((string) $activeFilters['funding_year'] === (string) $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sglgif-filter-field">
                        <label for="sglgif-level">Level</label>
                        <select id="sglgif-level" name="level">
                            <option value="">All</option>
                            @foreach($levelOptions as $level)
                                <option value="{{ $level }}" @selected((string) $activeFilters['level'] === (string) $level)>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sglgif-filter-field">
                        <label for="sglgif-type">Project Type</label>
                        <select id="sglgif-type" name="type">
                            <option value="">All</option>
                            @foreach($typeOptions as $type)
                                <option value="{{ $type }}" @selected((string) $activeFilters['type'] === (string) $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sglgif-filter-field">
                        <label for="sglgif-status">Project Status</label>
                        <select id="sglgif-status" name="status">
                            <option value="">All</option>
                            @foreach($statusOptions as $status)
                                <option value="{{ $status }}" @selected((string) $activeFilters['status'] === (string) $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="dashboard-filter-reset sglgif-filter-actions">
                        <button type="submit" class="sglgif-action-btn sglgif-action-btn--primary">
                            <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('projects.sglgif') }}" class="sglgif-action-btn sglgif-action-btn--muted">
                            <i class="fas fa-rotate-left" aria-hidden="true"></i>
                            Reset
                        </a>
                        <a href="{{ route('projects.sglgif.table', request()->query()) }}" class="sglgif-action-btn sglgif-action-btn--accent">
                            <i class="fas fa-table" aria-hidden="true"></i>
                            Open SGLGIF Table
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <div class="dashboard-top-cards">
            <section class="dashboard-card sglgif-hero-card">
                <div class="sglgif-hero-copy">
                    <span class="sglgif-kicker">Portfolio Pulse</span>
                    <h2>SGLGIF delivery is concentrated in local infrastructure, with a strong completion rate and a small set of active projects requiring focused follow-through.</h2>
                    <p>The current filter scope covers {{ number_format($totalProjects) }} SGLGIF projects, averaging {{ number_format($averageOverallPercent, 2) }}% overall accomplishment and {{ number_format($subsidyUtilizationPercent, 2) }}% cost absorption versus subsidy.</p>
                </div>
                <div class="sglgif-hero-metrics">
                    <a href="{{ route('projects.sglgif.table', request()->query()) }}" class="sglgif-hero-tile">
                        <span>Total Projects</span>
                        <strong>{{ number_format($totalProjects) }}</strong>
                        <small>{{ number_format($uniqueLguCount) }} LGUs / {{ number_format($uniqueProvinceCount) }} provinces</small>
                    </a>
                    <div class="sglgif-hero-tile">
                        <span>Total Subsidy</span>
                        <strong>&#8369; {{ number_format($totalSubsidyAmount, 2) }}</strong>
                        <small>Average per project: &#8369; {{ number_format($avgSubsidyPerProject, 2) }}</small>
                    </div>
                    <div class="sglgif-hero-tile">
                        <span>Average Overall</span>
                        <strong>{{ number_format($averageOverallPercent, 2) }}%</strong>
                        <small>Financial {{ number_format($averageFinancialPercent, 2) }}% / Physical {{ number_format($averagePhysicalPercent, 2) }}%</small>
                    </div>
                    <div class="sglgif-hero-tile">
                        <span>Delivery Rate</span>
                        <strong>{{ number_format($completedRatePercent, 2) }}%</strong>
                        <small>{{ number_format($completedProjects) }} completed / {{ number_format($ongoingProjects) }} ongoing</small>
                    </div>
                </div>
                <div class="sglgif-hero-ribbon">
                    <span><i class="fas fa-shield-heart"></i> {{ number_format($uniqueCategoryCount) }} strategic focus categories</span>
                    <span><i class="fas fa-city"></i> Municipality-led projects: {{ number_format($municipalityCount) }}</span>
                    <span><i class="fas fa-folder-open"></i> Attachment readiness average: {{ number_format($averageAttachmentPercent, 2) }}%</span>
                </div>
            </section>

            <section class="dashboard-card sglgif-card">
                <div class="sglgif-card-head">
                    <div>
                        <h2>PROJECT MIX</h2>
                        <p>Track what the SGLGIF portfolio is funding, where it is concentrated, and which years dominate the current scope.</p>
                    </div>
                </div>

                <div class="sglgif-stat-strip">
                    <div class="sglgif-mini-stat">
                        <span>Infrastructure</span>
                        <strong>{{ number_format($infrastructureCount) }}</strong>
                    </div>
                    <div class="sglgif-mini-stat">
                        <span>Non-Infrastructure</span>
                        <strong>{{ number_format($nonInfrastructureCount) }}</strong>
                    </div>
                    <div class="sglgif-mini-stat">
                        <span>Municipality Level</span>
                        <strong>{{ number_format($municipalityCount) }}</strong>
                    </div>
                    <div class="sglgif-mini-stat">
                        <span>Province/City Level</span>
                        <strong>{{ number_format($provinceLevelCount + $cityLevelCount) }}</strong>
                    </div>
                </div>

                <div class="sglgif-dual-panel">
                    <div class="sglgif-panel">
                        <div class="sglgif-panel-head">
                            <h3>Category Focus</h3>
                            <div class="sglgif-switch" data-sg-switch-group="category-focus">
                                <button type="button" class="is-active" data-sg-switch-target="count">Count</button>
                                <button type="button" data-sg-switch-target="funding">Funding</button>
                            </div>
                        </div>

                        <div class="sglgif-switch-panel is-active" data-sg-switch-panel="category-focus:count">
                            @forelse($categoryBreakdown as $item)
                                @php $barWidth = $topCategoryCount > 0 ? round(($item['count'] / $topCategoryCount) * 100, 2) : 0; @endphp
                                <div class="sglgif-bar-row">
                                    <div class="sglgif-bar-head"><span>{{ $item['label'] }}</span><strong>{{ number_format($item['count']) }}</strong></div>
                                    <div class="sglgif-bar-track"><div style="width: {{ $barWidth }}%; background: linear-gradient(90deg, #7c3aed, #4f46e5);"></div></div>
                                </div>
                            @empty
                                <p class="sglgif-empty">No category data for this filter set.</p>
                            @endforelse
                        </div>

                        <div class="sglgif-switch-panel" data-sg-switch-panel="category-focus:funding">
                            @forelse($categoryFundingBreakdown as $item)
                                @php $barWidth = $topCategoryFundingAmount > 0 ? round((($item['amount'] ?? 0) / $topCategoryFundingAmount) * 100, 2) : 0; @endphp
                                <div class="sglgif-bar-row">
                                    <div class="sglgif-bar-head"><span>{{ $item['label'] }}</span><strong>&#8369; {{ number_format((float) ($item['amount'] ?? 0), 2) }}</strong></div>
                                    <div class="sglgif-bar-track"><div style="width: {{ $barWidth }}%; background: linear-gradient(90deg, #0f766e, #10b981);"></div></div>
                                    <div class="sglgif-note">{{ number_format((int) ($item['count'] ?? 0)) }} projects</div>
                                </div>
                            @empty
                                <p class="sglgif-empty">No funding data for this filter set.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="sglgif-panel">
                        <div class="sglgif-panel-head">
                            <h3>Funding by Year</h3>
                        </div>
                        @forelse($fundingYearBreakdown as $item)
                            @php $barWidth = $topYearFundingAmount > 0 ? round((($item['amount'] ?? 0) / $topYearFundingAmount) * 100, 2) : 0; @endphp
                            <div class="sglgif-bar-row">
                                <div class="sglgif-bar-head"><span>{{ $item['label'] }}</span><strong>&#8369; {{ number_format((float) ($item['amount'] ?? 0), 2) }}</strong></div>
                                <div class="sglgif-bar-track"><div style="width: {{ $barWidth }}%; background: linear-gradient(90deg, #f59e0b, #f97316);"></div></div>
                                <div class="sglgif-note">{{ number_format((int) ($item['count'] ?? 0)) }} projects</div>
                            </div>
                        @empty
                            <p class="sglgif-empty">No funding-year data for this filter set.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="dashboard-card sglgif-card">
                <div class="sglgif-card-head">
                    <div>
                        <h2>FINANCIAL SNAPSHOT</h2>
                        <p>Read the portfolio through allocation, delivery, documentation, and overall accomplishment.</p>
                    </div>
                </div>

                <div class="sglgif-gauge-grid">
                    <article class="sglgif-gauge-card">
                        <div class="sglgif-gauge" style="--p: {{ max(0, min(100, (float) $averageFinancialPercent)) }}; --c: #0f766e;">
                            <span>{{ number_format($averageFinancialPercent, 2) }}%</span>
                        </div>
                        <h3>Financial</h3>
                        <p>Average financial accomplishment across filtered SGLGIF rows.</p>
                    </article>
                    <article class="sglgif-gauge-card">
                        <div class="sglgif-gauge" style="--p: {{ max(0, min(100, (float) $averagePhysicalPercent)) }}; --c: #2563eb;">
                            <span>{{ number_format($averagePhysicalPercent, 2) }}%</span>
                        </div>
                        <h3>Physical</h3>
                        <p>Average physical accomplishment from the uploaded project records.</p>
                    </article>
                    <article class="sglgif-gauge-card">
                        <div class="sglgif-gauge" style="--p: {{ max(0, min(100, (float) $averageAttachmentPercent)) }}; --c: #d97706;">
                            <span>{{ number_format($averageAttachmentPercent, 2) }}%</span>
                        </div>
                        <h3>Attachment</h3>
                        <p>Readiness of required attachments and supporting documents.</p>
                    </article>
                    <article class="sglgif-gauge-card">
                        <div class="sglgif-gauge" style="--p: {{ max(0, min(100, (float) $averageOverallPercent)) }}; --c: #7c3aed;">
                            <span>{{ number_format($averageOverallPercent, 2) }}%</span>
                        </div>
                        <h3>Overall</h3>
                        <p>Composite delivery score from the SGLGIF overall field.</p>
                    </article>
                </div>

                <div class="sglgif-financial-summary">
                    <div class="sglgif-financial-tile">
                        <span>Original Subsidy Allocation</span>
                        <strong>&#8369; {{ number_format($totalSubsidyAmount, 2) }}</strong>
                    </div>
                    <div class="sglgif-financial-tile">
                        <span>Total Project Cost</span>
                        <strong>&#8369; {{ number_format($totalProjectCostAmount, 2) }}</strong>
                    </div>
                    <div class="sglgif-financial-tile">
                        <span>Cost Absorption</span>
                        <strong>{{ number_format($subsidyUtilizationPercent, 2) }}%</strong>
                    </div>
                </div>
            </section>

            <section class="dashboard-card sglgif-card">
                <div class="sglgif-card-head">
                    <div>
                        <h2>IMPLEMENTATION WATCH</h2>
                        <p>Immediate attention points across ongoing SGLGIF projects and portfolio reporting completeness.</p>
                    </div>
                </div>

                <div class="sglgif-alert-grid">
                    <div class="sglgif-alert-card">
                        <span>Zero Financial</span>
                        <strong>{{ number_format($zeroFinancialCount) }}</strong>
                    </div>
                    <div class="sglgif-alert-card">
                        <span>Zero Physical</span>
                        <strong>{{ number_format($zeroPhysicalCount) }}</strong>
                    </div>
                    <div class="sglgif-alert-card">
                        <span>Attachment &lt; 100%</span>
                        <strong>{{ number_format($incompleteAttachmentCount) }}</strong>
                    </div>
                    <div class="sglgif-alert-card">
                        <span>Overall &lt; 50%</span>
                        <strong>{{ number_format($needsAttentionCount) }}</strong>
                    </div>
                </div>

                <div class="sglgif-table-wrap">
                    <table class="sglgif-table">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>LGU</th>
                                <th>Overall</th>
                                <th>Financial</th>
                                <th>Subsidy</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($watchlistRows as $row)
                                <tr>
                                    <td>
                                        <strong>{{ $row['project_title'] ?: $row['project_code'] }}</strong>
                                        <div class="sglgif-subline">{{ $row['project_code'] }}</div>
                                    </td>
                                    <td>{{ $row['city_municipality'] ?: '-' }}</td>
                                    <td>{{ $row['overall_pct'] !== null ? number_format($row['overall_pct'], 2) . '%' : '-' }}</td>
                                    <td>{{ $row['financial_pct'] !== null ? number_format($row['financial_pct'], 2) . '%' : '-' }}</td>
                                    <td>&#8369; {{ number_format((float) ($row['subsidy_value'] ?? 0), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="sglgif-empty-cell">No ongoing projects for the current filter set.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="dashboard-card sglgif-card">
                <div class="sglgif-card-head">
                    <div>
                        <h2>STATUS OF PROJECT</h2>
                        <p>Click a tile to open the filtered SGLGIF table directly from the dashboard.</p>
                    </div>
                </div>

                <div class="sglgif-status-grid">
                    @forelse($statusBreakdown as $item)
                        @php
                            $statusStyle = $statusStyles[$item['label']] ?? ['color' => '#334155', 'bg' => '#f8fafc', 'border' => '#cbd5e1'];
                            $statusUrl = route('projects.sglgif.table', array_merge(request()->query(), ['status' => $item['label']]));
                        @endphp
                        <a href="{{ $statusUrl }}" class="sglgif-status-tile" style="--status-color: {{ $statusStyle['color'] }}; --status-bg: {{ $statusStyle['bg'] }}; --status-border: {{ $statusStyle['border'] }};">
                            <span>{{ $item['label'] }}</span>
                            <strong>{{ number_format($item['count']) }}</strong>
                        </a>
                    @empty
                        <p class="sglgif-empty">No status data for this filter set.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="dashboard-status-row">
            <section class="dashboard-card sglgif-card">
                <div class="sglgif-card-head">
                    <div>
                        <h2>PROVINCIAL DEPLOYMENT</h2>
                        <p>Switch between project counts and subsidy intensity, then compare leading LGUs.</p>
                    </div>
                </div>

                <div class="sglgif-switch" data-sg-switch-group="province-footprint">
                    <button type="button" class="is-active" data-sg-switch-target="count">By Count</button>
                    <button type="button" data-sg-switch-target="funding">By Funding</button>
                </div>

                <div class="sglgif-switch-panel is-active" data-sg-switch-panel="province-footprint:count">
                    @forelse($provinceBreakdown as $item)
                        @php $barWidth = $topProvinceCount > 0 ? round(($item['count'] / $topProvinceCount) * 100, 2) : 0; @endphp
                        <div class="sglgif-bar-row">
                            <div class="sglgif-bar-head"><span>{{ $item['label'] }}</span><strong>{{ number_format($item['count']) }}</strong></div>
                            <div class="sglgif-bar-track"><div style="width: {{ $barWidth }}%; background: linear-gradient(90deg, #1d4ed8, #0ea5e9);"></div></div>
                        </div>
                    @empty
                        <p class="sglgif-empty">No province counts for this filter set.</p>
                    @endforelse
                </div>

                <div class="sglgif-switch-panel" data-sg-switch-panel="province-footprint:funding">
                    @forelse($provinceFundingBreakdown as $item)
                        @php $barWidth = $topProvinceFundingAmount > 0 ? round((($item['amount'] ?? 0) / $topProvinceFundingAmount) * 100, 2) : 0; @endphp
                        <div class="sglgif-bar-row">
                            <div class="sglgif-bar-head"><span>{{ $item['label'] }}</span><strong>&#8369; {{ number_format((float) ($item['amount'] ?? 0), 2) }}</strong></div>
                            <div class="sglgif-bar-track"><div style="width: {{ $barWidth }}%; background: linear-gradient(90deg, #0f766e, #14b8a6);"></div></div>
                            <div class="sglgif-note">{{ number_format((int) ($item['count'] ?? 0)) }} projects</div>
                        </div>
                    @empty
                        <p class="sglgif-empty">No province funding data for this filter set.</p>
                    @endforelse
                </div>

                <div class="sglgif-panel-head" style="margin-top: 18px;">
                    <h3>Top LGUs by Subsidy</h3>
                </div>
                @forelse($topLguFundingBreakdown as $item)
                    @php $barWidth = $topLguFundingAmount > 0 ? round((($item['amount'] ?? 0) / $topLguFundingAmount) * 100, 2) : 0; @endphp
                    <div class="sglgif-bar-row">
                        <div class="sglgif-bar-head"><span>{{ $item['label'] }}</span><strong>&#8369; {{ number_format((float) ($item['amount'] ?? 0), 2) }}</strong></div>
                        <div class="sglgif-bar-track"><div style="width: {{ $barWidth }}%; background: linear-gradient(90deg, #f59e0b, #f97316);"></div></div>
                        <div class="sglgif-note">{{ number_format((int) ($item['count'] ?? 0)) }} projects</div>
                    </div>
                @empty
                    <p class="sglgif-empty">No LGU funding data for this filter set.</p>
                @endforelse
            </section>

            <section class="dashboard-card sglgif-card">
                <div class="sglgif-card-head">
                    <div>
                        <h2>DELIVERY RISK REVIEW</h2>
                        <p>Portfolio attention signals based on low overall movement, zero progress markers, and status-alignment checks.</p>
                    </div>
                </div>

                <div class="sglgif-risk-grid">
                    @forelse($riskBreakdown as $item)
                        <div class="sglgif-risk-tile" style="--risk-color: {{ $item['color'] }}; --risk-bg: {{ $item['bg'] }};">
                            <span>{{ $item['label'] }}</span>
                            <strong>{{ number_format($item['count']) }}</strong>
                            <small>{{ $item['copy'] }}</small>
                        </div>
                    @empty
                        <p class="sglgif-empty">No delivery-risk data for this filter set.</p>
                    @endforelse
                </div>

                <div class="sglgif-panel-head" style="margin-top: 18px;">
                    <h3>Status Alignment Review</h3>
                </div>
                <div class="sglgif-table-wrap">
                    <table class="sglgif-table">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Status</th>
                                <th>Overall</th>
                                <th>Review Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statusReviewRows as $row)
                                <tr>
                                    <td>
                                        <strong>{{ $row['project_title'] ?: $row['project_code'] }}</strong>
                                        <div class="sglgif-subline">{{ $row['project_code'] }} · {{ $row['city_municipality'] ?: '-' }}</div>
                                    </td>
                                    <td>{{ $row['status'] ?: '-' }}</td>
                                    <td>{{ $row['overall_pct'] !== null ? number_format($row['overall_pct'], 2) . '%' : '-' }}</td>
                                    <td>
                                        @if($row['status_lc'] === 'completed' && ($row['overall_pct'] ?? 0) < 100)
                                            Completed tag but overall is below 100%.
                                        @elseif($row['status_lc'] === 'ongoing' && ($row['overall_pct'] ?? 0) >= 90)
                                            Ongoing tag despite already high overall completion.
                                        @else
                                            Review status and overall alignment.
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="sglgif-empty-cell">No alignment issues detected for the current filter set.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <style>
        .project-filter-toggle {
            width: 100%;
            border: none;
            background: transparent;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            text-align: left;
        }

        .sglgif-filter-title {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .project-filter-chevron,
        .project-filter-body {
            transition: all 0.28s ease;
        }

        .project-filter-body {
            overflow: hidden;
            max-height: 1200px;
            opacity: 1;
            transform: translateY(0);
        }

        .project-filter-form.collapsed .project-filter-body {
            max-height: 0;
            opacity: 0;
            transform: translateY(-6px);
            pointer-events: none;
        }

        .project-filter-form.collapsed .project-filter-chevron {
            transform: rotate(180deg);
        }

        .dashboard-main-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(320px, 1fr);
            gap: 20px;
            align-items: start;
            padding: 24px;
            border: 1px solid #dbe4ff;
            border-radius: 18px;
            background:
                radial-gradient(circle at top left, rgba(255, 215, 128, 0.25), transparent 32%),
                linear-gradient(180deg, #fbfdff 0%, #f3f7fb 100%);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            margin-bottom: 24px;
        }

        .dashboard-main-layout-filter {
            grid-column: 1 / -1;
        }

        .dashboard-main-layout > * {
            min-width: 0;
        }

        .dashboard-top-cards,
        .dashboard-status-row {
            display: grid;
            gap: 20px;
            grid-template-columns: 1fr;
        }

        .sglgif-filter-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px 16px;
            align-items: end;
            margin-top: 18px;
        }

        .sglgif-filter-summary {
            grid-column: 1 / -1;
            padding: 12px 14px;
            border-radius: 12px;
            background: linear-gradient(135deg, #eff6ff, #eef2ff);
            color: #1e3a8a;
            border: 1px solid #bfdbfe;
            font-size: 12px;
        }

        .sglgif-filter-field label {
            display: block;
            color: #1f2937;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .sglgif-filter-field input,
        .sglgif-filter-field select {
            width: 100%;
            height: 38px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background-color: #ffffff;
            color: #111827;
            padding: 0 10px;
            font-size: 12px;
        }

        .sglgif-filter-field--search {
            grid-column: span 2;
        }

        .sglgif-filter-actions {
            display: flex;
            align-items: end;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .sglgif-action-btn {
            min-height: 38px;
            min-width: 148px;
            border-radius: 10px;
            color: #ffffff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            padding: 0 14px;
            border: none;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .sglgif-action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
        }

        .sglgif-action-btn--primary {
            background: linear-gradient(135deg, #0f4fa8, #082f76);
        }

        .sglgif-action-btn--muted {
            background: linear-gradient(135deg, #64748b, #475569);
        }

        .sglgif-action-btn--accent {
            background: linear-gradient(135deg, #0f766e, #115e59);
        }

        .sglgif-card,
        .sglgif-hero-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            padding: 20px;
        }

        .sglgif-hero-card {
            background:
                radial-gradient(circle at top left, rgba(255, 215, 128, 0.28), transparent 34%),
                linear-gradient(135deg, #082f49 0%, #0f172a 48%, #7c2d12 100%);
            color: #fff7ed;
            padding: 24px;
        }

        .sglgif-kicker {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: #fde68a;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .sglgif-hero-copy h2 {
            margin: 12px 0 10px;
            font-size: 28px;
            line-height: 1.18;
            color: #ffffff;
        }

        .sglgif-hero-copy p {
            margin: 0;
            color: rgba(255, 247, 237, 0.88);
            font-size: 14px;
            line-height: 1.6;
        }

        .sglgif-hero-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 20px;
        }

        .sglgif-hero-tile {
            display: block;
            padding: 16px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: inherit;
            text-decoration: none;
            transition: transform 0.18s ease, background 0.18s ease;
        }

        .sglgif-hero-tile:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.14);
        }

        .sglgif-hero-tile span,
        .sglgif-mini-stat span,
        .sglgif-financial-tile span,
        .sglgif-alert-card span,
        .sglgif-risk-tile span {
            display: block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .sglgif-hero-tile span {
            color: #fde68a;
            margin-bottom: 8px;
        }

        .sglgif-hero-tile strong {
            display: block;
            font-size: 24px;
            line-height: 1.15;
            color: #ffffff;
        }

        .sglgif-hero-tile small {
            display: block;
            margin-top: 8px;
            font-size: 12px;
            color: rgba(255, 247, 237, 0.8);
            line-height: 1.45;
        }

        .sglgif-hero-ribbon {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .sglgif-hero-ribbon span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff7ed;
            font-size: 12px;
            font-weight: 600;
        }

        .sglgif-card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .sglgif-card-head h2 {
            margin: 0;
            color: #0f172a;
            font-size: 16px;
            font-weight: 800;
        }

        .sglgif-card-head p {
            margin: 6px 0 0;
            color: #475569;
            font-size: 12px;
            line-height: 1.55;
        }

        .sglgif-stat-strip,
        .sglgif-alert-grid,
        .sglgif-risk-grid,
        .sglgif-status-grid,
        .sglgif-financial-summary {
            display: grid;
            gap: 12px;
        }

        .sglgif-stat-strip,
        .sglgif-alert-grid,
        .sglgif-risk-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .sglgif-status-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sglgif-financial-summary {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-top: 16px;
        }

        .sglgif-mini-stat,
        .sglgif-alert-card,
        .sglgif-financial-tile,
        .sglgif-risk-tile {
            padding: 14px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .sglgif-mini-stat strong,
        .sglgif-alert-card strong,
        .sglgif-financial-tile strong,
        .sglgif-risk-tile strong {
            display: block;
            margin-top: 10px;
            color: #0f172a;
            font-size: 24px;
            line-height: 1.12;
        }

        .sglgif-mini-stat span {
            color: #475569;
        }

        .sglgif-dual-panel {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .sglgif-panel {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px;
            background: #fbfdff;
        }

        .sglgif-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }

        .sglgif-panel-head h3 {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .sglgif-switch {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .sglgif-switch button {
            border: none;
            background: transparent;
            color: #1e3a8a;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
        }

        .sglgif-switch button.is-active {
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            color: #ffffff;
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.25);
        }

        .sglgif-switch-panel {
            display: none;
        }

        .sglgif-switch-panel.is-active {
            display: block;
        }

        .sglgif-bar-row {
            margin-bottom: 10px;
        }

        .sglgif-bar-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 4px;
            color: #334155;
            font-size: 12px;
        }

        .sglgif-bar-head strong {
            color: #0f172a;
        }

        .sglgif-bar-track {
            height: 8px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .sglgif-bar-track > div {
            height: 100%;
            border-radius: 999px;
        }

        .sglgif-note,
        .sglgif-subline {
            margin-top: 4px;
            color: #64748b;
            font-size: 11px;
        }

        .sglgif-gauge-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .sglgif-gauge-card {
            text-align: center;
            padding: 16px 12px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #fbfdff;
        }

        .sglgif-gauge {
            --p: 0;
            --c: #2563eb;
            width: 126px;
            height: 126px;
            margin: 0 auto 12px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background:
                radial-gradient(closest-side, #ffffff 69%, transparent 71% 100%),
                conic-gradient(var(--c) calc(var(--p) * 1%), #e2e8f0 0);
        }

        .sglgif-gauge span {
            color: #0f172a;
            font-size: 19px;
            font-weight: 800;
        }

        .sglgif-gauge-card h3 {
            margin: 0 0 6px;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .sglgif-gauge-card p,
        .sglgif-risk-tile small {
            margin: 0;
            color: #475569;
            font-size: 12px;
            line-height: 1.55;
        }

        .sglgif-alert-card {
            background: linear-gradient(180deg, #fffaf0 0%, #ffffff 100%);
        }

        .sglgif-alert-card span {
            color: #92400e;
        }

        .sglgif-alert-card strong {
            color: #7c2d12;
        }

        .sglgif-status-tile {
            display: block;
            padding: 16px;
            border-radius: 14px;
            border: 1px solid var(--status-border);
            background: var(--status-bg);
            color: var(--status-color);
            text-decoration: none;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .sglgif-status-tile:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
        }

        .sglgif-status-tile span {
            display: block;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .sglgif-status-tile strong {
            display: block;
            margin-top: 10px;
            font-size: 28px;
            line-height: 1.1;
        }

        .sglgif-risk-tile {
            background: var(--risk-bg);
            border-color: transparent;
        }

        .sglgif-risk-tile span {
            color: var(--risk-color);
        }

        .sglgif-risk-tile strong {
            color: #0f172a;
        }

        .sglgif-table-wrap {
            overflow-x: auto;
        }

        .sglgif-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .sglgif-table th,
        .sglgif-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            vertical-align: top;
            color: #334155;
        }

        .sglgif-table th {
            color: #0f172a;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            background: #f8fafc;
        }

        .sglgif-empty,
        .sglgif-empty-cell {
            color: #64748b;
            font-size: 12px;
        }

        .sglgif-empty-cell {
            text-align: center;
            padding: 16px 10px;
        }

        @media (max-width: 1280px) {
            .dashboard-main-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 1080px) {
            .sglgif-filter-grid,
            .sglgif-hero-metrics,
            .sglgif-stat-strip,
            .sglgif-alert-grid,
            .sglgif-risk-grid,
            .sglgif-gauge-grid,
            .sglgif-financial-summary,
            .sglgif-dual-panel {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

        }

        @media (max-width: 760px) {
            .sglgif-filter-grid,
            .sglgif-hero-metrics,
            .sglgif-stat-strip,
            .sglgif-alert-grid,
            .sglgif-risk-grid,
            .sglgif-status-grid,
            .sglgif-gauge-grid,
            .sglgif-financial-summary,
            .sglgif-dual-panel {
                grid-template-columns: 1fr;
            }

            .sglgif-filter-field--search {
                grid-column: span 1;
            }

            .sglgif-filter-actions {
                justify-content: stretch;
            }

            .sglgif-action-btn {
                width: 100%;
            }

            .sglgif-hero-copy h2 {
                font-size: 23px;
            }

            .sglgif-gauge {
                width: 112px;
                height: 112px;
            }
        }
    </style>

    <script>
        function toggleProjectFilter(button) {
            const form = button.closest('.project-filter-form');
            if (!form) {
                return;
            }

            const body = form.querySelector('.project-filter-body');
            if (!body) {
                return;
            }

            const isCollapsed = form.classList.contains('collapsed');
            if (isCollapsed) {
                form.classList.remove('collapsed');
                requestAnimationFrame(() => {
                    body.style.maxHeight = `${body.scrollHeight}px`;
                });
            } else {
                body.style.maxHeight = `${body.scrollHeight}px`;
                requestAnimationFrame(() => {
                    form.classList.add('collapsed');
                    body.style.maxHeight = '0px';
                });
            }

            button.setAttribute('aria-expanded', isCollapsed ? 'true' : 'false');
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-sg-switch-group]').forEach((switchGroup) => {
                const groupName = switchGroup.getAttribute('data-sg-switch-group');
                const buttons = Array.from(switchGroup.querySelectorAll('[data-sg-switch-target]'));
                const panels = Array.from(document.querySelectorAll(`[data-sg-switch-panel^="${groupName}:"]`));

                buttons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const target = button.getAttribute('data-sg-switch-target');

                        buttons.forEach((item) => item.classList.toggle('is-active', item === button));
                        panels.forEach((panel) => {
                            panel.classList.toggle('is-active', panel.getAttribute('data-sg-switch-panel') === `${groupName}:${target}`);
                        });
                    });
                });
            });
        });
    </script>
@endsection
