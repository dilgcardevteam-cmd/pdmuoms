<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - DILG-CAR PDMU</title>
    <link rel="icon" type="image/png" href="/DILG-Logo.png">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Facebook Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-image: url('/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            overflow-x: hidden;
        }

        body.sidebar-open {
            overflow: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(135deg, #002C76 0%, #003d99 100%);
            padding: 20px;
            overflow-y: auto;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            will-change: transform;
            z-index: 1000;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
            transform: translateX(0);
        }
        
        .sidebar.collapsed {
            transform: translateX(-100%);
            box-shadow: none;
            pointer-events: none;
        }
        
        .sidebar-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
        }
        
        .sidebar-logo {
            width: 50px;
            height: 50px;
            margin-right: 12px;
        }
        
        .sidebar-title {
            color: white;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
        }
        
        .sidebar-title small {
            display: block;
            font-size: 12px;
            font-weight: 400;
            opacity: 0.8;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 8px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .sidebar-menu a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            padding-left: 20px;
        }
        
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.25);
            color: white;
            font-weight: 600;
        }
        
        .sidebar-menu i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }

        /* Submenu Styles */
        .submenu {
            list-style: none;
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            overflow: hidden;
            margin-top: 8px;
        }

        .submenu li {
            margin: 0;
        }

        .submenu a {
            display: flex;
            align-items: center;
            padding: 10px 16px 10px 48px !important;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .submenu a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            padding-left: 52px !important;
        }

        .submenu a.active {
            background-color: rgba(255, 255, 255, 0.25);
            color: white;
            font-weight: 600;
        }
        
        /* Topbar Styles */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 999;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: none;
        }
        
        .topbar.with-sidebar {
            padding-left: 280px;
        }
        
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .toggle-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 24px;
            color: #002C76;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            z-index: 1001;
            position: relative;
        }
        
        .toggle-btn:hover {
            background-color: #f0f0f0;
            color: #003d99;
        }
        
        .toggle-btn:active {
            transform: scale(0.95);
        }
        
        .topbar-title {
            font-size: 18px;
            font-weight: 600;
            color: #002C76;
            margin: 0;
        }
        
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .pagasa-clock {
            font-size: 12px;
            font-weight: 600;
            color: #002C76;
            white-space: nowrap;
            min-width: 250px;
            text-align: right;
        }
        
        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-direction: row-reverse;
        }
        
        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #002C76 0%, #003d99 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .profile-icon:hover {
            border-color: #002C76;
            box-shadow: 0 4px 12px rgba(0, 44, 118, 0.2);
        }

        /* Notification Bell */
        .notification-bell {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: transparent;
            color: #002C76;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.3s ease;
            border: none;
            padding: 0;
        }

        .notification-bell:hover {
            color: #003d99;
            transform: scale(1.1);
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc2626;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            border: 2px solid white;
        }

        .notification-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .notification-menu {
            position: absolute;
            top: 48px;
            right: 0;
            width: min(380px, calc(100vw - 24px));
            max-height: 420px;
            overflow-y: auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.18);
            display: none;
            z-index: 1100;
        }

        .notification-menu.show {
            display: block;
            animation: slideDown 0.2s ease;
        }

        .notification-menu-header {
            padding: 10px 14px;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .notification-menu-title {
            font-size: 13px;
            font-weight: 700;
            color: #002C76;
        }

        .notification-clear-btn {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1;
            cursor: pointer;
        }

        .notification-clear-btn:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .notification-menu-empty {
            padding: 14px;
            font-size: 13px;
            color: #6b7280;
        }

        .notification-menu-item {
            display: block;
            text-decoration: none;
            color: #1f2937;
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.16s ease, color 0.16s ease;
        }

        .notification-menu-item:visited {
            color: #1f2937;
        }

        .notification-menu-item:hover {
            background: #e5edf9;
            color: #0f172a;
        }

        .notification-menu-item.unread {
            background: #dbeafe;
            color: #0f172a;
        }

        .notification-menu-item.unread:visited {
            color: #0f172a;
        }

        .notification-menu-item.unread:hover {
            background: #bfdbfe;
            color: #0f172a;
        }

        .notification-menu-item:last-child {
            border-bottom: 0;
        }

        .notification-menu-message {
            font-size: 12px;
            line-height: 1.4;
            color: #1f2937;
            margin-bottom: 3px;
        }

        .notification-menu-message-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .notification-unread-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #dc2626;
            margin-top: 5px;
            flex-shrink: 0;
        }

        .notification-menu-item.unread .notification-menu-message {
            color: #0b1f4a;
            font-weight: 600;
        }

        .notification-menu-time {
            font-size: 11px;
            color: #64748b;
        }

        .notification-menu-item:hover .notification-menu-time,
        .notification-menu-item.unread:hover .notification-menu-time {
            color: #334155;
        }

        .profile-menu {
            position: absolute;
            top: 50px;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            display: none;
            z-index: 1001;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }
        
        .profile-menu.show {
            display: block;
            animation: slideDown 0.2s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .profile-menu-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        
        .profile-menu-name {
            font-weight: 600;
            color: #002C76;
            font-size: 14px;
        }
        
        .profile-menu-email {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }
        
        .profile-menu-item {
            padding: 12px 20px;
            color: #374151;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            border: none;
            background: none;
            cursor: pointer;
            width: 100%;
            text-align: left;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        .profile-menu-item:hover {
            background-color: #f3f4f6;
            color: #002C76;
        }
        
        .profile-menu-item.logout {
            color: #dc2626;
            border-top: 1px solid #e5e7eb;
        }
        
        .profile-menu-item.logout:hover {
            background-color: #fef2f2;
        }
        
        /* Main Content */
        .main-content {
            margin-top: 70px;
            margin-left: 250px;
            padding: 30px;
            min-height: calc(100vh - 70px);
            transition: none;
        }
        
        .main-content.with-sidebar {
            margin-left: 250px;
        }
        
        .main-content:not(.with-sidebar) {
            margin-left: 0;
        }
        
        .content-header {
            margin-bottom: 30px;
        }
        
        .content-header h1 {
            color: #002C76;
            font-size: 28px;
            margin-bottom: 8px;
        }
        
        .content-header p {
            color: #6b7280;
            font-size: 14px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 220px;
            }
            
            .sidebar.collapsed {
                transform: translateX(-100%);
            }
            
            .topbar {
                padding: 0 15px;
            }
            
            .topbar.with-sidebar {
                padding-left: 15px;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px 15px;
            }
            
            .main-content.with-sidebar {
                margin-left: 220px;
            }
            
            .topbar-title {
                font-size: 16px;
            }
            
            .sidebar-title {
                font-size: 14px;
            }
            
            .content-header h1 {
                font-size: 24px;
            }

            .content-header {
                flex-wrap: wrap;
                gap: 12px;
            }

            .main-content div[style*="display: flex"][style*="justify-content: space-between"] {
                flex-wrap: wrap;
                gap: 12px;
            }

            .main-content div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }

            .main-content div[style*="grid-template-columns: repeat(3"] {
                grid-template-columns: 1fr !important;
            }

            .main-content div[style*="grid-template-columns: repeat(2"] {
                grid-template-columns: 1fr !important;
            }

            .main-content div[style*="grid-template-columns: repeat(auto-fit"] {
                grid-template-columns: 1fr !important;
            }

            .main-content div[style*="grid-template-columns: minmax"] {
                grid-template-columns: 1fr !important;
            }
        }
        
        @media (max-width: 480px) {
            .sidebar {
                width: 80%;
                z-index: 1100;
                max-width: 320px;
            }
            
            .sidebar.collapsed {
                transform: translateX(-100%);
            }
            
            .topbar {
                padding: 0 12px;
                height: 60px;
            }
            
            .topbar.with-sidebar {
                padding-left: 12px;
            }
            
            .topbar-left {
                gap: 10px;
            }
            
            .toggle-btn {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }
            
            .topbar-title {
                display: none;
            }
            
            .topbar-right {
                gap: 15px;
            }

            .pagasa-clock {
                display: none;
            }
            
            .main-content {
                margin-top: 60px;
                margin-left: 0;
                padding: 15px 12px;
                min-height: calc(100vh - 60px);
            }
            
            .main-content.with-sidebar {
                margin-left: 0;
            }
            
            .content-header {
                margin-bottom: 20px;
            }
            
            .content-header h1 {
                font-size: 20px;
                margin-bottom: 6px;
            }
            
            .content-header p {
                font-size: 13px;
            }
            
            .sidebar-header {
                margin-bottom: 20px;
                padding-bottom: 15px;
            }
            
            .sidebar-logo {
                width: 40px;
                height: 40px;
                margin-right: 8px;
            }
            
            .sidebar-title {
                font-size: 12px;
            }
            
            .sidebar-title small {
                font-size: 10px;
            }
            
            .sidebar-menu a {
                padding: 10px 12px;
                font-size: 13px;
            }
            
            .sidebar-menu i {
                width: 18px;
                margin-right: 10px;
            }
            
            .profile-icon {
                width: 36px;
                height: 36px;
                font-size: 18px;
            }
            
            .profile-menu {
                right: -5px;
                min-width: 170px;
                font-size: 13px;
            }

            .notification-menu {
                right: -8px;
                width: min(320px, calc(100vw - 24px));
            }
            
            .profile-menu-item {
                padding: 10px 16px;
                font-size: 13px;
            }
            
            .profile-menu-header {
                padding: 12px 16px;
            }
            
            .profile-menu-name {
                font-size: 13px;
            }
            
            .profile-menu-email {
                font-size: 11px;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('DILG-Logo.png') }}" alt="DILG Logo" class="sidebar-logo">
            <div class="sidebar-title">
                DILG-CAR
                <small>PDMU OPERATIONS MANAGEMENT SYSTEM (PDMUOMS)</small>
            </div>
            <!-- Close Button for Mobile -->
            <button id="closeSidebarBtn" style="display: none; position: absolute; right: 15px; top: 20px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; width: 40px; height: 40px; padding: 0; border-radius: 6px; transition: all 0.3s ease;" title="Close Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('dashboard') }}" class="@if(Route::currentRouteName() == 'dashboard') active @endif">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#" class="@if(Route::currentRouteName() == 'projects') active @endif" onclick="toggleSubmenu(event, 'projectsMenu')">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projects</span>
                    <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 12px;"></i>
                </a>
                <ul id="projectsMenu" class="submenu" style="display: none;">
                    <li>
                        <a href="{{ route('projects.locally-funded') }}" class="@if(Route::currentRouteName() == 'projects.locally-funded') active @endif">
                            <i class="fas fa-hand-holding-usd"></i>
                            <span>Locally Funded Projects</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('projects.rlip-lime') }}" class="@if(Route::currentRouteName() == 'projects.rlip-lime') active @endif">
                            <i class="fas fa-leaf"></i>
                            <span>RLIP/LIME-20% Development Fund</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/project-at-risk') }}" class="@if(Route::currentRouteName() == 'projects.at-risk') active @endif">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Project At Risk</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#" class="@if(Route::currentRouteName() == 'reports') active @endif" onclick="toggleSubmenu(event, 'reportsMenu')">
                    <i class="fas fa-file-alt"></i>
                    <span>Reportorial Requirements</span>
                    <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 12px;"></i>
                </a>
                <ul id="reportsMenu" class="submenu" style="display: none;">
                    <li>
                        <a href="{{ route('fund-utilization.index') }}" class="@if(Route::currentRouteName() == 'fund-utilization.index') active @endif">
                            <i class="fas fa-coins"></i>
                            <span>Fund Utilization Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('local-project-monitoring-committee.index') }}" class="@if(Route::currentRouteName() == 'local-project-monitoring-committee.index') active @endif">
                            <i class="fas fa-users-cog"></i>
                            <span>Local Project Monitoring Committee</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('road-maintenance-status.index') }}" class="@if(request()->routeIs('road-maintenance-status.*')) active @endif">
                            <i class="fas fa-road"></i>
                            <span>Road Maintenance Status Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('rbis-annual-certification.index') }}" class="@if(request()->routeIs('rbis-annual-certification.*')) active @endif">
                            <i class="fas fa-bridge"></i>
                            <span>RBIS Annual Certification</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('pre-implementation-documents.sbdp') }}" class="@if(Route::currentRouteName() == 'pre-implementation-documents.sbdp') active @endif">
                    <i class="fas fa-folder-open"></i>
                    <span>Pre-Implementation Documents(SBDP Projects)</span>
                </a>
            </li>
            @php
                $isRegionalDilg = strtoupper(trim((string) (Auth::user()->agency ?? ''))) === 'DILG'
                    && strtolower(trim((string) (Auth::user()->province ?? ''))) === 'regional office';
            @endphp
            @if($isRegionalDilg)
                <li>
                    <a href="#" class="@if(Route::currentRouteName() == 'system-management.index' || Route::currentRouteName() == 'system-management.upload-subaybayan') active @endif" onclick="toggleSubmenu(event, 'systemManagementMenu')">
                        <i class="fas fa-cogs"></i>
                        <span>System Management</span>
                        <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 12px;"></i>
                    </a>
                    <ul id="systemManagementMenu" class="submenu" style="display: none;">
                        <li>
                            <a href="{{ route('system-management.upload-subaybayan') }}" class="@if(Route::currentRouteName() == 'system-management.upload-subaybayan') active @endif">
                                <i class="fas fa-upload"></i>
                                <span>Upload SubayBAYAN Data</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            @if(Auth::user()->role === 'superadmin')
            <li>
                <a href="{{ route('users.index') }}" class="@if(Route::currentRouteName() == 'users.index') active @endif">
                    <i class="fas fa-user-shield"></i>
                    <span>User Management</span>
                </a>
            </li>
            @endif
        </ul>
    </aside>
    
    <!-- Top Navigation Bar -->
    <div class="topbar" id="topbar">
        <div class="topbar-left">
            <button class="toggle-btn" id="toggleBtn" title="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="topbar-title" id="pageTitle">@yield('page-title', 'Dashboard')</h1>
        </div>
        
        <div class="topbar-right">
            <div class="profile-dropdown">
                <div class="profile-icon" id="profileIcon" title="Profile">
                    <i class="fas fa-user"></i>
                </div>
                @php
                    $unreadNotificationQuery = \Illuminate\Support\Facades\DB::table('tbnotifications')
                        ->where('user_id', Auth::id())
                        ->whereNull('read_at');
                    $unreadNotifications = (clone $unreadNotificationQuery)->count();
                    $recentNotifications = \Illuminate\Support\Facades\DB::table('tbnotifications')
                        ->where('user_id', Auth::id())
                        ->orderByDesc('created_at')
                        ->limit(12)
                        ->get(['id', 'message', 'created_at', 'read_at']);
                @endphp
                <div class="notification-wrap">
                    <button
                        class="notification-bell"
                        id="notificationBell"
                        title="Notifications"
                        data-confirm-skip="true"
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-controls="notificationMenu"
                    >
                        <i class="fas fa-bell"></i>
                        @if($unreadNotifications > 0)
                            <span class="notification-badge">{{ $unreadNotifications }}</span>
                        @endif
                    </button>
                    <div class="notification-menu" id="notificationMenu">
                        <div class="notification-menu-header">
                            <span class="notification-menu-title">Notifications</span>
                            @if($recentNotifications->isNotEmpty())
                                <form method="POST" action="{{ route('notifications.clear') }}">
                                    @csrf
                                    <button type="submit" class="notification-clear-btn">Clear Read</button>
                                </form>
                            @endif
                        </div>
                        @if($recentNotifications->isEmpty())
                            <div class="notification-menu-empty">No notifications yet.</div>
                        @else
                            @foreach($recentNotifications as $notificationItem)
                                <a
                                    href="{{ route('notifications.read', ['id' => $notificationItem->id]) }}"
                                    class="notification-menu-item {{ is_null($notificationItem->read_at) ? 'unread' : '' }}"
                                    data-confirm-skip="true"
                                >
                                    <div class="notification-menu-message-row">
                                        @if(is_null($notificationItem->read_at))
                                            <span class="notification-unread-dot" aria-label="Unread notification"></span>
                                        @endif
                                        <div class="notification-menu-message">{{ $notificationItem->message }}</div>
                                    </div>
                                    <div class="notification-menu-time">
                                        {{ \Illuminate\Support\Carbon::parse($notificationItem->created_at)->format('M d, Y h:i A') }}
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="profile-menu" id="profileMenu">
                    <div class="profile-menu-header">
                        <div class="profile-menu-name">{{ Auth::user()->fname ?? 'User' }} {{ Auth::user()->lname ?? '' }}</div>
                        <div class="profile-menu-email">{{ Auth::user()->emailaddress ?? 'user@example.com' }}</div>
                    </div>
                    <a href="{{ route('profile.show') }}" class="profile-menu-item">
                        <i class="fas fa-user-circle"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="{{ route('password.show') }}" class="profile-menu-item">
                        <i class="fas fa-lock"></i>
                        <span>Change Password</span>
                    </a>
                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <button class="profile-menu-item logout" onclick="document.getElementById('logoutForm').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <main class="main-content" id="mainContent">
        @yield('content')
    </main>

    @include('partials.global-error-confirm')
    
    <script>
        // Sidebar Toggle
        const toggleBtn = document.getElementById('toggleBtn');
        const closeSidebarBtn = document.getElementById('closeSidebarBtn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const topbar = document.getElementById('topbar');
        const body = document.body;
        
        // Check if sidebar should start collapsed (from localStorage)
        let sidebarExpanded = localStorage.getItem('sidebarExpanded') !== 'false';
        
        // Check if mobile
        function isMobile() {
            return window.innerWidth <= 480;
        }
        
        // Initialize sidebar state
        function updateSidebarState() {
            if (sidebarExpanded) {
                sidebar.classList.remove('collapsed');
                mainContent.classList.add('with-sidebar');
                topbar.classList.add('with-sidebar');
                
                // Show close button on mobile
                if (isMobile() && closeSidebarBtn) {
                    closeSidebarBtn.style.display = 'block';
                }
                
                if (isMobile()) {
                    body.classList.add('sidebar-open');
                }
            } else {
                sidebar.classList.add('collapsed');
                mainContent.classList.remove('with-sidebar');
                topbar.classList.remove('with-sidebar');
                
                // Hide close button when sidebar is collapsed
                if (closeSidebarBtn) {
                    closeSidebarBtn.style.display = 'none';
                }
                body.classList.remove('sidebar-open');
            }
            localStorage.setItem('sidebarExpanded', sidebarExpanded);
        }
        
        // Initialize on page load
        updateSidebarState();
        
        // Toggle button click handler
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sidebarExpanded = !sidebarExpanded;
            updateSidebarState();
        });

        // Close sidebar button click handler
        if (closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                sidebarExpanded = false;
                updateSidebarState();
            });

            // Also on hover for better UX
            closeSidebarBtn.addEventListener('mouseenter', function() {
                this.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';
            });

            closeSidebarBtn.addEventListener('mouseleave', function() {
                this.style.backgroundColor = 'transparent';
            });
        }

        // Close sidebar when clicking on content area on mobile
        if (isMobile()) {
            mainContent.addEventListener('click', function() {
                if (sidebarExpanded) {
                    sidebarExpanded = false;
                    updateSidebarState();
                }
            });

            // Close sidebar on window resize if opening desktop size
            window.addEventListener('resize', function() {
                if (window.innerWidth > 480) {
                    if (!sidebarExpanded) {
                        sidebarExpanded = true;
                        updateSidebarState();
                    }
                    if (closeSidebarBtn) {
                        closeSidebarBtn.style.display = 'none';
                    }
                } else {
                    // On mobile, update close button visibility
                    if (sidebarExpanded && closeSidebarBtn) {
                        closeSidebarBtn.style.display = 'block';
                    }
                }
            });
        }
        
        // Profile Dropdown Toggle
        const profileIcon = document.getElementById('profileIcon');
        const profileMenu = document.getElementById('profileMenu');
        const notificationBell = document.getElementById('notificationBell');
        const notificationMenu = document.getElementById('notificationMenu');
        
        // Toggle submenu function
        function toggleSubmenu(event, submenuId) {
            event.preventDefault();
            const submenu = document.getElementById(submenuId);
            submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
        }

        if (profileIcon && profileMenu) {
            profileIcon.addEventListener('click', function(e) {
                e.stopPropagation();
                profileMenu.classList.toggle('show');
                if (notificationMenu) {
                    notificationMenu.classList.remove('show');
                }
                if (notificationBell) {
                    notificationBell.setAttribute('aria-expanded', 'false');
                }
            });
        }

        if (notificationBell && notificationMenu) {
            notificationBell.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                notificationMenu.classList.toggle('show');
                this.setAttribute('aria-expanded', notificationMenu.classList.contains('show') ? 'true' : 'false');
                if (profileMenu) {
                    profileMenu.classList.remove('show');
                }
            });
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(e) {
            if (profileMenu && profileIcon && !profileMenu.contains(e.target) && !profileIcon.contains(e.target)) {
                profileMenu.classList.remove('show');
            }
            if (notificationMenu && notificationBell && !notificationMenu.contains(e.target) && !notificationBell.contains(e.target)) {
                notificationMenu.classList.remove('show');
                notificationBell.setAttribute('aria-expanded', 'false');
            }
        });

        (function initializeGlobalPagasaClock() {
            const endpoint = @json(route('pagasa-time.current'));
            let serverBaseMs = null;
            let syncedAtMs = null;

            function formatManila(date) {
                return date.toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true,
                    timeZone: 'Asia/Manila'
                });
            }

            function updateGlobalClock(text, color) {
                document.querySelectorAll('[data-pagasa-global-clock]').forEach((el) => {
                    el.textContent = text;
                    el.style.color = color;
                });
            }

            function updateTaggedTimeBlocks(isoTime) {
                document.querySelectorAll('[data-pagasa-time]').forEach((el) => {
                    // Keep PAGASA time running without rendering the "Current Time" text.
                    el.dataset.pagasaIso = isoTime;
                    el.style.display = 'none';
                    el.textContent = '';
                });
            }

            function tick() {
                if (serverBaseMs === null || syncedAtMs === null) {
                    return;
                }

                const now = new Date(serverBaseMs + (Date.now() - syncedAtMs));
                const formatted = formatManila(now);

                updateGlobalClock(`PAGASA Time: ${formatted}`, '#002C76');
                updateTaggedTimeBlocks(now.toISOString());
            }

            async function syncServerTime() {
                try {
                    const response = await fetch(endpoint, {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();
                    const parsedMs = Date.parse(data?.ntp_time ?? '');

                    if (!data?.success || Number.isNaN(parsedMs)) {
                        throw new Error('Invalid time payload');
                    }

                    serverBaseMs = parsedMs;
                    syncedAtMs = Date.now();
                    tick();
                } catch (error) {
                    updateGlobalClock('PAGASA Time unavailable', '#dc2626');
                    updateTaggedTimeBlocks('');
                }
            }

            syncServerTime();
            setInterval(tick, 1000);
            setInterval(syncServerTime, 60000);

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    syncServerTime();
                }
            });
        })();
    </script>
    
    @yield('scripts')
</body>
</html>
