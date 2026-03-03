@extends('layouts.dashboard')

@section('page-title', 'RBIS Annual Certification')

@section('content')
<div class="content-header">
    <h1>RBIS Annual Certification</h1>
    <p>Each city/municipality and PLGU has its own profile page for document uploads.</p>
</div>

@if (session('success'))
    <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 14px 16px; border-radius: 8px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px;">
    <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 9999px; padding: 8px 14px; font-size: 12px; font-weight: 600; color: #374151;">
        Provinces: {{ $totalProvinces }}
    </div>
    <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 9999px; padding: 8px 14px; font-size: 12px; font-weight: 600; color: #374151;">
        Offices: {{ $totalOffices }}
    </div>
    <form method="GET" style="display: flex; align-items: center; gap: 8px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 9999px; padding: 6px 10px;">
        <label for="rbis-reporting-year" style="font-size: 12px; font-weight: 600; color: #374151;">Year</label>
        <select id="rbis-reporting-year" name="year" onchange="this.form.submit()" style="border: 1px solid #d1d5db; border-radius: 9999px; padding: 4px 10px; font-size: 12px; background-color: #fff;">
            @for ($yearOption = now()->year + 1; $yearOption >= now()->year - 5; $yearOption--)
                <option value="{{ $yearOption }}" @selected($reportingYear === $yearOption)>{{ $yearOption }}</option>
            @endfor
        </select>
    </form>
</div>

<div style="background: white; padding: 24px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); overflow-x: auto;">
    <div style="margin-bottom: 16px; max-width: 420px; position: relative;">
        <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 12px;"></i>
        <input
            id="rbis-office-search"
            type="text"
            placeholder="Search province or city/municipality..."
            style="width: 100%; padding: 10px 12px 10px 32px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background-color: #f9fafb;"
        >
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 14px;">Province</th>
                <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 14px;">City / Municipality / PLGU</th>
                <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 14px;">Uploaded Files (CY {{ $reportingYear }})</th>
                <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 14px;">Actions</th>
            </tr>
        </thead>
        <tbody id="rbis-office-table-body">
            @forelse ($officeRows as $row)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px; color: #111827; font-size: 13px;">{{ $row['province'] }}</td>
                    <td style="padding: 12px; color: #111827; font-size: 13px;">{{ $row['city_municipality'] }}</td>
                    <td style="padding: 12px; color: #111827; font-size: 13px; text-align: center;">
                        {{ (int) ($uploadCountsByOffice[$row['city_municipality']] ?? 0) }}
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="{{ route('rbis-annual-certification.edit', ['office' => $row['city_municipality'], 'year' => $reportingYear]) }}" style="display: inline-block; padding: 8px 14px; background-color: #002C76; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 12px; text-decoration: none;">
                            <i class="fas fa-eye" style="margin-right: 4px;"></i> View Profile
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding: 40px; text-align: center; color: #6b7280;">
                        <i class="fas fa-inbox" style="font-size: 30px; margin-bottom: 8px; display: block;"></i>
                        No offices found.
                    </td>
                </tr>
            @endforelse
            <tr id="rbis-office-no-results" style="display: none;">
                <td colspan="4" style="padding: 40px; text-align: center; color: #6b7280;">
                    <i class="fas fa-search" style="font-size: 30px; margin-bottom: 8px; display: block;"></i>
                    No matching records found.
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    (function () {
        const searchInput = document.getElementById('rbis-office-search');
        const tableBody = document.getElementById('rbis-office-table-body');
        const noResultsRow = document.getElementById('rbis-office-no-results');

        if (!searchInput || !tableBody) return;

        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const rows = Array.from(tableBody.querySelectorAll('tr')).filter((row) => row.id !== 'rbis-office-no-results');
            let visibleCount = 0;

            rows.forEach((row) => {
                const cells = row.querySelectorAll('td');
                if (cells.length < 2) return;

                const province = (cells[0].textContent || '').trim().toLowerCase();
                const city = (cells[1].textContent || '').trim().toLowerCase();
                const matches = province.includes(query) || city.includes(query);

                row.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            });

            if (noResultsRow) {
                noResultsRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        });
    })();
</script>
@endsection
