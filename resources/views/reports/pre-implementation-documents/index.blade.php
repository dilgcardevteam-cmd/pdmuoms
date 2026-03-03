@extends('layouts.dashboard')

@section('title', 'Pre-Implementation Documents (SBDP Projects)')
@section('page-title', 'Pre-Implementation Documents (SBDP Projects)')

@section('content')
    <div class="content-header">
        <h1>Pre-Implementation Documents (SBDP Projects)</h1>
        <p>View SBDP projects and open each project profile to manage pre-implementation records.</p>
    </div>

    <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); margin-bottom: 20px; border: 1px solid #e5e7eb;">
        <form method="GET" action="{{ route('pre-implementation-documents.sbdp') }}" style="display: grid; grid-template-columns: minmax(220px, 2fr) minmax(160px, 1fr) minmax(120px, 1fr) auto auto; gap: 10px; align-items: center;">
            <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
            <div style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 12px;"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Search project code, title, location..."
                    style="width: 100%; padding: 10px 12px 10px 32px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background-color: #f9fafb;"
                >
            </div>
            <select name="province" style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background-color: #f9fafb;">
                <option value="">All Provinces</option>
                @foreach(($filterOptions['provinces'] ?? []) as $option)
                    <option value="{{ $option }}" {{ ($filters['province'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
            <select name="funding_year" style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background-color: #f9fafb;">
                <option value="">All Years</option>
                @foreach(($filterOptions['funding_years'] ?? []) as $option)
                    <option value="{{ $option }}" {{ (string) ($filters['funding_year'] ?? '') === (string) $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
            <button type="submit" style="padding: 10px 14px; background-color: #002C76; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; white-space: nowrap;">
                <i class="fas fa-filter" style="margin-right: 6px;"></i> Apply
            </button>
            <a href="{{ route('pre-implementation-documents.sbdp') }}" style="padding: 10px 14px; background-color: #6b7280; color: white; border: none; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; text-align: center; white-space: nowrap;">
                Reset
            </a>
        </form>
    </div>

    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 14px;">Project Code</th>
                    <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 14px;">Project Title</th>
                    <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 14px;">Fund Source</th>
                    <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 14px;">Funding Year</th>
                    <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 14px;">City / Municipality</th>
                    <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 14px;">Province</th>
                    <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 14px;">Status</th>
                    <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 14px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 12px; color: #111827; font-size: 13px; font-weight: 600;">{{ $project->project_code }}</td>
                        <td style="padding: 12px; color: #111827; font-size: 13px;">{{ $project->project_title ?: '-' }}</td>
                        <td style="padding: 12px; color: #111827; font-size: 13px; text-align: center;">{{ $project->fund_source ?: 'SBDP' }}</td>
                        <td style="padding: 12px; color: #111827; font-size: 13px; text-align: center;">{{ $project->funding_year ?: '-' }}</td>
                        <td style="padding: 12px; color: #111827; font-size: 13px;">{{ $project->city_municipality ?: '-' }}</td>
                        <td style="padding: 12px; color: #111827; font-size: 13px;">{{ $project->province ?: '-' }}</td>
                        <td style="padding: 12px; text-align: center;">
                            <span style="display: inline-block; padding: 4px 10px; background-color: #e5e7eb; color: #374151; border-radius: 9999px; font-size: 11px; font-weight: 600;">
                                {{ $project->status ?: '-' }}
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="{{ route('pre-implementation-documents.sbdp.show', $project->project_code) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; background-color: #002C76; color: white; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                <i class="fas fa-folder-open"></i> Open
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 40px; text-align: center; color: #6b7280;">
                            <i class="fas fa-inbox" style="font-size: 30px; margin-bottom: 8px; display: block;"></i>
                            No SBDP projects found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($projects->count() > 0)
            <div style="margin-top: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <div style="font-size: 12px; color: #6b7280;">
                        Page {{ $projects->currentPage() }} of {{ $projects->lastPage() }} ·
                        Showing {{ $projects->firstItem() ?? 0 }}–{{ $projects->lastItem() ?? 0 }} of {{ $projects->total() }}
                    </div>
                    <form method="GET" action="{{ route('pre-implementation-documents.sbdp') }}" style="display: inline-flex; align-items: center;">
                        <input type="hidden" name="search" value="{{ $filters['search'] ?? '' }}">
                        <input type="hidden" name="province" value="{{ $filters['province'] ?? '' }}">
                        <input type="hidden" name="funding_year" value="{{ $filters['funding_year'] ?? '' }}">
                        <select id="per-page" name="per_page" onchange="this.form.submit()" aria-label="Rows per page" title="Rows per page" style="padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                            @foreach([10, 15, 25, 50] as $option)
                                <option value="{{ $option }}" {{ (int) ($perPage ?? 10) === $option ? 'selected' : '' }}>{{ $option }}</option>
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
    </div>
@endsection
