@extends('layouts.dashboard')

@section('title', 'Pre-Implementation Upload')
@section('page-title', 'Pre-Implementation Documents (SBDP Projects)')

@section('content')
    <div class="content-header" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
        <div>
            <h1>Update - {{ $project->project_code }}</h1>
            <p>Upload and validate pre-implementation documents for this SBDP project.</p>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
            <a href="{{ route('pre-implementation-documents.sbdp') }}" style="display: inline-flex; padding: 10px 18px; background-color: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px; text-decoration: none; align-items: center; gap: 6px; white-space: nowrap;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    @if (session('success'))
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
            <div>
                <label style="display: block; color: #6b7280; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Project Code</label>
                <p style="color: #111827; font-size: 15px; font-weight: 500; margin: 0;">{{ $project->project_code }}</p>
            </div>
            <div>
                <label style="display: block; color: #6b7280; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Funding Year</label>
                <p style="color: #111827; font-size: 15px; font-weight: 500; margin: 0;">{{ $project->funding_year ?: '-' }}</p>
            </div>
            <div>
                <label style="display: block; color: #6b7280; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Province</label>
                <p style="color: #111827; font-size: 15px; font-weight: 500; margin: 0;">{{ $project->province ?: '-' }}</p>
            </div>
            <div>
                <label style="display: block; color: #6b7280; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">City/Municipality</label>
                <p style="color: #111827; font-size: 15px; font-weight: 500; margin: 0;">{{ $project->city_municipality ?: '-' }}</p>
            </div>
            <div style="grid-column: 1 / -1;">
                <label style="display: block; color: #6b7280; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Project Title</label>
                <p style="color: #111827; font-size: 15px; font-weight: 500; margin: 0;">{{ $project->project_title ?: '-' }}</p>
            </div>
        </div>
    </div>

    @php
        $currentUser = Auth::user();
        $isDilg = strtoupper(trim((string) ($currentUser->agency ?? ''))) === 'DILG';
        $isRegionalDilg = $isDilg && strtolower(trim((string) ($currentUser->province ?? ''))) === 'regional office';
        $isProvincialDilg = $isDilg && !$isRegionalDilg;

        $resolveUserName = function ($id) use ($usersById) {
            if (!$id) {
                return 'Unknown';
            }

            $user = $usersById[$id] ?? null;
            if (!$user) {
                return 'Unknown';
            }

            return trim(($user->fname ?? '') . ' ' . ($user->lname ?? '')) ?: 'Unknown';
        };

        $asLocalTime = function ($value) {
            if (!$value) {
                return null;
            }

            if ($value instanceof \DateTimeInterface) {
                return \Carbon\Carbon::instance($value)->setTimezone(config('app.timezone'));
            }

            return \Carbon\Carbon::parse($value)->setTimezone(config('app.timezone'));
        };
    @endphp

    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 18px; flex-wrap: wrap;">
            <h2 style="color: #002C76; font-size: 18px; margin: 0; font-weight: 600;">Uploading of Documents</h2>

            <form method="POST" action="{{ route('pre-implementation-documents.sbdp.save', $project->project_code) }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                @csrf
                <label for="mode_of_contract" style="color: #374151; font-size: 12px; font-weight: 600;">Mode of Contract</label>
                <select id="mode_of_contract" name="mode_of_contract" style="padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; min-width: 220px;">
                    <option value="">Select</option>
                    @foreach ($allowedModeOfContract as $option)
                        <option value="{{ $option }}" {{ old('mode_of_contract', $document->mode_of_contract ?? '') === $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" style="padding: 9px 14px; background-color: #002C76; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 12px;">
                    Save Mode
                </button>
            </form>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, minmax(260px, 1fr)); gap: 16px; margin-bottom: 24px;">
            @foreach ($documentFields as $field => $label)
                @php
                    $fileRecord = $documentFilesByType[$field] ?? null;
                    $path = $fileRecord->file_path ?? ($document->{$field} ?? null);
                    $fileName = $path ? basename($path) : null;
                    $fileUrl = $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;

                    $hasFile = !empty($path);
                    $isReturned = $fileRecord && $fileRecord->status === 'returned';
                    $isApprovedRo = $fileRecord && $fileRecord->approved_at_dilg_ro;
                    $isPendingRo = $fileRecord && $fileRecord->approved_at_dilg_po && !$fileRecord->approved_at_dilg_ro;

                    $statusLabel = 'Pending Upload';
                    $statusColor = '#f59e0b';
                    if ($hasFile) {
                        $statusLabel = 'For DILG Provincial Office Validation';
                        $statusColor = '#3b82f6';
                    }
                    if ($isPendingRo) {
                        $statusLabel = 'For DILG Regional Office Validation';
                        $statusColor = '#3b82f6';
                    }
                    if ($isApprovedRo) {
                        $statusLabel = 'Approved';
                        $statusColor = '#059669';
                    }
                    if ($isReturned) {
                        $statusLabel = 'Returned';
                        $statusColor = '#dc2626';
                    }

                    $inputId = 'pre-impl-doc-input-' . $field;
                    $buttonId = 'pre-impl-doc-btn-' . $field;
                    $filenameId = 'pre-impl-doc-file-' . $field;

                    $uploadedTime = $asLocalTime($fileRecord->uploaded_at ?? $fileRecord->created_at ?? $fileRecord->updated_at ?? null);
                    $uploaderName = $resolveUserName($fileRecord->uploaded_by ?? null);
                    $poValidatedAt = $asLocalTime($fileRecord->approved_at_dilg_po ?? null);
                    $poApproverName = $resolveUserName($fileRecord->approved_by_dilg_po ?? null);
                    $roValidatedAt = $asLocalTime($fileRecord->approved_at_dilg_ro ?? null);
                    $roApproverName = $resolveUserName($fileRecord->approved_by_dilg_ro ?? null);

                    $uploaderUser = $fileRecord && $fileRecord->uploaded_by && isset($usersById[$fileRecord->uploaded_by])
                        ? $usersById[$fileRecord->uploaded_by]
                        : null;
                    $isDilgMountainUploader = $uploaderUser
                        && strtoupper(trim((string) ($uploaderUser->agency ?? ''))) === 'DILG'
                        && strtolower(trim((string) ($uploaderUser->province ?? ''))) === 'mountain province';

                    $isUploadedAndPoValidatedBySameUser = $fileRecord
                        && $uploadedTime
                        && $poValidatedAt
                        && $isDilgMountainUploader
                        && !empty($fileRecord->uploaded_by)
                        && !empty($fileRecord->approved_by_dilg_po)
                        && (string) $fileRecord->uploaded_by === (string) $fileRecord->approved_by_dilg_po
                        && $uploadedTime->getTimestamp() === $poValidatedAt->getTimestamp();

                    $returnedAt = $asLocalTime($fileRecord->approved_at ?? null);
                    $returnedByName = $resolveUserName($fileRecord?->approved_by_dilg_ro ?: $fileRecord?->approved_by_dilg_po ?: $fileRecord?->approved_by);
                    $returnedByLevel = null;
                    if (!empty($fileRecord?->approved_by_dilg_ro)) {
                        $returnedByLevel = 'DILG Regional Office';
                    } elseif (!empty($fileRecord?->approved_by_dilg_po)) {
                        $returnedByLevel = 'DILG Provincial Office';
                    }
                    $returnedRemarks = trim((string) ($fileRecord->approval_remarks ?? ''));
                    $returnedRemarks = $returnedRemarks !== '' ? $returnedRemarks : null;

                    $timelineEvents = [];
                    if ($uploadedTime) {
                        $timelineEvents[] = [
                            'timestamp' => $uploadedTime,
                            'priority' => 10,
                            'message' => $isUploadedAndPoValidatedBySameUser
                                ? 'Uploaded and Validated at: ' . $uploadedTime->format('M d, Y h:i A') . ' by ' . $uploaderName . ' (DILG Provincial Office)'
                                : 'Uploaded at: ' . $uploadedTime->format('M d, Y h:i A') . ' by ' . $uploaderName,
                            'color' => '#6b7280',
                            'font_size' => '11px',
                            'font_weight' => 'normal',
                        ];
                    }

                    if ($poValidatedAt && !$isUploadedAndPoValidatedBySameUser) {
                        $timelineEvents[] = [
                            'timestamp' => $poValidatedAt,
                            'priority' => 20,
                            'message' => 'DILG Provincial Validated at: ' . $poValidatedAt->format('M d, Y h:i A') . ' by ' . $poApproverName,
                            'color' => '#059669',
                            'font_size' => '10px',
                            'font_weight' => 'normal',
                        ];
                    }

                    if ($roValidatedAt) {
                        $timelineEvents[] = [
                            'timestamp' => $roValidatedAt,
                            'priority' => 30,
                            'message' => 'DILG Regional Validated at: ' . $roValidatedAt->format('M d, Y h:i A') . ' by ' . $roApproverName,
                            'color' => '#0891b2',
                            'font_size' => '10px',
                            'font_weight' => 'normal',
                        ];
                    }

                    if ($isReturned) {
                        $returnSuffix = '';
                        if ($returnedByLevel) {
                            $returnSuffix .= ' (' . $returnedByLevel . ')';
                        }
                        if ($returnedRemarks) {
                            $returnSuffix .= ' - Remarks: ' . $returnedRemarks;
                        }

                        $timelineEvents[] = [
                            'timestamp' => $returnedAt,
                            'priority' => 40,
                            'message' => 'Returned at: ' . ($returnedAt ? $returnedAt->format('M d, Y h:i A') : '-') . ' by ' . $returnedByName . $returnSuffix,
                            'color' => '#dc2626',
                            'font_size' => '10px',
                            'font_weight' => 'normal',
                        ];
                    }

                    usort($timelineEvents, function ($a, $b) {
                        $aTime = $a['timestamp'] instanceof \DateTimeInterface ? $a['timestamp']->getTimestamp() : PHP_INT_MAX;
                        $bTime = $b['timestamp'] instanceof \DateTimeInterface ? $b['timestamp']->getTimestamp() : PHP_INT_MAX;

                        if ($aTime === $bTime) {
                            return ($a['priority'] ?? 0) <=> ($b['priority'] ?? 0);
                        }

                        return $aTime <=> $bTime;
                    });

                    $isForRegionalValidation = $fileRecord && $fileRecord->approved_at_dilg_po && !$fileRecord->approved_at_dilg_ro;
                    $isApproved = $fileRecord && $fileRecord->status === 'approved';
                    $hideReturnButton = $isProvincialDilg && $isReturned;
                    $showApprovalButtons = $fileRecord
                        && $hasFile
                        && $isDilg
                        && !($isProvincialDilg && $isForRegionalValidation)
                        && !($isRegionalDilg && $isReturned)
                        && !($isRegionalDilg && $isApproved)
                        && !($isProvincialDilg && $isApproved);
                @endphp

                <form method="POST" action="{{ route('pre-implementation-documents.sbdp.save', $project->project_code) }}" enctype="multipart/form-data" style="border: 1px dashed #cbd5f5; padding: 18px; border-radius: 8px; background-color: #f9fafb;">
                    @csrf

                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 6px;">
                        <label style="display: block; color: #374151; font-weight: 600; font-size: 13px; margin: 0;">{{ $label }}</label>
                        <span style="display: inline-block; padding: 4px 10px; background-color: {{ $statusColor }}; color: white; border-radius: 20px; font-size: 10px; font-weight: 600;">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div style="font-size: 11px; color: #6b7280; margin-bottom: 8px; min-height: 40px;">
                        @if (empty($timelineEvents))
                            <div style="color: #9ca3af;">No upload activity yet.</div>
                        @endif
                        @foreach ($timelineEvents as $timelineEvent)
                            <div style="display: block; font-size: {{ $timelineEvent['font_size'] }}; font-weight: {{ $timelineEvent['font_weight'] }}; color: {{ $timelineEvent['color'] }}; {{ $loop->first ? '' : 'margin-top: 4px;' }}">
                                {{ $timelineEvent['message'] }}
                            </div>
                        @endforeach
                    </div>

                    @if ($fileUrl)
                        <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-bottom: 8px; color: #002C76; font-size: 12px; text-decoration: none;">
                            <i class="fas fa-file"></i> View current file{{ $fileName ? ': ' . $fileName : '' }}
                        </a>
                    @endif

                    <input
                        id="{{ $inputId }}"
                        type="file"
                        name="{{ $field }}"
                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                        required
                        @disabled($isRegionalDilg)
                        style="width: 100%; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 12px; margin-bottom: 8px;"
                        onchange="showPreImplementationSaveButton(this, '{{ $buttonId }}', '{{ $filenameId }}')"
                    >

                    @if ($isRegionalDilg)
                        <div style="margin-bottom: 8px; font-size: 11px; color: #6b7280;">
                            Regional Office cannot upload files. Choose file is disabled.
                        </div>
                    @endif

                    @error($field)
                        <div style="margin-bottom: 8px; color: #dc2626; font-size: 11px;">{{ $message }}</div>
                    @enderror

                    <div id="{{ $filenameId }}" style="display: none; margin-bottom: 8px; font-size: 12px; color: #6b7280;"></div>

                    <button
                        type="submit"
                        id="{{ $buttonId }}"
                        style="width: 100%; padding: 8px 12px; background-color: #002C76; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 12px; opacity: 0; pointer-events: none; transition: all 0.3s ease;"
                    >
                        Upload
                    </button>

                    @if ($isRegionalDilg && $hasFile && (!$fileRecord || !$fileRecord->approved_at_dilg_po))
                        <div style="font-size: 11px; color: #92400e; margin-top: 8px;">
                            Waiting for DILG Provincial validation.
                        </div>
                    @endif

                    @if ($showApprovalButtons)
                        <div style="display: flex; gap: 8px; margin-top: 8px;">
                            <button type="button" onclick="openPreImplementationApprovalModal('{{ $field }}', 'approve')" style="flex: 1; padding: 8px 12px; background-color: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 12px;">
                                Approve
                            </button>
                            @if (!$hideReturnButton)
                                <button type="button" onclick="openPreImplementationApprovalModal('{{ $field }}', 'return')" style="flex: 1; padding: 8px 12px; background-color: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 12px;">
                                    Return
                                </button>
                            @endif
                        </div>
                    @endif
                </form>
            @endforeach
        </div>

        <div style="margin-top: 12px; font-size: 11px; color: #6b7280;">
            Accepted formats: PDF, JPG, JPEG, PNG, DOC, DOCX. Maximum file size per document: 15 MB.
        </div>
    </div>

    <div id="preImplActivityLogModal" role="dialog" aria-modal="true" aria-labelledby="preImplActivityLogTitle" aria-hidden="true">
        <div style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; border-bottom: 2px solid #00267C; padding-bottom: 10px;">
                <h3 id="preImplActivityLogTitle" style="color: #002C76; font-size: 16px; font-weight: 700; margin: 0;">Activity Logs</h3>
                <button type="button" id="preImplActivityLogClose" aria-label="Close activity logs" style="border: none; background: #e2e8f0; color: #0f172a; width: 28px; height: 28px; border-radius: 999px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 16px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div style="max-height: 60vh; overflow-y: auto;">
                @if (empty($activityLogs))
                    <div style="padding: 16px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; color: #6b7280; font-size: 13px;">
                        No activity recorded yet.
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                                    <th style="padding: 10px; text-align: left; color: #374151; font-weight: 600; font-size: 12px;">Date/Time</th>
                                    <th style="padding: 10px; text-align: left; color: #374151; font-weight: 600; font-size: 12px;">Action</th>
                                    <th style="padding: 10px; text-align: left; color: #374151; font-weight: 600; font-size: 12px;">Document</th>
                                    <th style="padding: 10px; text-align: left; color: #374151; font-weight: 600; font-size: 12px;">User</th>
                                    <th style="padding: 10px; text-align: left; color: #374151; font-weight: 600; font-size: 12px;">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activityLogs as $log)
                                    @php
                                        $logUser = $log['user_id'] && isset($usersById[$log['user_id']])
                                            ? $usersById[$log['user_id']]
                                            : null;
                                    @endphp
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 10px; color: #111827; font-size: 12px;">
                                            {{ $log['timestamp'] ? $log['timestamp']->format('M d, Y H:i') : '—' }}
                                        </td>
                                        <td style="padding: 10px; color: #111827; font-size: 12px;">
                                            {{ $log['action'] }}
                                        </td>
                                        <td style="padding: 10px; color: #111827; font-size: 12px;">
                                            {{ $log['document'] }}
                                        </td>
                                        <td style="padding: 10px; color: #111827; font-size: 12px;">
                                            {{ $logUser ? trim($logUser->fname . ' ' . $logUser->lname) : 'Unknown' }}
                                        </td>
                                        <td style="padding: 10px; color: #6b7280; font-size: 12px;">
                                            {{ $log['remarks'] ?: '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="preImplActivityLogBackdrop" aria-hidden="true"></div>

    <button id="preImplActivityLogFab" type="button" aria-controls="preImplActivityLogModal" aria-expanded="false" data-state="closed">
        <i class="fas fa-clipboard-list" aria-hidden="true" style="font-size: 14px;"></i>
        <span>Activity Logs</span>
    </button>

    <style>
        #preImplActivityLogBackdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            z-index: 1190;
        }

        #preImplActivityLogBackdrop.is-visible {
            opacity: 1;
            visibility: visible;
        }

        #preImplActivityLogModal {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) scale(0.96);
            opacity: 0;
            visibility: hidden;
            width: min(920px, 92vw);
            max-height: 85vh;
            overflow: hidden;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.2);
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
            z-index: 1200;
        }

        #preImplActivityLogModal.is-visible {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, -50%) scale(1);
        }

        body.modal-open-pre-impl-logs {
            overflow: hidden;
        }

        #preImplActivityLogFab {
            position: fixed;
            right: 24px;
            bottom: 24px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            background-color: #002C76;
            color: white;
            border: none;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.18);
            z-index: 1200;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        #preImplActivityLogFab:hover {
            background-color: #0b3b84;
            transform: translateY(-2px);
            box-shadow: 0 14px 22px rgba(15, 23, 42, 0.22);
        }

        #preImplActivityLogFab:active {
            transform: translateY(0);
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.2);
        }

        #preImplActivityLogFab[data-state="open"] {
            background-color: #0f172a;
        }

        #preImplActivityLogFab span {
            white-space: nowrap;
        }
        @media (max-width: 900px) {
            div[style*="grid-template-columns: repeat(3, minmax(260px, 1fr));"] {
                grid-template-columns: repeat(2, minmax(240px, 1fr)) !important;
            }
        }

        @media (max-width: 768px) {
            #preImplActivityLogModal {
                width: 94vw;
            }

            #preImplActivityLogFab {
                right: 16px;
                bottom: 16px;
                padding: 10px 12px;
            }

            #preImplActivityLogFab span {
                display: none;
            }

            div[style*="grid-template-columns: repeat(3, minmax(260px, 1fr));"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <script>
        function showPreImplementationSaveButton(fileInput, buttonId, filenameId) {
            const saveBtn = document.getElementById(buttonId);
            const filenameDiv = document.getElementById(filenameId);
            if (!saveBtn || !filenameDiv) return;

            if (fileInput && fileInput.files && fileInput.files.length > 0) {
                const fileName = fileInput.files[0].name;
                saveBtn.style.opacity = '1';
                saveBtn.style.pointerEvents = 'auto';
                filenameDiv.textContent = `Selected: ${fileName}`;
                filenameDiv.style.display = 'block';
            } else {
                saveBtn.style.opacity = '0';
                saveBtn.style.pointerEvents = 'none';
                if (!filenameDiv.textContent.trim()) {
                    filenameDiv.style.display = 'none';
                }
            }
        }
    </script>

    <script>
        const preImplActivityLogModal = document.getElementById('preImplActivityLogModal');
        const preImplActivityLogBackdrop = document.getElementById('preImplActivityLogBackdrop');
        const preImplActivityLogFab = document.getElementById('preImplActivityLogFab');
        const preImplActivityLogClose = document.getElementById('preImplActivityLogClose');

        function setPreImplActivityLogVisibility(isVisible) {
            if (!preImplActivityLogModal || !preImplActivityLogBackdrop || !preImplActivityLogFab) {
                return;
            }

            preImplActivityLogModal.classList.toggle('is-visible', isVisible);
            preImplActivityLogBackdrop.classList.toggle('is-visible', isVisible);
            document.body.classList.toggle('modal-open-pre-impl-logs', isVisible);
            preImplActivityLogFab.setAttribute('aria-expanded', isVisible ? 'true' : 'false');
            preImplActivityLogFab.dataset.state = isVisible ? 'open' : 'closed';
            preImplActivityLogModal.setAttribute('aria-hidden', isVisible ? 'false' : 'true');
            preImplActivityLogBackdrop.setAttribute('aria-hidden', isVisible ? 'false' : 'true');

            const labelSpan = preImplActivityLogFab.querySelector('span');
            if (labelSpan) {
                labelSpan.textContent = isVisible ? 'Hide Activity Logs' : 'Activity Logs';
            }

            if (isVisible && preImplActivityLogClose) {
                preImplActivityLogClose.focus();
            }
        }

        if (preImplActivityLogFab && preImplActivityLogModal && preImplActivityLogBackdrop) {
            preImplActivityLogFab.addEventListener('click', () => {
                const isOpen = preImplActivityLogModal.classList.contains('is-visible');
                setPreImplActivityLogVisibility(!isOpen);
            });

            preImplActivityLogBackdrop.addEventListener('click', () => {
                setPreImplActivityLogVisibility(false);
            });

            if (preImplActivityLogClose) {
                preImplActivityLogClose.addEventListener('click', () => {
                    setPreImplActivityLogVisibility(false);
                });
            }
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && preImplActivityLogModal && preImplActivityLogModal.classList.contains('is-visible')) {
                setPreImplActivityLogVisibility(false);
            }
        });
    </script>

    <div id="preImplApprovalModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 24px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15); max-width: 420px; width: 90%;">
            <h3 id="preImplApprovalTitle" style="margin: 0 0 12px 0; color: #111827; font-size: 18px; font-weight: 600;">Approve Document</h3>
            <form id="preImplApprovalForm" method="POST">
                @csrf
                <input type="hidden" name="action" id="preImplApprovalAction">
                <textarea id="preImplApprovalRemarks" name="remarks" placeholder="Enter remarks (required for return)..." style="width: 100%; padding: 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px; font-family: inherit; resize: vertical; min-height: 120px;"></textarea>
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 14px;">
                    <button type="button" onclick="closePreImplementationApprovalModal()" style="padding: 10px 16px; background-color: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px;">Cancel</button>
                    <button type="submit" id="preImplApprovalSubmit" style="padding: 10px 16px; background-color: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px;">Confirm</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const preImplValidateBaseUrl = @json(url('/pre-implementation-documents/sbdp-projects/' . $project->project_code . '/validate'));

        function openPreImplementationApprovalModal(documentType, action) {
            const modal = document.getElementById('preImplApprovalModal');
            const form = document.getElementById('preImplApprovalForm');
            const title = document.getElementById('preImplApprovalTitle');
            const actionInput = document.getElementById('preImplApprovalAction');
            const remarks = document.getElementById('preImplApprovalRemarks');
            const submitBtn = document.getElementById('preImplApprovalSubmit');

            form.action = preImplValidateBaseUrl + '/' + encodeURIComponent(documentType);
            actionInput.value = action;
            remarks.value = '';

            if (action === 'return') {
                title.textContent = 'Return Document';
                submitBtn.style.backgroundColor = '#dc2626';
                remarks.required = true;
            } else {
                title.textContent = 'Approve Document';
                submitBtn.style.backgroundColor = '#10b981';
                remarks.required = false;
            }

            modal.style.display = 'block';
        }

        function closePreImplementationApprovalModal() {
            document.getElementById('preImplApprovalModal').style.display = 'none';
        }

        window.addEventListener('click', function (event) {
            const modal = document.getElementById('preImplApprovalModal');
            if (event.target === modal) {
                closePreImplementationApprovalModal();
            }
        });
    </script>
@endsection
