<?php

namespace App\Services;


ini_set('memory_limit', '512M');
// or temporarily:
// ini_set('memory_limit', '-1'); // unlimited, only for debugging

use Illuminate\Support\Str;
use RuntimeException;
use Shuchkin\SimpleXLS;

class RlipLimeDataService
{
    private const SOURCE_RELATIVE_PATH = 'resources/sample file/rbme_master_list_2026_03_09_091438.xls';
    private const CACHE_RELATIVE_PATH = 'app/private/rlip_lime_master_cache.json';
    private const CACHE_SCHEMA_VERSION = 3;

    private const HEADER_SECTION_ROW = 8;
    private const HEADER_FIELD_ROW = 9;
    private const HEADER_SUBFIELD_ROW = 10;
    private const DATA_START_ROW = 11;

    private const TABLE_COLUMN_INDEXES = [
        'psgc' => 0,
        'region' => 1,
        'province' => 2,
        'city_municipality' => 3,
        'barangay' => 4,
        'project_code' => 5,
        'project_title' => 6,
        'project_description' => 7,
        'mode_of_implementation' => 8,
        'funding_year' => 9,
        'fund_source' => 10,
        'fund_source_if_others' => 11,
        'project_type' => 12,
        'project_type_if_others' => 13,
        'project_brief_attachment' => 14,
        'project_status' => 15,
        'profile_approval_status' => 16,
        'has_aip' => 17,
        'project_schedule_start_date' => 34,
        'project_schedule_end_date' => 35,
        'total_amount_programmed' => 36,
        'overall_completion' => 37,
        'employment_generated' => 38,
        'date_of_completion' => 97,
        'completion_approval_status' => 98,
        'completion_has_attachment' => 99,
        'contractor_name' => 105,
        'contract_amount' => 106,
        'contract_duration' => 107,
        'office_address' => 108,
        'ntp_date' => 109,
        'expiration_date' => 110,
        'revised_expiration_date' => 111,
    ];

