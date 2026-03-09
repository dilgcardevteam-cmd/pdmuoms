<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SystemManagementController extends Controller
{
    private const IMPORT_HISTORY_TABLE = 'subaybayan_import_histories';

    public function uploadSubaybayan()
    {
        if (!Schema::hasTable('subay_project_profiles')) {
            return view('system-management.upload-subaybayan', [
                'tableMissing' => true,
                'filters' => [],
                'filterOptions' => [],
                'importHistoryRows' => collect(),
                'importHistoryTableMissing' => !Schema::hasTable(self::IMPORT_HISTORY_TABLE),
            ]);
        }

        $filters = [
            'province' => request('province'),
            'city_municipality' => request('city_municipality'),
            'barangay' => request('barangay'),
            'program' => request('program'),
            'status' => request('status'),
            'funding_year' => request('funding_year'),
            'procurement_type' => request('procurement_type'),
            'project_code' => request('project_code'),
            'project_title' => request('project_title'),
            'procurement' => request('procurement'),
            'type_of_project' => request('type_of_project'),
            'implementing_unit' => request('implementing_unit'),
            'profile_approval_status' => request('profile_approval_status'),
        ];

        $filterOptions = [
            'provinces' => DB::table('subay_project_profiles')
                ->select('province')
                ->whereNotNull('province')
                ->where('province', '!=', '')
                ->distinct()
                ->orderBy('province')
                ->pluck('province'),
            'cities' => DB::table('subay_project_profiles')
                ->select('city_municipality')
                ->whereNotNull('city_municipality')
                ->where('city_municipality', '!=', '')
                ->distinct()
                ->orderBy('city_municipality')
                ->pluck('city_municipality'),
            'barangays' => DB::table('subay_project_profiles')
                ->select('barangay')
                ->whereNotNull('barangay')
                ->where('barangay', '!=', '')
                ->distinct()
                ->orderBy('barangay')
                ->pluck('barangay'),
            'programs' => DB::table('subay_project_profiles')
                ->select('program')
                ->whereNotNull('program')
                ->where('program', '!=', '')
                ->distinct()
                ->orderBy('program')
                ->pluck('program'),
            'statuses' => DB::table('subay_project_profiles')
                ->select('status')
                ->whereNotNull('status')
                ->where('status', '!=', '')
                ->distinct()
                ->orderBy('status')
                ->pluck('status'),
            'funding_years' => DB::table('subay_project_profiles')
                ->select('funding_year')
                ->whereNotNull('funding_year')
                ->where('funding_year', '!=', '')
                ->distinct()
                ->orderBy('funding_year')
                ->pluck('funding_year'),
            'procurement_types' => DB::table('subay_project_profiles')
                ->select('procurement_type')
                ->whereNotNull('procurement_type')
                ->where('procurement_type', '!=', '')
                ->distinct()
                ->orderBy('procurement_type')
                ->pluck('procurement_type'),
            'procurements' => DB::table('subay_project_profiles')
                ->select('procurement')
                ->whereNotNull('procurement')
                ->where('procurement', '!=', '')
                ->distinct()
                ->orderBy('procurement')
                ->pluck('procurement'),
            'project_types' => DB::table('subay_project_profiles')
                ->select('type_of_project')
                ->whereNotNull('type_of_project')
                ->where('type_of_project', '!=', '')
                ->distinct()
                ->orderBy('type_of_project')
                ->pluck('type_of_project'),
            'implementing_units' => DB::table('subay_project_profiles')
                ->select('implementing_unit')
                ->whereNotNull('implementing_unit')
                ->where('implementing_unit', '!=', '')
                ->distinct()
                ->orderBy('implementing_unit')
                ->pluck('implementing_unit'),
            'profile_statuses' => DB::table('subay_project_profiles')
                ->select('profile_approval_status')
                ->whereNotNull('profile_approval_status')
                ->where('profile_approval_status', '!=', '')
                ->distinct()
                ->orderBy('profile_approval_status')
                ->pluck('profile_approval_status'),
        ];

        $importHistoryTableMissing = !Schema::hasTable(self::IMPORT_HISTORY_TABLE);
        $importHistoryRows = $importHistoryTableMissing
            ? collect()
            : DB::table(self::IMPORT_HISTORY_TABLE)
                ->orderByDesc('imported_at')
                ->orderByDesc('id')
                ->paginate(15, ['*'], 'imports_page')
                ->withQueryString();

        return view('system-management.upload-subaybayan', [
            'tableMissing' => false,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
            'importHistoryRows' => $importHistoryRows,
            'importHistoryTableMissing' => $importHistoryTableMissing,
        ]);
    }

    public function importSubaybayan(Request $request)
    {
        if (!Schema::hasTable('subay_project_profiles')) {
            return back()->with('error', 'SubayBAYAN data table is not available yet.');
        }

        $request->validate(
            [
                'file' => ['required', 'file', 'mimes:csv,txt', 'max:51200'],
            ],
            [
                'file.mimes' => 'Please upload a CSV file. If your data is in Excel, save it as CSV first.',
            ]
        );

        $file = $request->file('file');
        if (!$file) {
            return back()->with('error', 'No file was uploaded.');
        }

        $originalFileName = (string) $file->getClientOriginalName();
        $storageFileName = $this->generateImportStorageFileName($originalFileName);
        $storedPath = $file->storeAs('subaybayan-imports', $storageFileName, 'local');
        if (!$storedPath) {
            return back()->with('error', 'Unable to store the uploaded file.');
        }

        if (!Schema::hasTable(self::IMPORT_HISTORY_TABLE)) {
            Storage::disk('local')->delete($storedPath);
            return back()->with('error', 'Import history table is not available yet. Please run migration first.');
        }

        $now = now();
        DB::table(self::IMPORT_HISTORY_TABLE)->insert([
            'original_file_name' => $originalFileName !== '' ? $originalFileName : basename($storedPath),
            'stored_file_path' => $storedPath,
            'file_size_bytes' => $file->getSize(),
            'imported_at' => $now,
            'last_loaded_at' => null,
            'created_by' => auth()->id(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return back()->with('success', 'CSV file added to import history. Click Load to import it into SubayBAYAN data.');
    }

    public function loadSubaybayanImport($importId)
    {
        if (!Schema::hasTable('subay_project_profiles')) {
            return back()->with('error', 'SubayBAYAN data table is not available yet.');
        }

        if (!Schema::hasTable(self::IMPORT_HISTORY_TABLE)) {
            return back()->with('error', 'Import history table is not available yet. Please run migration first.');
        }

        $record = DB::table(self::IMPORT_HISTORY_TABLE)
            ->where('id', (int) $importId)
            ->first();

        if (!$record) {
            return back()->with('error', 'Selected import record was not found.');
        }

        $storedPath = (string) ($record->stored_file_path ?? '');
        if ($storedPath === '' || !Storage::disk('local')->exists($storedPath)) {
            return back()->with('error', 'The selected imported file is no longer available.');
        }

        $absolutePath = Storage::disk('local')->path($storedPath);
        try {
            $inserted = $this->importCsvSnapshot($absolutePath);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        if ($inserted === 0) {
            return back()->with('error', 'No valid rows were loaded from the selected import file.');
        }

        DB::table(self::IMPORT_HISTORY_TABLE)
            ->where('id', (int) $importId)
            ->update([
                'last_loaded_at' => now(),
                'updated_at' => now(),
            ]);

        $displayName = trim((string) ($record->original_file_name ?? ''));
        if ($displayName === '') {
            $displayName = basename($storedPath);
        }

        return back()->with('success', "Loaded {$inserted} rows from {$displayName}.");
    }

    public function deleteSubaybayanImport($importId)
    {
        if (!Schema::hasTable(self::IMPORT_HISTORY_TABLE)) {
            return back()->with('error', 'Import history table is not available yet. Please run migration first.');
        }

        $record = DB::table(self::IMPORT_HISTORY_TABLE)
            ->where('id', (int) $importId)
            ->first();

        if (!$record) {
            return back()->with('error', 'Selected import record was not found.');
        }

        $storedPath = (string) ($record->stored_file_path ?? '');
        if ($storedPath !== '' && Storage::disk('local')->exists($storedPath)) {
            Storage::disk('local')->delete($storedPath);
        }

        DB::table(self::IMPORT_HISTORY_TABLE)
            ->where('id', (int) $importId)
            ->delete();

        return back()->with('success', 'Imported file record deleted successfully.');
    }

    public function downloadSubaybayanImport($importId)
    {
        if (!Schema::hasTable(self::IMPORT_HISTORY_TABLE)) {
            return back()->with('error', 'Import history table is not available yet. Please run migration first.');
        }

        $record = DB::table(self::IMPORT_HISTORY_TABLE)
            ->where('id', (int) $importId)
            ->first();

        if (!$record) {
            return back()->with('error', 'Selected import record was not found.');
        }

        $storedPath = (string) ($record->stored_file_path ?? '');
        if ($storedPath === '' || !Storage::disk('local')->exists($storedPath)) {
            return back()->with('error', 'The selected imported file is no longer available.');
        }

        $downloadName = trim((string) ($record->original_file_name ?? ''));
        if ($downloadName === '') {
            $downloadName = basename($storedPath);
        }
        $downloadName = basename($downloadName);

        $extension = strtolower(pathinfo($downloadName, PATHINFO_EXTENSION));
        $contentType = in_array($extension, ['csv', 'txt'], true)
            ? 'text/csv; charset=UTF-8'
            : 'application/octet-stream';

        return response()->download(
            Storage::disk('local')->path($storedPath),
            $downloadName,
            [
                'Content-Type' => $contentType,
            ]
        );
    }

    private function importCsvSnapshot(string $path): int
    {
        if (!is_readable($path)) {
            throw new \RuntimeException('Unable to read the selected file.');
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Unable to open the selected file.');
        }

        try {
            $headers = fgetcsv($handle);
            if ($headers === false) {
                throw new \RuntimeException('The selected file appears to be empty.');
            }

            $columns = Schema::getColumnListing('subay_project_profiles');
            $headerMap = $this->buildHeaderMap($headers, $columns);
            if (empty($headerMap)) {
                throw new \RuntimeException('No recognizable columns were found in the CSV file.');
            }

            return DB::transaction(function () use ($handle, $headerMap) {
                $now = now();
                $rows = [];
                $inserted = 0;

                // Treat each load as the latest full snapshot to avoid duplicates.
                DB::table('subay_project_profiles')->delete();

                while (($data = fgetcsv($handle)) !== false) {
                    if ($this->rowIsEmpty($data)) {
                        continue;
                    }

                    $row = [];
                    foreach ($headerMap as $index => $column) {
                        $value = $data[$index] ?? null;
                        if (is_string($value)) {
                            $value = $this->sanitizeValue($value);
                        }
                        $row[$column] = $value === '' ? null : $value;
                    }

                    if (empty($row)) {
                        continue;
                    }

                    $row['created_at'] = $now;
                    $row['updated_at'] = $now;
                    $rows[] = $row;

                    if (count($rows) >= 500) {
                        DB::table('subay_project_profiles')->insert($rows);
                        $inserted += count($rows);
                        $rows = [];
                    }
                }

                if (!empty($rows)) {
                    DB::table('subay_project_profiles')->insert($rows);
                    $inserted += count($rows);
                }

                return $inserted;
            });
        } finally {
            fclose($handle);
        }
    }

    private function generateImportStorageFileName(string $originalFileName): string
    {
        $extension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);
        $baseNameSlug = Str::slug($baseName);
        if ($baseNameSlug === '') {
            $baseNameSlug = 'subaybayan';
        }

        $timestamp = now()->format('Ymd_His');
        $randomSuffix = Str::lower(Str::random(8));
        $fileName = $timestamp . '_' . $baseNameSlug . '_' . $randomSuffix;

        return $fileName . ($extension !== '' ? '.' . $extension : '.csv');
    }

    private function buildHeaderMap(array $headers, array $columns): array
    {
        $columnLookup = array_fill_keys($columns, true);
        $customMap = [
            'barangay_s' => 'barangay',
            'barangays' => 'barangay',
            'amount' => 'obligation',
            'amount_2' => 'disbursement',
            'amount_3' => 'liquidations',
            'ded_pow_preparation_and_submission_of_notarized_lce_certification' => 'ded_pow_prep_notarized_lce_cert',
        ];

        $headerMap = [];
        $counts = [];

        foreach ($headers as $index => $header) {
            $base = $this->normalizeHeader($header);
            if ($base === '') {
                continue;
            }

            $counts[$base] = ($counts[$base] ?? 0) + 1;
            $candidate = $base;
            if ($counts[$base] > 1) {
                $candidate = $base . '_' . $counts[$base];
            }

            if (isset($customMap[$candidate])) {
                $column = $customMap[$candidate];
            } elseif (isset($customMap[$base])) {
                $column = $customMap[$base];
            } elseif (isset($columnLookup[$candidate])) {
                $column = $candidate;
            } elseif (isset($columnLookup[$base])) {
                $column = $base;
            } else {
                continue;
            }

            $headerMap[$index] = $column;
        }

        return $headerMap;
    }

    private function normalizeHeader($value): string
    {
        $value = is_string($value) ? $value : '';
        $value = ltrim($value, "\xEF\xBB\xBF");
        $value = str_replace(["\r", "\n"], ' ', $value);
        $value = str_replace('&', ' and ', $value);
        $value = preg_replace('/[\\/\\(\\)\\#\\-:]/', ' ', $value);
        $value = preg_replace('/\\s+/', ' ', $value ?? '');
        $value = trim(strtolower($value ?? ''));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value);
        return trim($value, '_');
    }

    private function rowIsEmpty(array $data): bool
    {
        foreach ($data as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }
        return true;
    }

    private function sanitizeValue(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $clean = $value;
        if (function_exists('mb_convert_encoding')) {
            $clean = mb_convert_encoding($clean, 'UTF-8', 'UTF-8,ISO-8859-1,WINDOWS-1252');
        } elseif (function_exists('utf8_encode')) {
            $clean = utf8_encode($clean);
        }

        if (function_exists('iconv')) {
            $iconv = @iconv('UTF-8', 'UTF-8//IGNORE', $clean);
            if ($iconv !== false) {
                $clean = $iconv;
            }
        }

        return $clean;
    }

    private function buildSubaybayanQuery(Request $request)
    {
        $query = DB::table('subay_project_profiles');

        $filters = [
            'province' => 'province',
            'city_municipality' => 'city_municipality',
            'barangay' => 'barangay',
            'program' => 'program',
            'status' => 'status',
            'funding_year' => 'funding_year',
            'procurement_type' => 'procurement_type',
            'procurement' => 'procurement',
            'type_of_project' => 'type_of_project',
            'implementing_unit' => 'implementing_unit',
            'profile_approval_status' => 'profile_approval_status',
        ];

        foreach ($filters as $param => $column) {
            if ($request->filled($param)) {
                $query->where($column, $request->input($param));
            }
        }

        if ($request->filled('project_code')) {
            $code = trim((string) $request->input('project_code'));
            if ($code !== '') {
                $query->where('project_code', 'like', '%' . $code . '%');
            }
        }

        if ($request->filled('project_title')) {
            $title = trim((string) $request->input('project_title'));
            if ($title !== '') {
                $query->where('project_title', 'like', '%' . $title . '%');
            }
        }

        return $query;
    }
}
