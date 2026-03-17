@extends('layouts.dashboard')

@section('title', 'Role Configuration')
@section('page-title', 'Role Configuration')

@section('content')
    @php
        $activeRole = request()->query('role', $roleConfigurations[0]['role'] ?? \App\Models\User::ROLE_REGIONAL);
    @endphp

    @if (session('success'))
        <div style="margin-bottom: 18px; padding: 12px 16px; border-radius: 10px; border: 1px solid #a7f3d0; background: #ecfdf5; color: #166534; font-size: 13px; font-weight: 600;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="margin-bottom: 18px; padding: 12px 16px; border-radius: 10px; border: 1px solid #fecaca; background: #fff1f2; color: #be123c; font-size: 13px; font-weight: 600;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="margin-bottom: 18px; padding: 14px 16px; border-radius: 10px; border: 1px solid #fecaca; background: #fff7f7; color: #991b1b;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="role-config-page-header" style="display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; margin-bottom: 12px; flex-wrap: wrap;">
        <div class="content-header" style="margin-bottom: 0;">
            <h1>Role Configuration</h1>
            <p>Manage centralized CRUD access for each hierarchy role used by the application.</p>
        </div>

        <a href="{{ route('utilities.system-setup.index') }}" class="role-config-back-link" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 8px; background: linear-gradient(180deg, #0a4cb3 0%, #002c76 100%); color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #002c76; box-shadow: 0 8px 18px rgba(0, 44, 118, 0.18);">
            <i class="fas fa-arrow-left"></i>
            <span>Back to System Setup</span>
        </a>
    </div>

    <section class="role-config-shell" style="background: white; padding: 28px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);">
        <div class="role-config-intro" style="display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; flex-wrap: wrap; margin-bottom: 18px;">
            <div class="role-config-intro-copy" style="display: flex; align-items: flex-start; gap: 14px;">
                <div class="role-config-intro-icon" style="width: 52px; height: 52px; border-radius: 14px; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h2 style="margin: 0 0 6px; color: #002C76; font-size: 20px;">Role Configuration</h2>
                    <p style="margin: 0; color: #475569; font-size: 13px; line-height: 1.7; max-width: 820px;">
                        Configure CRUD access by role instead of by individual user. Changes save automatically and affect all users currently assigned to that hierarchy role.
                    </p>
                </div>
            </div>
            <div class="role-config-superadmin-note" style="padding: 10px 14px; border-radius: 10px; background: #eff6ff; color: #1d4ed8; font-size: 12px; font-weight: 700;">
                Superadmin remains full access by design
            </div>
        </div>

        <div class="role-config-superadmin-card" style="margin-bottom: 18px; padding: 16px 18px; border: 1px solid #dbeafe; border-radius: 12px; background: linear-gradient(180deg, #f8fbff 0%, #eff6ff 100%);">
            <div style="font-size: 14px; font-weight: 700; color: #002C76; margin-bottom: 6px;">Superadmin</div>
            <div style="font-size: 13px; color: #475569; line-height: 1.7;">
                {{ $roleDescriptions[\App\Models\User::ROLE_SUPERADMIN] ?? 'Superadmin keeps full access across all modules and utilities.' }}
            </div>
        </div>

        <div class="role-config-tabs" role="tablist" aria-label="Role configuration tabs">
            @foreach ($roleConfigurations as $roleConfiguration)
                @php
                    $roleKey = $roleConfiguration['role'];
                    $isActiveRole = $activeRole === $roleKey;
                @endphp
                <button
                    type="button"
                    class="role-config-tab{{ $isActiveRole ? ' is-active' : '' }}"
                    data-role-config-tab
                    data-target="role-panel-{{ $roleKey }}"
                    aria-selected="{{ $isActiveRole ? 'true' : 'false' }}"
                    role="tab"
                >
                    {{ $roleConfiguration['label'] }}
                </button>
            @endforeach
        </div>

        @foreach ($roleConfigurations as $roleConfiguration)
            @php
                $roleKey = $roleConfiguration['role'];
                $isActiveRole = $activeRole === $roleKey;
                $configuredPermissions = $roleConfiguration['permissions'] ?? [];
            @endphp
            <section
                id="role-panel-{{ $roleKey }}"
                class="role-config-panel{{ $isActiveRole ? ' is-active' : '' }}"
                data-role-config-panel
                role="tabpanel"
            >
                <div class="role-config-panel-header" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; flex-wrap: wrap;">
                    <div>
                        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 8px;">
                            <h3 style="margin: 0; color: #002C76; font-size: 18px;">{{ $roleConfiguration['label'] }}</h3>
                            <span
                                class="role-config-badge"
                                data-role-config-badge
                                data-default-bg="#dcfce7"
                                data-default-color="#166534"
                                data-custom-bg="#ede9fe"
                                data-custom-color="#5b21b6"
                                data-default-text="Using recommended baseline"
                                data-custom-text="Custom role configuration"
                                style="padding: 5px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; background: {{ $roleConfiguration['uses_recommended_defaults'] ? '#dcfce7' : '#ede9fe' }}; color: {{ $roleConfiguration['uses_recommended_defaults'] ? '#166534' : '#5b21b6' }};"
                            >
                                {{ $roleConfiguration['uses_recommended_defaults'] ? 'Using recommended baseline' : 'Custom role configuration' }}
                            </span>
                        </div>
                        <p style="margin: 0; color: #475569; font-size: 13px; line-height: 1.7; max-width: 860px;">
                            {{ $roleConfiguration['description'] }}
                        </p>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('utilities.role-configuration.roles.update', ['role' => $roleKey]) }}"
                    data-role-config-form
                    data-role-key="{{ $roleKey }}"
                    data-role-label="{{ $roleConfiguration['label'] }}"
                    data-reset-url="{{ route('utilities.role-configuration.roles.reset', ['role' => $roleKey]) }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="role-config-status-row" style="margin-bottom: 18px;">
                        <div style="font-size: 12px; color: #64748b; line-height: 1.7;">
                            Tick or untick access items to save the permission matrix automatically for every user assigned to the <strong>{{ $roleConfiguration['label'] }}</strong> role.
                        </div>
                        <div class="role-config-save-status" data-role-config-save-status aria-live="polite">
                            <span class="role-config-save-status__spinner" aria-hidden="true"></span>
                            <span class="role-config-save-status__text" data-role-config-save-status-text>No unsaved changes.</span>
                        </div>
                    </div>

                    <div class="crud-permission-table-wrap">
                        <table class="crud-permission-table">
                            <thead>
                                <tr>
                                    <th>Module</th>
                                    <th>Submodule</th>
                                    <th>Description</th>
                                    @foreach($crudActionOptions as $actionKey => $actionLabel)
                                        <th>{{ $actionLabel }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($accessGrantModules as $module)
                                    @php
                                        $items = $module['items'] ?? [];
                                        $rowspan = count($items);
                                    @endphp
                                    @foreach($items as $itemIndex => $item)
                                        <tr class="crud-permission-row">
                                            @if($itemIndex === 0)
                                                <td rowspan="{{ $rowspan }}" class="crud-permission-module-cell">
                                                    <div class="crud-permission-module-title">{{ $module['module'] }}</div>
                                                    <div class="crud-permission-module-description">{{ $module['description'] }}</div>
                                                </td>
                                            @endif
                                            <td class="crud-permission-submodule-cell" data-label="Submodule">
                                                <div class="crud-permission-mobile-module">
                                                    <div class="crud-permission-mobile-label">Module</div>
                                                    <div class="crud-permission-module-title">{{ $module['module'] }}</div>
                                                    <div class="crud-permission-module-description">{{ $module['description'] }}</div>
                                                </div>
                                                {{ $item['label'] }}
                                            </td>
                                            <td class="crud-permission-description-cell" data-label="Description">{{ $item['description'] }}</td>
                                            @foreach($crudActionOptions as $actionKey => $actionLabel)
                                                @php
                                                    $permissionKey = $item['aspect'] . '.' . $actionKey;
                                                @endphp
                                                <td class="crud-permission-check-cell" data-label="{{ $actionLabel }}">
                                                    <label class="crud-check-item">
                                                        <input
                                                            type="checkbox"
                                                            name="crud_permissions[]"
                                                            value="{{ $permissionKey }}"
                                                            @checked(in_array($permissionKey, $configuredPermissions, true))
                                                        >
                                                        <span>Allow</span>
                                                    </label>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="display: flex; justify-content: flex-end; align-items: center; gap: 16px; margin-top: 18px; flex-wrap: wrap;">
                        <button
                            type="button"
                            class="role-config-reset-btn"
                            data-role-config-reset
                            @disabled($roleConfiguration['uses_recommended_defaults'])
                        >
                            Reset Role
                        </button>
                    </div>
                </form>
            </section>
        @endforeach
    </section>

    <style>
        .role-config-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .role-config-tab {
            padding: 10px 16px;
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .role-config-tab:hover {
            background: #dbeafe;
            border-color: #93c5fd;
        }

        .role-config-tab.is-active {
            background: #002C76;
            border-color: #002C76;
            color: #ffffff;
            box-shadow: 0 10px 24px rgba(0, 44, 118, 0.18);
        }

        .role-config-back-link {
            justify-content: center;
        }

        .role-config-panel {
            display: none;
        }

        .role-config-panel.is-active {
            display: block;
        }

        .crud-permission-table-wrap {
            overflow-x: auto;
            border: 1px solid #dbe4f0;
            border-radius: 12px;
        }

        .crud-permission-table {
            width: 100%;
            min-width: 980px;
            border-collapse: collapse;
        }

        .crud-permission-table th,
        .crud-permission-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
            text-align: left;
            font-size: 13px;
        }

        .crud-permission-table th {
            background: #f8fafc;
            color: #002C76;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .crud-permission-module-cell {
            min-width: 200px;
            background: #f8fbff;
        }

        .crud-permission-module-title {
            color: #0f172a;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .crud-permission-module-description,
        .crud-permission-description-cell {
            color: #64748b;
            line-height: 1.6;
        }

        .crud-permission-mobile-module {
            display: none;
        }

        .crud-permission-mobile-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #1d4ed8;
            margin-bottom: 4px;
        }

        .crud-permission-submodule-cell {
            min-width: 180px;
            color: #0f172a;
            font-weight: 600;
        }

        .crud-permission-check-cell {
            min-width: 108px;
            text-align: center;
        }

        .crud-check-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #334155;
            font-size: 12px;
            font-weight: 600;
        }

        .crud-check-item input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #002C76;
        }

        .role-config-status-row {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .role-config-save-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
        }

        .role-config-save-status__spinner {
            width: 12px;
            height: 12px;
            border: 2px solid rgba(29, 78, 216, 0.18);
            border-top-color: currentColor;
            border-radius: 999px;
            animation: role-config-spinner 0.7s linear infinite;
            opacity: 0;
            visibility: hidden;
        }

        .role-config-save-status[data-state="saving"] .role-config-save-status__spinner {
            opacity: 1;
            visibility: visible;
        }

        .role-config-save-status[data-state="saving"] {
            color: #1d4ed8;
        }

        .role-config-save-status[data-state="saved"] {
            color: #166534;
        }

        .role-config-save-status[data-state="error"] {
            color: #be123c;
        }

        .role-config-reset-btn {
            padding: 10px 16px;
            background-color: #ffffff;
            color: #991b1b;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 13px;
        }

        .role-config-reset-btn:hover:not(:disabled) {
            background-color: #fff1f2;
        }

        .role-config-reset-btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        @keyframes role-config-spinner {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .role-config-page-header {
                gap: 12px;
            }

            .role-config-back-link {
                width: 100%;
            }

            .role-config-shell {
                padding: 18px 14px !important;
                border-radius: 10px !important;
            }

            .role-config-intro {
                gap: 14px !important;
            }

            .role-config-intro-copy {
                align-items: flex-start !important;
            }

            .role-config-intro-icon {
                width: 44px !important;
                height: 44px !important;
                border-radius: 12px !important;
                font-size: 17px !important;
                flex: 0 0 auto;
            }

            .role-config-superadmin-note,
            .role-config-superadmin-card {
                width: 100%;
            }

            .role-config-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                overflow-y: hidden;
                padding-bottom: 4px;
                scrollbar-width: none;
                -webkit-overflow-scrolling: touch;
            }

            .role-config-tabs::-webkit-scrollbar {
                display: none;
            }

            .role-config-tab {
                flex: 0 0 auto;
                white-space: nowrap;
                width: auto;
            }

            .role-config-panel-header {
                margin-bottom: 14px !important;
            }

            .role-config-status-row {
                gap: 10px;
            }

            .role-config-save-status {
                align-self: flex-start;
            }

            .crud-permission-table-wrap {
                overflow: visible;
                border: none;
                background: transparent;
            }

            .crud-permission-table {
                min-width: 0;
            }

            .crud-permission-table,
            .crud-permission-table thead,
            .crud-permission-table tbody,
            .crud-permission-table tr,
            .crud-permission-table td {
                display: block;
                width: 100%;
            }

            .crud-permission-table thead {
                display: none;
            }

            .crud-permission-row {
                border: 1px solid #dbe4f0;
                border-radius: 14px;
                background: #ffffff;
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
                padding: 16px 14px;
                margin-bottom: 14px;
            }

            .crud-permission-table tbody tr:last-child {
                margin-bottom: 0;
            }

            .crud-permission-module-cell {
                display: none !important;
            }

            .crud-permission-submodule-cell,
            .crud-permission-description-cell,
            .crud-permission-check-cell {
                min-width: 0;
                border-bottom: none !important;
                padding: 0;
                text-align: left;
            }

            .crud-permission-submodule-cell {
                margin-bottom: 12px;
                font-size: 15px;
                line-height: 1.5;
            }

            .crud-permission-mobile-module {
                display: block;
                padding: 12px;
                margin-bottom: 10px;
                border-radius: 12px;
                background: linear-gradient(180deg, #f8fbff 0%, #eff6ff 100%);
                border: 1px solid #dbeafe;
            }

            .crud-permission-description-cell {
                margin-bottom: 14px;
                font-size: 13px;
                line-height: 1.6;
            }

            .crud-permission-description-cell::before,
            .crud-permission-check-cell::before {
                content: attr(data-label);
                display: block;
                margin-bottom: 6px;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: #1d4ed8;
            }

            .crud-permission-check-cell {
                padding: 10px 12px;
                margin-bottom: 10px;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                background: #f8fafc;
            }

            .crud-permission-check-cell:last-child {
                margin-bottom: 0;
            }

            .crud-check-item {
                width: 100%;
                justify-content: space-between;
                gap: 12px;
                font-size: 13px;
            }

            .role-config-reset-btn {
                width: 100%;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = Array.from(document.querySelectorAll('[data-role-config-tab]'));
            const panels = Array.from(document.querySelectorAll('[data-role-config-panel]'));
            const roleConfigForms = Array.from(document.querySelectorAll('[data-role-config-form]'));

            const activatePanel = (panelId) => {
                tabs.forEach((tab) => {
                    const isActive = tab.dataset.target === panelId;
                    tab.classList.toggle('is-active', isActive);
                    tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                panels.forEach((panel) => {
                    panel.classList.toggle('is-active', panel.id === panelId);
                });
            };

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => activatePanel(tab.dataset.target));
            });

            const getCheckedPermissions = (form) => Array.from(form.querySelectorAll('input[name="crud_permissions[]"]:checked'))
                .map((input) => input.value)
                .sort();

            const setCheckedPermissions = (form, permissions) => {
                const selected = new Set(permissions);
                form.querySelectorAll('input[name="crud_permissions[]"]').forEach((input) => {
                    input.checked = selected.has(input.value);
                });
            };

            const setInputsDisabled = (form, disabled) => {
                form.querySelectorAll('input[name="crud_permissions[]"]').forEach((input) => {
                    input.disabled = disabled;
                });

                const resetButton = form.querySelector('[data-role-config-reset]');
                if (resetButton) {
                    if (disabled) {
                        resetButton.disabled = true;
                        return;
                    }

                    resetButton.disabled = resetButton.dataset.allowReset !== 'true';
                }
            };

            const updateBadgeState = (form, usesRecommendedDefaults) => {
                const badge = form.closest('[data-role-config-panel]')?.querySelector('[data-role-config-badge]');
                const resetButton = form.querySelector('[data-role-config-reset]');

                if (badge) {
                    badge.textContent = usesRecommendedDefaults ? badge.dataset.defaultText : badge.dataset.customText;
                    badge.style.background = usesRecommendedDefaults ? badge.dataset.defaultBg : badge.dataset.customBg;
                    badge.style.color = usesRecommendedDefaults ? badge.dataset.defaultColor : badge.dataset.customColor;
                }

                if (resetButton) {
                    resetButton.dataset.allowReset = usesRecommendedDefaults ? 'false' : 'true';
                    resetButton.disabled = usesRecommendedDefaults;
                }
            };

            const setSaveStatus = (form, state, message) => {
                const status = form.querySelector('[data-role-config-save-status]');
                if (!status) {
                    return;
                }

                status.dataset.state = state;
                const statusText = status.querySelector('[data-role-config-save-status-text]');
                if (statusText) {
                    statusText.textContent = message;
                }
            };

            const saveForm = async (form) => {
                const payload = new FormData();
                const checkedPermissions = getCheckedPermissions(form);

                payload.append('_token', form.querySelector('input[name="_token"]').value);
                payload.append('_method', 'PUT');
                checkedPermissions.forEach((permission) => payload.append('crud_permissions[]', permission));

                setSaveStatus(form, 'saving', 'Saving changes...');
                setInputsDisabled(form, true);

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: payload,
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Unable to save role configuration.');
                }

                form.dataset.savedPermissions = JSON.stringify(data.permissions || checkedPermissions);
                updateBadgeState(form, Boolean(data.uses_recommended_defaults));
                setSaveStatus(form, 'saved', data.message || 'Changes saved.');
            };

            const resetForm = async (form) => {
                const payload = new FormData();
                payload.append('_token', form.querySelector('input[name="_token"]').value);
                payload.append('_method', 'DELETE');

                setSaveStatus(form, 'saving', 'Resetting role configuration...');
                setInputsDisabled(form, true);

                const response = await fetch(form.dataset.resetUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: payload,
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Unable to reset role configuration.');
                }

                const permissions = Array.isArray(data.permissions) ? data.permissions : [];
                setCheckedPermissions(form, permissions);
                form.dataset.savedPermissions = JSON.stringify([...permissions].sort());
                updateBadgeState(form, Boolean(data.uses_recommended_defaults));
                setSaveStatus(form, 'saved', data.message || 'Role configuration reset.');
            };

            roleConfigForms.forEach((form) => {
                form.dataset.savedPermissions = JSON.stringify(getCheckedPermissions(form));
                updateBadgeState(form, form.querySelector('[data-role-config-reset]')?.disabled !== false);

                form.querySelectorAll('input[name="crud_permissions[]"]').forEach((input) => {
                    input.addEventListener('change', async () => {
                        const previousPermissions = JSON.parse(form.dataset.savedPermissions || '[]');

                        try {
                            await saveForm(form);
                        } catch (error) {
                            setCheckedPermissions(form, previousPermissions);
                            setSaveStatus(form, 'error', error.message || 'Unable to save role configuration.');
                        } finally {
                            setInputsDisabled(form, false);
                        }
                    });
                });

                const resetButton = form.querySelector('[data-role-config-reset]');
                if (!resetButton) {
                    return;
                }

                resetButton.dataset.allowReset = resetButton.disabled ? 'false' : 'true';
                resetButton.addEventListener('click', () => {
                    if (resetButton.disabled) {
                        return;
                    }

                    window.openConfirmationModal(
                        `Reset ${form.dataset.roleLabel} access back to the recommended default configuration?`,
                        async () => {
                            const previousPermissions = JSON.parse(form.dataset.savedPermissions || '[]');

                            try {
                                await resetForm(form);
                            } catch (error) {
                                setCheckedPermissions(form, previousPermissions);
                                setSaveStatus(form, 'error', error.message || 'Unable to reset role configuration.');
                                setInputsDisabled(form, false);
                            } finally {
                                setInputsDisabled(form, false);
                            }
                        }
                    );
                });
            });
        });
    </script>
@endsection