    /**
     * Load parsed RLIP/LIME dataset from cache or source XLS.
     *
     * @return array{
     *     meta: array<string, mixed>,
     *     categories: array<string, array<int, array<string, mixed>>>,
     *     columns: array<int, array<string, mixed>>,
     *     rows: array<int, array<string, mixed>>
     * }
     */
    public function getDataset(): array
    {
        $sourcePath = base_path(self::SOURCE_RELATIVE_PATH);
        if (!is_file($sourcePath)) {
            throw new RuntimeException('RLIP/LIME source file not found: ' . $sourcePath);
        }

        $sourceMtime = filemtime($sourcePath) ?: 0;
        $sourceSize = filesize($sourcePath) ?: 0;
        $cachePath = storage_path(self::CACHE_RELATIVE_PATH);

        if (is_file($cachePath)) {
            $cached = json_decode((string) file_get_contents($cachePath), true);
            if (
                is_array($cached)
                && (int) ($cached['meta']['cache_schema_version'] ?? 0) === self::CACHE_SCHEMA_VERSION
                && (int) ($cached['meta']['source_mtime'] ?? 0) === $sourceMtime
                && (int) ($cached['meta']['source_size'] ?? 0) === $sourceSize
                && isset($cached['rows'], $cached['columns'], $cached['categories'])
            ) {
                return $cached;
            }
        }

        $parsed = $this->parseWorkbook($sourcePath);
        $meta = [
            'source_file' => self::SOURCE_RELATIVE_PATH,
            'cache_schema_version' => self::CACHE_SCHEMA_VERSION,
            'source_mtime' => $sourceMtime,
            'source_size' => $sourceSize,
            'generated_at' => now()->toIso8601String(),
            'row_count' => count($parsed['rows']),
            'column_count' => count($parsed['columns']),
        ];

        $dataset = [
            'meta' => $meta,
            'categories' => $parsed['categories'],
            'columns' => $parsed['columns'],
            'rows' => $parsed['rows'],
        ];

        $cacheDir = dirname($cachePath);
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0777, true);
        }
        @file_put_contents($cachePath, json_encode($dataset, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $dataset;
    }

    /**
     * @return array{categories: array<string, array<int, array<string, mixed>>>, columns: array<int, array<string, mixed>>, rows: array<int, array<string, mixed>>}
     */
    private function parseWorkbook(string $sourcePath): array
    {
        $xls = SimpleXLS::parse($sourcePath);
        if (!$xls) {
            throw new RuntimeException('Unable to parse RLIP/LIME XLS: ' . (SimpleXLS::parseError() ?: 'Unknown parser error'));
        }

        $rows = $xls->rows();
        if (!isset($rows[self::HEADER_SECTION_ROW], $rows[self::HEADER_FIELD_ROW], $rows[self::HEADER_SUBFIELD_ROW])) {
            throw new RuntimeException('RLIP/LIME XLS is missing expected header rows.');
        }

        $columnCount = $this->resolveColumnCount($rows);
        $sectionRow = $rows[self::HEADER_SECTION_ROW];
        $fieldRow = $rows[self::HEADER_FIELD_ROW];
        $subFieldRow = $rows[self::HEADER_SUBFIELD_ROW];

        $columns = [];
        $lastSection = '';
        $lastFieldBySection = [];

        for ($index = 0; $index < $columnCount; $index++) {
            $sectionValue = $this->normalizeCell($sectionRow[$index] ?? null);
            if ($sectionValue !== '') {
                $lastSection = $sectionValue;
            }
            $section = $sectionValue !== '' ? $sectionValue : $lastSection;

            $fieldValue = $this->normalizeCell($fieldRow[$index] ?? null);
            if ($fieldValue !== '') {
                $lastFieldBySection[$section] = $fieldValue;
            }
            $field = $fieldValue !== '' ? $fieldValue : ($lastFieldBySection[$section] ?? '');
            $subField = $this->normalizeCell($subFieldRow[$index] ?? null);

            $columns[$index] = [
                'index' => $index,
                'section' => $section !== '' ? $section : 'UNCATEGORIZED',
                'field' => $field,
                'subfield' => $subField,
                'key' => $this->buildColumnKey($index, $section, $field, $subField),
                'non_empty_count' => 0,
                'sample' => '',
            ];
        }

        $tableRows = [];
        $dataRowCount = count($rows);
        for ($rowIndex = self::DATA_START_ROW; $rowIndex < $dataRowCount; $rowIndex++) {
            $row = $rows[$rowIndex] ?? [];
            if ($this->isEmptyRow($row, $columnCount)) {
                continue;
            }

            $normalizedRow = [];
            for ($colIndex = 0; $colIndex < $columnCount; $colIndex++) {
                $value = $this->normalizeCell($row[$colIndex] ?? null);
                $normalizedRow[$colIndex] = $value;

                if ($value !== '') {
                    $columns[$colIndex]['non_empty_count']++;
                    if ($columns[$colIndex]['sample'] === '') {
                        $columns[$colIndex]['sample'] = Str::limit(str_replace(["\r\n", "\n", "\r"], ' | ', $value), 180, '');
                    }
                }
            }

            $tableRows[] = $this->buildTableRow($normalizedRow, $rowIndex + 1);
        }

        return [
            'categories' => $this->buildCategories($columns),
            'columns' => array_values($columns),
            'rows' => $tableRows,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $columns
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function buildCategories(array $columns): array
    {
        $categories = [];
        foreach ($columns as $column) {
            $section = (string) ($column['section'] ?? 'UNCATEGORIZED');
            $field = (string) ($column['field'] ?? '');
            $subField = (string) ($column['subfield'] ?? '');
            $label = $field;
            if ($subField !== '') {
                $label = $label !== '' ? "{$label} - {$subField}" : $subField;
            }
            if ($label === '') {
                $label = 'Column ' . $column['index'];
            }

            if ($section === 'UNCATEGORIZED' && $field === '' && $subField === '') {
                continue;
            }

            $categories[$section][] = [
                'index' => (int) $column['index'],
                'label' => $label,
                'field' => $field,
                'subfield' => $subField,
                'non_empty_count' => (int) $column['non_empty_count'],
                'sample' => (string) ($column['sample'] ?? ''),
            ];
        }

        return $categories;
    }

    /**
     * @param array<int, string> $row
     * @return array<string, mixed>
     */
    private function buildTableRow(array $row, int $rowNumber): array
    {
        $mapped = [];
        foreach (self::TABLE_COLUMN_INDEXES as $key => $index) {
            $mapped[$key] = $row[$index] ?? '';
        }

        if ($mapped['project_type'] === '' && $mapped['project_type_if_others'] !== '') {
            $mapped['project_type'] = $mapped['project_type_if_others'];
        }

        $mapped['row_number'] = $rowNumber;
        $mapped['location'] = implode(', ', array_filter([
            $mapped['province'],
            $mapped['city_municipality'],
            $mapped['barangay'],
        ]));
        $mapped['total_amount_programmed_value'] = $this->toFloat($mapped['total_amount_programmed']);
        $mapped['overall_completion_value'] = $this->toFloat($mapped['overall_completion']);
        $mapped['employment_generated_value'] = $this->toFloat($mapped['employment_generated']);
        $mapped['funding_year_value'] = $this->toInt($mapped['funding_year']);
        $mapped['project_code_lc'] = mb_strtolower(trim((string) $mapped['project_code']));
        $mapped['fund_source_lc'] = mb_strtolower(trim((string) $mapped['fund_source']));
        $mapped['project_status_lc'] = mb_strtolower(trim((string) $mapped['project_status']));
        $mapped['province_lc'] = mb_strtolower(trim((string) $mapped['province']));
        $mapped['city_municipality_lc'] = mb_strtolower(trim((string) $mapped['city_municipality']));
        $mapped['region_lc'] = mb_strtolower(trim((string) $mapped['region']));
        $mapped['search_index'] = mb_strtolower(implode(' ', array_filter([
            trim((string) $mapped['project_code']),
            trim((string) $mapped['project_title']),
            trim((string) $mapped['province']),
            trim((string) $mapped['city_municipality']),
            trim((string) $mapped['barangay']),
            trim((string) $mapped['fund_source']),
            trim((string) $mapped['project_type']),
            trim((string) $mapped['project_status']),
        ], fn (string $value) => $value !== '')));
        $mapped['cells'] = $row;

        return $mapped;
    }

    /**
     * @param array<int, mixed> $rows
     */
    private function resolveColumnCount(array $rows): int
    {
        $max = 0;
        foreach ($rows as $row) {
            if (is_array($row)) {
                $max = max($max, count($row));
            }
        }

        return $max;
    }

    /**
     * @param array<int, mixed> $row
     */
    private function isEmptyRow(array $row, int $columnCount): bool
    {
        for ($index = 0; $index < $columnCount; $index++) {
            if ($this->normalizeCell($row[$index] ?? null) !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_numeric($value)) {
            $numeric = (string) $value;
            if (str_contains($numeric, '.')) {
                $numeric = rtrim(rtrim($numeric, '0'), '.');
            }

            return trim($numeric);
        }

        return trim((string) $value);
    }

    private function buildColumnKey(int $index, string $section, string $field, string $subField): string
    {
        $parts = array_filter([$section, $field, $subField], fn ($part) => trim((string) $part) !== '');
        $base = Str::slug(implode(' ', $parts), '_');

        if ($base === '') {
            $base = 'column';
        }

        return "{$base}_{$index}";
    }

    private function toFloat(string $value): ?float
    {
        $normalized = trim((string) $value);
        if ($normalized === '') {
            return null;
        }

        $clean = preg_replace('/[^0-9.\-]/', '', str_replace(',', '', $normalized));
        if ($clean === null || $clean === '' || !is_numeric($clean)) {
            return null;
        }

        return (float) $clean;
    }

    private function toInt(string $value): ?int
    {
        $normalized = trim((string) $value);
        if ($normalized === '') {
            return null;
        }

        $clean = preg_replace('/[^0-9\-]/', '', $normalized);
        if ($clean === null || $clean === '' || !is_numeric($clean)) {
            return null;
        }

        return (int) $clean;
    }
}
