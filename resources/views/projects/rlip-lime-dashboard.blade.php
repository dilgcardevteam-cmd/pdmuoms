@extends('layouts.dashboard')

@section('title', 'RLIP/LIME Dashboard')
@section('page-title', 'RLIP/LIME Dashboard')

@section('content')
    <div class="content-header">
        <h1>RLIP/LIME-20% Development Fund Dashboard</h1>
        <p>
            Metrics and distribution based on RLIP/LIME dataset.
            @if(!empty($sourceMeta['generated_at']))
                Last parsed: {{ \Illuminate\Support\Carbon::parse($sourceMeta['generated_at'])->format('Y-m-d h:i A') }}.
            @endif
        </p>
    </div>

    @include('projects.partials.project-section-tabs', ['activeTab' => $activeTab ?? 'rlip-lime'])

    <div style="background: #ffffff; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(15,23,42,0.06);">
        @php
            $activeFilters = array_merge([
                'search' => '',
                'funding_year' => '',
                'fund_source' => '',
                'province' => '',
                'city' => '',
                'status' => '',
            ], $filters ?? []);
        @endphp

        <form id="rlip-dashboard-filters" method="GET" action="{{ route('projects.rlip-lime.dashboard') }}" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: end; margin-bottom: 18px;">
            <div style="min-width: 220px; flex: 1;">
                <label for="dashboard-search" style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px;">Search</label>
                <input id="dashboard-search" type="text" name="search" value="{{ $activeFilters['search'] }}" placeholder="Search project code, title, location..." style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
            </div>
            <div style="min-width: 140px;">
                <label for="dashboard-year" style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px;">Funding Year</label>
                <select id="dashboard-year" name="funding_year" style="width: 100%; padding: 7px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                    <option value="">All</option>
                    @foreach($fundingYears as $year)
                        <option value="{{ $year }}" {{ (string) $activeFilters['funding_year'] === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 160px;">
                <label for="dashboard-source" style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px;">Fund Source</label>
                <select id="dashboard-source" name="fund_source" style="width: 100%; padding: 7px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                    <option value="">All</option>
                    @foreach($fundSources as $source)
                        <option value="{{ $source }}" {{ (string) $activeFilters['fund_source'] === (string) $source ? 'selected' : '' }}>{{ $source }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 160px;">
                <label for="dashboard-province" style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px;">Province</label>
                <select id="dashboard-province" name="province" style="width: 100%; padding: 7px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                    <option value="">All</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" {{ (string) $activeFilters['province'] === (string) $province ? 'selected' : '' }}>{{ $province }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 160px;">
                <label for="dashboard-city" style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px;">City/Mun</label>
                <select id="dashboard-city" name="city" data-selected-city="{{ $activeFilters['city'] }}" style="width: 100%; padding: 7px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                    <option value="">All</option>
                    @foreach($cityOptions as $city)
                        <option value="{{ $city }}" {{ (string) $activeFilters['city'] === (string) $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 160px;">
                <label for="dashboard-status" style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px;">Status</label>
                <select id="dashboard-status" name="status" style="width: 100%; padding: 7px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                    <option value="">All</option>
                    @foreach($statusOptions as $status)
                        <option value="{{ $status }}" {{ (string) $activeFilters['status'] === (string) $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <a href="{{ route('projects.rlip-lime.dashboard') }}" style="padding: 8px 12px; background: #64748b; color: #ffffff; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none;">
                Clear
            </a>
            <a href="{{ route('projects.rlip-lime', request()->query()) }}" style="padding: 8px 12px; background: #0369a1; color: #ffffff; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none;">
                Open Table
            </a>
        </form>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 12px; margin-bottom: 18px;">
            <div style="border: 1px solid #dbeafe; background: #eff6ff; border-radius: 10px; padding: 14px;">
                <div style="font-size: 11px; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Total Projects</div>
                <div style="margin-top: 6px; font-size: 28px; color: #0f172a; font-weight: 800;">{{ number_format($totalProjects) }}</div>
            </div>
            <div style="border: 1px solid #dcfce7; background: #f0fdf4; border-radius: 10px; padding: 14px;">
                <div style="font-size: 11px; color: #166534; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Total Programmed</div>
                <div style="margin-top: 6px; font-size: 24px; color: #0f172a; font-weight: 800;">&#8369; {{ number_format($totalProgrammedAmount, 2) }}</div>
            </div>
            <div style="border: 1px solid #fde68a; background: #fffbeb; border-radius: 10px; padding: 14px;">
                <div style="font-size: 11px; color: #92400e; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Avg Completion</div>
                <div style="margin-top: 6px; font-size: 28px; color: #0f172a; font-weight: 800;">{{ number_format($averageCompletion, 2) }}%</div>
            </div>
            <div style="border: 1px solid #e9d5ff; background: #faf5ff; border-radius: 10px; padding: 14px;">
                <div style="font-size: 11px; color: #6b21a8; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Total Employment</div>
                <div style="margin-top: 6px; font-size: 28px; color: #0f172a; font-weight: 800;">{{ number_format($totalEmployment) }}</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 18px;" class="rlip-dashboard-breakdown-grid">
            <section style="border: 1px solid #e5e7eb; border-radius: 10px; background: #ffffff; padding: 12px;">
                <h3 style="margin: 0 0 10px; color: #0f172a; font-size: 14px; font-weight: 700;">Projects by Status</h3>
                @forelse($statusBreakdown as $item)
                    @php
                        $barWidth = $topStatusCount > 0 ? round(($item['count'] / $topStatusCount) * 100, 2) : 0;
                    @endphp
                    <div style="margin-bottom: 9px;">
                        <div style="display: flex; justify-content: space-between; gap: 8px; font-size: 12px; margin-bottom: 4px;">
                            <span style="color: #334155;">{{ $item['label'] }}</span>
                            <strong style="color: #0f172a;">{{ number_format($item['count']) }}</strong>
                        </div>
                        <div style="height: 6px; border-radius: 999px; background: #f1f5f9;">
                            <div style="width: {{ $barWidth }}%; height: 100%; border-radius: 999px; background: #2563eb;"></div>
                        </div>
                    </div>
                @empty
                    <p style="margin: 0; color: #64748b; font-size: 12px;">No data for selected filters.</p>
                @endforelse
            </section>

            <section style="border: 1px solid #e5e7eb; border-radius: 10px; background: #ffffff; padding: 12px;">
                <h3 style="margin: 0 0 10px; color: #0f172a; font-size: 14px; font-weight: 700;">Projects by Fund Source</h3>
                @forelse($fundSourceBreakdown as $item)
                    @php
                        $barWidth = $topFundSourceCount > 0 ? round(($item['count'] / $topFundSourceCount) * 100, 2) : 0;
                    @endphp
                    <div style="margin-bottom: 9px;">
                        <div style="display: flex; justify-content: space-between; gap: 8px; font-size: 12px; margin-bottom: 4px;">
                            <span style="color: #334155;">{{ $item['label'] }}</span>
                            <strong style="color: #0f172a;">{{ number_format($item['count']) }}</strong>
                        </div>
                        <div style="height: 6px; border-radius: 999px; background: #f1f5f9;">
                            <div style="width: {{ $barWidth }}%; height: 100%; border-radius: 999px; background: #059669;"></div>
                        </div>
                    </div>
                @empty
                    <p style="margin: 0; color: #64748b; font-size: 12px;">No data for selected filters.</p>
                @endforelse
            </section>

            <section style="border: 1px solid #e5e7eb; border-radius: 10px; background: #ffffff; padding: 12px;">
                <h3 style="margin: 0 0 10px; color: #0f172a; font-size: 14px; font-weight: 700;">Top Provinces</h3>
                @forelse($provinceBreakdown as $item)
                    @php
                        $barWidth = $topProvinceCount > 0 ? round(($item['count'] / $topProvinceCount) * 100, 2) : 0;
                    @endphp
                    <div style="margin-bottom: 9px;">
                        <div style="display: flex; justify-content: space-between; gap: 8px; font-size: 12px; margin-bottom: 4px;">
                            <span style="color: #334155;">{{ $item['label'] }}</span>
                            <strong style="color: #0f172a;">{{ number_format($item['count']) }}</strong>
                        </div>
                        <div style="height: 6px; border-radius: 999px; background: #f1f5f9;">
                            <div style="width: {{ $barWidth }}%; height: 100%; border-radius: 999px; background: #7c3aed;"></div>
                        </div>
                    </div>
                @empty
                    <p style="margin: 0; color: #64748b; font-size: 12px;">No data for selected filters.</p>
                @endforelse
            </section>
        </div>

    </div>

    <style>
        @media (max-width: 1000px) {
            .rlip-dashboard-breakdown-grid {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 768px) {
            #rlip-dashboard-filters > div {
                width: 100%;
                min-width: 0 !important;
                flex: 1 1 100%;
            }

            #rlip-dashboard-filters > a {
                width: 100%;
                text-align: center;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filtersForm = document.getElementById('rlip-dashboard-filters');
            const searchInput = document.getElementById('dashboard-search');
            const provinceSelect = document.getElementById('dashboard-province');
            const citySelect = document.getElementById('dashboard-city');
            const yearSelect = document.getElementById('dashboard-year');
            const sourceSelect = document.getElementById('dashboard-source');
            const statusSelect = document.getElementById('dashboard-status');
            const locationData = @json($provinceMunicipalities);
            const selectedCity = citySelect ? (citySelect.dataset.selectedCity || '') : '';
            const AUTO_SEARCH_DELAY_MS = 700;
            const AUTO_SEARCH_MIN_CHARS = 2;
            let searchTimer = null;
            let lastSubmittedSearch = searchInput ? searchInput.value.trim() : '';

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
                if (searchInput) {
                    lastSubmittedSearch = searchInput.value.trim();
                }
                filtersForm.requestSubmit();
            }

            function scheduleAutoSearch() {
                if (!searchInput) {
                    return;
                }

                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    const currentSearch = searchInput.value.trim();
                    const hasMinChars = currentSearch.length >= AUTO_SEARCH_MIN_CHARS;
                    const isCleared = currentSearch.length === 0;
                    if (!hasMinChars && !isCleared) {
                        return;
                    }
                    if (currentSearch === lastSubmittedSearch) {
                        return;
                    }
                    submitFilters();
                }, AUTO_SEARCH_DELAY_MS);
            }

            if (searchInput) {
                searchInput.addEventListener('input', scheduleAutoSearch);
            }

            provinceSelect.addEventListener('change', function () {
                populateCityOptions(this.value);
                citySelect.value = '';
                submitFilters();
            });

            [yearSelect, sourceSelect, citySelect, statusSelect]
                .filter(Boolean)
                .forEach(function (select) {
                    select.addEventListener('change', submitFilters);
                });

            populateCityOptions(provinceSelect.value, selectedCity);
        });
    </script>
@endsection
