@extends('layouts.dashboard')

@section('title', 'Upload SubayBAYAN Data')
@section('page-title', 'Upload SubayBAYAN Data')

@section('content')
    <div class="content-header">
        <h1>Upload SubayBAYAN Data</h1>
        <p>Upload SubayBAYAN data files for system processing.</p>
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

    @if($tableMissing ?? false)
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 16px; border-radius: 8px; margin-top: 16px;">
            SubayBAYAN data table is not available yet. Please run the migration first.
        </div>
    @else
        <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 16px; overflow-x: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 12px;">
                <h2 style="color: #002C76; font-size: 18px; margin: 0;">SubayBAYAN Data</h2>
                <button type="button" onclick="openImportModal()" style="padding: 8px 14px; background-color: #002C76; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 12px;">
                    Import CSV
                </button>
            </div>
            <form method="GET" action="{{ route('system-management.upload-subaybayan') }}" style="display: grid; grid-template-columns: repeat(4, minmax(160px, 1fr)); gap: 12px; align-items: flex-end; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; margin-bottom: 16px;">
                <div>
                    <label for="filter-province" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Province</label>
                    <select id="filter-province" name="province" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                        <option value="">All</option>
                        @foreach(($filterOptions['provinces'] ?? []) as $option)
                            <option value="{{ $option }}" {{ ($filters['province'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-city" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">City Municipality</label>
                    <select id="filter-city" name="city_municipality" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                        <option value="">All</option>
                        @foreach(($filterOptions['cities'] ?? []) as $option)
                            <option value="{{ $option }}" {{ ($filters['city_municipality'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-barangay" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Barangay</label>
                    <select id="filter-barangay" name="barangay" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                        <option value="">All</option>
                        @foreach(($filterOptions['barangays'] ?? []) as $option)
                            <option value="{{ $option }}" {{ ($filters['barangay'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-program" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Program</label>
                    <select id="filter-program" name="program" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                        <option value="">All</option>
                        @foreach(($filterOptions['programs'] ?? []) as $option)
                            <option value="{{ $option }}" {{ ($filters['program'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-status" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Status</label>
                    <select id="filter-status" name="status" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                        <option value="">All</option>
                        @foreach(($filterOptions['statuses'] ?? []) as $option)
                            <option value="{{ $option }}" {{ ($filters['status'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-funding-year" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Funding Year</label>
                    <select id="filter-funding-year" name="funding_year" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                        <option value="">All</option>
                        @foreach(($filterOptions['funding_years'] ?? []) as $option)
                            <option value="{{ $option }}" {{ ($filters['funding_year'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-proc-type" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Procurement Type</label>
                    <select id="filter-proc-type" name="procurement_type" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                        <option value="">All</option>
                        @foreach(($filterOptions['procurement_types'] ?? []) as $option)
                            <option value="{{ $option }}" {{ ($filters['procurement_type'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-project-code" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Project Code</label>
                    <input id="filter-project-code" name="project_code" type="text" value="{{ $filters['project_code'] ?? '' }}" placeholder="Search code" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                </div>
                <div>
                    <label for="filter-project-title" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Project Title</label>
                    <input id="filter-project-title" name="project_title" type="text" value="{{ $filters['project_title'] ?? '' }}" placeholder="Search title" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                </div>
                <div>
                    <label for="filter-procurement" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Procurement</label>
                    <select id="filter-procurement" name="procurement" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                        <option value="">All</option>
                        @foreach(($filterOptions['procurements'] ?? []) as $option)
                            <option value="{{ $option }}" {{ ($filters['procurement'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-project-type" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Project Type</label>
                    <select id="filter-project-type" name="type_of_project" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                        <option value="">All</option>
                        @foreach(($filterOptions['project_types'] ?? []) as $option)
                            <option value="{{ $option }}" {{ ($filters['type_of_project'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-implementing" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Implementing Unit</label>
                    <select id="filter-implementing" name="implementing_unit" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                        <option value="">All</option>
                        @foreach(($filterOptions['implementing_units'] ?? []) as $option)
                            <option value="{{ $option }}" {{ ($filters['implementing_unit'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-profile-status" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Status of Project Profile</label>
                    <select id="filter-profile-status" name="profile_approval_status" style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                        <option value="">All</option>
                        @foreach(($filterOptions['profile_statuses'] ?? []) as $option)
                            <option value="{{ $option }}" {{ ($filters['profile_approval_status'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column: 1 / -1; display: flex; gap: 8px; align-items: center; justify-content: flex-end;">
                    <button type="submit" style="padding: 8px 12px; background-color: #002C76; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">Apply</button>
                    <a href="{{ route('system-management.upload-subaybayan') }}" style="padding: 8px 12px; background-color: #6b7280; color: white; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; text-align: center;">Reset</a>
                </div>
            </form>
            @php
                $sanitizeCell = function ($value) {
                    if ($value === null) {
                        return '-';
                    }
                    $string = (string) $value;
                    if ($string === '') {
                        return '-';
                    }
                    if (function_exists('mb_convert_encoding')) {
                        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8,ISO-8859-1,WINDOWS-1252');
                    } elseif (function_exists('utf8_encode')) {
                        $string = utf8_encode($string);
                    }
                    if (function_exists('iconv')) {
                        $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $string);
                        if ($clean !== false) {
                            $string = $clean;
                        }
                    }
                    return $string;
                };
            @endphp
            <div style="max-height: 520px; overflow: auto; border: 1px solid #e5e7eb; border-radius: 8px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px; table-layout: auto;">
                    <thead>
                        <tr style="background-color: #f3f4f6; border-bottom: 2px solid #d1d5db;">
                            @foreach($columns as $column)
                                <th style="padding: 10px; text-align: left; font-weight: 600; color: #374151; position: sticky; top: 0; background-color: #f3f4f6; z-index: 1; white-space: normal; word-break: break-word; min-width: 160px;">
                                    {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $column)) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                            @foreach($columns as $column)
                                <td style="padding: 10px; color: #374151; vertical-align: top; word-break: break-word;">
                                    {{ $sanitizeCell($row->$column ?? null) }}
                                </td>
                            @endforeach
                        </tr>
                        @empty
                            <tr>
                                <td colspan="{{ max(1, count($columns)) }}" style="padding: 20px; text-align: center; color: #6b7280;">
                                    No data available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rows->hasPages())
                <div style="margin-top: 16px; display: flex; justify-content: space-between; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <div style="font-size: 12px; color: #6b7280;">
                        Page {{ $rows->currentPage() }} of {{ $rows->lastPage() }} ·
                        Showing {{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }} of {{ $rows->total() }}
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 8px; flex-wrap: wrap;">
                        @if($rows->onFirstPage())
                            <span style="padding: 8px 12px; background-color: #e5e7eb; color: #9ca3af; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-chevron-left"></i> Back
                            </span>
                        @else
                            <a href="{{ $rows->previousPageUrl() }}" style="padding: 8px 12px; background-color: #ffffff; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                <i class="fas fa-chevron-left"></i> Back
                            </a>
                        @endif

                        @if($rows->hasMorePages())
                            <a href="{{ $rows->nextPageUrl() }}" style="padding: 8px 12px; background-color: #002C76; color: white; border: 1px solid #002C76; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
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
    @endif

    <div id="importModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.45); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 24px; border-radius: 10px; width: 100%; max-width: 480px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <h3 style="margin: 0 0 12px 0; color: #111827; font-size: 18px; font-weight: 600;">Import SubayBAYAN Data (CSV)</h3>
            <form method="POST" action="{{ route('system-management.upload-subaybayan.import') }}" enctype="multipart/form-data">
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
