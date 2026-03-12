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

            $columnToggleOptions = [
                'funding_year' => 'Funding Year',
                'fund_source' => 'Fund Source',
                'procurement_type' => 'Procurement Type',
                'lgsf_allocation' => 'LGSF Allocation',
                'obligation' => 'Obligation',
                'utilization_rate' => 'Utilization Rate',
                'physical_status_subaybayan' => 'Physical Status (Subaybayan %)',
                'status_actual' => 'Status (Actual)',
                'status_subaybayan' => 'Status (Subaybayan)',
                'last_updated_at' => 'Last Updated At',
            ];
        @endphp

        <details id="lfp-filters-panel" class="lfp-filters-panel" open>
            <summary class="lfp-filters-summary">
                <span>Filters</span>
                <span class="lfp-filters-summary-icon" aria-hidden="true"></span>
            </summary>
            <div class="lfp-filters-body">
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

                <div class="lfp-column-toggle-panel" aria-label="Table columns filter">
                    <div class="lfp-column-toggle-header">
                        <div class="lfp-column-toggle-label">Visible Columns</div>
                        <label class="lfp-column-toggle-option lfp-column-toggle-option--master">
                            <input type="checkbox" id="lfp-column-toggle-all" checked>
                            <span>Select All</span>
                        </label>
                    </div>
                    <div class="lfp-column-toggle-grid">
                        @foreach($columnToggleOptions as $columnKey => $columnLabel)
                            <label class="lfp-column-toggle-option">
                                <input type="checkbox" class="lfp-column-toggle-checkbox" data-column-toggle="{{ $columnKey }}" checked>
                                <span>{{ $columnLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </details>

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
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; min-width: 220px;">
                                <a href="{{ $sortUrl('location') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: flex-start;">
                                    <span>Location</span><span class="lfp-sort-indicator">{{ $sortIndicator('location') }}</span>
                                </a>
                            </th>
                            <th data-column-key="funding_year" style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('funding_year', 'desc') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Funding Year</span><span class="lfp-sort-indicator">{{ $sortIndicator('funding_year') }}</span>
                                </a>
                            </th>
                            <th data-column-key="fund_source" style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('fund_source') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Fund Source</span><span class="lfp-sort-indicator">{{ $sortIndicator('fund_source') }}</span>
                                </a>
                            </th>
                            <th data-column-key="procurement_type" style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('procurement') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Procurement Type</span><span class="lfp-sort-indicator">{{ $sortIndicator('procurement') }}</span>
                                </a>
                            </th>
                            <th data-column-key="lgsf_allocation" style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('lgsf_allocation', 'desc') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>LGSF Allocation</span><span class="lfp-sort-indicator">{{ $sortIndicator('lgsf_allocation') }}</span>
                                </a>
                            </th>
                            <th data-column-key="obligation" style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('obligation', 'desc') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Obligation</span><span class="lfp-sort-indicator">{{ $sortIndicator('obligation') }}</span>
                                </a>
                            </th>
                            <th data-column-key="utilization_rate" style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('utilization_rate', 'desc') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Utilization Rate</span><span class="lfp-sort-indicator">{{ $sortIndicator('utilization_rate') }}</span>
                                </a>
                            </th>
                            <th data-column-key="physical_status_subaybayan" style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('physical_subaybayan', 'desc') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Physical Status (Subaybayan %)</span><span class="lfp-sort-indicator">{{ $sortIndicator('physical_subaybayan') }}</span>
                                </a>
                            </th>
                            <th data-column-key="status_actual" style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('status_actual') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Status (Actual)</span><span class="lfp-sort-indicator">{{ $sortIndicator('status_actual') }}</span>
                                </a>
                            </th>
                            <th data-column-key="status_subaybayan" style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
                                <a href="{{ $sortUrl('status_subaybayan') }}" class="lfp-sort-link" style="display: flex; align-items: center; gap: 4px; width: 100%; justify-content: center;">
                                    <span>Status (Subaybayan)</span><span class="lfp-sort-indicator">{{ $sortIndicator('status_subaybayan') }}</span>
                                </a>
                            </th>
                            <th data-column-key="last_updated_at" style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">
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
                                <td style="padding: 12px; color: #374151; min-width: 220px;">
                                    <div class="wrap-text" style="font-size: 12px; line-height: 1.4; white-space: normal; max-width: 220px;">
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
                                <td data-column-key="funding_year" style="padding: 12px; color: #374151; text-align: center;">{{ $project->funding_year }}</td>
                                <td data-column-key="fund_source" style="padding: 12px; color: #374151; text-align: center;">{{ $project->fund_source }}</td>
                                <td data-column-key="procurement_type" style="padding: 12px; color: #374151; text-align: center;">{{ $project->mode_of_procurement }}</td>
                                <td data-column-key="lgsf_allocation" style="padding: 12px; color: #374151; text-align: center;">
                                    @if($project->lgsf_allocation !== null)
                                        ₱ {{ number_format($project->lgsf_allocation, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td data-column-key="obligation" style="padding: 12px; color: #374151; text-align: center;">
                                    @if($project->obligation !== null)
                                        ₱ {{ number_format($project->obligation, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td data-column-key="utilization_rate" style="padding: 12px; color: #374151; text-align: center;">
                                    @if($project->utilization_rate !== null)
                                        @php $utilizationRate = (float) $project->utilization_rate; @endphp
                                        <span style="color: {{ $utilizationRate < 100 ? '#dc2626' : '#374151' }};">
                                            {{ number_format($utilizationRate, 2) . '%' }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td data-column-key="physical_status_subaybayan" style="padding: 12px; color: #374151; text-align: center;">
                                    {{ $subayAccomplishment !== null
                                        ? number_format((float) $subayAccomplishment, 2) . '%'
                                        : '-' }}
                                </td>
                                <td data-column-key="status_actual" style="padding: 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 8px; background-color: #dbeafe; color: #0369a1; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                        {{ $statusActual }}
                                    </span>
                                </td>
                                <td data-column-key="status_subaybayan" style="padding: 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 8px; background-color: #dbeafe; color: #0369a1; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                        {{ $statusSubaybayan }}
                                    </span>
                                </td>
                                <td data-column-key="last_updated_at" style="padding: 12px; color: #374151; text-align: center;">
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
            <div class="lfp-mobile-cards" aria-label="Locally Funded Projects cards">
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
                        $hasLfp = !empty($lfpId);
                        $viewUrl = $hasLfp
                            ? route('locally-funded-project.show', $lfpId)
                            : route('locally-funded-project.ensure', $project->subaybayan_project_code);
                        $barangays = array_filter(array_map('trim', explode(',', (string) $project->barangay)));
                    @endphp
                    <details class="lfp-mobile-card">
                        <summary class="lfp-mobile-card-summary">
                            <div class="lfp-mobile-card-summary-main">
                                <div class="lfp-mobile-card-code">{{ $project->subaybayan_project_code }}</div>
                                <h3 class="lfp-mobile-card-title">{{ $project->project_name }}</h3>
                            </div>
                            <span class="lfp-mobile-card-chevron" aria-hidden="true"></span>
                        </summary>

                        <div class="lfp-mobile-card-body">
                            <div class="lfp-mobile-card-body-inner">
                                <div class="lfp-mobile-card-actions">
                                    <a href="{{ $viewUrl }}" class="lfp-mobile-card-action">View</a>
                                </div>

                                <div class="lfp-mobile-card-section">
                                    <div class="lfp-mobile-card-section-label">Location</div>
                                    <div class="lfp-mobile-card-location">
                                        <div><strong>Province:</strong> {{ $project->province }}</div>
                                        <div><strong>City/Mun:</strong> {{ $project->city_municipality }}</div>
                                        <div>
                                            <strong>Barangay:</strong>
                                            @if(count($barangays))
                                                <ul class="lfp-mobile-card-list">
                                                    @foreach($barangays as $barangay)
                                                        <li>{{ $barangay }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span>-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="lfp-mobile-card-details">
                                    <div class="lfp-mobile-card-detail" data-column-key="funding_year">
                                        <span class="lfp-mobile-card-detail-label">Funding Year</span>
                                        <strong>{{ $project->funding_year ?: '-' }}</strong>
                                    </div>
                                    <div class="lfp-mobile-card-detail" data-column-key="fund_source">
                                        <span class="lfp-mobile-card-detail-label">Fund Source</span>
                                        <strong>{{ $project->fund_source ?: '-' }}</strong>
                                    </div>
                                    <div class="lfp-mobile-card-detail" data-column-key="procurement_type">
                                        <span class="lfp-mobile-card-detail-label">Procurement Type</span>
                                        <strong>{{ $project->mode_of_procurement ?: '-' }}</strong>
                                    </div>
                                    <div class="lfp-mobile-card-detail" data-column-key="lgsf_allocation">
                                        <span class="lfp-mobile-card-detail-label">LGSF Allocation</span>
                                        <strong>{{ $project->lgsf_allocation !== null ? '₱ ' . number_format($project->lgsf_allocation, 2) : '-' }}</strong>
                                    </div>
                                    <div class="lfp-mobile-card-detail" data-column-key="obligation">
                                        <span class="lfp-mobile-card-detail-label">Obligation</span>
                                        <strong>{{ $project->obligation !== null ? '₱ ' . number_format($project->obligation, 2) : '-' }}</strong>
                                    </div>
                                    <div class="lfp-mobile-card-detail" data-column-key="utilization_rate">
                                        <span class="lfp-mobile-card-detail-label">Utilization Rate</span>
                                        <strong>{{ $project->utilization_rate !== null ? number_format((float) $project->utilization_rate, 2) . '%' : '-' }}</strong>
                                    </div>
                                    <div class="lfp-mobile-card-detail" data-column-key="physical_status_subaybayan">
                                        <span class="lfp-mobile-card-detail-label">Physical Status (Subaybayan %)</span>
                                        <strong>{{ $subayAccomplishment !== null ? number_format((float) $subayAccomplishment, 2) . '%' : '-' }}</strong>
                                    </div>
                                    <div class="lfp-mobile-card-detail" data-column-key="status_actual">
                                        <span class="lfp-mobile-card-detail-label">Status (Actual)</span>
                                        <strong>{{ $statusActual }}</strong>
                                    </div>
                                    <div class="lfp-mobile-card-detail" data-column-key="status_subaybayan">
                                        <span class="lfp-mobile-card-detail-label">Status (Subaybayan)</span>
                                        <strong>{{ $statusSubaybayan }}</strong>
                                    </div>
                                    <div class="lfp-mobile-card-detail" data-column-key="last_updated_at">
                                        <span class="lfp-mobile-card-detail-label">Last Updated At</span>
                                        <strong>
                                            @if($project->updated_at)
                                                {{ $project->updated_at->format('Y-m-d h:i A') }}
                                            @else
                                                -
                                            @endif
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </details>
                @endforeach
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

        .lfp-filters-panel {
            margin-bottom: 16px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            background: #ffffff;
            overflow: hidden;
        }

        .lfp-filters-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            color: #1f2937;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            list-style: none;
            user-select: none;
        }

        .lfp-filters-summary::-webkit-details-marker {
            display: none;
        }

        .lfp-filters-summary-icon::before {
            content: '+';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 999px;
            background: #e5e7eb;
            color: #374151;
            font-size: 16px;
            line-height: 1;
        }

        .lfp-filters-panel[open] .lfp-filters-summary {
            border-bottom: 1px solid #e5e7eb;
        }

        .lfp-filters-panel[open] .lfp-filters-summary-icon::before {
            content: '-';
        }

        .lfp-filters-body {
            padding: 16px;
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

        .lfp-mobile-cards {
            display: none;
            gap: 12px;
        }

        .lfp-mobile-card {
            border: 1px solid #d1d5db;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .lfp-mobile-card-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 14px;
            cursor: pointer;
            list-style: none;
        }

        .lfp-mobile-card-summary::-webkit-details-marker {
            display: none;
        }

        .lfp-mobile-card-summary-main {
            min-width: 0;
        }

        .lfp-mobile-card-chevron {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 999px;
            background: #eff6ff;
            color: #0369a1;
            flex: 0 0 auto;
            transition: transform 0.25s ease;
        }

        .lfp-mobile-card-chevron::before {
            content: '+';
            font-size: 18px;
            line-height: 1;
        }

        .lfp-mobile-card-code {
            color: #1f2937;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.4;
        }

        .lfp-mobile-card-title {
            margin: 4px 0 0;
            color: #111827;
            font-size: 15px;
            line-height: 1.4;
        }

        .lfp-mobile-card[open] .lfp-mobile-card-chevron {
            transform: rotate(135deg);
        }

        .lfp-mobile-card-body {
            display: grid;
            grid-template-rows: 0fr;
            opacity: 0;
            transition: grid-template-rows 0.28s ease, opacity 0.22s ease;
        }

        .lfp-mobile-card[open] .lfp-mobile-card-body {
            grid-template-rows: 1fr;
            opacity: 1;
        }

        .lfp-mobile-card-body-inner {
            min-height: 0;
            overflow: hidden;
            padding: 0 14px 14px;
        }

        .lfp-mobile-card-actions {
            margin-bottom: 12px;
        }

        .lfp-mobile-card-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 8px;
            background: #0369a1;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
        }

        .lfp-mobile-card-section {
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
        }

        .lfp-mobile-card-section-label,
        .lfp-mobile-card-detail-label {
            display: block;
            margin-bottom: 4px;
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .lfp-mobile-card-location {
            color: #374151;
            font-size: 12px;
            line-height: 1.5;
        }

        .lfp-mobile-card-list {
            margin: 4px 0 0 16px;
            padding: 0;
        }

        .lfp-mobile-card-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 12px;
        }

        .lfp-mobile-card-detail {
            padding: 10px 12px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #111827;
            font-size: 12px;
            line-height: 1.4;
        }

        .lfp-mobile-card-detail strong {
            display: block;
            color: #111827;
            font-size: 13px;
            line-height: 1.5;
        }

        #lfp-table {
            width: max-content !important;
            min-width: 100%;
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
        #lfp-table td:nth-child(2) {
            min-width: 320px;
            max-width: 420px;
            white-space: normal !important;
        }

        #lfp-table th:nth-child(3),
        #lfp-table td:nth-child(3) {
            min-width: 220px;
            max-width: 280px;
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

        .lfp-column-toggle-panel {
            margin-bottom: 16px;
            padding: 14px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #f9fafb;
        }

        .lfp-column-toggle-label {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .lfp-column-toggle-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .lfp-column-toggle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px 16px;
        }

        .lfp-column-toggle-option {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #374151;
            cursor: pointer;
        }

        .lfp-column-toggle-option input {
            margin: 0;
        }

        .lfp-column-toggle-option--master {
            font-weight: 600;
        }

        #lfp-table [data-column-key].is-column-hidden {
            display: none;
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

            .lfp-column-toggle-header {
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

            .lfp-filters-summary,
            .lfp-filters-body {
                padding-left: 12px;
                padding-right: 12px;
            }

            .lfp-column-toggle-panel {
                display: none;
            }

            .lfp-table-wrap {
                display: none;
            }

            .lfp-mobile-cards {
                display: grid;
            }

            .lfp-mobile-card-summary {
                align-items: flex-start;
            }

            .lfp-mobile-card-action {
                width: 100%;
            }

            .lfp-mobile-card-details {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .content-header h1 {
                font-size: 20px;
            }

            .content-header p {
                font-size: 12px;
            }

            .lfp-mobile-card {
                border-radius: 10px;
            }

            .lfp-mobile-card-summary {
                padding: 12px;
            }

            .lfp-mobile-card-body-inner {
                padding: 0 12px 12px;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filtersPanel = document.getElementById('lfp-filters-panel');
            const filtersForm = document.getElementById('lfp-filters-form');
            const searchInput = document.getElementById('lfp-search');
            const provinceSelect = document.getElementById('filter-province');
            const citySelect = document.getElementById('filter-city');
            const yearSelect = document.getElementById('filter-year');
            const fundSourceSelect = document.getElementById('filter-fund-source');
            const procurementSelect = document.getElementById('filter-procurement');
            const statusSelect = document.getElementById('filter-status');
            const selectAllColumnsToggle = document.getElementById('lfp-column-toggle-all');
            const columnToggles = Array.from(document.querySelectorAll('.lfp-column-toggle-checkbox'));
            const locationData = @json($provinceMunicipalities);
            const columnToggleStorageKey = 'lfp-visible-columns';
            const selectedCity = citySelect ? (citySelect.dataset.selectedCity || '') : '';
            let searchTimer = null;

            if (filtersPanel && window.matchMedia('(max-width: 768px)').matches) {
                filtersPanel.removeAttribute('open');
            }

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

            function applyVisibleColumns(visibleColumns) {
                document.querySelectorAll('#lfp-table [data-column-key]').forEach(function (cell) {
                    const columnKey = cell.dataset.columnKey || '';
                    cell.classList.toggle('is-column-hidden', !visibleColumns.includes(columnKey));
                });
            }

            function syncVisibleColumns() {
                const visibleColumns = columnToggles
                    .filter(function (toggle) {
                        return toggle.checked;
                    })
                    .map(function (toggle) {
                        return toggle.dataset.columnToggle || '';
                    })
                    .filter(Boolean);

                applyVisibleColumns(visibleColumns);

                if (columnToggles.length > 0) {
                    localStorage.setItem(columnToggleStorageKey, JSON.stringify(visibleColumns));
                }

                if (selectAllColumnsToggle) {
                    const checkedCount = visibleColumns.length;
                    const totalCount = columnToggles.length;
                    selectAllColumnsToggle.checked = totalCount > 0 && checkedCount === totalCount;
                    selectAllColumnsToggle.indeterminate = checkedCount > 0 && checkedCount < totalCount;
                }
            }

            function initializeColumnToggles() {
                if (columnToggles.length === 0) {
                    return;
                }

                const storedColumnsRaw = localStorage.getItem(columnToggleStorageKey);
                let savedColumns = null;

                try {
                    savedColumns = JSON.parse(storedColumnsRaw || 'null');
                } catch (error) {
                    savedColumns = null;
                }

                const validColumns = Array.isArray(savedColumns)
                    ? savedColumns.filter(function (columnKey) {
                        return columnToggles.some(function (toggle) {
                            return toggle.dataset.columnToggle === columnKey;
                        });
                    })
                    : null;

                if (storedColumnsRaw !== null && validColumns) {
                    columnToggles.forEach(function (toggle) {
                        toggle.checked = validColumns.includes(toggle.dataset.columnToggle || '');
                    });
                }

                columnToggles.forEach(function (toggle) {
                    toggle.addEventListener('change', syncVisibleColumns);
                });

                if (selectAllColumnsToggle) {
                    selectAllColumnsToggle.addEventListener('change', function () {
                        columnToggles.forEach(function (toggle) {
                            toggle.checked = selectAllColumnsToggle.checked;
                        });

                        syncVisibleColumns();
                    });
                }

                syncVisibleColumns();
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
            initializeColumnToggles();
        });
    </script>
@endsection
