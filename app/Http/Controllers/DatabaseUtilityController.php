<?php

namespace App\Http\Controllers;

use App\Mail\AutomatedDatabaseBackupMail;
use App\Models\BackupAutomationSetting;
use App\Models\DatabaseBackupRun;
use App\Services\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class DatabaseUtilityController extends Controller
{
    public function __construct(
        private readonly DatabaseBackupService $backupService,
    ) {
        $this->middleware('auth');
        $this->middleware('superadmin');
    }

    public function index(): View
    {
        $connection = $this->backupService->databaseConnection();
        $automationSetting = BackupAutomationSetting::query()->first();

        return view('admin.utilities.backup-and-restore', [
            'databaseName' => $connection['database'],
            'databaseHost' => $connection['host'],
            'automationSetting' => $automationSetting,
            'recentBackupRuns' => DatabaseBackupRun::query()
                ->orderByDesc('started_at')
                ->limit(10)
                ->get(),
            'dayOptions' => [
                0 => 'Sunday',
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
            ],
            'nextScheduledRun' => $this->nextScheduledRun($automationSetting),
        ]);
    }

    public function downloadBackup()
    {
        $backup = $this->backupService->createBackup([
            'type' => 'manual',
            'directory' => 'app/backups/manual',
            'prefix' => $this->backupService->databaseConnection()['database'] . '-backup',
        ]);

        return response()->download($backup['absolute_path'], $backup['filename'], [
            'Content-Type' => $backup['mime_type'],
        ])->deleteFileAfterSend(true);
    }

    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'max:102400'],
        ]);

        $uploadedFile = $request->file('backup_file');
        if (strtolower((string) $uploadedFile->getClientOriginalExtension()) !== 'sql') {
            return back()->withErrors([
                'backup_file' => 'The backup file must use the .sql extension.',
            ]);
        }

        try {
            $this->backupService->restoreFromSqlFile($uploadedFile->getRealPath());
        } catch (\Throwable $exception) {
            return back()->with('error', 'Database restore failed. ' . $exception->getMessage());
        }

        return redirect()
            ->route('utilities.backup-and-restore.index')
            ->with('success', 'Database restore completed successfully.');
    }

    public function saveSchedule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'is_enabled' => ['nullable', 'boolean'],
            'frequency' => ['required', 'in:daily,weekly'],
            'weekly_day' => ['nullable', 'integer', 'between:0,6'],
            'run_time' => ['required', 'date_format:H:i'],
            'recipient_emails' => ['required', 'string'],
            'retention_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'encrypt_backup' => ['nullable', 'boolean'],
            'encryption_password' => ['nullable', 'string', 'min:8', 'max:255'],
        ]);

        $isEnabled = $request->boolean('is_enabled');
        $encryptBackup = $request->boolean('encrypt_backup');
        $recipientEmails = $this->parseEmailList($validated['recipient_emails']);

        if ($recipientEmails === []) {
            return back()->withErrors([
                'recipient_emails' => 'Enter at least one valid email address.',
            ])->withInput();
        }

        if (($validated['frequency'] ?? null) === 'weekly' && ! $request->filled('weekly_day')) {
            return back()->withErrors([
                'weekly_day' => 'Choose the weekday for weekly backups.',
            ])->withInput();
        }

        if ($encryptBackup && ! $request->filled('encryption_password')) {
            return back()->withErrors([
                'encryption_password' => 'Set an encryption password when password protection is enabled.',
            ])->withInput();
        }

        $setting = BackupAutomationSetting::query()->firstOrNew();
        $setting->fill([
            'is_enabled' => $isEnabled,
            'frequency' => $validated['frequency'],
            'weekly_day' => $validated['frequency'] === 'weekly' ? (int) $validated['weekly_day'] : null,
            'run_time' => $validated['run_time'] . ':00',
            'recipient_emails' => $recipientEmails,
            'retention_days' => $request->filled('retention_days') ? (int) $validated['retention_days'] : null,
            'encrypt_backup' => $encryptBackup,
        ]);

        if ($encryptBackup && $request->filled('encryption_password')) {
            $setting->encryption_password = $validated['encryption_password'];
        }

        if (! $encryptBackup) {
            $setting->encryption_password = null;
        }

        $setting->save();

        return redirect()
            ->route('utilities.backup-and-restore.index')
            ->with('success', 'Backup scheduler settings saved successfully.');
    }

    public function sendTestBackupNow(): RedirectResponse
    {
        $setting = BackupAutomationSetting::query()->first();

        if (! $setting) {
            return redirect()
                ->route('utilities.backup-and-restore.index', ['tab' => 'scheduler'])
                ->with('error', 'Save scheduler settings before sending a test backup.');
        }

        $recipients = array_values(array_filter($setting->recipient_emails ?? []));
        if ($recipients === []) {
            return redirect()
                ->route('utilities.backup-and-restore.index', ['tab' => 'scheduler'])
                ->with('error', 'Add at least one recipient email before sending a test backup.');
        }

        try {
            $connection = $this->backupService->databaseConnection();
            $backup = $this->backupService->createBackup([
                'type' => 'test',
                'directory' => 'app/backups/automated',
                'prefix' => $connection['database'] . '-test-backup',
                'encrypt' => $setting->encrypt_backup,
                'encryption_password' => $setting->encrypt_backup ? $setting->encryption_password : null,
                'setting_id' => $setting->id,
                'mailed_to' => $recipients,
            ]);

            Mail::to($recipients)->send(new AutomatedDatabaseBackupMail(
                databaseName: $connection['database'],
                filePath: $backup['absolute_path'],
                fileName: $backup['filename'],
                wasEncrypted: $backup['was_encrypted'],
                retentionDays: $setting->retention_days,
            ));

            $deletedCount = $this->backupService->pruneOldBackups($setting->retention_days);

            DatabaseBackupRun::query()
                ->where('stored_path', $backup['stored_path'])
                ->latest('id')
                ->first()?->update([
                    'retention_deleted_count' => $deletedCount,
                ]);

            return redirect()
                ->route('utilities.backup-and-restore.index', ['tab' => 'scheduler'])
                ->with('success', 'Test backup email sent successfully.');
        } catch (\Throwable $exception) {
            return redirect()
                ->route('utilities.backup-and-restore.index', ['tab' => 'scheduler'])
                ->with('error', 'Test backup failed. ' . $exception->getMessage());
        }
    }

    private function parseEmailList(string $emails): array
    {
        $items = preg_split('/[\s,;]+/', $emails) ?: [];
        $items = array_unique(array_filter(array_map('trim', $items)));

        return array_values(array_filter($items, fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false));
    }

    private function nextScheduledRun(?BackupAutomationSetting $setting): ?string
    {
        if (! $setting || ! $setting->is_enabled) {
            return null;
        }

        $candidate = now()->copy()->setTimeFromTimeString($setting->run_time);

        if ($setting->frequency === 'daily') {
            if ($candidate->lte(now())) {
                $candidate->addDay();
            }

            return $candidate->format('F j, Y g:i A');
        }

        $weeklyDay = (int) $setting->weekly_day;
        while ($candidate->dayOfWeek !== $weeklyDay || $candidate->lte(now())) {
            $candidate->addDay();
        }

        return $candidate->format('F j, Y g:i A');
    }
}
