@extends('layouts.dashboard')

@section('title', 'Report on PD No. PBBM-2025-1572-1573')
@section('page-title', 'Report on PD No. PBBM-2025-1572-1573')

@section('content')
<div class="content-header">
    <h1>Report on PD No. PBBM-2025-1572-1573</h1>
    <p>Monthly submission monitoring for all provinces, cities, and municipalities.</p>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div style="margin-bottom: 16px; display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                    <div style="position: relative; width: 100%; max-width: 420px;">
                        <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 12px;"></i>
                        <input
                            type="text"
                            id="pd-monthly-search"
                            placeholder="Search province or city/municipality..."
                            style="width: 100%; padding: 10px 12px 10px 32px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background-color: #f9fafb;"
                        >
                    </div>
                    <form method="GET" style="display: inline-flex; align-items: center; gap: 8px;">
                        <label for="reporting-year" style="font-size: 13px; color: #374151; font-weight: 600;">Year</label>
                        <select id="reporting-year" name="year" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background-color: #fff;">
                            @for ($yearOption = now()->year + 1; $yearOption >= now()->year - 5; $yearOption--)
                                <option value="{{ $yearOption }}" @selected($reportingYear === $yearOption)>{{ $yearOption }}</option>
                            @endfor
                        </select>
                    </form>
                </div>

                <div class="table-responsive" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 1400px;">
                        <thead>
                            <tr style="background-color: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                                <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 14px; white-space: nowrap;">Province</th>
                                <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 14px; white-space: nowrap;">City/Municipality</th>
                                @foreach ($months as $monthCode => $monthLabel)
                                    <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 12px; white-space: nowrap;">
                                        {{ strtoupper(substr($monthLabel, 0, 3)) }}
                                    </th>
                                @endforeach
                                <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 14px; white-space: nowrap;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="pd-monthly-table-body">
                            @forelse ($officeRows as $row)
                                @php
                                    $officeDocs = $documentsByOffice[$row['city_municipality']] ?? [];
                                    $statusIcon = function ($doc) {
                                        if (!$doc) {
                                            return '<span style="color: #9ca3af;">-</span>';
                                        }

                                        if ($doc->status === 'approved') {
                                            return '<i class="fas fa-check-circle" title="Approved" style="color: #10b981;"></i>';
                                        }

                                        if ($doc->status === 'returned') {
                                            return '<i class="fas fa-undo" title="Returned" style="color: #dc2626;"></i>';
                                        }

                                        return '<i class="fas fa-clock" title="For Validation" style="color: #3b82f6;"></i>';
                                    };
                                @endphp
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 12px; color: #111827; font-size: 13px; white-space: nowrap;">{{ $row['province'] }}</td>
                                    <td style="padding: 12px; color: #111827; font-size: 13px; white-space: nowrap;">{{ $row['city_municipality'] }}</td>
                                    @foreach ($months as $monthCode => $monthLabel)
                                        <td style="padding: 12px; text-align: center; color: #111827; font-size: 13px;">
                                            {!! $statusIcon($officeDocs['pd_no_pbbm_2025_1572_1573|' . $reportingYear . '|' . $monthCode] ?? null) !!}
                                        </td>
                                    @endforeach
                                    <td style="padding: 12px; text-align: center; white-space: nowrap;">
                                        <a
                                            href="{{ route('reports.monthly.pd-no-pbbm-2025-1572-1573.edit', ['office' => $row['city_municipality'], 'year' => $reportingYear]) }}"
                                            style="display: inline-block; padding: 8px 16px; background-color: #002C76; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; text-decoration: none;"
                                        >
                                            <i class="fas fa-eye" style="margin-right: 4px;"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td colspan="{{ count($months) + 3 }}" style="padding: 40px; text-align: center; color: #6b7280;">
                                        <i class="fas fa-table" style="font-size: 32px; margin-bottom: 10px; display: block;"></i>
                                        No records found.
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="pd-monthly-no-results" style="display: none; border-bottom: 1px solid #e5e7eb;">
                                <td colspan="{{ count($months) + 3 }}" style="padding: 40px; text-align: center; color: #6b7280;">
                                    <i class="fas fa-search" style="font-size: 32px; margin-bottom: 10px; display: block;"></i>
                                    No matching records found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #pd-monthly-table-body tr:hover {
        background-color: #f9fafb !important;
    }
</style>

<script>
    (function () {
        const searchInput = document.getElementById('pd-monthly-search');
        const tableBody = document.getElementById('pd-monthly-table-body');
        const noResultsRow = document.getElementById('pd-monthly-no-results');

        if (!searchInput || !tableBody) return;

        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const rows = Array.from(tableBody.querySelectorAll('tr')).filter((row) => row.id !== 'pd-monthly-no-results');
            let visibleCount = 0;

            rows.forEach((row) => {
                const cells = row.querySelectorAll('td');
                if (!cells.length) return;

                const province = (cells[0].textContent || '').trim().toLowerCase();
                const city = (cells[1].textContent || '').trim().toLowerCase();
                const matches = province.includes(query) || city.includes(query);

                row.style.display = matches ? '' : 'none';
                if (matches) visibleCount += 1;
            });

            if (noResultsRow) {
                noResultsRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        });
    })();
</script>
@endsection
