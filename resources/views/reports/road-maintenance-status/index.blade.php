@extends('layouts.dashboard')

@section('page-title', 'Road Maintenance Status Report')

@section('content')
<div class="content-header">
    <h1>Road Maintenance Status Report</h1>
    <p>Quarterly upload monitoring for all provinces, cities, and municipalities.</p>
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
                            id="road-maintenance-search"
                            placeholder="Search province or city/municipality..."
                            style="width: 100%; padding: 10px 12px 10px 32px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background-color: #f9fafb;"
                        >
                    </div>
                    <form method="GET" style="display: inline-flex; align-items: center; gap: 8px;">
                        <label for="reporting-year" style="font-size: 13px; color: #374151; font-weight: 600;">Year</label>
                        <select id="reporting-year" name="year" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background-color: #fff;">
                            @for ($yearOption = now()->year + 1; $yearOption >= now()->year - 5; $yearOption--)
                                <option value="{{ $yearOption }}" @selected($reportingYear === $yearOption)>
                                    {{ $yearOption }}
                                </option>
                            @endfor
                        </select>
                    </form>
                </div>

                <div class="table-responsive" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                                <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 14px;">Province</th>
                                <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 14px;">City/Municipality</th>
                                <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 14px;">Q1</th>
                                <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 14px;">Q2</th>
                                <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 14px;">Q3</th>
                                <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 14px;">Q4</th>
                                <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 14px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="road-maintenance-table-body">
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
                                <tr style="border-bottom: 1px solid #e5e7eb; transition: all 0.3s ease;">
                                    <td style="padding: 12px; color: #111827; font-size: 14px;">{{ $row['province'] }}</td>
                                    <td style="padding: 12px; color: #111827; font-size: 14px;">{{ $row['city_municipality'] }}</td>
                                    <td style="padding: 12px; text-align: center; color: #111827; font-size: 14px;">{!! $statusIcon($officeDocs['road_maintenance_status|' . $reportingYear . '|Q1'] ?? null) !!}</td>
                                    <td style="padding: 12px; text-align: center; color: #111827; font-size: 14px;">{!! $statusIcon($officeDocs['road_maintenance_status|' . $reportingYear . '|Q2'] ?? null) !!}</td>
                                    <td style="padding: 12px; text-align: center; color: #111827; font-size: 14px;">{!! $statusIcon($officeDocs['road_maintenance_status|' . $reportingYear . '|Q3'] ?? null) !!}</td>
                                    <td style="padding: 12px; text-align: center; color: #111827; font-size: 14px;">{!! $statusIcon($officeDocs['road_maintenance_status|' . $reportingYear . '|Q4'] ?? null) !!}</td>
                                    <td style="padding: 12px; text-align: center;">
                                        <a href="{{ route('road-maintenance-status.edit', ['roadMaintenance' => $row['city_municipality'], 'year' => $reportingYear]) }}" style="display: inline-block; padding: 8px 16px; background-color: #002C76; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; text-decoration: none; transition: all 0.3s ease;">
                                            <i class="fas fa-eye" style="margin-right: 4px;"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr style="border-bottom: 1px solid #e5e7eb; transition: all 0.3s ease;">
                                    <td colspan="7" style="padding: 40px; text-align: center; color: #6b7280;">
                                        <i class="fas fa-table" style="font-size: 32px; margin-bottom: 10px; display: block;"></i>
                                        No records found.
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="road-maintenance-no-results" style="display: none; border-bottom: 1px solid #e5e7eb; transition: all 0.3s ease;">
                                <td colspan="7" style="padding: 40px; text-align: center; color: #6b7280;">
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
    tr:hover {
        background-color: #f9fafb !important;
    }
</style>

<script>
    (function () {
        const searchInput = document.getElementById('road-maintenance-search');
        const tableBody = document.getElementById('road-maintenance-table-body');
        const noResultsRow = document.getElementById('road-maintenance-no-results');

        if (!searchInput || !tableBody) return;

        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const rows = Array.from(tableBody.querySelectorAll('tr'))
                .filter(row => row.id !== 'road-maintenance-no-results');

            let visibleCount = 0;

            rows.forEach(row => {
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
