@extends('layouts.dashboard')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
    <div class="content-header">
        <h1>Notifications</h1>
        <p>Review your recent system notifications and open the related records.</p>
    </div>

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

    <section style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; margin-bottom: 18px;">
            <div style="display: flex; align-items: flex-start; gap: 14px;">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: #eff6ff; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <h2 style="margin: 0; color: #002C76; font-size: 20px;">Notification Inbox</h2>
                    <p style="margin: 6px 0 0; color: #64748b; font-size: 14px; line-height: 1.6;">
                        Items open through the same notification routing used by the bell icon in the top bar.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('notifications.clear') }}">
                @csrf
                <button type="submit" style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc; color: #334155; font-size: 13px; font-weight: 700; cursor: pointer;">
                    Clear Read
                </button>
            </form>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 20px;">
            <div style="border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px; background: #eff6ff;">
                <span style="display: block; color: #1e3a8a; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Unread</span>
                <strong style="display: block; margin-top: 8px; color: #002C76; font-size: 30px; line-height: 1;">{{ number_format((int) $unreadNotifications) }}</strong>
            </div>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f8fafc;">
                <span style="display: block; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Read</span>
                <strong style="display: block; margin-top: 8px; color: #0f172a; font-size: 30px; line-height: 1;">{{ number_format((int) $readNotifications) }}</strong>
            </div>
            <div style="border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; background: #f0fdf4;">
                <span style="display: block; color: #166534; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Total</span>
                <strong style="display: block; margin-top: 8px; color: #14532d; font-size: 30px; line-height: 1;">{{ number_format((int) ($unreadNotifications + $readNotifications)) }}</strong>
            </div>
        </div>

        @if ($notifications->isEmpty())
            <div style="padding: 22px 18px; border-radius: 12px; border: 1px dashed #cbd5e1; background: #f8fafc; color: #64748b; text-align: center; font-size: 14px;">
                No notifications found.
            </div>
        @else
            <div style="display: grid; gap: 12px;">
                @foreach ($notifications as $notification)
                    @php
                        $isUnread = is_null($notification->read_at);
                    @endphp
                    <a
                        href="{{ route('notifications.read', ['id' => $notification->id]) }}"
                        style="display: block; text-decoration: none; border: 1px solid {{ $isUnread ? '#bfdbfe' : '#e5e7eb' }}; border-radius: 12px; padding: 16px 18px; background: {{ $isUnread ? '#eff6ff' : '#ffffff' }}; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);"
                    >
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
                            <div style="display: flex; align-items: flex-start; gap: 10px; min-width: 0; flex: 1;">
                                <span style="width: 10px; height: 10px; border-radius: 999px; margin-top: 6px; flex: 0 0 auto; background: {{ $isUnread ? '#2563eb' : '#cbd5e1' }};"></span>
                                <div style="min-width: 0;">
                                    <div style="color: #0f172a; font-size: 14px; font-weight: {{ $isUnread ? '700' : '600' }}; line-height: 1.6;">
                                        {{ $notification->message }}
                                    </div>
                                    <div style="margin-top: 6px; color: #64748b; font-size: 12px;">
                                        {{ \Illuminate\Support\Carbon::parse($notification->created_at)->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                            </div>
                            <span style="display: inline-flex; align-items: center; padding: 5px 10px; border-radius: 999px; background: {{ $isUnread ? '#dbeafe' : '#f3f4f6' }}; color: {{ $isUnread ? '#1d4ed8' : '#475569' }}; font-size: 11px; font-weight: 700;">
                                {{ $isUnread ? 'Unread' : 'Read' }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div style="margin-top: 20px;">
                {{ $notifications->links() }}
            </div>
        @endif
    </section>
@endsection
