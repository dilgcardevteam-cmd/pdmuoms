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

    <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; margin-bottom: 12px; flex-wrap: wrap;">
        <div class="content-header" style="margin-bottom: 0;">
            <h1>Role Configuration</h1>
            <p>Manage centralized CRUD access for each hierarchy role used by the application.</p>
        </div>

        <a href="{{ route('utilities.system-setup.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 8px; background: linear-gradient(180deg, #0a4cb3 0%, #002c76 100%); color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #002c76; box-shadow: 0 8px 18px rgba(0, 44, 118, 0.18);">
            <i class="fas fa-arrow-left"></i>
            <span>Back to System Setup</span>
        </a>
    </div>

    <section style="background: white; padding: 28px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);">
        <div style="display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; flex-wrap: wrap; margin-bottom: 18px;">
            <div style="display: flex; align-items: flex-start; gap: 14px;">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h2 style="margin: 0 0 6px; color: #002C76; font-size: 20px;">Role Configuration</h2>
                    <p style="margin: 0; color: #475569; font-size: 13px; line-height: 1.7; max-width: 820px;">
                        Configure CRUD access by role instead of by individual user. Saving a role here affects all users currently assigned to that hierarchy role.
                    </p>
                </div>
            </div>
            <div style="padding: 10px 14px; border-radius: 10px; background: #eff6ff; color: #1d4ed8; font-size: 12px; font-weight: 700;">
                Superadmin remains full access by design
            </div>
        </div>

        <div style="margin-bottom: 18px; padding: 16px 18px; border: 1px solid #dbeafe; border-radius: 12px; background: linear-gradient(180deg, #f8fbff 0%, #eff6ff 100%);">
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
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; flex-wrap: wrap;">
                    <div>
                        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 8px;">
                            <h3 style="margin: 0; color: #002C76; font-size: 18px;">{{ $roleConfiguration['label'] }}</h3>
                            <span style="padding: 5px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; background: {{ $roleConfiguration['uses_recommended_defaults'] ? '#dcfce7' : '#ede9fe' }}; color: {{ $roleConfiguration['uses_recommended_defaults'] ? '#166534' : '#5b21b6' }};">
                                {{ $roleConfiguration['uses_recommended_defaults'] ? 'Using recommended baseline' : 'Custom role configuration' }}
                            </span>
                        </div>
                        <p style="margin: 0; color: #475569; font-size: 13px; line-height: 1.7; max-width: 860px;">
                            {{ $roleConfiguration['description'] }}
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('utilities.role-configuration.roles.update', ['role' => $roleKey]) }}">
                    @csrf
                    @method('PUT')

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
                                        <tr>
                                            @if($itemIndex === 0)
                                                <td rowspan="{{ $rowspan }}" class="crud-permission-module-cell">
                                                    <div class="crud-permission-module-title">{{ $module['module'] }}</div>
                                                    <div class="crud-permission-module-description">{{ $module['description'] }}</div>
                                                </td>
                                            @endif
                                            <td class="crud-permission-submodule-cell">{{ $item['label'] }}</td>
                                            <td class="crud-permission-description-cell">{{ $item['description'] }}</td>
                                            @foreach($crudActionOptions as $actionKey => $actionLabel)
                                                @php
                                                    $permissionKey = $item['aspect'] . '.' . $actionKey;
                                                @endphp
                                                <td class="crud-permission-check-cell">
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

                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-top: 18px; flex-wrap: wrap;">
                        <div style="font-size: 12px; color: #64748b; line-height: 1.7;">
                            Save changes to apply this permission matrix to every user assigned to the <strong>{{ $roleConfiguration['label'] }}</strong> role.
                        </div>
                        <button type="submit" class="role-config-save-btn">
                            Save Role Configuration
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

        .role-config-save-btn {
            padding: 10px 16px;
            background-color: #002C76;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 13px;
        }

        .role-config-save-btn:hover {
            background-color: #0a4cb3;
        }

        @media (max-width: 768px) {
            .role-config-tab {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = Array.from(document.querySelectorAll('[data-role-config-tab]'));
            const panels = Array.from(document.querySelectorAll('[data-role-config-panel]'));

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
        });
    </script>
@endsection
