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
            transition: transform 280ms cubic-bezier(0.2, 0.8, 0.2, 1), width 280ms cubic-bezier(0.2, 0.8, 0.2, 1), padding 280ms cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 280ms cubic-bezier(0.2, 0.8, 0.2, 1);
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

        .sidebar.icon-collapsed {
            width: 78px;
            padding: 20px 10px;
            transform: translateX(0);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
            pointer-events: auto;
        }
        
        .sidebar-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            transition: margin-bottom 220ms ease, padding-bottom 220ms ease;
        }

        .sidebar-brand-link {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-width: 0;
            width: 100%;
            padding-right: 0;
            color: inherit;
            text-decoration: none;
            transition: padding-right 220ms ease;
        }
        
        .sidebar-logo {
            width: 100%;
            max-width: 195px;
            height: auto;
            margin-right: 0;
            transition: width 220ms ease, max-width 220ms ease, height 220ms ease, margin-right 220ms ease;
        }
        
        .sidebar-title {
            color: white;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
            max-width: 220px;
            overflow: hidden;
            transition: opacity 200ms ease, max-width 220ms ease, transform 220ms ease;
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
            transition: background-color 0.22s ease, color 0.22s ease, padding-left 0.22s ease;
            font-size: 14px;
        }
        
        .sidebar-menu a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            padding-left: 20px;
        }
        
        .sidebar-menu a.active {
            background-color: #ffffff;
            color: #002C76;
            font-weight: 700;
            box-shadow: none;
        }
        
        .sidebar-menu i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
            transition: margin-right 220ms ease, transform 220ms ease;
        }

        .sidebar-menu a span {
            display: inline-block;
            overflow: hidden;
            max-width: 1000px;
            opacity: 1;
            transform: translateX(0);
            transition: opacity 180ms ease, max-width 220ms ease, transform 220ms ease;
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
            background-color: #ffffff;
            color: #002C76;
            font-weight: 700;
            box-shadow: none;
        }

        .submenu .submenu {
            margin-top: 0;
            border-radius: 0;
            background-color: rgba(0, 0, 0, 0.15);
        }

        .submenu .submenu a {
            padding-left: 64px !important;
            font-size: 12px;
        }

        .submenu .submenu a:hover {
            padding-left: 68px !important;
        }

        .submenu-empty {
            display: block;
            padding: 10px 16px 10px 64px;
            color: rgba(255, 255, 255, 0.65);
            font-size: 12px;
            font-style: italic;
        }

        .sidebar-menu a.submenu-toggle {
            cursor: pointer;
        }

        .submenu-chevron {
            transition: transform 0.2s ease;
        }

        .sidebar-menu a.submenu-toggle[aria-expanded="true"] .submenu-chevron {
            transform: rotate(180deg);
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
            transition: padding-left 280ms cubic-bezier(0.2, 0.8, 0.2, 1);
        }
        
        .topbar.with-sidebar {
            padding-left: 280px;
        }

        .sidebar.icon-collapsed ~ .topbar {
            padding-left: 108px;
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
            color: inherit;
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.16s ease;
        }

        .notification-menu-item:hover {
            background: #f8fafc;
        }

        .notification-menu-item.unread {
            background: #eff6ff;
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
            font-weight: 600;
        }

        .notification-menu-time {
            font-size: 11px;
            color: #64748b;
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
            transition: margin-left 280ms cubic-bezier(0.2, 0.8, 0.2, 1);
        }
        
        .main-content.with-sidebar {
            margin-left: 250px;
        }
        
        .main-content:not(.with-sidebar) {
            margin-left: 0;
        }

        .sidebar.icon-collapsed ~ .main-content {
            margin-left: 78px;
        }

        .sidebar.icon-collapsed .sidebar-header {
            justify-content: center;
            margin-bottom: 18px;
            padding-bottom: 16px;
        }

        .sidebar.icon-collapsed .sidebar-brand-link {
            justify-content: center;
            padding-right: 0;
        }

        .sidebar.icon-collapsed .sidebar-logo {
            width: 56px;
            max-width: 56px;
            height: auto;
            margin-right: 0;
        }

        .sidebar.icon-collapsed .submenu,
        .sidebar.icon-collapsed .submenu-empty,
        .sidebar.icon-collapsed .submenu-chevron {
            display: none !important;
        }

        .sidebar.icon-collapsed .sidebar-menu a {
            justify-content: center;
            padding: 11px 0 !important;
        }

        .sidebar.icon-collapsed .sidebar-menu a:hover {
            padding-left: 0 !important;
        }

        .sidebar.icon-collapsed .sidebar-menu a span {
            opacity: 0;
            max-width: 0;
            transform: translateX(-6px);
            white-space: nowrap;
        }

        .sidebar.icon-collapsed .sidebar-menu i {
            margin-right: 0;
            transform: translateX(0);
        }

        .sidebar.icon-collapsed .sidebar-title {
            opacity: 0;
            max-width: 0;
            transform: translateX(-8px);
            margin-right: 0;
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
                transition: transform 320ms cubic-bezier(0.22, 1, 0.36, 1), opacity 220ms ease, box-shadow 320ms ease;
                opacity: 1;
            }
            
            .sidebar.collapsed {
                transform: translateX(-100%);
                opacity: 0;
            }

            .sidebar.icon-collapsed {
                width: 220px;
                padding: 20px;
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

        @media (prefers-reduced-motion: reduce) {
            .sidebar,
            .topbar,
            .main-content,
            .sidebar-title,
            .sidebar-logo,
            .sidebar-menu i,
            .sidebar-menu a span {
                transition: none !important;
            }
        }
        
        @media (max-width: 480px) {
            .sidebar {
                width: 80%;
                z-index: 1100;
                max-width: 320px;
                transition: transform 320ms cubic-bezier(0.22, 1, 0.36, 1), opacity 220ms ease, box-shadow 320ms ease;
            }
            
            .sidebar.collapsed {
                transform: translateX(-100%);
                opacity: 0;
            }

            .sidebar.icon-collapsed {
                width: 80%;
                max-width: 320px;
                padding: 20px;
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
                width: 100%;
                max-width: 165px;
                height: auto;
                margin-right: 0;
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

        .system-dialog-modal {
            position: fixed;
            inset: 0;
            z-index: 3000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .system-dialog-modal.is-open {
            display: flex;
        }

        .system-dialog-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
        }

        .system-dialog-card {
            position: relative;
            z-index: 1;
            width: min(460px, 100%);
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.25);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .system-dialog-header {
            padding: 16px 18px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .system-dialog-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .system-dialog-body {
            padding: 14px 18px;
            font-size: 14px;
            line-height: 1.6;
            color: #334155;
        }

        .system-dialog-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 12px 18px 18px;
        }

        .system-dialog-btn {
            border: none;
            border-radius: 8px;
            padding: 9px 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .system-dialog-btn.cancel {
            background: #e5e7eb;
            color: #1f2937;
        }

        .system-dialog-btn.confirm {
            background: #002c76;
            color: #ffffff;
        }

        .system-dialog-btn.error-ok {
            background: #dc2626;
            color: #ffffff;
        }

        body.system-dialog-open {
            overflow: hidden;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="sidebar-brand-link" aria-label="Go to dashboard">
                <img src="{{ asset('PDMUOMS.png') }}" alt="PDMUOMS" class="sidebar-logo">
            </a>
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('dashboard') }}" class="@if(Route::currentRouteName() == 'dashboard') active @endif">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                @php
                    $projectsMenuActive = request()->routeIs('projects.*') || request()->routeIs('projects.at-risk');
                @endphp
                <a href="#" class="@if($projectsMenuActive) active @endif submenu-toggle" onclick="toggleSubmenu(event, 'projectsMenu')">
                    <i class="fas fa-project-diagram"></i>
                    <span>Project Monitoring</span>
                    <i class="fas fa-chevron-down submenu-chevron" style="margin-left: auto; font-size: 12px;"></i>
                </a>
                <ul id="projectsMenu" class="submenu" style="display: {{ $projectsMenuActive ? 'block' : 'none' }};">
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
                @php
                    $reportsAnnualActive = request()->routeIs('rbis-annual-certification.*');
                    $reportsQuarterlyActive = request()->routeIs('fund-utilization.*')
                        || request()->routeIs('local-project-monitoring-committee.*')
                        || request()->routeIs('road-maintenance-status.*');
                    $reportsMonthlyActive = request()->routeIs('reports.monthly.pd-no-pbbm-2025-1572-1573*');
                    $reportsMenuActive = Route::currentRouteName() == 'reports'
                        || $reportsAnnualActive
                        || $reportsQuarterlyActive
                        || $reportsMonthlyActive;
                @endphp
                <a href="#" class="@if($reportsMenuActive) active @endif submenu-toggle" onclick="toggleSubmenu(event, 'reportsMenu')">
                    <i class="fas fa-file-alt"></i>
                    <span>LGU Reportorial Requirements</span>
                    <i class="fas fa-chevron-down submenu-chevron" style="margin-left: auto; font-size: 12px;"></i>
                </a>
                <ul id="reportsMenu" class="submenu" style="display: {{ $reportsMenuActive ? 'block' : 'none' }};">
                    <li>
                        <a href="#" class="@if($reportsAnnualActive) active @endif submenu-toggle" onclick="toggleSubmenu(event, 'reportsAnnualMenu')">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Annual</span>
                            <i class="fas fa-chevron-down submenu-chevron" style="margin-left: auto; font-size: 11px;"></i>
                        </a>
                        <ul id="reportsAnnualMenu" class="submenu" style="display: {{ $reportsAnnualActive ? 'block' : 'none' }};">
                            <li>
                                <a href="{{ route('rbis-annual-certification.index') }}" class="@if(request()->routeIs('rbis-annual-certification.*')) active @endif">
                                    <i class="fas fa-bridge"></i>
                                    <span>RBIS Annual Certification</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="@if($reportsQuarterlyActive) active @endif submenu-toggle" onclick="toggleSubmenu(event, 'reportsQuarterlyMenu')">
                            <i class="fas fa-calendar-check"></i>
                            <span>Quarterly</span>
                            <i class="fas fa-chevron-down submenu-chevron" style="margin-left: auto; font-size: 11px;"></i>
                        </a>
                        <ul id="reportsQuarterlyMenu" class="submenu" style="display: {{ $reportsQuarterlyActive ? 'block' : 'none' }};">
                            <li>
                                <a href="{{ route('fund-utilization.index') }}" class="@if(request()->routeIs('fund-utilization.*')) active @endif">
                                    <i class="fas fa-coins"></i>
                                    <span>Fund Utilization Report</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('local-project-monitoring-committee.index') }}" class="@if(request()->routeIs('local-project-monitoring-committee.*')) active @endif">
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
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="@if($reportsMonthlyActive) active @endif submenu-toggle" onclick="toggleSubmenu(event, 'reportsMonthlyMenu')">
                            <i class="fas fa-calendar-day"></i>
                            <span>Monthly</span>
                            <i class="fas fa-chevron-down submenu-chevron" style="margin-left: auto; font-size: 11px;"></i>
                        </a>
                        <ul id="reportsMonthlyMenu" class="submenu" style="display: {{ $reportsMonthlyActive ? 'block' : 'none' }};">
                            <li>
                                <a href="{{ route('reports.monthly.pd-no-pbbm-2025-1572-1573') }}" class="@if(request()->routeIs('reports.monthly.pd-no-pbbm-2025-1572-1573*')) active @endif">
                                    <i class="fas fa-file-alt"></i>
                                    <span>Report on PD No. PBBM-2025-1572-1573</span>
                                </a>
                            </li>
                        </ul>
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
                    @php
                        $systemManagementActive = request()->routeIs('system-management.*');
                    @endphp
                    <a href="#" class="@if($systemManagementActive) active @endif submenu-toggle" onclick="toggleSubmenu(event, 'systemManagementMenu')">
                        <i class="fas fa-cogs"></i>
                        <span>System Management</span>
                        <i class="fas fa-chevron-down submenu-chevron" style="margin-left: auto; font-size: 12px;"></i>
                    </a>
                    <ul id="systemManagementMenu" class="submenu" style="display: {{ $systemManagementActive ? 'block' : 'none' }};">
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
            @php
                $topbarPageTitle = trim((string) $__env->yieldContent('page-title', 'Dashboard'));
            @endphp
            @if ($topbarPageTitle !== 'Locally Funded Projects')
                <h1 class="topbar-title" id="pageTitle">{{ $topbarPageTitle }}</h1>
            @endif
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

    <div id="globalConfirmModal" class="system-dialog-modal" aria-hidden="true">
        <div class="system-dialog-backdrop" data-confirm-dismiss></div>
        <div class="system-dialog-card" role="dialog" aria-modal="true" aria-labelledby="globalConfirmModalTitle">
            <div class="system-dialog-header">
                <h3 id="globalConfirmModalTitle" class="system-dialog-title">Please Confirm</h3>
            </div>
            <div class="system-dialog-body" id="globalConfirmModalMessage"></div>
            <div class="system-dialog-actions">
                <button type="button" class="system-dialog-btn cancel" id="globalConfirmCancelBtn">Cancel</button>
                <button type="button" class="system-dialog-btn confirm" id="globalConfirmOkBtn">Confirm</button>
            </div>
        </div>
    </div>

    <div id="globalErrorModal" class="system-dialog-modal" aria-hidden="true">
        <div class="system-dialog-backdrop" data-error-dismiss></div>
        <div class="system-dialog-card" role="dialog" aria-modal="true" aria-labelledby="globalErrorModalTitle">
            <div class="system-dialog-header">
                <h3 id="globalErrorModalTitle" class="system-dialog-title">System Error</h3>
            </div>
            <div class="system-dialog-body" id="globalErrorModalMessage">An unexpected error occurred.</div>
            <div class="system-dialog-actions">
                <button type="button" class="system-dialog-btn error-ok" id="globalErrorOkBtn">OK</button>
            </div>
        </div>
    </div>
    
    <script>
        // Sidebar Toggle
        const toggleBtn = document.getElementById('toggleBtn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const topbar = document.getElementById('topbar');
        const body = document.body;
        
        // Check if sidebar should start collapsed (from localStorage)
        let sidebarExpanded = localStorage.getItem('sidebarExpanded') !== 'false';
        
        // Check if mobile
        function isMobile() {
            return window.innerWidth <= 768;
        }
        
        // Initialize sidebar state
        function updateSidebarState() {
            const mobileView = isMobile();
            sidebar.classList.remove('collapsed', 'icon-collapsed');

            if (sidebarExpanded) {
                mainContent.classList.add('with-sidebar');
                topbar.classList.add('with-sidebar');
                
                if (mobileView) {
                    body.classList.add('sidebar-open');
                } else {
                    body.classList.remove('sidebar-open');
                }
            } else {
                mainContent.classList.remove('with-sidebar');
                topbar.classList.remove('with-sidebar');

                if (mobileView) {
                    sidebar.classList.add('collapsed');
                } else {
                    sidebar.classList.add('icon-collapsed');
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

        // Close sidebar when clicking on content area on mobile
        mainContent.addEventListener('click', function() {
            if (isMobile() && sidebarExpanded) {
                sidebarExpanded = false;
                updateSidebarState();
            }
        });

        // Recompute sidebar mode when viewport changes
        window.addEventListener('resize', function() {
            updateSidebarState();
        });
        
        // Profile Dropdown Toggle
        const profileIcon = document.getElementById('profileIcon');
        const profileMenu = document.getElementById('profileMenu');
        const notificationBell = document.getElementById('notificationBell');
        const notificationMenu = document.getElementById('notificationMenu');
        
        const SIDEBAR_SUBMENU_STORAGE_KEY = 'pdmuoms.sidebar.openSubmenus';

        function findDirectSubmenu(listItem) {
            if (!listItem || !listItem.children) {
                return null;
            }

            return Array.from(listItem.children).find((child) => child.classList && child.classList.contains('submenu')) || null;
        }

        function getSubmenuToggle(submenuId) {
            if (!submenuId) {
                return null;
            }

            return document.querySelector(`.sidebar-menu a.submenu-toggle[data-submenu-id="${submenuId}"]`);
        }

        function isSubmenuOpen(submenu) {
            return !!submenu && window.getComputedStyle(submenu).display !== 'none';
        }

        function setSubmenuState(submenu, shouldOpen) {
            if (!submenu) {
                return;
            }

            submenu.style.display = shouldOpen ? 'block' : 'none';
            submenu.setAttribute('data-open', shouldOpen ? 'true' : 'false');

            const toggle = getSubmenuToggle(submenu.id);
            if (toggle) {
                toggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
            }
        }

        function closeSubmenuRecursively(submenu) {
            if (!submenu) {
                return;
            }

            const nestedSubmenus = submenu.querySelectorAll('.submenu');
            nestedSubmenus.forEach((nestedSubmenu) => {
                setSubmenuState(nestedSubmenu, false);
            });

            setSubmenuState(submenu, false);
        }

        function closeSiblingSubmenus(submenu) {
            if (!submenu) {
                return;
            }

            const submenuListItem = submenu.closest('li');
            if (!submenuListItem || !submenuListItem.parentElement) {
                return;
            }

            const siblingListItems = Array.from(submenuListItem.parentElement.children || []);
            siblingListItems.forEach((siblingListItem) => {
                if (siblingListItem === submenuListItem) {
                    return;
                }

                const siblingSubmenu = findDirectSubmenu(siblingListItem);
                if (siblingSubmenu) {
                    closeSubmenuRecursively(siblingSubmenu);
                }
            });
        }

        function openAncestorSubmenus(submenu) {
            if (!submenu) {
                return;
            }

            let currentSubmenu = submenu;
            while (currentSubmenu) {
                const parentListItem = currentSubmenu.parentElement ? currentSubmenu.parentElement.closest('li') : null;
                if (!parentListItem || !parentListItem.parentElement) {
                    break;
                }

                const parentSubmenu = parentListItem.parentElement.classList.contains('submenu')
                    ? parentListItem.parentElement
                    : null;

                if (!parentSubmenu) {
                    break;
                }

                setSubmenuState(parentSubmenu, true);
                currentSubmenu = parentSubmenu;
            }
        }

        function readStoredOpenSubmenus() {
            try {
                const raw = localStorage.getItem(SIDEBAR_SUBMENU_STORAGE_KEY);
                if (!raw) {
                    return new Set();
                }

                const ids = JSON.parse(raw);
                if (!Array.isArray(ids)) {
                    return new Set();
                }

                return new Set(ids.filter((id) => typeof id === 'string' && id !== ''));
            } catch (error) {
                return new Set();
            }
        }

        function saveOpenSubmenus() {
            try {
                const openSubmenuIds = Array.from(document.querySelectorAll('.sidebar-menu .submenu[id]'))
                    .filter((submenu) => isSubmenuOpen(submenu))
                    .map((submenu) => submenu.id);
                localStorage.setItem(SIDEBAR_SUBMENU_STORAGE_KEY, JSON.stringify(openSubmenuIds));
            } catch (error) {
                // Ignore storage errors.
            }
        }

        function initializeSidebarSubmenus() {
            const submenuToggles = document.querySelectorAll('.sidebar-menu a.submenu-toggle[onclick*="toggleSubmenu"]');
            const storedOpenSubmenus = readStoredOpenSubmenus();
            const hasActiveMenuSelection = !!document.querySelector('.sidebar-menu a.active');

            submenuToggles.forEach((submenuToggle) => {
                const onclickExpression = submenuToggle.getAttribute('onclick') || '';
                const match = onclickExpression.match(/toggleSubmenu\(event,\s*'([^']+)'\)/);
                if (!match || !match[1]) {
                    return;
                }

                const submenuId = match[1];
                submenuToggle.dataset.submenuId = submenuId;
                submenuToggle.setAttribute('aria-controls', submenuId);
                submenuToggle.setAttribute('aria-expanded', 'false');

                if (submenuToggle.dataset.keyToggleAttached !== '1') {
                    submenuToggle.dataset.keyToggleAttached = '1';
                    submenuToggle.addEventListener('keydown', function (keyboardEvent) {
                        if (keyboardEvent.key === 'Enter' || keyboardEvent.key === ' ') {
                            toggleSubmenu(keyboardEvent, submenuId);
                        }
                    });
                }
            });

            const allSubmenus = document.querySelectorAll('.sidebar-menu .submenu[id]');
            allSubmenus.forEach((submenu) => {
                const hasInlineOpenState = submenu.style.display === 'block';
                const hasActiveDescendant = !!submenu.querySelector('a.active');
                const hasStoredOpenState = storedOpenSubmenus.has(submenu.id);
                const shouldOpen = hasInlineOpenState
                    || hasActiveDescendant
                    || (!hasActiveMenuSelection && hasStoredOpenState);

                setSubmenuState(submenu, shouldOpen);
                if (shouldOpen) {
                    openAncestorSubmenus(submenu);
                }
            });

            // Keep only the active path expanded, including top-level menus.
            if (hasActiveMenuSelection) {
                const activePathSubmenus = Array.from(allSubmenus).filter((submenu) => submenu.querySelector('a.active'));
                activePathSubmenus.forEach((submenu) => {
                    setSubmenuState(submenu, true);
                    openAncestorSubmenus(submenu);
                    closeSiblingSubmenus(submenu);
                });
            }

            saveOpenSubmenus();
        }

        // Toggle submenu function
        function toggleSubmenu(event, submenuId) {
            event.preventDefault();
            event.stopPropagation();

            if (!isMobile() && sidebar.classList.contains('icon-collapsed')) {
                sidebarExpanded = true;
                updateSidebarState();
            }

            const submenu = document.getElementById(submenuId);
            if (!submenu) {
                return;
            }

            const shouldOpen = !isSubmenuOpen(submenu);
            if (shouldOpen) {
                closeSiblingSubmenus(submenu);
                setSubmenuState(submenu, true);
                openAncestorSubmenus(submenu);
            } else {
                closeSubmenuRecursively(submenu);
            }

            saveOpenSubmenus();
        }

        initializeSidebarSubmenus();

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

        (function initializeSystemDialogs() {
            const confirmModal = document.getElementById('globalConfirmModal');
            const confirmMessage = document.getElementById('globalConfirmModalMessage');
            const confirmOkBtn = document.getElementById('globalConfirmOkBtn');
            const confirmCancelBtn = document.getElementById('globalConfirmCancelBtn');
            const confirmDismissTargets = document.querySelectorAll('[data-confirm-dismiss]');
            const errorModal = document.getElementById('globalErrorModal');
            const errorMessage = document.getElementById('globalErrorModalMessage');
            const errorOkBtn = document.getElementById('globalErrorOkBtn');
            const errorDismissTargets = document.querySelectorAll('[data-error-dismiss]');
            const nativeConfirm = window.confirm.bind(window);
            let nativeConfirmBypassCount = 0;
            let confirmCallback = null;
            let confirmCancelCallback = null;

            function openModal(modal) {
                if (!modal) return;
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('system-dialog-open');
            }

            function closeModal(modal) {
                if (!modal) return;
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                if (!document.querySelector('.system-dialog-modal.is-open')) {
                    document.body.classList.remove('system-dialog-open');
                }
            }

            function closeConfirmModal(runCancelCallback) {
                const shouldRunCancel = runCancelCallback === true;
                const pendingCancel = confirmCancelCallback;
                confirmCallback = null;
                confirmCancelCallback = null;
                closeModal(confirmModal);
                if (shouldRunCancel && pendingCancel) {
                    pendingCancel();
                }
            }

            window.openConfirmationModal = function(message, onConfirm, onCancel) {
                if (!confirmModal || !confirmMessage) return;
                if (confirmModal.classList.contains('is-open')) {
                    return;
                }
                confirmCallback = typeof onConfirm === 'function' ? onConfirm : null;
                confirmCancelCallback = typeof onCancel === 'function' ? onCancel : null;
                confirmMessage.textContent = message || 'Please confirm this action.';
                openModal(confirmModal);
                if (confirmOkBtn) {
                    confirmOkBtn.focus();
                }
            };

            window.showSystemErrorModal = function(message) {
                if (!errorModal || !errorMessage) return;
                errorMessage.textContent = message || 'An unexpected system error occurred. Please try again.';
                openModal(errorModal);
                if (errorOkBtn) {
                    errorOkBtn.focus();
                }
            };

            window.withNativeConfirmBypass = function(callback) {
                nativeConfirmBypassCount += 1;
                try {
                    return callback();
                } finally {
                    setTimeout(function() {
                        nativeConfirmBypassCount = Math.max(nativeConfirmBypassCount - 1, 0);
                    }, 0);
                }
            };

            window.confirm = function(message) {
                if (nativeConfirmBypassCount > 0) {
                    nativeConfirmBypassCount -= 1;
                    return true;
                }
                return nativeConfirm(message);
            };

            if (confirmOkBtn) {
                confirmOkBtn.addEventListener('click', function() {
                    const pending = confirmCallback;
                    closeConfirmModal(false);
                    if (pending) pending();
                });
            }

            if (confirmCancelBtn) {
                confirmCancelBtn.addEventListener('click', function() {
                    closeConfirmModal(true);
                });
            }

            confirmDismissTargets.forEach((el) => {
                el.addEventListener('click', function() {
                    closeConfirmModal(true);
                });
            });

            if (errorOkBtn) {
                errorOkBtn.addEventListener('click', function() {
                    closeModal(errorModal);
                });
            }

            errorDismissTargets.forEach((el) => {
                el.addEventListener('click', function() {
                    closeModal(errorModal);
                });
            });

            document.addEventListener('keydown', function(event) {
                if (event.key !== 'Escape') return;
                if (confirmModal && confirmModal.classList.contains('is-open')) {
                    closeConfirmModal(true);
                    return;
                }
                if (errorModal && errorModal.classList.contains('is-open')) {
                    closeModal(errorModal);
                }
            });

            const initialError = @json(session('error'));
            if (initialError) {
                window.showSystemErrorModal(initialError);
            }

            window.addEventListener('error', function(event) {
                const message = (event && event.message) ? event.message : '';
                if (!message || message === 'Script error.') return;
                const source = event && typeof event.filename === 'string' ? event.filename : '';
                const sameOriginSource = !source || source.startsWith(window.location.origin) || source.startsWith('/');
                if (!sameOriginSource) return;
                window.showSystemErrorModal(message);
            });

            window.addEventListener('unhandledrejection', function(event) {
                const reason = event ? event.reason : null;
                const message = typeof reason === 'string' ? reason : (reason && reason.message ? reason.message : '');
                window.showSystemErrorModal(message || 'A background process failed. Please try again.');
            });
        })();

        // Confirmation for save/update/delete actions
        (function attachActionConfirms() {
            const defaultMessages = {
                save: 'Are you sure you want to save these changes?',
                delete: 'Are you sure you want to delete this item? This action cannot be undone.'
            };

            function getActionText(el) {
                const text = (el.textContent || el.value || '').trim().toLowerCase();
                return text;
            }

            function extractInlineConfirmMessage(code) {
                if (!code) return '';
                const match = code.match(/confirm\s*\(\s*(['"])(.*?)\1\s*\)/i);
                return match && match[2] ? match[2] : '';
            }

            function normalizeInlineConfirmHandlers() {
                document.querySelectorAll('form[onsubmit*="confirm("]').forEach((form) => {
                    const inlineCode = form.getAttribute('onsubmit') || '';
                    const message = extractInlineConfirmMessage(inlineCode);
                    if (message && !form.dataset.confirm) {
                        form.dataset.confirm = message;
                    }
                    form.removeAttribute('onsubmit');
                });
            }

            function needsAutoConfirm(el, form) {
                if (!el || el.disabled) return false;
                if (el.dataset && el.dataset.confirmSkip === 'true') return false;
                if (el.dataset && el.dataset.confirm) return true;
                if (form && form.dataset && form.dataset.confirm) return true;
                const text = getActionText(el);
                if (!text) return false;
                const isSave = text.includes('save');
                const isDelete = text.includes('delete');
                return isSave || isDelete;
            }

            function resolveMessage(el, form) {
                if (el.dataset && el.dataset.confirm) return el.dataset.confirm;
                if (form && form.dataset && form.dataset.confirm) return form.dataset.confirm;
                const text = getActionText(el);
                return text.includes('delete') ? defaultMessages.delete : defaultMessages.save;
            }

            normalizeInlineConfirmHandlers();

            document.addEventListener('click', function(e) {
                const target = e.target.closest('button, input[type="submit"], input[type="button"], a');
                if (!target) return;
                const form = target.closest('form');

                if (target.dataset && target.dataset.confirmed === 'true') {
                    delete target.dataset.confirmed;
                    return;
                }

                if (!needsAutoConfirm(target, form)) return;

                e.preventDefault();
                e.stopPropagation();
                const message = resolveMessage(target, form);
                window.openConfirmationModal(message, function() {
                    target.dataset.confirmed = 'true';
                    if (form && (target.type === 'submit' || target.getAttribute('type') === 'submit' || target.tagName === 'BUTTON')) {
                        window.withNativeConfirmBypass(function() {
                            if (typeof form.requestSubmit === 'function') {
                                form.requestSubmit(target);
                            } else {
                                form.submit();
                            }
                        });
                        return;
                    }

                    window.withNativeConfirmBypass(function() {
                        target.click();
                    });
                });
            }, true);

            document.addEventListener('submit', function(e) {
                const submitter = e.submitter;
                const form = e.target;

                if (form && form.dataset && form.dataset.confirmed === 'true') {
                    delete form.dataset.confirmed;
                    return;
                }

                if (!submitter) {
                    if (!form || !form.dataset || !form.dataset.confirm) return;
                    e.preventDefault();
                    e.stopPropagation();
                    window.openConfirmationModal(form.dataset.confirm, function() {
                        form.dataset.confirmed = 'true';
                        window.withNativeConfirmBypass(function() {
                            form.submit();
                        });
                    });
                    return;
                }

                if (submitter.dataset && submitter.dataset.confirmed === 'true') {
                    delete submitter.dataset.confirmed;
                    return;
                }

                if (!needsAutoConfirm(submitter, form)) return;

                e.preventDefault();
                e.stopPropagation();
                const message = resolveMessage(submitter, form);
                window.openConfirmationModal(message, function() {
                    submitter.dataset.confirmed = 'true';
                    window.withNativeConfirmBypass(function() {
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit(submitter);
                        } else {
                            form.submit();
                        }
                    });
                });
            }, true);
        })();

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
