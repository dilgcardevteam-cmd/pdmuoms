@extends('layouts.dashboard')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
    @php
        $activeUserTab = request()->query('tab') === 'access-grants' ? 'accessGrantsPanel' : 'usersPanel';
        $viewerIsSuperAdmin = Auth::user()->isSuperAdmin();
    @endphp

    <div class="content-header">
        <h1>User Management</h1>
        <p>Manage all system users, their roles, and their CRUD permissions.</p>
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

    <div class="project-tabs" role="tablist" aria-label="User management sections" style="margin-top: 28px;">
        <button
            type="button"
            class="project-tab {{ $activeUserTab === 'usersPanel' ? 'is-active' : '' }}"
            data-user-tab-target="usersPanel"
            role="tab"
            aria-controls="usersPanel"
            aria-selected="{{ $activeUserTab === 'usersPanel' ? 'true' : 'false' }}"
        >
            Users
        </button>
        @if($viewerIsSuperAdmin)
            <button
                type="button"
                class="project-tab {{ $activeUserTab === 'accessGrantsPanel' ? 'is-active' : '' }}"
                data-user-tab-target="accessGrantsPanel"
                role="tab"
                aria-controls="accessGrantsPanel"
                aria-selected="{{ $activeUserTab === 'accessGrantsPanel' ? 'true' : 'false' }}"
            >
                Access Grant
            </button>
        @endif
    </div>

    <section id="usersPanel" class="project-tab-panel {{ $activeUserTab === 'usersPanel' ? 'is-active' : '' }}" role="tabpanel">
        <div class="user-management-panel">
            <div class="user-management-header">
                <h2 style="color: #002C76; font-size: 18px; margin: 0;">Active Users ({{ $users->total() }})</h2>
                <a href="{{ route('users.create') }}" class="user-management-add-btn" style="padding: 10px 20px; background-color: #002C76; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.3s ease;">
                    <i class="fas fa-user-plus"></i> Add New User
                </a>
            </div>

            <div class="user-table-wrap">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 13px;">Name</th>
                            <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 13px;">Email</th>
                            <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 13px;">Username</th>
                            <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 13px;">Role</th>
                            <th style="padding: 12px; text-align: left; color: #374151; font-weight: 600; font-size: 13px;">Status</th>
                            <th style="padding: 12px; text-align: center; color: #374151; font-weight: 600; font-size: 13px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s ease;">
                                <td style="padding: 15px 12px; color: #374151;">
                                    <strong>{{ $user->fname }} {{ $user->lname }}</strong>
                                </td>
                                <td style="padding: 15px 12px; color: #6b7280; font-size: 13px;">{{ $user->emailaddress }}</td>
                                <td style="padding: 15px 12px; color: #6b7280; font-size: 13px;">{{ $user->username }}</td>
                                <td style="padding: 15px 12px;">
                                    <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                        @if($user->isSuperAdmin()) background-color: #fee2e2; color: #991b1b;
                                        @elseif($user->isRegionalUser()) background-color: #dbeafe; color: #1d4ed8;
                                        @elseif($user->isProvincialUser()) background-color: #ede9fe; color: #6d28d9;
                                        @else background-color: #dcfce7; color: #166534; @endif">
                                        {{ $user->roleLabel() }}
                                    </span>
                                </td>
                                <td style="padding: 15px 12px;">
                                    <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                        @if($user->status === 'active') background-color: #d1fae5; color: #065f46;
                                        @else background-color: #fed7aa; color: #92400e; @endif">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td style="padding: 15px 12px; text-align: center;">
                                    <a href="{{ route('users.show', $user->idno) }}" style="padding: 6px 12px; background-color: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; text-decoration: none; margin-right: 5px; transition: all 0.3s ease;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($user->idno !== Auth::id())
                                        <form action="{{ route('users.block', $user->idno) }}" method="POST" style="display: inline;" onsubmit="return confirm('{{ $user->status === 'inactive' ? 'Unblock this user? They will be able to log in again.' : 'Block this user? They will no longer be able to log in.' }}');">
                                            @csrf
                                            @method('PUT')
                                            @if($user->status === 'inactive')
                                                <button type="submit" style="padding: 6px 12px; background-color: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; transition: all 0.3s ease;">
                                                    <i class="fas fa-user-check"></i> Unblock
                                                </button>
                                            @else
                                                <button type="submit" style="padding: 6px 12px; background-color: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; transition: all 0.3s ease;">
                                                    <i class="fas fa-user-slash"></i> Block
                                                </button>
                                            @endif
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 40px; text-align: center; color: #9ca3af;">
                                    <i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 10px;"></i>
                                    <p>No users found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="user-mobile-cards">
                @forelse($users as $user)
                    <article class="user-mobile-card" data-user-mobile-card>
                        <button type="button" class="user-mobile-card__toggle" data-user-mobile-toggle aria-expanded="false">
                            <div class="user-mobile-card__top">
                                <div>
                                    <h3 class="user-mobile-card__name">{{ $user->fname }} {{ $user->lname }}</h3>
                                    <p class="user-mobile-card__username">{{ $user->username }}</p>
                                </div>
                                <div class="user-mobile-card__summary">
                                    <div class="user-mobile-card__badges">
                                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                            @if($user->isSuperAdmin()) background-color: #fee2e2; color: #991b1b;
                                            @elseif($user->isRegionalUser()) background-color: #dbeafe; color: #1d4ed8;
                                            @elseif($user->isProvincialUser()) background-color: #ede9fe; color: #6d28d9;
                                            @else background-color: #dcfce7; color: #166534; @endif">
                                            {{ $user->roleLabel() }}
                                        </span>
                                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                            @if($user->status === 'active') background-color: #d1fae5; color: #065f46;
                                            @else background-color: #fed7aa; color: #92400e; @endif">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </div>
                                    <span class="user-mobile-card__chevron" aria-hidden="true">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </div>
                            </div>
                        </button>

                        <div class="user-mobile-card__content" data-user-mobile-content hidden>
                            <dl class="user-mobile-card__meta">
                                <div>
                                    <dt>Email</dt>
                                    <dd>{{ $user->emailaddress }}</dd>
                                </div>
                                <div>
                                    <dt>Username</dt>
                                    <dd>{{ $user->username }}</dd>
                                </div>
                            </dl>

                            <div class="user-mobile-card__actions">
                                <a href="{{ route('users.show', $user->idno) }}" class="user-mobile-card__action user-mobile-card__action--view">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($user->idno !== Auth::id())
                                    <form action="{{ route('users.block', $user->idno) }}" method="POST" class="user-mobile-card__form" onsubmit="return confirm('{{ $user->status === 'inactive' ? 'Unblock this user? They will be able to log in again.' : 'Block this user? They will no longer be able to log in.' }}');">
                                        @csrf
                                        @method('PUT')
                                        @if($user->status === 'inactive')
                                            <button type="submit" class="user-mobile-card__action user-mobile-card__action--unblock">
                                                <i class="fas fa-user-check"></i> Unblock
                                            </button>
                                        @else
                                            <button type="submit" class="user-mobile-card__action user-mobile-card__action--block">
                                                <i class="fas fa-user-slash"></i> Block
                                            </button>
                                        @endif
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="user-mobile-empty">
                        <i class="fas fa-inbox" style="font-size: 28px;"></i>
                        <p style="margin: 0;">No users found</p>
                    </div>
                @endforelse
            </div>

            <div style="margin-top: 20px;">
                {{ $users->links() }}
            </div>
        </div>
    </section>

    @if($viewerIsSuperAdmin)
        <section id="accessGrantsPanel" class="project-tab-panel {{ $activeUserTab === 'accessGrantsPanel' ? 'is-active' : '' }}" role="tabpanel">
            <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 24px; flex-wrap: wrap;">
                    <div>
                        <h2 style="color: #002C76; font-size: 18px; margin: 0 0 6px;">Access Grant</h2>
                        <p style="margin: 0; color: #6b7280; font-size: 13px; max-width: 700px;">
                                            Grant per-feature record permissions. Role defaults come from the user's hierarchy level, and Access Grant adds extra module permissions beyond that baseline.
                        </p>
                    </div>
                    <div style="padding: 10px 14px; border-radius: 10px; background: #eff6ff; color: #1d4ed8; font-size: 12px; font-weight: 700;">
                        Visible only to superadmin
                    </div>
                </div>

                @if ($errors->has('crud_permissions') || $errors->has('crud_permissions.*'))
                    <div style="background-color: #fff7ed; border: 1px solid #fdba74; color: #9a3412; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                        {{ $errors->first('crud_permissions') ?: $errors->first('crud_permissions.*') }}
                    </div>
                @endif

                <div class="access-grant-table-wrap">
                    <table class="access-grant-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Access Status</th>
                                <th style="width: 140px; text-align: center;">Permissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @php
                                    $grantedPermissions = $user->grantedCrudPermissions();
                                    $hasFullAccess = in_array('*', $grantedPermissions, true);
                                    $isScopedAccess = $user->usesScopedCrudAccess();
                                @endphp
                                <tr class="access-grant-row">
                                    <td>
                                        <div class="access-grant-user">
                                            <div class="access-grant-user__name">{{ $user->fname }} {{ $user->lname }}</div>
                                            <div class="access-grant-user__meta">{{ $user->username }}</div>
                                        </div>
                                    </td>
                                    <td class="access-grant-cell-muted">{{ $user->emailaddress }}</td>
                                    <td>
                                        <span class="access-role-badge access-role-badge--{{ $user->role }}">
                                            {{ $user->roleLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="access-state-badge access-state-badge--{{ $user->isSuperAdmin() ? 'role' : ($isScopedAccess ? ($hasFullAccess ? 'all' : ($grantedPermissions === [] ? 'empty' : 'custom')) : 'legacy') }}">
                                            @if($user->isSuperAdmin())
                                                Full access by role
                                            @elseif(!$isScopedAccess)
                                                Role defaults only
                                            @elseif($hasFullAccess)
                                                All additional permissions
                                            @elseif($grantedPermissions === [])
                                                No additional grants
                                            @else
                                                Custom additional grants
                                            @endif
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <button
                                            type="button"
                                            class="access-accordion-toggle"
                                            data-access-accordion-toggle
                                            data-target="access-grant-{{ $user->idno }}"
                                            aria-expanded="false"
                                            aria-controls="access-grant-{{ $user->idno }}"
                                        >
                                            <span>View Access</span>
                                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="access-grant-detail-row" id="access-grant-{{ $user->idno }}" data-access-accordion-content hidden>
                                    <td colspan="5">
                                        <div class="access-grant-detail">
                                            @if($user->isSuperAdmin())
                                                <div class="access-grant-note">
                                                    Superadmin accounts always keep full access and do not require CRUD permission grants.
                                                </div>
                                            @else
                                                <form method="POST" action="{{ route('users.access.update', $user->idno) }}">
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
                                                                                    $checked = $hasFullAccess || $user->hasCrudPermission($item['aspect'], $actionKey);
                                                                                    $isDefaultPermission = !$hasFullAccess && $user->hasDefaultCrudPermission($item['aspect'], $actionKey);
                                                                                @endphp
                                                                                <td class="crud-permission-check-cell">
                                                                                    <label class="crud-check-item {{ $isDefaultPermission ? 'crud-check-item--default' : '' }}">
                                                                                        <input
                                                                                            type="checkbox"
                                                                                            name="crud_permissions[]"
                                                                                            value="{{ $permissionKey }}"
                                                                                            @checked($checked)
                                                                                            @disabled($isDefaultPermission)
                                                                                        >
                                                                                        <span>{{ $isDefaultPermission ? 'Role default' : 'Grant' }}</span>
                                                                                    </label>
                                                                                    @if($isDefaultPermission)
                                                                                        <input type="hidden" name="crud_permissions[]" value="{{ $permissionKey }}">
                                                                                    @endif
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 18px; flex-wrap: wrap;">
                                                        <div style="font-size: 12px; color: #64748b;">
                                                            Checked boxes marked as `Role default` come from the user's hierarchy role. Use unchecked boxes to add more access beyond that baseline.
                                                        </div>
                                                        <button type="submit" style="padding: 10px 16px; background-color: #002C76; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px;">
                                                            Save Permissions
                                                        </button>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 20px;">
                    {{ $users->appends(['tab' => 'access-grants'])->links() }}
                </div>
            </div>
        </section>
    @endif

    <style>
        .project-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .project-tab {
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

        .project-tab:hover {
            background: #dbeafe;
            border-color: #93c5fd;
        }

        .project-tab.is-active {
            background: #002C76;
            border-color: #002C76;
            color: #ffffff;
            box-shadow: 0 10px 24px rgba(0, 44, 118, 0.18);
        }

        .project-tab-panel {
            display: none;
        }

        .project-tab-panel.is-active {
            display: block;
        }

        .user-management-panel {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .user-management-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .user-table-wrap {
            overflow-x: auto;
        }

        .user-mobile-cards {
            display: none;
        }

        .user-mobile-card {
            border: 1px solid #dbe4f0;
            border-radius: 18px;
            padding: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
        }

        .user-mobile-card__top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .user-mobile-card__toggle {
            width: 100%;
            padding: 0;
            border: none;
            background: transparent;
            text-align: left;
            cursor: pointer;
        }

        .user-mobile-card__summary {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-mobile-card__content {
            margin-top: 14px;
        }

        .user-mobile-card__chevron {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, background 0.2s ease;
            flex: 0 0 auto;
        }

        .user-mobile-card.is-expanded .user-mobile-card__chevron {
            transform: rotate(180deg);
            background: #dbeafe;
        }

        .user-mobile-card__name {
            margin: 0 0 4px;
            color: #0f172a;
            font-size: 17px;
            font-weight: 700;
        }

        .user-mobile-card__username {
            margin: 0;
            color: #64748b;
            font-size: 13px;
        }

        .user-mobile-card__badges {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }

        .user-mobile-card__meta {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin: 0 0 16px;
        }

        .user-mobile-card__meta div {
            padding: 12px 14px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .user-mobile-card__meta dt {
            margin-bottom: 4px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .user-mobile-card__meta dd {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            word-break: break-word;
        }

        .user-mobile-card__actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .user-mobile-card__form {
            margin: 0;
        }

        .user-mobile-card__action {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 11px 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-mobile-card__action--view {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .user-mobile-card__action--block {
            background: #fee2e2;
            color: #b91c1c;
        }

        .user-mobile-card__action--unblock {
            background: #d1fae5;
            color: #047857;
        }

        .user-mobile-empty {
            display: none;
            padding: 40px 20px;
            border: 1px dashed #cbd5e1;
            border-radius: 18px;
            text-align: center;
            color: #94a3b8;
            background: #f8fafc;
        }

        .access-grant-table-wrap {
            overflow-x: auto;
            border: 1px solid #dbe4f0;
            border-radius: 16px;
        }

        .access-grant-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 860px;
            background: #ffffff;
        }

        .access-grant-table th,
        .access-grant-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
            text-align: left;
        }

        .access-grant-table th {
            background: #f8fafc;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .access-grant-row {
            background: #ffffff;
        }

        .access-grant-detail-row[hidden] {
            display: none;
        }

        .access-grant-detail-row td {
            padding: 0;
            background: #f8fbff;
        }

        .access-grant-user__name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .access-grant-user__meta,
        .access-grant-cell-muted {
            font-size: 13px;
            color: #64748b;
        }

        .access-role-badge,
        .access-state-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 700;
        }

        .access-role-badge {
            background: #e2e8f0;
            color: #334155;
        }

        .access-role-badge--superadmin {
            background: #fee2e2;
            color: #991b1b;
        }

        .access-role-badge--user_regional {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .access-role-badge--user_provincial {
            background: #ede9fe;
            color: #6d28d9;
        }

        .access-role-badge--user_lgu {
            background: #dcfce7;
            color: #166534;
        }

        .access-state-badge--role {
            background: #fee2e2;
            color: #991b1b;
        }

        .access-state-badge--legacy {
            background: #f3f4f6;
            color: #475569;
        }

        .access-state-badge--all,
        .access-state-badge--custom {
            background: #dcfce7;
            color: #166534;
        }

        .access-state-badge--empty {
            background: #fef3c7;
            color: #92400e;
        }

        .access-accordion-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .access-accordion-toggle i {
            transition: transform 0.2s ease;
        }

        .access-accordion-toggle[aria-expanded="true"] i {
            transform: rotate(180deg);
        }

        .access-grant-detail {
            padding: 20px;
        }

        .access-grant-note {
            padding: 14px;
            border: 1px solid #fecaca;
            background: #fff1f2;
            color: #9f1239;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.6;
        }

        .crud-permission-table-wrap {
            overflow-x: auto;
            border: 1px solid #dbeafe;
            border-radius: 12px;
        }

        .crud-permission-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1120px;
            background: #ffffff;
        }

        .crud-permission-table th,
        .crud-permission-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        .crud-permission-table th {
            background: #eff6ff;
            color: #1e3a8a;
            font-size: 13px;
            font-weight: 700;
        }

        .crud-permission-table tbody tr:last-child td {
            border-bottom: none;
        }

        .crud-check-item {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
            color: #334155;
            font-size: 12px;
            font-weight: 600;
        }

        .crud-check-item--default span {
            color: #2563eb;
        }

        .crud-permission-module-cell {
            background: #f8fbff;
            min-width: 200px;
            vertical-align: top;
        }

        .crud-permission-module-title,
        .crud-permission-submodule-cell {
            color: #0f172a;
            font-weight: 700;
            font-size: 13px;
        }

        .crud-permission-module-description,
        .crud-permission-description-cell {
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
        }

        .crud-permission-description-cell {
            min-width: 280px;
        }

        .crud-permission-check-cell {
            min-width: 110px;
            text-align: center;
        }

        table tbody tr:hover {
            background-color: #f9fafb;
        }

        a[style*="background-color: #3b82f6"]:hover {
            background-color: #2563eb !important;
        }

        button[style*="background-color: #ef4444"]:hover {
            background-color: #dc2626 !important;
        }

        a[style*="background-color: #002C76"]:hover {
            background-color: #001f59 !important;
            transform: translateY(-2px);
        }

        .user-mobile-card__action:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
        }

        @media (max-width: 768px) {
            .project-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                overflow-y: hidden;
                width: 100%;
                padding-bottom: 4px;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .project-tabs::-webkit-scrollbar {
                display: none;
            }

            .project-tab {
                flex: 0 0 auto;
                white-space: nowrap;
            }

            .user-management-panel,
            #accessGrantsPanel > div[style*="background: white"] {
                padding: 18px !important;
            }

            .user-management-header {
                align-items: stretch;
            }

            .user-management-add-btn {
                width: 100%;
                justify-content: center;
            }

            .user-table-wrap {
                display: none;
            }

            .user-mobile-cards {
                display: grid;
                gap: 14px;
            }

            .user-mobile-empty {
                display: block;
            }

            .user-mobile-card__top {
                align-items: center;
            }

            .user-mobile-card__badges {
                flex-direction: row;
                align-items: center;
                flex-wrap: wrap;
            }

            .user-mobile-card__summary {
                width: 100%;
                justify-content: space-between;
            }

            .access-grant-table-wrap {
                margin-left: -6px;
                margin-right: -6px;
                border-radius: 12px;
            }

            .access-grant-table {
                min-width: 720px;
            }

            .access-grant-table th,
            .access-grant-table td {
                padding: 12px;
            }

            .crud-permission-table-wrap {
                margin-left: -6px;
                margin-right: -6px;
                border-radius: 10px;
            }

            .crud-permission-table {
                min-width: 460px;
            }

            .crud-permission-table th,
            .crud-permission-table td {
                padding: 10px 12px;
                font-size: 12px;
            }

            .crud-check-item {
                white-space: nowrap;
            }

            #accessGrantsPanel button[type="submit"] {
                width: 100%;
            }
        }
    </style>

    <script>
        (function attachUserManagementTabs() {
            const tabs = Array.from(document.querySelectorAll('[data-user-tab-target]'));
            const panels = Array.from(document.querySelectorAll('.project-tab-panel'));

            if (tabs.length === 0 || panels.length === 0) {
                return;
            }

            function activateTab(panelId) {
                tabs.forEach((tab) => {
                    const isActive = tab.dataset.userTabTarget === panelId;
                    tab.classList.toggle('is-active', isActive);
                    tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                panels.forEach((panel) => {
                    panel.classList.toggle('is-active', panel.id === panelId);
                });
            }

            tabs.forEach((tab) => {
                tab.addEventListener('click', function () {
                    activateTab(tab.dataset.userTabTarget);
                });
            });
        }());

        (function attachUserMobileCards() {
            const cards = Array.from(document.querySelectorAll('[data-user-mobile-card]'));

            if (cards.length === 0) {
                return;
            }

            cards.forEach((card) => {
                const toggle = card.querySelector('[data-user-mobile-toggle]');
                const content = card.querySelector('[data-user-mobile-content]');

                if (!toggle || !content) {
                    return;
                }

                toggle.addEventListener('click', function () {
                    const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                    toggle.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
                    content.hidden = isExpanded;
                    card.classList.toggle('is-expanded', !isExpanded);
                });
            });
        }());

        (function attachAccessGrantAccordion() {
            const toggles = Array.from(document.querySelectorAll('[data-access-accordion-toggle]'));

            if (toggles.length === 0) {
                return;
            }

            toggles.forEach((toggle) => {
                const targetId = toggle.dataset.target;
                const content = targetId ? document.getElementById(targetId) : null;

                if (!content) {
                    return;
                }

                toggle.addEventListener('click', function () {
                    const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                    toggle.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
                    content.hidden = isExpanded;
                });
            });
        }());
    </script>
@endsection
