<?php

namespace App\Http\Controllers;

use App\Models\PreImplementationDocument;
use App\Models\PreImplementationDocumentFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PreImplementationDocumentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $allowedPerPage = [10, 15, 25, 50];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $filters = [
            'search' => trim((string) $request->input('search', '')),
            'province' => trim((string) $request->input('province', '')),
            'funding_year' => trim((string) $request->input('funding_year', '')),
        ];

        if (!Schema::hasTable('subay_project_profiles')) {
            $projects = new LengthAwarePaginator([], 0, $perPage, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            $filterOptions = [
                'provinces' => collect(),
                'funding_years' => collect(),
            ];

            return view('reports.pre-implementation-documents.index', compact('projects', 'filters', 'filterOptions', 'perPage'));
        }

        $baseQuery = $this->buildAccessibleSubayQuery(Auth::user());

        $filterOptions = [
            'provinces' => (clone $baseQuery)
                ->select('spp.province')
                ->whereNotNull('spp.province')
                ->where('spp.province', '!=', '')
                ->distinct()
                ->orderBy('spp.province')
                ->pluck('spp.province'),
            'funding_years' => (clone $baseQuery)
                ->select('spp.funding_year')
                ->whereNotNull('spp.funding_year')
                ->where('spp.funding_year', '!=', '')
                ->distinct()
                ->orderByRaw('CAST(spp.funding_year AS UNSIGNED) DESC')
                ->pluck('spp.funding_year'),
        ];

        $query = clone $baseQuery;

        if ($filters['search'] !== '') {
            $keyword = strtolower($filters['search']);
            $query->where(function ($subQuery) use ($keyword) {
                $like = '%' . $keyword . '%';
                $subQuery
                    ->whereRaw('LOWER(spp.project_code) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(spp.project_title) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(spp.province) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(spp.city_municipality) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(spp.barangay) LIKE ?', [$like]);
            });
        }

        if ($filters['province'] !== '') {
            $query->where('spp.province', $filters['province']);
        }

        if ($filters['funding_year'] !== '') {
            $query->whereRaw('CAST(NULLIF(TRIM(COALESCE(spp.funding_year, \'\')), \'\') AS UNSIGNED) = ?', [(int) $filters['funding_year']]);
        }

        $projects = $query
            ->select([
                'spp.project_code',
                'spp.project_title',
                'spp.province',
                'spp.city_municipality',
                'spp.barangay',
                'spp.funding_year',
                'spp.status',
                'spp.updated_at',
                DB::raw("'SBDP' as fund_source"),
            ])
            ->orderByRaw("CASE WHEN spp.funding_year IS NULL OR TRIM(spp.funding_year) = '' THEN 1 ELSE 0 END")
            ->orderByRaw('CAST(spp.funding_year AS UNSIGNED) ASC')
            ->orderBy('spp.project_code')
            ->paginate($perPage)
            ->withQueryString();

        return view('reports.pre-implementation-documents.index', compact('projects', 'filters', 'filterOptions', 'perPage'));
    }

    public function show(string $projectCode)
    {
        $project = $this->resolveProjectForUser($projectCode, Auth::user());
        if (!$project) {
            abort(404);
        }

        $document = PreImplementationDocument::where('project_code', $project->project_code)->first();
        $documentFiles = PreImplementationDocumentFile::where('project_code', $project->project_code)->get();
        $documentFilesByType = $documentFiles->keyBy('document_type');
        $activityLogs = $this->buildActivityLogs($documentFiles, $project->project_code);

        $documentUserIds = $documentFilesByType
            ->flatMap(function ($row) {
                return [
                    $row->uploaded_by,
                    $row->approved_by,
                    $row->approved_by_dilg_po,
                    $row->approved_by_dilg_ro,
                ];
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
        $logUserIds = collect($activityLogs)->pluck('user_id')->filter()->unique()->values()->all();
        $userIds = array_values(array_unique(array_merge($documentUserIds, $logUserIds)));

        $usersById = empty($userIds)
            ? collect()
            : User::whereIn('idno', $userIds)->get()->keyBy('idno');

        return view('reports.pre-implementation-documents.show', [
            'project' => $project,
            'document' => $document,
            'documentFilesByType' => $documentFilesByType,
            'usersById' => $usersById,
            'activityLogs' => $activityLogs,
            'documentFields' => $this->documentFieldMap(),
            'allowedModeOfContract' => ['By Contract', 'By Administration'],
        ]);
    }

    public function save(Request $request, string $projectCode)
    {
        $project = $this->resolveProjectForUser($projectCode, Auth::user());
        if (!$project) {
            abort(404);
        }

        $validationRules = [
            'mode_of_contract' => ['nullable', 'in:By Contract,By Administration'],
        ];

        foreach (array_keys($this->documentFieldMap()) as $field) {
            $validationRules[$field] = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:15360'];
        }

        $validated = $request->validate($validationRules);

        $document = PreImplementationDocument::firstOrNew(['project_code' => $project->project_code]);
        $document->project_title = $project->project_title;
        $document->province = $project->province;
        $document->city_municipality = $project->city_municipality;
        $document->funding_year = $project->funding_year;
        $document->mode_of_contract = $validated['mode_of_contract'] ?? $document->mode_of_contract;
        $document->updated_by = Auth::user()->idno ?? null;

        $folder = 'pre-implementation/sbdp/' . Str::slug((string) $project->project_code, '_');
        $now = now();
        $userId = Auth::user()->idno ?? null;

        foreach (array_keys($this->documentFieldMap()) as $field) {
            if (!$request->hasFile($field)) {
                continue;
            }

            $fileRecord = PreImplementationDocumentFile::firstOrNew([
                'project_code' => $project->project_code,
                'document_type' => $field,
            ]);

            if (!empty($fileRecord->file_path) && Storage::disk('public')->exists($fileRecord->file_path)) {
                Storage::disk('public')->delete($fileRecord->file_path);
            }

            $path = $request->file($field)->store($folder, 'public');

            $document->{$field} = $path;
            $fileRecord->file_path = $path;
            $fileRecord->uploaded_at = $now;
            $fileRecord->uploaded_by = $userId;
            $fileRecord->status = 'pending';
            $fileRecord->approved_at = null;
            $fileRecord->approved_by = null;
            $fileRecord->approved_at_dilg_po = null;
            $fileRecord->approved_by_dilg_po = null;
            $fileRecord->approved_at_dilg_ro = null;
            $fileRecord->approved_by_dilg_ro = null;
            $fileRecord->approval_remarks = null;
            $fileRecord->user_remarks = null;
            $fileRecord->save();

            $this->logActivity(
                $project->project_code,
                'upload',
                'Uploaded',
                $fileRecord,
                null,
                $now
            );
        }

        $document->save();

        return redirect()
            ->route('pre-implementation-documents.sbdp.show', $project->project_code)
            ->with('success', 'Pre-implementation documents saved successfully.');
    }

    public function validateDocument(Request $request, string $projectCode, string $documentType)
    {
        $project = $this->resolveProjectForUser($projectCode, Auth::user());
        if (!$project) {
            abort(404);
        }

        if (!array_key_exists($documentType, $this->documentFieldMap())) {
            abort(404);
        }

        $user = Auth::user();
        $isDilg = strtoupper(trim((string) ($user->agency ?? ''))) === 'DILG';
        if (!$isDilg) {
            abort(403);
        }

        $validated = $request->validate([
            'action' => ['required', 'in:approve,return'],
            'remarks' => ['nullable', 'string', 'max:1000', 'required_if:action,return'],
        ]);

        $fileRecord = PreImplementationDocumentFile::where('project_code', $project->project_code)
            ->where('document_type', $documentType)
            ->firstOrFail();

        if (empty($fileRecord->file_path)) {
            return back()->with('error', 'No file uploaded for this document yet.');
        }

        $action = $validated['action'];
        $remarks = trim((string) ($validated['remarks'] ?? ''));
        $isRegionalOffice = strcasecmp(trim((string) ($user->province ?? '')), 'Regional Office') === 0;
        $now = now();
        $userId = $user->idno ?? null;

        if ($action === 'approve') {
            if ($isRegionalOffice) {
                if (!$fileRecord->approved_at_dilg_po) {
                    return back()->with('error', 'Regional validation requires DILG Provincial validation first.');
                }

                $fileRecord->approved_at_dilg_ro = $now;
                $fileRecord->approved_by_dilg_ro = $userId;
                $fileRecord->status = 'approved';
            } else {
                $fileRecord->approved_at_dilg_po = $now;
                $fileRecord->approved_by_dilg_po = $userId;
                $fileRecord->approved_at_dilg_ro = null;
                $fileRecord->approved_by_dilg_ro = null;
                $fileRecord->status = 'pending_ro';
            }

            $fileRecord->approved_at = $now;
            $fileRecord->approved_by = $userId;
            $fileRecord->approval_remarks = null;
            $fileRecord->user_remarks = null;
            $fileRecord->save();

            $fileRecord->refresh();
            $this->logActivity(
                $project->project_code,
                $isRegionalOffice ? 'validate_ro' : 'validate_po',
                $isRegionalOffice ? 'Validated (DILG RO)' : 'Validated (DILG PO)',
                $fileRecord,
                null,
                $now
            );

            $this->notifyLguUsersAfterRegionalApproval(
                $project,
                $documentType,
                $fileRecord,
                $action,
                $isRegionalOffice,
                null
            );

            return back()->with('success', 'Document validated successfully.');
        }

        // return
        if ($isRegionalOffice) {
            $fileRecord->approved_at_dilg_ro = null;
            $fileRecord->approved_by_dilg_ro = $userId;
        } else {
            $fileRecord->approved_at_dilg_po = null;
            $fileRecord->approved_by_dilg_po = $userId;
            $fileRecord->approved_at_dilg_ro = null;
            $fileRecord->approved_by_dilg_ro = null;
        }

        $fileRecord->status = 'returned';
        $fileRecord->approved_at = $now;
        $fileRecord->approved_by = $userId;
        $fileRecord->approval_remarks = $remarks;
        $fileRecord->user_remarks = $remarks;
        $fileRecord->save();

        $fileRecord->refresh();
        $this->logActivity(
            $project->project_code,
            'return',
            'Returned',
            $fileRecord,
            $remarks !== '' ? $remarks : null,
            $now
        );

        $this->notifyLguUsersAfterRegionalApproval(
            $project,
            $documentType,
            $fileRecord,
            $action,
            $isRegionalOffice,
            $remarks !== '' ? $remarks : null
        );

        return back()->with('success', 'Document returned with remarks.');
    }

    private function formatDocumentLabel(string $documentType): string
    {
        $label = $this->documentFieldMap()[$documentType] ?? null;
        if ($label) {
            return $label;
        }

        return strtoupper(str_replace('_', ' ', $documentType));
    }

    private function notifyLguUsersAfterRegionalApproval(
        object $project,
        string $documentType,
        PreImplementationDocumentFile $fileRecord,
        string $action,
        bool $isRegionalOffice,
        ?string $remarks = null
    ): void
    {
        try {
            if (!Schema::hasTable('tbnotifications')) {
                return;
            }

            $actor = Auth::user();
            if (!$actor || strtoupper(trim((string) ($actor->agency ?? ''))) !== 'DILG') {
                return;
            }

            $targetProvince = trim((string) ($project->province ?? ''));
            $targetOffice = trim((string) ($project->city_municipality ?? ''));

            if ($targetProvince === '' && $targetOffice === '') {
                return;
            }

            $candidateOfficeNames = collect([$targetOffice])
                ->map(function ($value) {
                    return strtolower(trim((string) $value));
                })
                ->filter(function ($value) {
                    return $value !== '';
                })
                ->flatMap(function ($value) {
                    $withoutPrefix = trim((string) preg_replace('/^(municipality|city)\s+of\s+/i', '', $value));
                    return array_values(array_unique(array_filter([$value, $withoutPrefix])));
                })
                ->values()
                ->all();

            $recipientQuery = User::query()
                ->whereRaw('UPPER(TRIM(COALESCE(agency, ""))) = ?', ['LGU'])
                ->where('status', 'active');

            if ($targetProvince !== '') {
                $recipientQuery->whereRaw('LOWER(TRIM(COALESCE(province, ""))) = ?', [strtolower($targetProvince)]);
            }

            $provinceRecipients = $recipientQuery->get(['idno', 'office']);
            if ($provinceRecipients->isEmpty()) {
                return;
            }

            $recipients = $provinceRecipients;
            if (!empty($candidateOfficeNames)) {
                $filteredRecipients = $provinceRecipients->filter(function ($lguUser) use ($candidateOfficeNames) {
                    $office = strtolower(trim((string) ($lguUser->office ?? '')));
                    $officeWithoutPrefix = trim((string) preg_replace('/^(municipality|city)\s+of\s+/i', '', $office));
                    return in_array($office, $candidateOfficeNames, true)
                        || in_array($officeWithoutPrefix, $candidateOfficeNames, true);
                })->values();

                // Fallback to province-level recipients when office normalization does not match.
                if ($filteredRecipients->isNotEmpty()) {
                    $recipients = $filteredRecipients;
                }
            }

            $relatedUserIds = collect([
                $fileRecord->uploaded_by,
                $fileRecord->approved_by_dilg_po,
                $fileRecord->approved_by_dilg_ro,
                $fileRecord->approved_by,
            ])->filter()->map(function ($value) {
                return (int) $value;
            });

            $recipientIds = $recipients->pluck('idno')->merge($relatedUserIds);
            if (!$isRegionalOffice) {
                $regionalDilgIds = User::query()
                    ->whereRaw('UPPER(TRIM(COALESCE(agency, ""))) = ?', ['DILG'])
                    ->where('status', 'active')
                    ->where(function ($query) {
                        $query->whereRaw('LOWER(TRIM(COALESCE(province, ""))) = ?', ['regional office'])
                            ->orWhereRaw('LOWER(TRIM(COALESCE(office, ""))) LIKE ?', ['%regional office%']);
                    })
                    ->pluck('idno');
                $recipientIds = $recipientIds->merge($regionalDilgIds);
            }

            $actorName = trim((string) ($actor->fname ?? '') . ' ' . (string) ($actor->lname ?? ''));
            if ($actorName === '') {
                $actorName = 'DILG Regional Office';
            }

            $projectCode = trim((string) ($project->project_code ?? ''));
            $projectTitle = trim((string) ($project->project_title ?? ''));
            $projectLabel = $projectCode;
            if ($projectTitle !== '') {
                $projectLabel .= ' (' . $projectTitle . ')';
            }

            $actionLabel = $action === 'approve'
                ? ($isRegionalOffice ? 'approved' : 'validated (DILG PO)')
                : 'returned';

            $message = sprintf(
                '%s %s %s for %s%s%s.',
                $actorName,
                $actionLabel,
                $this->formatDocumentLabel($documentType),
                $projectLabel !== '' ? $projectLabel : 'an SBDP project',
                $targetOffice !== '' ? ' - ' . $targetOffice : '',
                $targetProvince !== '' ? ' - ' . $targetProvince : ''
            );

            if ($action === 'return' && $remarks) {
                $message .= ' Remarks: ' . $remarks;
            }

            $now = now();
            $url = $projectCode !== ''
                ? route('pre-implementation-documents.sbdp.show', ['projectCode' => $projectCode])
                : route('pre-implementation-documents.sbdp');
            $actorId = (int) Auth::id();

            $rows = collect($recipientIds)
                ->map(function ($id) {
                    return (int) $id;
                })
                ->filter(function ($id) use ($actorId) {
                    return $id > 0 && $id !== $actorId;
                })
                ->unique()
                ->values()
                ->map(function ($recipientId) use ($message, $url, $documentType, $now) {
                    return [
                        'user_id' => $recipientId,
                        'message' => $message,
                        'url' => $url,
                        'document_type' => substr('pre-implementation-' . $documentType, 0, 100),
                        'quarter' => null,
                        'read_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })
                ->values()
                ->all();

            if (!empty($rows)) {
                DB::table('tbnotifications')->insert($rows);
            }
        } catch (\Throwable $error) {
            Log::warning('Failed to create approval notifications (Pre-Implementation).', [
                'project_code' => $project->project_code ?? null,
                'document_type' => $documentType,
                'error' => $error->getMessage(),
            ]);
        }
    }

    private function buildCurrentActivityLogs($documentFiles): array
    {
        $logs = [];

        foreach ($documentFiles as $fileRecord) {
            $documentLabel = $this->formatDocumentLabel((string) $fileRecord->document_type);

            if ($fileRecord->uploaded_at) {
                $logs[] = [
                    'timestamp' => $fileRecord->uploaded_at,
                    'action' => 'Uploaded',
                    'document' => $documentLabel,
                    'user_id' => $fileRecord->uploaded_by,
                    'remarks' => null,
                ];
            }

            if ($fileRecord->approved_at_dilg_po) {
                $logs[] = [
                    'timestamp' => $fileRecord->approved_at_dilg_po,
                    'action' => 'Validated (DILG PO)',
                    'document' => $documentLabel,
                    'user_id' => $fileRecord->approved_by_dilg_po,
                    'remarks' => null,
                ];
            }

            if ($fileRecord->approved_at_dilg_ro) {
                $logs[] = [
                    'timestamp' => $fileRecord->approved_at_dilg_ro,
                    'action' => 'Validated (DILG RO)',
                    'document' => $documentLabel,
                    'user_id' => $fileRecord->approved_by_dilg_ro,
                    'remarks' => null,
                ];
            }

            if ($fileRecord->status === 'returned') {
                $logs[] = [
                    'timestamp' => $fileRecord->approved_at ?? $fileRecord->updated_at ?? $fileRecord->uploaded_at,
                    'action' => 'Returned',
                    'document' => $documentLabel,
                    'user_id' => $fileRecord->approved_by_dilg_ro ?: ($fileRecord->approved_by_dilg_po ?: $fileRecord->approved_by),
                    'remarks' => $fileRecord->approval_remarks,
                ];
            }
        }

        return $logs;
    }

    private function parsePersistedActivityLog(string $line, string $projectCode): ?array
    {
        $pattern = '/^\[([^\]]+)\]\s+[^\:]+\.\w+:\s+([^{]+)\s*(\{.*)/';
        if (!preg_match($pattern, $line, $matches)) {
            return null;
        }

        $loggedAt = trim($matches[1]);
        $contextJson = $matches[3];
        $context = json_decode($contextJson, true);
        if (!is_array($context)) {
            return null;
        }

        if (($context['module'] ?? null) !== 'pre_implementation_documents') {
            return null;
        }

        if (trim((string) ($context['project_code'] ?? '')) !== trim($projectCode)) {
            return null;
        }

        $timestampRaw = $context['action_timestamp'] ?? $loggedAt;
        try {
            $timestamp = Carbon::parse($timestampRaw)->setTimezone(config('app.timezone'));
        } catch (\Throwable $e) {
            $timestamp = Carbon::parse($loggedAt)->setTimezone(config('app.timezone'));
        }

        return [
            'timestamp' => $timestamp,
            'action' => $context['action_label'] ?? 'Updated',
            'document' => $context['document_label'] ?? 'Document',
            'user_id' => $context['user_id'] ?? null,
            'remarks' => $context['remarks'] ?? null,
        ];
    }

    private function getPersistedActivityLogs(string $projectCode): array
    {
        $logFiles = glob(storage_path('logs/upload_timestamps-*.log')) ?: [];
        $singleLogFile = storage_path('logs/upload_timestamps.log');
        if (is_file($singleLogFile)) {
            $logFiles[] = $singleLogFile;
        }
        rsort($logFiles);

        $entries = [];
        foreach ($logFiles as $logFile) {
            $content = @file_get_contents($logFile);
            if (!$content) {
                continue;
            }

            $logEntries = preg_split('/(?=\[\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}\])/', $content, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($logEntries as $logEntry) {
                $logEntry = trim($logEntry);
                if ($logEntry === '' || strpos($logEntry, '"module":"pre_implementation_documents"') === false) {
                    continue;
                }

                $parsed = $this->parsePersistedActivityLog($logEntry, $projectCode);
                if ($parsed) {
                    $entries[] = $parsed;
                }
            }
        }

        return $entries;
    }

    private function buildActivityLogs($documentFiles, string $projectCode): array
    {
        $persistedLogs = $this->getPersistedActivityLogs($projectCode);
        $currentLogs = $this->buildCurrentActivityLogs($documentFiles);

        if (empty($persistedLogs)) {
            $logs = $currentLogs;
        } else {
            $logs = $persistedLogs;

            foreach ($currentLogs as $currentLog) {
                $existsInPersisted = false;
                foreach ($persistedLogs as $persistedLog) {
                    $currentTs = ($currentLog['timestamp'] instanceof \DateTimeInterface) ? $currentLog['timestamp']->getTimestamp() : null;
                    $persistedTs = ($persistedLog['timestamp'] instanceof \DateTimeInterface) ? $persistedLog['timestamp']->getTimestamp() : null;

                    if (
                        $currentTs === $persistedTs
                        && ($currentLog['action'] ?? '') === ($persistedLog['action'] ?? '')
                        && ($currentLog['document'] ?? '') === ($persistedLog['document'] ?? '')
                        && (string) ($currentLog['user_id'] ?? '') === (string) ($persistedLog['user_id'] ?? '')
                        && (string) ($currentLog['remarks'] ?? '') === (string) ($persistedLog['remarks'] ?? '')
                    ) {
                        $existsInPersisted = true;
                        break;
                    }
                }

                if (!$existsInPersisted) {
                    $logs[] = $currentLog;
                }
            }
        }

        usort($logs, function ($a, $b) {
            $aTime = $a['timestamp'] ? $a['timestamp']->getTimestamp() : 0;
            $bTime = $b['timestamp'] ? $b['timestamp']->getTimestamp() : 0;
            return $bTime <=> $aTime;
        });

        return $logs;
    }

    private function logActivity(
        string $projectCode,
        string $action,
        string $actionLabel,
        PreImplementationDocumentFile $documentFile,
        ?string $remarks = null,
        ?Carbon $timestamp = null
    ): void {
        $timestamp = $timestamp ?: now();

        Log::channel('upload_timestamps')->info('Document action', [
            'module' => 'pre_implementation_documents',
            'project_code' => $projectCode,
            'document_type' => $documentFile->document_type,
            'document_label' => $this->formatDocumentLabel((string) $documentFile->document_type),
            'action' => $action,
            'action_label' => $actionLabel,
            'action_timestamp' => $timestamp->format('Y-m-d H:i:s'),
            'user_id' => Auth::id(),
            'remarks' => $remarks,
        ]);
    }

    private function buildAccessibleSubayQuery($user)
    {
        $agency = strtoupper(trim((string) ($user->agency ?? '')));
        $province = trim((string) ($user->province ?? ''));
        $office = trim((string) ($user->office ?? ''));
        $region = trim((string) ($user->region ?? ''));
        $provinceLower = strtolower($province);
        $officeLower = strtolower($office);
        $regionLower = strtolower($region);

        $query = DB::table('subay_project_profiles as spp')
            ->whereRaw('LOWER(TRIM(COALESCE(spp.project_code, \'\'))) LIKE ?', ['%sbdp%'])
            ->whereRaw('CAST(NULLIF(TRIM(COALESCE(spp.funding_year, \'\')), \'\') AS UNSIGNED) >= 2024');

        if ($agency === 'LGU') {
            if ($office !== '') {
                if ($province !== '') {
                    $query
                        ->whereRaw('LOWER(spp.province) = ?', [$provinceLower])
                        ->whereRaw('LOWER(spp.city_municipality) = ?', [$officeLower]);
                } else {
                    $query->whereRaw('LOWER(spp.city_municipality) = ?', [$officeLower]);
                }
            } elseif ($province !== '') {
                $query->whereRaw('LOWER(spp.province) = ?', [$provinceLower]);
            }
        } elseif ($agency === 'DILG') {
            if ($provinceLower === 'regional office') {
                // Regional Office can access all matched projects.
            } elseif ($province !== '') {
                $query->whereRaw('LOWER(spp.province) = ?', [$provinceLower]);
            } elseif ($region !== '') {
                $query->whereRaw('LOWER(spp.region) = ?', [$regionLower]);
            }
        }

        return $query;
    }

    private function resolveProjectForUser(string $projectCode, $user): ?object
    {
        $projectCode = trim($projectCode);
        if ($projectCode === '') {
            return null;
        }

        return $this->buildAccessibleSubayQuery($user)
            ->where('spp.project_code', $projectCode)
            ->select([
                'spp.project_code',
                'spp.project_title',
                'spp.province',
                'spp.city_municipality',
                'spp.barangay',
                'spp.funding_year',
                'spp.status',
            ])
            ->first();
    }

    private function documentFieldMap(): array
    {
        return [
            'signed_lgu_letter_path' => 'Signed LGU Letter',
            'signed_lgu_contact_details_path' => 'Signed LGU Contact Details',
            'nadai_path' => 'NADAI',
            'confirmation_receipt_fund_path' => 'Confirmation on the Receipt of Fund',
            'proof_transfer_trust_fund_path' => 'Proof on the Transfer of Fund to LGU Trust Fund',
            'approved_ldip_path' => 'Approved LDIP',
            'approved_aip_path' => 'Approved AIP',
            'approved_dtp_path' => 'Approved DTP',
            'ecc_or_cnc_path' => 'ECC or CNC',
            'water_permit_or_application_path' => 'Water Permit or Application',
            'fpic_or_ncip_certification_path' => 'FPIC / NCIP Certification',
            'itb_posting_philgeps_path' => 'ITB Posting on PhilGEPS',
            'noa_path' => 'NOA',
            'contract_path' => 'Contract',
            'ntp_path' => 'NTP',
            'land_ownership_path' => 'Land Ownership',
            'right_of_way_path' => 'Right of Way',
            'moa_rural_electrification_path' => 'MOA (For Rural Electrification Projects)',
        ];
    }
}

