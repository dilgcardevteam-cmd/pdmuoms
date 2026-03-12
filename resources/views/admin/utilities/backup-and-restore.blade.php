@extends('layouts.dashboard')

@section('title', 'Backup and Restore')
@section('page-title', 'Backup and Restore')

@section('content')
    <div class="content-header">
        <h1>Backup and Restore</h1>
        <p>Create a full SQL backup of the current database or restore from an existing SQL file.</p>
    </div>

    @if (session('success'))
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div style="background-color: #fff7ed; border: 1px solid #fdba74; color: #9a3412; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <strong style="display: block; margin-bottom: 8px;">Please resolve the following issue(s):</strong>
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <section style="background: white; padding: 28px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                <div style="width: 46px; height: 46px; border-radius: 12px; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fas fa-database"></i>
                </div>
                <div>
                    <h2 style="margin: 0; color: #002C76; font-size: 18px;">Generate SQL Backup</h2>
                    <p style="margin: 4px 0 0; color: #6b7280; font-size: 13px;">Downloads a full dump of the active database.</p>
                </div>
            </div>

            <div style="background: #dbeafe; border: 1px solid #1d4ed8; border-radius: 8px; padding: 14px; margin-bottom: 18px; color: #002C76; font-size: 13px; line-height: 1.6;">
                If problems occur, such as system failure or accidental deletion, backup can recover the database.
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 18px; color: #334155; font-size: 13px; line-height: 1.6;">
                <div><strong>Database:</strong> {{ $databaseName }}</div>
                <div><strong>Host:</strong> {{ $databaseHost }}</div>
            </div>

            <form method="GET" action="{{ route('utilities.backup-and-restore.download') }}">
                <button type="submit" class="transition hover:scale-101" style="width: 100%; padding: 12px 18px; background-color: #002C76; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="fas fa-download"></i>
                    <span>Download SQL Backup</span>
                </button>
            </form>
        </section>

        <section style="background: white; padding: 28px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                <div style="width: 46px; height: 46px; border-radius: 12px; background: #fee2e2; color: #b91c1c; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fas fa-upload"></i>
                </div>
                <div>
                    <h2 style="margin: 0; color: #002C76; font-size: 18px;">Restore Database</h2>
                    <p style="margin: 4px 0 0; color: #6b7280; font-size: 13px;">Imports a `.sql` backup into the active database.</p>
                </div>
            </div>

            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 14px; margin-bottom: 18px; color: #991b1b; font-size: 13px; line-height: 1.6;">
                Restoring a backup can overwrite existing tables and data. Use a verified SQL backup before continuing.
            </div>

            <form id="restoreDatabaseForm" method="POST" action="{{ route('utilities.backup-and-restore.restore') }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 14px;">
                    <label for="backup_file" style="display: block; margin-bottom: 8px; color: #374151; font-weight: 600; font-size: 13px;">SQL Backup File</label>
                    <input id="backup_file" name="backup_file" type="file" accept=".sql" required style="width: 100%; padding: 11px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; color: #374151; background: #fff;">
                    <div id="backupFileValidationMessage" style="display: none; margin-top: 8px; color: #b91c1c; font-size: 12px; font-weight: 600;">
                        Please select a `.sql` backup file to proceed.
                    </div>
                </div>

                <button type="submit" class="transition hover:scale-101" style="width: 100%; padding: 12px 18px; background-color: #b91c1c; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="fas fa-rotate-left"></i>
                    <span>Restore Database</span>
                </button>
            </form>
        </section>
    </div>

    <style>
        @keyframes backup-file-shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-6px); }
            80% { transform: translateX(6px); }
        }

        .backup-file-input-error {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.18);
            animation: backup-file-shake 0.35s ease-in-out;
        }
    </style>

    <script>
        (function attachRestoreFormValidation() {
            const restoreForm = document.getElementById('restoreDatabaseForm');
            const backupFileInput = document.getElementById('backup_file');
            const validationMessage = document.getElementById('backupFileValidationMessage');

            if (!restoreForm || !backupFileInput || !validationMessage) {
                return;
            }

            function showValidationError(message) {
                validationMessage.textContent = message;
                validationMessage.style.display = 'block';
                backupFileInput.classList.remove('backup-file-input-error');
                void backupFileInput.offsetWidth;
                backupFileInput.classList.add('backup-file-input-error');
            }

            function clearValidationError() {
                validationMessage.style.display = 'none';
                backupFileInput.classList.remove('backup-file-input-error');
            }

            restoreForm.addEventListener('submit', function (event) {
                const selectedFile = backupFileInput.files && backupFileInput.files[0] ? backupFileInput.files[0] : null;
                if (!selectedFile) {
                    event.preventDefault();
                    showValidationError('Please select a `.sql` backup file to proceed.');
                    backupFileInput.focus();
                    return;
                }

                if (!selectedFile.name.toLowerCase().endsWith('.sql')) {
                    event.preventDefault();
                    showValidationError('Only `.sql` backup files are allowed.');
                    backupFileInput.focus();
                    return;
                }

                clearValidationError();

                if (!window.confirm('Restoring a backup may overwrite the current database. Continue?')) {
                    event.preventDefault();
                }
            });

            backupFileInput.addEventListener('change', function () {
                const selectedFile = backupFileInput.files && backupFileInput.files[0] ? backupFileInput.files[0] : null;
                if (selectedFile && selectedFile.name.toLowerCase().endsWith('.sql')) {
                    clearValidationError();
                }
            });
        })();
    </script>
@endsection
