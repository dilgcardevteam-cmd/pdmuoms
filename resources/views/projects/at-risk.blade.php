@extends('layouts.dashboard')

@section('title', 'Project At Risk')
@section('page-title', 'Project At Risk')

@section('content')
    <div class="content-header">
        <h1>Project At Risk</h1>
        <p>Monitor projects flagged as at risk.</p>
    </div>

    @if (session('success'))
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 14px 16px; border-radius: 8px; margin-top: 16px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 16px; border-radius: 8px; margin-top: 16px;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 16px; border-radius: 8px; margin-top: 16px;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; margin-top: 16px;">
        <div>
            <h2 style="margin: 0 0 10px 0; font-size: 16px; color: #374151;">Risk as to Slippage</h2>
            <div style="display: grid; gap: 8px; color: #374151; font-size: 13px; line-height: 1.6;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 3px; background-color: #16a34a;"></span>
                    <span>Ahead (+ value of slippage)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 3px; background-color: #3b82f6;"></span>
                    <span>On Schedule (0%)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 3px; background-color: #0ea5e9;"></span>
                    <span>No Risk (-0.01% to -4.99% slippage)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 3px; background-color: #f59e0b;"></span>
                    <span>Low Risk (-5% to -9.99% slippage)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 3px; background-color: #f97316;"></span>
                    <span>Moderate Risk (-10% to -14.99% slippage)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 3px; background-color: #dc2626;"></span>
                    <span>High Risk (-15% and higher slippage)</span>
                </div>
            </div>
        </div>
    </div>

    <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 16px; overflow-x: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 12px;">
            <h2 style="color: #002C76; font-size: 18px; margin: 0;">Projects</h2>
        </div>
        <form method="GET" action="{{ route('projects.at-risk') }}" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; margin-bottom: 16px;">
            @php
                $selectedExtractionMonth = $filters['extraction_month'] ?? '';
                $selectedExtractionYear = $filters['extraction_year'] ?? '';
                $allExtractionMonth = $selectedExtractionMonth === 'all';
                $allExtractionYear = $selectedExtractionYear === 'all';
                if ($selectedExtractionMonth === '' || $selectedExtractionMonth === null) {
                    $selectedExtractionMonth = now()->month;
                }
                if ($selectedExtractionYear === '' || $selectedExtractionYear === null) {
                    $selectedExtractionYear = now()->year;
                }
            @endphp
            <div style="flex: 0 0 50%; max-width: 50%;">
                <div style="display: grid; grid-template-columns: repeat(4, minmax(120px, 1fr)); gap: 8px 12px; align-items: flex-end; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px;">
                    <div>
                        <label for="risk-filter-province" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Province</label>
                        <select id="risk-filter-province" name="province" style="width: 100%; padding: 5px 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 11px;">
                            <option value="">All</option>
                            @foreach(($filterOptions['provinces'] ?? []) as $provinceOption)
                                <option value="{{ $provinceOption }}" {{ ($filters['province'] ?? '') === $provinceOption ? 'selected' : '' }}>{{ $provinceOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="risk-filter-city" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">City/Municipality</label>
                        <select id="risk-filter-city" name="city_municipality" style="width: 100%; padding: 5px 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 11px;">
                            <option value="">All</option>
                            @foreach(($filterOptions['cities'] ?? []) as $cityOption)
                                <option value="{{ $cityOption }}" {{ ($filters['city_municipality'] ?? '') === $cityOption ? 'selected' : '' }}>{{ $cityOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="risk-filter-year" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Funding Year</label>
                        <select id="risk-filter-year" name="funding_year" style="width: 100%; padding: 5px 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 11px;">
                            <option value="">All</option>
                            @foreach(($filterOptions['funding_years'] ?? []) as $yearOption)
                                <option value="{{ $yearOption }}" {{ (string) ($filters['funding_year'] ?? '') === (string) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="risk-filter-program" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Program</label>
                        <select id="risk-filter-program" name="program" style="width: 100%; padding: 5px 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 11px;">
                            <option value="">All</option>
                            @foreach(($filterOptions['programs'] ?? []) as $programOption)
                                <option value="{{ $programOption }}" {{ ($filters['program'] ?? '') === $programOption ? 'selected' : '' }}>{{ $programOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="risk-filter-level" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Risk Level</label>
                        <select id="risk-filter-level" name="risk_level" style="width: 100%; padding: 5px 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 11px;">
                            <option value="">All</option>
                            @foreach(($filterOptions['risk_levels'] ?? []) as $riskOption)
                                <option value="{{ $riskOption }}" {{ ($filters['risk_level'] ?? '') === $riskOption ? 'selected' : '' }}>{{ $riskOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="risk-filter-aging" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Aging</label>
                        <select id="risk-filter-aging" name="aging_range" style="width: 100%; padding: 5px 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 11px;">
                            <option value="">All</option>
                            <option value="gt_30" {{ ($filters['aging_range'] ?? '') === 'gt_30' ? 'selected' : '' }}>Greater than 30</option>
                            <option value="between_11_30" {{ ($filters['aging_range'] ?? '') === 'between_11_30' ? 'selected' : '' }}>11 to 30</option>
                            <option value="lte_10" {{ ($filters['aging_range'] ?? '') === 'lte_10' ? 'selected' : '' }}>10 and below</option>
                        </select>
                    </div>
                    <div>
                        <label for="risk-filter-extraction-month" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Extraction Month</label>
                        <select id="risk-filter-extraction-month" name="extraction_month" style="width: 100%; padding: 5px 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 11px;">
                            <option value="all" {{ $allExtractionMonth ? 'selected' : '' }}>All</option>
                            @php
                                $monthNames = [
                                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                                ];
                            @endphp
                            @foreach(($filterOptions['extraction_months'] ?? []) as $monthOption)
                                @php $monthOption = (int) $monthOption; @endphp
                                <option value="{{ $monthOption }}" {{ !$allExtractionMonth && (int) $selectedExtractionMonth === $monthOption ? 'selected' : '' }}>
                                    {{ $monthNames[$monthOption] ?? $monthOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="risk-filter-extraction-year" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Extraction Year</label>
                        <select id="risk-filter-extraction-year" name="extraction_year" style="width: 100%; padding: 5px 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 11px;">
                            <option value="all" {{ $allExtractionYear ? 'selected' : '' }}>All</option>
                            @foreach(($filterOptions['extraction_years'] ?? []) as $yearOption)
                                <option value="{{ $yearOption }}" {{ !$allExtractionYear && (string) $selectedExtractionYear === (string) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="grid-column: 1 / -1; display: flex; gap: 8px; align-items: center; justify-content: space-between; margin-top: 4px;">
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <a id="risk-export" href="{{ route('projects.at-risk.export', request()->query()) }}" style="padding: 6px 10px; background-color: #15803d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 11px; text-decoration: none;">
                                Export Excel
                            </a>
                            <button type="button" onclick="openImportModal()" style="padding: 6px 10px; background-color: #002C76; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 11px;">
                                Import CSV
                            </button>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <button type="submit" style="padding: 8px 12px; background-color: #002C76; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">Apply</button>
                            <a href="{{ route('projects.at-risk') }}" style="padding: 8px 12px; background-color: #6b7280; color: white; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; text-align: center;">Reset</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <table id="project-at-risk-table" style="width: 100%; border-collapse: collapse; font-size: 12px; table-layout: fixed;">
            <thead>
                <tr style="background-color: #f3f4f6; border-bottom: 2px solid #d1d5db;">
                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">Project Code</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">LGU</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">Barangay/s</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">Funding Year</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">Program</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">Project Title</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">National Subsidy (Original Allocation)</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">Slippage</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">Risk Level as to Slippage</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">Aging</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($records ?? collect()) as $record)
                    <tr
                        data-project-code="{{ e($record->project_code) }}"
                        data-city="{{ e($record->city_municipality) }}"
                        data-province="{{ e($record->province) }}"
                        data-region="{{ e($record->region) }}"
                        data-barangays="{{ e($record->barangays) }}"
                        data-funding-year="{{ e($record->funding_year) }}"
                        data-program="{{ e($record->name_of_program) }}"
                        data-title="{{ e($record->project_title) }}"
                        data-risk-level="{{ e($record->risk_level) }}"
                        data-slippage="{{ e($record->slippage) }}"
                        data-national-subsidy="{{ e($record->national_subsidy) }}"
                    >
                        <td style="padding: 12px; color: #374151; font-weight: 500; text-align: center;">{{ $record->project_code ?? '-' }}</td>
                        <td style="padding: 12px; color: #374151; text-align: center;">
                            @php
                                $city = trim((string) ($record->city_municipality ?? ''));
                                $province = trim((string) ($record->province ?? ''));
                                $region = trim((string) ($record->region ?? ''));
                                $parts = array_filter([$province, $region], fn ($value) => $value !== '');
                            @endphp
                            @if($city !== '' || $province !== '' || $region !== '')
                                @if($city !== '')
                                    <strong>{{ $city }}</strong>
                                @endif
                                @if($province !== '')
                                    <div>{{ $province }}</div>
                                @endif
                                @if($region !== '')
                                    <div>{{ $region }}</div>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 12px; color: #374151; text-align: center;">{{ $record->barangays ?? '-' }}</td>
                        <td style="padding: 12px; color: #374151; text-align: center;">{{ $record->funding_year ?? '-' }}</td>
                        <td style="padding: 12px; color: #374151; text-align: center;">{{ $record->name_of_program ?? '-' }}</td>
                        <td style="padding: 12px; color: #374151; text-align: center;">{{ $record->project_title ?? '-' }}</td>
                        <td style="padding: 12px; color: #374151; text-align: center;">
                            {{ $record->national_subsidy !== null ? '₱' . number_format((float) $record->national_subsidy, 2) : '-' }}
                        </td>
                        <td style="padding: 12px; color: #374151; text-align: center;">
                            {{ $record->slippage !== null && $record->slippage !== '' ? rtrim(rtrim(number_format((float) $record->slippage, 2), '0'), '.') . '%' : '-' }}
                        </td>
                        <td style="padding: 12px; color: #374151; text-align: center;">
                            @php
                                $riskLevel = trim((string) ($record->risk_level ?? ''));
                                $riskKey = strtolower($riskLevel);
                                $riskColors = [
                                    'ahead' => ['bg' => '#16a34a', 'text' => '#ffffff'],
                                    'on schedule' => ['bg' => '#3b82f6', 'text' => '#ffffff'],
                                    'no risk' => ['bg' => '#0ea5e9', 'text' => '#ffffff'],
                                    'low risk' => ['bg' => '#f59e0b', 'text' => '#ffffff'],
                                    'moderate risk' => ['bg' => '#f97316', 'text' => '#ffffff'],
                                    'high risk' => ['bg' => '#dc2626', 'text' => '#ffffff'],
                                ];
                                $riskColor = $riskColors[$riskKey] ?? null;
                            @endphp
                            @if($riskLevel !== '')
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; {{ $riskColor ? 'background-color: ' . $riskColor['bg'] . '; color: ' . $riskColor['text'] . ';' : 'background-color: #e5e7eb; color: #374151;' }}">
                                    {{ $riskLevel }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 12px; color: #374151; text-align: center;">
                            @php
                                $agingValue = $record->aging ?? null;
                                $agingNumber = is_numeric($agingValue) ? (float) $agingValue : null;
                                $agingColor = '#374151';
                                if ($agingNumber !== null) {
                                    if ($agingNumber > 30) {
                                        $agingColor = '#dc2626';
                                    } elseif ($agingNumber > 10) {
                                        $agingColor = '#f59e0b';
                                    } else {
                                        $agingColor = '#16a34a';
                                    }
                                }
                            @endphp
                            @if($agingNumber !== null)
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; color: #ffffff; background-color: {{ $agingColor }};">
                                    {{ rtrim(rtrim(number_format($agingNumber, 2), '0'), '.') }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="padding: 16px; text-align: center; color: #6b7280;">No records yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($records->hasPages())
            <div style="margin-top: 16px; display: flex; justify-content: space-between; gap: 12px; align-items: center; flex-wrap: wrap;">
                <div style="font-size: 12px; color: #6b7280;">
                    Page {{ $records->currentPage() }} of {{ $records->lastPage() }} ·
                    Showing {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} of {{ $records->total() }}
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 8px; flex-wrap: wrap;">
                @if($records->onFirstPage())
                    <span style="padding: 8px 12px; background-color: #e5e7eb; color: #9ca3af; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-chevron-left"></i> Back
                    </span>
                @else
                    <a href="{{ $records->previousPageUrl() }}" style="padding: 8px 12px; background-color: #ffffff; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                        <i class="fas fa-chevron-left"></i> Back
                    </a>
                @endif

                @if($records->hasMorePages())
                    <a href="{{ $records->nextPageUrl() }}" style="padding: 8px 12px; background-color: #002C76; color: white; border: 1px solid #002C76; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
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
    </div>
    <style>
        table td {
            vertical-align: top;
        }

        #project-at-risk-table th,
        #project-at-risk-table td {
            white-space: normal !important;
            word-break: break-word;
            overflow-wrap: anywhere;
            min-width: 0 !important;
            padding: 8px !important;
        }

        #project-at-risk-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        #project-at-risk-table tbody tr:hover {
            background-color: #f9fafb;
        }

        @media (max-width: 768px) {
            th, td {
                padding: 6px !important;
                font-size: 11px;
            }
        }
    </style>
    <div id="importModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.45); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 24px; border-radius: 10px; width: 100%; max-width: 480px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <h3 style="margin: 0 0 12px 0; color: #111827; font-size: 18px; font-weight: 600;">Import Project At Risk (CSV)</h3>
            <form method="POST" action="{{ route('projects.at-risk.import') }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label for="import-file" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Upload CSV File</label>
                    <input id="import-file" type="file" name="file" accept=".csv" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: #f9fafb;">
                    <div style="margin-top: 6px; font-size: 11px; color: #6b7280;">Excel users: Save As CSV first.</div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeImportModal()" style="padding: 8px 14px; background-color: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 12px;">Cancel</button>
                    <button type="submit" style="padding: 8px 14px; background-color: #002C76; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 12px;">Upload</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function openImportModal() {
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeImportModal() {
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        window.openImportModal = openImportModal;
        window.closeImportModal = closeImportModal;
    </script>
@endsection
