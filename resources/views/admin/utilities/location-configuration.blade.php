@extends('layouts.dashboard')

@section('title', 'Location Configuration')
@section('page-title', 'Location Configuration')

@section('content')
    @if (session('success'))
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 14px 16px; border-radius: 8px; margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 16px; border-radius: 8px; margin-bottom: 16px;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 16px; border-radius: 8px; margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; margin-bottom: 12px; flex-wrap: wrap;">
        <div class="content-header" style="margin-bottom: 0;">
            <h1>Location Configuration</h1>
            <p>Manage the location-related configuration used across the application.</p>
        </div>

        <a href="{{ route('utilities.system-setup.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 8px; background: linear-gradient(180deg, #0a4cb3 0%, #002c76 100%); color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #002c76; box-shadow: 0 8px 18px rgba(0, 44, 118, 0.18);">
            <i class="fas fa-arrow-left"></i>
            <span>Back to System Setup</span>
        </a>
    </div>

    <section style="background: white; padding: 28px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); margin-bottom: 20px;">
        <div style="display: flex; align-items: flex-start; gap: 14px; margin-bottom: 18px;">
            <div style="width: 52px; height: 52px; border-radius: 14px; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
                <h2 style="margin: 0; color: #002C76; font-size: 20px;">Location Configuration</h2>
                <p style="margin: 6px 0 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                    This page is reserved for configuring provinces, municipalities, barangays, and other location references used by the system.
                </p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 18px;">
            @foreach ($locationDatasets as $dataset)
                @php
                    $lastUpdated = !empty($dataset['last_updated_at'])
                        ? \Carbon\Carbon::parse($dataset['last_updated_at'])->setTimezone(config('app.timezone'))
                        : null;
                    $importHistoryRows = $dataset['import_history_rows'] ?? collect();
                @endphp
                <article style="border: 1px solid #dbe4f0; border-radius: 16px; padding: 22px; background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%); box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 14px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <div style="width: 46px; height: 46px; border-radius: 12px; background: #eff6ff; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                <i class="{{ $dataset['icon'] }}"></i>
                            </div>
                            <div>
                                <h3 style="margin: 0; color: #002C76; font-size: 17px;">{{ $dataset['label'] }}</h3>
                                <p style="margin: 6px 0 0; color: #64748b; font-size: 13px; line-height: 1.6;">{{ $dataset['description'] }}</p>
                            </div>
                        </div>
                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 72px; padding: 6px 10px; border-radius: 999px; background: #dbeafe; color: #1d4ed8; font-size: 12px; font-weight: 700;">
                            {{ number_format((int) $dataset['row_count']) }}
                        </span>
                    </div>

                    <div style="display: grid; gap: 8px; margin-bottom: 16px; padding: 14px 16px; border: 1px solid #dbe4f0; border-radius: 12px; background: rgba(255, 255, 255, 0.78);">
                        <p style="margin: 0; color: #334155; font-size: 12px; line-height: 1.6;">
                            <strong>Accepted files:</strong> `.csv`
                        </p>
                        <p style="margin: 0; color: #334155; font-size: 12px; line-height: 1.6;">
                            <strong>Suggested CSV columns:</strong> {{ implode(', ', $dataset['columns']) }}
                        </p>
                        <p style="margin: 0; color: #334155; font-size: 12px; line-height: 1.6;">
                            <strong>Last loaded:</strong>
                            {{ $lastUpdated ? $lastUpdated->format('M d, Y h:i A') : 'No data loaded yet' }}
                        </p>
                        <p style="margin: 0; color: #64748b; font-size: 12px; line-height: 1.6;">
                            Upload adds the file to import history. Click Load to replace the current dataset.
                        </p>
                    </div>

                    @if ($dataset['table_exists'])
                        <form method="POST" action="{{ $dataset['route'] }}" enctype="multipart/form-data" style="display: grid; gap: 12px;">
                            @csrf
                            <div>
                                <label for="upload-{{ $dataset['key'] }}" style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px;">Upload CSV file</label>
                                <input id="upload-{{ $dataset['key'] }}" type="file" name="file" accept=".csv" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 12px; background: #ffffff; color: #1f2937;">
                            </div>
                            <button type="submit" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 14px; border: 1px solid #002c76; border-radius: 10px; background: linear-gradient(180deg, #0a4cb3 0%, #002c76 100%); color: #ffffff; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: 0 10px 20px rgba(0, 44, 118, 0.18);">
                                <i class="fas fa-upload"></i>
                                <span>Upload {{ $dataset['label'] }} CSV</span>
                            </button>
                        </form>

                        <div style="margin-top: 18px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 10px;">
                                <h4 style="margin: 0; color: #0f172a; font-size: 13px; font-weight: 700;">Imported CSV Files</h4>
                                <span style="font-size: 11px; color: #64748b;">Latest uploads</span>
                            </div>

                            @if ($dataset['history_table_missing'])
                                <div style="padding: 12px 14px; border: 1px solid #fdba74; border-radius: 10px; background: #fff7ed; color: #9a3412; font-size: 12px; line-height: 1.6;">
                                    Import history table is not available yet. Run the latest migration to enable Load, Download CSV, and Delete actions.
                                </div>
                            @else
                                <div style="overflow-x: auto; border: 1px solid #dbe4f0; border-radius: 12px; background: #ffffff;">
                                    <table style="width: 100%; min-width: 520px; border-collapse: collapse; font-size: 12px;">
                                        <thead>
                                            <tr style="background: #eff6ff; border-bottom: 1px solid #dbe4f0;">
                                                <th style="padding: 10px 12px; text-align: left; color: #334155; font-weight: 700;">Date</th>
                                                <th style="padding: 10px 12px; text-align: left; color: #334155; font-weight: 700;">Time</th>
                                                <th style="padding: 10px 12px; text-align: left; color: #334155; font-weight: 700;">File Name</th>
                                                <th style="padding: 10px 12px; text-align: center; color: #334155; font-weight: 700;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($importHistoryRows as $historyRow)
                                                @php
                                                    $importedAt = !empty($historyRow->imported_at)
                                                        ? \Carbon\Carbon::parse($historyRow->imported_at)->setTimezone(config('app.timezone'))
                                                        : null;
                                                    $lastLoadedAt = !empty($historyRow->last_loaded_at)
                                                        ? \Carbon\Carbon::parse($historyRow->last_loaded_at)->setTimezone(config('app.timezone'))
                                                        : null;
                                                @endphp
                                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                                    <td style="padding: 10px 12px; color: #334155; vertical-align: top; white-space: nowrap;">
                                                        {{ $importedAt ? $importedAt->format('M d, Y') : '-' }}
                                                    </td>
                                                    <td style="padding: 10px 12px; color: #334155; vertical-align: top; white-space: nowrap;">
                                                        {{ $importedAt ? $importedAt->format('h:i A') : '-' }}
                                                    </td>
                                                    <td style="padding: 10px 12px; color: #334155; vertical-align: top; word-break: break-word;">
                                                        <div>{{ $historyRow->original_file_name ?: '-' }}</div>
                                                        @if ($lastLoadedAt)
                                                            <span style="display: inline-flex; align-items: center; margin-top: 6px; padding: 4px 8px; border-radius: 999px; background: #dcfce7; color: #166534; font-size: 10px; font-weight: 700;">
                                                                Loaded {{ $lastLoadedAt->format('M d, Y h:i A') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 10px 12px; vertical-align: top;">
                                                        <div style="display: flex; justify-content: center; gap: 6px; flex-wrap: wrap;">
                                                            <form method="POST" action="{{ route('utilities.location-configuration.load', ['dataset' => $dataset['key'], 'importId' => $historyRow->id]) }}" onsubmit="return confirm('Loading this file will replace the current {{ $dataset['label'] }} data. Continue?');">
                                                                @csrf
                                                                <button type="submit" style="padding: 6px 10px; background-color: #002c76; color: #ffffff; border: none; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: 700;">
                                                                    Load
                                                                </button>
                                                            </form>
                                                            <a href="{{ route('utilities.location-configuration.download', ['dataset' => $dataset['key'], 'importId' => $historyRow->id]) }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 10px; background-color: #0f766e; color: #ffffff; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: 700;">
                                                                Download CSV
                                                            </a>
                                                            <form method="POST" action="{{ route('utilities.location-configuration.delete', ['dataset' => $dataset['key'], 'importId' => $historyRow->id]) }}" onsubmit="return confirm('Delete this imported file record?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" style="padding: 6px 10px; background-color: #dc2626; color: #ffffff; border: none; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: 700;">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" style="padding: 18px 12px; text-align: center; color: #64748b;">
                                                        No imported files yet.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @else
                        <div style="padding: 12px 14px; border: 1px solid #fecaca; border-radius: 10px; background: #fff5f5; color: #991b1b; font-size: 12px; line-height: 1.6;">
                            The {{ $dataset['label'] }} table is not available yet. Run the latest migration before uploading files.
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    </section>
@endsection
