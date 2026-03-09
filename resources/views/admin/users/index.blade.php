@extends('layouts.dashboard')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
    <div class="content-header">
        <h1>User Management</h1>
        <p>Manage all system users and their roles</p>
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

    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
        <!-- Header with Create Button -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="color: #002C76; font-size: 18px; margin: 0;">Active Users ({{ $users->total() }})</h2>
            <a href="{{ route('users.create') }}" style="padding: 10px 20px; background-color: #002C76; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.3s ease;">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
        </div>

        <!-- Users Table -->
        <div style="overflow-x: auto;">
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
                                    @if($user->role === 'superadmin') background-color: #fee2e2; color: #991b1b;
                                    @elseif($user->role === 'admin') background-color: #dbeafe; color: #0c2d6b;
                                    @else background-color: #dcfce7; color: #166534; @endif">
                                    {{ ucfirst($user->role) }}
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
                                <a href="{{ route('users.edit', $user->idno) }}" style="padding: 6px 12px; background-color: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; text-decoration: none; margin-right: 5px; transition: all 0.3s ease;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @if($user->idno !== Auth::id())
                                    <form action="{{ route('users.destroy', $user->idno) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 6px 12px; background-color: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; transition: all 0.3s ease;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
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

        <!-- Pagination -->
        <div style="margin-top: 20px;">
            {{ $users->links() }}
        </div>
    </div>

    <style>
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

        @media (max-width: 768px) {
            div[style*="overflow-x: auto"] {
                font-size: 12px !important;
            }

            table {
                font-size: 12px !important;
            }

            th, td {
                padding: 10px 8px !important;
            }

            a[style*="padding: 6px"], button[type="submit"] {
                padding: 5px 8px !important;
                font-size: 11px !important;
            }
        }
    </style>
@endsection
