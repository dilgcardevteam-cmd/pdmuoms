<?php

namespace App\Http\Controllers;

use App\Models\RbisAnnualCertificationDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RbisAnnualCertificationController extends Controller
{
    private function getOffices(): array
    {
        return [
            'Abra' => [
                'PLGU Abra', 'Bangued', 'Boliney', 'Bucay', 'Bucloc', 'Daguioman', 'Danglas', 'Dolores',
                'La Paz', 'Lacub', 'Lagangilang', 'Lagayan', 'Langiden', 'Licuan-Baay', 'Luba', 'Malibcong',
                'Manabo', 'Peñarrubia', 'Pidigan', 'Pilar', 'Sallapadan', 'San Isidro', 'San Juan',
                'San Quintin', 'Tayum', 'Tineg', 'Tubo', 'Villaviciosa',
            ],
            'Apayao' => [
                'PLGU Apayao', 'Calanasan', 'Conner', 'Flora', 'Kabugao', 'Luna', 'Pudtol', 'Santa Marcela',
            ],
            'Benguet' => [
                'PLGU Benguet', 'Atok', 'Bakun', 'Bokod', 'Buguias', 'Itogon', 'Kabayan', 'Kapangan',
                'Kibungan', 'La Trinidad', 'Mankayan', 'Sablan', 'Tuba', 'Tublay',
            ],
            'City of Baguio' => [
                'City of Baguio',
            ],
            'Ifugao' => [
                'PLGU Ifugao', 'Aguinaldo', 'Alfonso Lista', 'Asipulo', 'Banaue', 'Hingyon', 'Hungduan',
                'Kiangan', 'Lagawe', 'Lamut', 'Mayoyao', 'Tinoc',
            ],
            'Kalinga' => [
                'PLGU Kalinga', 'Balbalan', 'Lubuagan', 'Pasil', 'Pinukpuk', 'Rizal', 'Tabuk', 'Tanudan',
            ],
            'Mountain Province' => [
                'PLGU Mountain Province', 'Barlig', 'Bauko', 'Besao', 'Bontoc', 'Natonin', 'Paracelis',
                'Sabangan', 'Sadanga', 'Sagada', 'Tadian',
            ],
        ];
    }

    private function getSortedOfficesByProvince(): array
    {
        $officesByProvince = $this->getOffices();
        ksort($officesByProvince, SORT_NATURAL | SORT_FLAG_CASE);

        foreach ($officesByProvince as $province => $offices) {
            usort($offices, function (string $a, string $b): int {
                $aIsPlgu = str_starts_with($a, 'PLGU ');
                $bIsPlgu = str_starts_with($b, 'PLGU ');

                if ($aIsPlgu && !$bIsPlgu) {
                    return -1;
                }

                if (!$aIsPlgu && $bIsPlgu) {
                    return 1;
                }

                return strcasecmp($a, $b);
            });

            $officesByProvince[$province] = $offices;
        }

        return $officesByProvince;
    }

    private function resolveReportingYear(Request $request): int
    {
        $year = (int) $request->query('year', now()->year);
        if ($year < 2000 || $year > 2100) {
            $year = (int) now()->year;
        }

        return $year;
    }

    private function applyReportingYearFilter($query, int $reportingYear): void
    {
        $query->where(function ($yearQuery) use ($reportingYear) {
            $yearQuery->where('document_year', $reportingYear)
                ->orWhere(function ($legacyQuery) use ($reportingYear) {
                    $legacyQuery->whereNull('document_year')
                        ->whereYear('uploaded_at', $reportingYear);
                });
        });
    }

    private function buildOfficeRows(array $officesByProvince): array
    {
        $officeRows = [];
        foreach ($officesByProvince as $province => $offices) {
            foreach ($offices as $office) {
                $officeRows[] = [
                    'province' => $province,
                    'city_municipality' => $office,
                ];
            }
        }

        return $officeRows;
    }

    private function findProvinceByOffice(string $officeName): ?string
    {
        foreach ($this->getOffices() as $province => $offices) {
            if (in_array($officeName, $offices, true)) {
                return $province;
            }
        }

        return null;
    }

    private function canAccessOffice(string $officeName, string $province): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        $agency = strtoupper(trim((string) $user->agency));
        $userProvince = trim((string) $user->province);
        $userOffice = trim((string) $user->office);

        if ($agency === 'LGU') {
            return $userOffice !== '' && $userOffice === $officeName;
        }

        if ($agency === 'DILG') {
            if ($userProvince === '' || $userProvince === 'Regional Office') {
                return true;
            }

            return $userProvince === $province;
        }

        return true;
    }

    private function formatDocumentLabel(RbisAnnualCertificationDocument $document): string
    {
        $label = trim((string) ($document->document_name ?: 'RBIS Annual Certification Document'));
        if (!empty($document->document_year)) {
            $label .= ' (CY ' . $document->document_year . ')';
        }

        return $label;
    }

    private function buildCurrentActivityLogs($documents): array
    {
        $logs = [];

        foreach ($documents as $doc) {
            $docLabel = $this->formatDocumentLabel($doc);

            if ($doc->uploaded_at) {
                $logs[] = [
                    'timestamp' => $doc->uploaded_at,
                    'action' => 'Uploaded',
                    'document' => $docLabel,
                    'user_id' => $doc->uploaded_by,
                    'remarks' => null,
                ];
            }

            if ($doc->approved_at_dilg_po) {
                $logs[] = [
                    'timestamp' => $doc->approved_at_dilg_po,
                    'action' => 'Validated (DILG PO)',
                    'document' => $docLabel,
                    'user_id' => $doc->approved_by_dilg_po,
                    'remarks' => null,
                ];
            }

            if ($doc->approved_at_dilg_ro) {
                $logs[] = [
                    'timestamp' => $doc->approved_at_dilg_ro,
                    'action' => 'Validated (DILG RO)',
                    'document' => $docLabel,
                    'user_id' => $doc->approved_by_dilg_ro,
                    'remarks' => null,
                ];
            }

            if ($doc->status === 'returned') {
                $logs[] = [
                    'timestamp' => $doc->approved_at ?? $doc->updated_at ?? $doc->uploaded_at,
                    'action' => 'Returned',
                    'document' => $docLabel,
                    'user_id' => $doc->approved_by_dilg_ro ?: $doc->approved_by_dilg_po,
                    'remarks' => $doc->approval_remarks,
                ];
            }
        }

        return $logs;
    }

    private function parsePersistedActivityLog(string $line, string $officeName): ?array
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

        if (($context['module'] ?? null) !== 'rbis_annual_certification') {
            return null;
        }

        if (trim((string) ($context['office'] ?? '')) !== trim($officeName)) {
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
            'document' => $context['document_label'] ?? 'RBIS Annual Certification Document',
            'user_id' => $context['user_id'] ?? null,
            'remarks' => $context['remarks'] ?? null,
        ];
    }

    private function getPersistedActivityLogs(string $officeName): array
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
                if ($logEntry === '' || strpos($logEntry, '"module":"rbis_annual_certification"') === false) {
                    continue;
                }

                $parsed = $this->parsePersistedActivityLog($logEntry, $officeName);
                if ($parsed) {
                    $entries[] = $parsed;
                }
            }
        }

        return $entries;
    }

    private function buildActivityLogs($documents, string $officeName): array
    {
        $persistedLogs = $this->getPersistedActivityLogs($officeName);
        $currentLogs = $this->buildCurrentActivityLogs($documents);

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
        string $officeName,
        string $action,
        string $actionLabel,
        RbisAnnualCertificationDocument $document,
        ?string $remarks = null,
        ?Carbon $timestamp = null
    ): void {
        $timestamp = $timestamp ?: now();

        Log::channel('upload_timestamps')->info('Document action', [
            'module' => 'rbis_annual_certification',
            'office' => $officeName,
            'document_label' => $this->formatDocumentLabel($document),
            'action' => $action,
            'action_label' => $actionLabel,
            'action_timestamp' => $timestamp->format('Y-m-d H:i:s'),
            'user_id' => auth()->id(),
            'remarks' => $remarks,
        ]);
    }

    private function notifyLguUsersAfterRegionalApproval(RbisAnnualCertificationDocument $document, string $officeName): void
    {
        try {
            if (!Schema::hasTable('tbnotifications')) {
                return;
            }

            $actor = auth()->user();
            if (!$actor || strtoupper(trim((string) ($actor->agency ?? ''))) !== 'DILG') {
                return;
            }

            $isRegionalOffice = strcasecmp(trim((string) ($actor->province ?? '')), 'Regional Office') === 0
                || str_contains(strtolower(trim((string) ($actor->office ?? ''))), 'regional office');
            if (!$isRegionalOffice) {
                return;
            }

            $targetOffice = trim((string) $officeName);
            $targetProvince = trim((string) ($document->province ?? ''));
            if ($targetProvince === '' && $targetOffice !== '') {
                $targetProvince = trim((string) ($this->findProvinceByOffice($targetOffice) ?? ''));
            }

            if ($targetOffice === '' && $targetProvince === '') {
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

            $actorName = trim((string) ($actor->fname ?? '') . ' ' . (string) ($actor->lname ?? ''));
            if ($actorName === '') {
                $actorName = 'DILG Regional Office';
            }

            $message = sprintf(
                '%s approved %s for %s%s.',
                $actorName,
                $this->formatDocumentLabel($document),
                $targetOffice !== '' ? $targetOffice : 'the LGU',
                $targetProvince !== '' ? ' - ' . $targetProvince : ''
            );

            $now = now();
            $url = $targetOffice !== ''
                ? route('rbis-annual-certification.edit', ['office' => $targetOffice])
                : route('rbis-annual-certification.index');
            $actorId = (int) auth()->id();

            $rows = $recipients
                ->filter(function ($recipient) use ($actorId) {
                    return (int) ($recipient->idno ?? 0) !== $actorId;
                })
                ->map(function ($recipient) use ($message, $url, $now) {
                    return [
                        'user_id' => (int) $recipient->idno,
                        'message' => $message,
                        'url' => $url,
                        'document_type' => 'rbis-annual-certification',
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
            Log::warning('Failed to create LGU notifications after regional approval (RBIS).', [
                'document_id' => $document->id ?? null,
                'office' => $officeName,
                'error' => $error->getMessage(),
            ]);
        }
    }

    public function index(Request $request)
    {
        $reportingYear = $this->resolveReportingYear($request);
        $officeRows = $this->buildOfficeRows($this->getSortedOfficesByProvince());

        $user = auth()->user();
        if ($user && $user->agency === 'LGU' && !empty($user->office)) {
            $officeRows = array_values(array_filter($officeRows, function ($row) use ($user) {
                return $row['city_municipality'] === $user->office;
            }));
        } elseif ($user && $user->agency === 'DILG' && !empty($user->province) && $user->province !== 'Regional Office') {
            $officeRows = array_values(array_filter($officeRows, function ($row) use ($user) {
                return $row['province'] === $user->province;
            }));
        }

        $officeNames = array_values(array_unique(array_map(function ($row) {
            return $row['city_municipality'];
        }, $officeRows)));

        $uploadCountsByOffice = collect();
        if (!empty($officeNames)) {
            $uploadCountsByOfficeQuery = RbisAnnualCertificationDocument::query()
                ->whereIn('office', $officeNames)
                ->selectRaw('office, COUNT(*) as total');
            $this->applyReportingYearFilter($uploadCountsByOfficeQuery, $reportingYear);

            $uploadCountsByOffice = $uploadCountsByOfficeQuery
                ->groupBy('office')
                ->pluck('total', 'office');
        }

        $totalProvinces = count(array_unique(array_map(function ($row) {
            return $row['province'];
        }, $officeRows)));
        $totalOffices = count($officeRows);

        return view('reports.rbis-annual-certification.index', compact('officeRows', 'uploadCountsByOffice', 'totalProvinces', 'totalOffices', 'reportingYear'));
    }

    public function edit(Request $request, $id)
    {
        $reportingYear = $this->resolveReportingYear($request);
        $officeName = $id;
        $province = $this->findProvinceByOffice($officeName);
        if (!$province) {
            abort(404);
        }

        if (!$this->canAccessOffice($officeName, $province)) {
            abort(403);
        }

        $documentsQuery = RbisAnnualCertificationDocument::query()
            ->where('office', $officeName);
        $this->applyReportingYearFilter($documentsQuery, $reportingYear);
        $documents = $documentsQuery
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id')
            ->limit(1)
            ->get();
        $activityLogs = $this->buildActivityLogs($documents, $officeName);

        $uploaderIds = $documents->pluck('uploaded_by')->filter()->unique()->values()->all();
        $approverIds = $documents->pluck('approved_by_dilg_po')
            ->merge($documents->pluck('approved_by_dilg_ro'))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $logUserIds = collect($activityLogs)->pluck('user_id')->filter()->unique()->values()->all();
        $userIds = array_values(array_unique(array_merge($uploaderIds, $approverIds, $logUserIds)));
        $usersById = $userIds
            ? User::whereIn('idno', $userIds)->get()->keyBy('idno')
            : collect();

        return view('reports.rbis-annual-certification.edit', compact('officeName', 'province', 'documents', 'usersById', 'activityLogs', 'reportingYear'));
    }

    public function upload(Request $request, $id)
    {
        $officeName = $id;
        $province = $this->findProvinceByOffice($officeName);
        if (!$province) {
            abort(404);
        }

        if (!$this->canAccessOffice($officeName, $province)) {
            abort(403);
        }

        $user = auth()->user();
        if ($user && strtoupper(trim((string) $user->agency)) === 'DILG' && trim((string) $user->province) === 'Regional Office') {
            return back()->withErrors([
                'document' => 'Regional Office cannot upload files.',
            ]);
        }

        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
            'year' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $reportingYear = (int) $request->input('year');
        $file = $request->file('document');
        $officeSlug = Str::slug($officeName, '_');
        $existingDocumentQuery = RbisAnnualCertificationDocument::query()
            ->where('office', $officeName);
        $this->applyReportingYearFilter($existingDocumentQuery, $reportingYear);
        $existingDocument = $existingDocumentQuery
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id')
            ->first();
        $oldFilePath = $existingDocument?->file_path;

        $path = $file->store('rbis-annual-certification/' . $officeSlug, 'public');
        $uploadedAt = now();
        $isMountainProvinceDilgUploader = $user
            && strtoupper(trim((string) $user->agency)) === 'DILG'
            && strtolower(trim((string) $user->province)) === 'mountain province';

        $documentPayload = [
            'province' => $province,
            'document_name' => 'RBIS Annual Certification Document (CY ' . $reportingYear . ')',
            'document_year' => $reportingYear,
            'remarks' => null,
            'file_path' => $path,
            'uploaded_by' => auth()->id(),
            'uploaded_at' => $uploadedAt,
            'status' => $isMountainProvinceDilgUploader ? 'pending_ro' : 'pending',
            'approved_at' => $isMountainProvinceDilgUploader ? $uploadedAt : null,
            'approved_at_dilg_po' => $isMountainProvinceDilgUploader ? $uploadedAt : null,
            'approved_at_dilg_ro' => null,
            'approved_by_dilg_po' => $isMountainProvinceDilgUploader ? ($user->idno ?? auth()->id()) : null,
            'approved_by_dilg_ro' => null,
            'approval_remarks' => null,
            'user_remarks' => null,
        ];

        if ($existingDocument) {
            $existingDocument->update($documentPayload);
            $document = $existingDocument->refresh();
        } else {
            $document = RbisAnnualCertificationDocument::create(array_merge([
                'office' => $officeName,
            ], $documentPayload));
        }

        if ($oldFilePath && $oldFilePath !== $path && Storage::disk('public')->exists($oldFilePath)) {
            Storage::disk('public')->delete($oldFilePath);
        }

        $this->logActivity($officeName, 'upload', 'Uploaded', $document, null, $uploadedAt);
        if ($isMountainProvinceDilgUploader) {
            $this->logActivity($officeName, 'validate_po', 'Validated (DILG PO)', $document, null, $uploadedAt);
        }

        return back()->with('success', 'Annual certification document uploaded successfully.');
    }

    public function viewDocument($id, $docId)
    {
        $officeName = $id;
        $document = RbisAnnualCertificationDocument::query()
            ->where('office', $officeName)
            ->where('id', $docId)
            ->firstOrFail();

        if (!$this->canAccessOffice($officeName, (string) $document->province)) {
            abort(403);
        }

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($document->file_path));
    }

    public function approveDocument(Request $request, $id, $docId)
    {
        $officeName = $id;
        $user = auth()->user();
        if (!$user || $user->agency !== 'DILG') {
            abort(403);
        }

        $request->validate([
            'action' => ['required', 'in:approve,return'],
            'remarks' => ['required_if:action,return', 'nullable', 'string'],
        ]);

        $document = RbisAnnualCertificationDocument::query()
            ->where('office', $officeName)
            ->where('id', $docId)
            ->firstOrFail();

        if (!$this->canAccessOffice($officeName, (string) $document->province)) {
            abort(403);
        }

        $now = now();
        $action = $request->input('action');
        $remarks = $request->input('remarks');

        $isRegionalOffice = $user->province === 'Regional Office';
        $isProvincialOffice = !$isRegionalOffice;

        $updates = [
            'approved_at' => $now,
        ];

        if ($action === 'approve') {
            if ($isProvincialOffice) {
                $updates['approved_at_dilg_po'] = $now;
                $updates['approved_by_dilg_po'] = $user->idno;
                $updates['status'] = 'pending_ro';
                $updates['approval_remarks'] = null;
            } else {
                $updates['approved_at_dilg_ro'] = $now;
                $updates['approved_by_dilg_ro'] = $user->idno;
                $updates['status'] = 'approved';
                $updates['approval_remarks'] = null;
            }
        } else {
            if ($isRegionalOffice) {
                $updates['approved_at_dilg_ro'] = null;
                $updates['approved_by_dilg_ro'] = $user->idno;
            } else {
                $updates['approved_by_dilg_po'] = $user->idno;
            }
            $updates['status'] = 'returned';
            $updates['approval_remarks'] = $remarks;
            $updates['user_remarks'] = $remarks;
        }

        $document->update($updates);

        $document->refresh();
        if ($action === 'approve') {
            if ($isProvincialOffice) {
                $this->logActivity($officeName, 'validate_po', 'Validated (DILG PO)', $document, null, $now);
            } else {
                $this->logActivity($officeName, 'validate_ro', 'Validated (DILG RO)', $document, null, $now);
            }
        } else {
            $this->logActivity($officeName, 'return', 'Returned', $document, $remarks, $now);
        }

        if ($action === 'approve' && $isRegionalOffice) {
            $this->notifyLguUsersAfterRegionalApproval($document, $officeName);
        }

        return back()->with('success', $action === 'approve' ? 'Document validated.' : 'Document returned.');
    }
}

