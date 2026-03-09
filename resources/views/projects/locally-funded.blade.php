@extends('layouts.dashboard')

@section('title', 'Locally Funded Projects')
@section('page-title', 'Locally Funded Projects')

@section('content')
    <div class="content-header">
        <h1>Locally Funded Projects</h1>
        <p>Manage and review locally funded project records.</p>
    </div>

    <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Header with Create Button -->
        <div class="projects-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="color: #002C76; font-size: 18px; margin: 0;">Projects</h2>
        </div>

        @php
            $activeFilters = array_merge([
                'search' => '',
                'project_code' => '',
                'funding_year' => '',
                'fund_source' => '',
                'province' => '',
                'city' => '',
                'procurement' => '',
                'status' => '',
                'project_update_status' => '',
            ], $filters ?? []);

            $selectedProvinceFilter = trim((string) ($activeFilters['province'] ?? ''));
            if ($selectedProvinceFilter !== '' && array_key_exists($selectedProvinceFilter, $provinceMunicipalities)) {
                $cityOptions = collect($provinceMunicipalities[$selectedProvinceFilter] ?? []);
            } else {
                $cityOptions = collect($provinceMunicipalities)->flatten(1);
            }

            $cityOptions = $cityOptions
                ->map(fn($city) => trim((string) $city))
                ->filter()
                ->unique()
                ->sort()
                ->values();
        @endphp

        <form id="lfp-filters-form" method="GET" action="{{ route('projects.locally-funded') }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; margin-bottom: 16px;">
            <input type="hidden" name="sort_by" value="{{ $sortBy ?? 'funding_year' }}">
            <input type="hidden" name="sort_dir" value="{{ $sortDir ?? 'asc' }}">
            <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
            @if($activeFilters['project_code'] !== '')
                <input type="hidden" name="project_code" value="{{ $activeFilters['project_code'] }}">
            @endif
            @if($activeFilters['project_update_status'] !== '')
                <input type="hidden" name="project_update_status" value="{{ $activeFilters['project_update_status'] }}">
            @endif
            <div style="min-width: 220px; flex: 1;">
                <label for="lfp-search" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Search</label>
                <input id="lfp-search" name="search" type="text" value="{{ $activeFilters['search'] }}" placeholder="Search project code, title, province, fund source..." style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
            </div>
            <div style="min-width: 150px;">
                <label for="filter-year" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Funding Year</label>
                <select id="filter-year" name="funding_year" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                    <option value="">All</option>
                    @foreach($fundingYears as $year)
                        <option value="{{ $year }}" {{ (string) $activeFilters['funding_year'] === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 160px;">
                <label for="filter-fund-source" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Fund Source</label>
                <select id="filter-fund-source" name="fund_source" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                    <option value="">All</option>
                    @foreach($fundSources as $source)
                        <option value="{{ $source }}" {{ (string) $activeFilters['fund_source'] === (string) $source ? 'selected' : '' }}>{{ $source }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 170px;">
                <label for="filter-province" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Province</label>
                <select id="filter-province" name="province" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                    <option value="">All</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" {{ (string) $activeFilters['province'] === (string) $province ? 'selected' : '' }}>{{ $province }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 170px;">
                <label for="filter-city" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">City/Mun</label>
                <select id="filter-city" name="city" data-selected-city="{{ $activeFilters['city'] }}" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                    <option value="">All</option>
                    @foreach($cityOptions as $city)
                        <option value="{{ $city }}" {{ (string) $activeFilters['city'] === (string) $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 170px;">
                <label for="filter-procurement" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Procurement Type</label>
                <select id="filter-procurement" name="procurement" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                    <option value="">All</option>
                    @foreach($procurementTypes as $type)
                        <option value="{{ $type }}" {{ (string) $activeFilters['procurement'] === (string) $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 170px;">
                <label for="filter-status" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Status</label>
                <select id="filter-status" name="status" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                    <option value="">All</option>
                    @foreach($statusOptions as $status)
                        <option value="{{ $status }}" {{ (string) $activeFilters['status'] === (string) $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <a href="{{ route('projects.locally-funded', ['sort_by' => $sortBy ?? 'funding_year', 'sort_dir' => $sortDir ?? 'asc', 'per_page' => $perPage ?? 10]) }}" style="padding: 8px 12px; background-color: #6b7280; color: white; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none;">
                Clear
            </a>
        </form>

        @if($projects->isEmpty())
            @if(Auth::user()->agency === 'DILG' && Auth::user()->province === 'Regional Office')
            <p style="margin: 0; color: #6b7280; text-align: center; padding: 40px 0;">No projects found. <a href="{{ route('locally-funded-project.create') }}" style="color: #002C76; text-decoration: none; font-weight: 600;">Create one now</a></p>
            @else
            <p style="margin: 0; color: #6b7280; text-align: center; padding: 40px 0;">No projects found.</p>
            @endif
        @else
            @php
                $currentSortBy = $sortBy ?? request('sort_by', 'default');
                $currentSortDir = $sortDir ?? request('sort_dir', 'asc');
                $currentSortDir = strtolower((string) $currentSortDir) === 'desc' ? 'desc' : 'asc';

                $nextSortDirection = function (string $column, string $defaultDirection = 'asc') use ($currentSortBy, $currentSortDir): string {
                    if ($currentSortBy === $column) {
                        return $currentSortDir === 'asc' ? 'desc' : 'asc';
                    }

                    return $defaultDirection;
                };

                $sortIndicator = function (string $column) use ($currentSortBy, $currentSortDir): string {
                    if ($currentSortBy !== $column) {
                        return '';
                    }

                    return $currentSortDir === 'asc' ? '▲' : '▼';
                };

                $sortUrl = function (string $column, string $defaultDirection = 'asc') use ($nextSortDirection): string {
                    $query = array_merge(request()->query(), [
                        'sort_by' => $column,
                        'sort_dir' => $nextSortDirection($column, $defaultDirection),
                    ]);
                    unset($query['page']);

                    return route('projects.locally-funded', $query);
                };
            @endphp
            <div class="lfp-table-wrap" role="region" aria-label="Locally Funded Projects table" tabindex="0">
                <table id="lfp-table" style="width: 100%; border-collapse: collapse; font-size: 12px; table-layout: fixed;">
                    <thead>
                        <tr style="background-color: #f3f4f6; border-bottom: 2px solid #d1d5db;">
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('project_code') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: flex-start;">
                                    <span>Project Code</span><span class="lfp-sort-indicator">{{ $sortIndicator('project_code') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; min-width: 240px;">
                                <a href="{{ $sortUrl('project_title') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: flex-start;">
                                    <span>Project Title</span><span class="lfp-sort-indicator">{{ $sortIndicator('project_title') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; min-width: 260px;">
                                <a href="{{ $sortUrl('location') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: flex-start;">
                                    <span>Location</span><span class="lfp-sort-indicator">{{ $sortIndicator('location') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('funding_year', 'desc') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Funding Year</span><span class="lfp-sort-indicator">{{ $sortIndicator('funding_year') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('fund_source') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Fund Source</span><span class="lfp-sort-indicator">{{ $sortIndicator('fund_source') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('procurement') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Procurement Type</span><span class="lfp-sort-indicator">{{ $sortIndicator('procurement') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('lgsf_allocation', 'desc') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>LGSF Allocation</span><span class="lfp-sort-indicator">{{ $sortIndicator('lgsf_allocation') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('obligation', 'desc') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Obligation</span><span class="lfp-sort-indicator">{{ $sortIndicator('obligation') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('utilization_rate', 'desc') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Utilization Rate</span><span class="lfp-sort-indicator">{{ $sortIndicator('utilization_rate') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('physical_subaybayan', 'desc') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Physical Status (Subaybayan %)</span><span class="lfp-sort-indicator">{{ $sortIndicator('physical_subaybayan') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('status_actual') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Status (Actual)</span><span class="lfp-sort-indicator">{{ $sortIndicator('status_actual') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('status_subaybayan') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Status (Subaybayan)</span><span class="lfp-sort-indicator">{{ $sortIndicator('status_subaybayan') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('last_updated', 'desc') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Last Updated At</span><span class="lfp-sort-indicator">{{ $sortIndicator('last_updated') }}</span>
                                </a>
                            </th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                            @php
                                $lfpId = $project->lfp_id ?? null;
                                $statusActual = $lfpId && isset($physicalStatuses[$lfpId]['status_actual'])
                                    ? $physicalStatuses[$lfpId]['status_actual']
                                    : 'Pending';
                                $statusSubaybayan = $lfpId && isset($physicalStatuses[$lfpId]['status_subaybayan'])
                                    ? $physicalStatuses[$lfpId]['status_subaybayan']
                                    : ($project->status_subaybayan ?? 'Pending');
                                $subayAccomplishment = $lfpId && isset($physicalStatuses[$lfpId]['accomplishment_pct_ro'])
                                    ? $physicalStatuses[$lfpId]['accomplishment_pct_ro']
                                    : ($project->subay_accomplishment_pct ?? null);
                            @endphp
                            @php
                                $hasLfp = !empty($lfpId);
                                $viewUrl = $hasLfp
                                    ? route('locally-funded-project.show', $lfpId)
                                    : route('locally-funded-project.ensure', $project->subaybayan_project_code);
                            @endphp
                            <tr
                                style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s ease; cursor: pointer;"
                                onmouseover="this.style.backgroundColor='#f9fafb'"
                                onmouseout="this.style.backgroundColor='white'"
                                data-project-code="{{ e($project->subaybayan_project_code) }}"
                                data-project-title="{{ e($project->project_name) }}"
                                data-province="{{ e($project->province) }}"
                                data-city="{{ e($project->city_municipality) }}"
                                data-barangay="{{ e($project->barangay) }}"
                                data-location-sort="{{ e(trim(($project->province ?? '') . ' ' . ($project->city_municipality ?? '') . ' ' . ($project->barangay ?? ''))) }}"
                                data-fund-source="{{ e($project->fund_source) }}"
                                data-funding-year="{{ e($project->funding_year) }}"
                                data-procurement="{{ e($project->mode_of_procurement) }}"
                                data-lgsf-allocation="{{ $project->lgsf_allocation !== null ? (float) $project->lgsf_allocation : '' }}"
                                data-utilization-rate="{{ $project->utilization_rate !== null ? (float) $project->utilization_rate : '' }}"
                                data-physical-subaybayan="{{ $subayAccomplishment !== null ? (float) $subayAccomplishment : '' }}"
                                data-status-actual="{{ e($statusActual) }}"
                                data-status-subaybayan="{{ e($statusSubaybayan) }}"
                                data-last-updated-ts="{{ $project->updated_at ? $project->updated_at->timestamp : '' }}"
                            >
                                <td style="padding: 12px; color: #374151; font-weight: 500;">{{ $project->subaybayan_project_code }}</td>
                                <td style="padding: 12px; color: #374151; min-width: 240px;">
                                    <span class="wrap-text" title="{{ $project->project_name }}" style="display: block; max-width: 240px; white-space: normal; overflow-wrap: anywhere; word-break: break-word;">
                                        {{ $project->project_name }}
                                    </span>
                                </td>
                                <td style="padding: 12px; color: #374151; min-width: 260px;">
                                    <div class="wrap-text" style="font-size: 12px; line-height: 1.4; white-space: normal; max-width: 260px;">
                                        <strong>Province:</strong> {{ $project->province }}<br>
                                        <strong>City/Mun:</strong> {{ $project->city_municipality }}<br>
                                        @php
                                            $barangays = array_filter(array_map('trim', explode(',', (string) $project->barangay)));
                                        @endphp
                                        <strong>Barangay:</strong>
                                        @if(count($barangays))
                                            <ul style="margin: 4px 0 0 16px; padding: 0;">
                                                @foreach($barangays as $barangay)
                                                    <li style="margin: 0; list-style: disc;">{{ $barangay }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 12px; color: #374151; text-align: center;">{{ $project->funding_year }}</td>
                                <td style="padding: 12px; color: #374151; text-align: center;">{{ $project->fund_source }}</td>
                                <td style="padding: 12px; color: #374151; text-align: center;">{{ $project->mode_of_procurement }}</td>
                                <td style="padding: 12px; color: #374151; text-align: center;">
                                    @if($project->lgsf_allocation !== null)
                                        ₱ {{ number_format($project->lgsf_allocation, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="padding: 12px; color: #374151; text-align: center;">
                                    @if($project->obligation !== null)
                                        ₱ {{ number_format($project->obligation, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="padding: 12px; color: #374151; text-align: center;">
                                    @if($project->utilization_rate !== null)
                                        @php $utilizationRate = (float) $project->utilization_rate; @endphp
                                        <span style="color: {{ $utilizationRate < 100 ? '#dc2626' : '#374151' }};">
                                            {{ number_format($utilizationRate, 2) . '%' }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="padding: 12px; color: #374151; text-align: center;">
                                    {{ $subayAccomplishment !== null
                                        ? number_format((float) $subayAccomplishment, 2) . '%'
                                        : '-' }}
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 8px; background-color: #dbeafe; color: #0369a1; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                        {{ $statusActual }}
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 8px; background-color: #dbeafe; color: #0369a1; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                        {{ $statusSubaybayan }}
                                    </span>
                                </td>
                                <td style="padding: 12px; color: #374151; text-align: center;">
                                    @if($project->updated_at)
                                        {{ $project->updated_at->format('Y-m-d') }}<br>
                                        <span style="font-size: 11px; color: #6b7280;">{{ $project->updated_at->format('h:i A') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        <a href="{{ $viewUrl }}" style="padding: 6px 10px; background-color: #0369a1; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 11px; text-decoration: none; transition: background-color 0.2s ease;" onmouseover="this.style.backgroundColor='#0c4a6e'" onmouseout="this.style.backgroundColor='#0369a1'">View</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($projects->hasPages())
                <div style="margin-top: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <div style="font-size: 12px; color: #6b7280;">
                            Page {{ $projects->currentPage() }} of {{ $projects->lastPage() }} ·
                            Showing {{ $projects->firstItem() ?? 0 }}–{{ $projects->lastItem() ?? 0 }} of {{ $projects->total() }}
                        </div>
                        <form method="GET" action="{{ route('projects.locally-funded') }}" style="display: inline-flex; align-items: center;">
                            <input type="hidden" name="search" value="{{ $activeFilters['search'] ?? '' }}">
                            <input type="hidden" name="project_code" value="{{ $activeFilters['project_code'] ?? '' }}">
                            <input type="hidden" name="funding_year" value="{{ $activeFilters['funding_year'] ?? '' }}">
                            <input type="hidden" name="fund_source" value="{{ $activeFilters['fund_source'] ?? '' }}">
                            <input type="hidden" name="province" value="{{ $activeFilters['province'] ?? '' }}">
                            <input type="hidden" name="city" value="{{ $activeFilters['city'] ?? '' }}">
                            <input type="hidden" name="procurement" value="{{ $activeFilters['procurement'] ?? '' }}">
                            <input type="hidden" name="status" value="{{ $activeFilters['status'] ?? '' }}">
                            <input type="hidden" name="sort_by" value="{{ $currentSortBy }}">
                            <input type="hidden" name="sort_dir" value="{{ $currentSortDir }}">
                            <select id="per-page" name="per_page" onchange="this.form.submit()" aria-label="Rows per page" title="Rows per page" style="padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                                @foreach([10, 15, 25, 50] as $option)
                                    <option value="{{ $option }}" {{ ($perPage ?? 10) == $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 8px; flex-wrap: wrap;">
                        @if($projects->onFirstPage())
                            <span style="padding: 8px 12px; background-color: #e5e7eb; color: #9ca3af; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-chevron-left"></i> Back
                            </span>
                        @else
                            <a href="{{ $projects->previousPageUrl() }}" style="padding: 8px 12px; background-color: #ffffff; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                <i class="fas fa-chevron-left"></i> Back
                            </a>
                        @endif

                        @if($projects->hasMorePages())
                            <a href="{{ $projects->nextPageUrl() }}" style="padding: 8px 12px; background-color: #002C76; color: white; border: 1px solid #002C76; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <span style="padding: 8px 12px; background-color: #e5e7eb; color: #9ca3af; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                Next <i class="fas fa-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
    <style>
        table td {
            vertical-align: top;
        }

        .lfp-table-wrap {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #ffffff;
        }

        #lfp-table {
            width: max-content !important;
            min-width: 2000px;
            table-layout: auto !important;
        }

        #lfp-table th,
        #lfp-table td {
            white-space: nowrap !important;
            word-break: normal !important;
            overflow-wrap: normal !important;
            padding: 8px !important;
        }

        #lfp-table th:nth-child(2),
        #lfp-table td:nth-child(2),
        #lfp-table th:nth-child(3),
        #lfp-table td:nth-child(3) {
            min-width: 320px;
            max-width: 420px;
            white-space: normal !important;
        }

        #lfp-table .wrap-text,
        #lfp-table td:nth-child(3) .wrap-text {
            white-space: normal;
        }

        .lfp-sort-link {
            border: none;
            padding: 0;
            margin: 0;
            font: inherit;
            color: inherit;
            cursor: pointer;
            text-decoration: none;
        }

        .lfp-sort-indicator {
            font-size: 10px;
            color: #6b7280;
            min-width: 10px;
            display: inline-block;
            text-align: center;
        }

                        .projects-header {
                            flex-wrap: wrap;
                            gap: 12px;
                        }

                        #lfp-table tbody tr {
                            cursor: pointer;
                        }

        @media (max-width: 1024px) {
            .projects-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 768px) {
            #lfp-filters-form > div {
                width: 100%;
                min-width: 0 !important;
                flex: 1 1 100%;
            }

            #lfp-filters-form > a {
                width: 100%;
                text-align: center;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .lfp-table-wrap {
                margin: 0 -8px;
                border-left: 0;
                border-right: 0;
                border-radius: 0;
            }

            #lfp-table {
                min-width: 1900px;
            }

            #lfp-table th,
            #lfp-table td {
                padding: 7px !important;
                font-size: 11px;
            }

            #lfp-table td .wrap-text {
                min-width: 260px;
            }
        }

        @media (max-width: 480px) {
            .content-header h1 {
                font-size: 20px;
            }

            .content-header p {
                font-size: 12px;
            }

            #lfp-table {
                min-width: 1800px;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filtersForm = document.getElementById('lfp-filters-form');
            const searchInput = document.getElementById('lfp-search');
            const provinceSelect = document.getElementById('filter-province');
            const citySelect = document.getElementById('filter-city');
            const yearSelect = document.getElementById('filter-year');
            const fundSourceSelect = document.getElementById('filter-fund-source');
            const procurementSelect = document.getElementById('filter-procurement');
            const statusSelect = document.getElementById('filter-status');
            const locationData = @json($provinceMunicipalities);
            const selectedCity = citySelect ? (citySelect.dataset.selectedCity || '') : '';
            let searchTimer = null;

            if (!filtersForm || !provinceSelect || !citySelect) {
                return;
            }

            const allCities = new Set();
            Object.values(locationData).forEach(function (cities) {
                if (!Array.isArray(cities)) {
                    return;
                }
                cities.forEach(function (city) {
                    allCities.add(city);
                });
            });

            function populateCityOptions(selectedProvince, preferredValue) {
                const currentValue = preferredValue || citySelect.value || '';

                citySelect.innerHTML = '';
                const allOption = document.createElement('option');
                allOption.value = '';
                allOption.textContent = 'All';
                citySelect.appendChild(allOption);

                const cities = selectedProvince && Array.isArray(locationData[selectedProvince])
                    ? locationData[selectedProvince]
                    : Array.from(allCities);

                cities.sort().forEach(function (city) {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    citySelect.appendChild(option);
                });

                if (currentValue && cities.includes(currentValue)) {
                    citySelect.value = currentValue;
                }
            }

            function submitFilters() {
                filtersForm.requestSubmit();
            }

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(submitFilters, 450);
                });
            }

            provinceSelect.addEventListener('change', function () {
                populateCityOptions(this.value);
                citySelect.value = '';
                submitFilters();
            });

            [yearSelect, fundSourceSelect, citySelect, procurementSelect, statusSelect]
                .filter(Boolean)
                .forEach(function (select) {
                    select.addEventListener('change', submitFilters);
                });

            populateCityOptions(provinceSelect.value, selectedCity);
        });
    </script>
@endsection
