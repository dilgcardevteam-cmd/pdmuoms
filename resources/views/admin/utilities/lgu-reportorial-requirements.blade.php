@extends('layouts.dashboard')

@section('title', 'LGU Reportorial Requirements')
@section('page-title', 'LGU Reportorial Requirements')

@section('content')
    <div class="content-header">
        <h1>LGU Reportorial Requirements</h1>
        <p>Manage LGU-specific reportorial requirement settings and future workflow controls.</p>
    </div>

    <section class="reportorial-shell reportorial-shell--lgu">
        <div class="reportorial-header">
            <div class="reportorial-icon" aria-hidden="true">
                <i class="fas fa-landmark"></i>
            </div>
            <div>
                <h2>LGU Configuration Workspace</h2>
                <p>
                    Review LGU reportorial requirements by reporting timeline so annual, quarterly, and monthly
                    requirement groups are easier to manage from one place.
                </p>
            </div>
        </div>

        <div class="reportorial-timeline-grid">
            @foreach ($timelineCards as $timelineCard)
                <article class="reportorial-timeline-card">
                    <div class="reportorial-timeline-card__header">
                        <span class="reportorial-timeline-card__badge">{{ $timelineCard['badge'] }}</span>
                        <i class="{{ $timelineCard['icon'] }}" aria-hidden="true"></i>
                    </div>
                    <h3>{{ $timelineCard['title'] }}</h3>
                    <p>{{ $timelineCard['description'] }}</p>
                    <div class="reportorial-timeline-card__items">
                        @forelse ($timelineCard['items'] as $item)
                            @if (!empty($item['route']))
                                <a href="{{ $item['route'] }}" class="reportorial-timeline-card__item-link">
                                    <div class="reportorial-timeline-card__item-main">
                                        <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
                                        <span>{{ $item['label'] }}</span>
                                    </div>
                                    <i class="fas fa-arrow-right reportorial-timeline-card__item-arrow" aria-hidden="true"></i>
                                </a>
                            @else
                                <div class="reportorial-timeline-card__item">
                                    <div class="reportorial-timeline-card__item-main">
                                        <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
                                        <span>{{ $item['label'] }}</span>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="reportorial-timeline-card__item reportorial-timeline-card__item--empty">
                                No submenu items are currently configured for this timeline.
                            </div>
                        @endforelse
                    </div>
                </article>
            @endforeach
        </div>

        <a href="{{ route('utilities.deadlines-configuration.index') }}" class="reportorial-back-link">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Deadlines Configuration</span>
        </a>
    </section>

    <style>
        .reportorial-shell {
            background: #ffffff;
            border: 1px solid #dbe4f0;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        .reportorial-shell--lgu {
            background: linear-gradient(180deg, #ffffff 0%, #eef4ff 100%);
            border-color: #93b7f3;
        }

        .reportorial-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
        }

        .reportorial-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: #dbeafe;
            color: #002c76;
            flex: 0 0 auto;
        }

        .reportorial-header h2 {
            margin: 0;
            color: #002c76;
            font-size: 20px;
        }

        .reportorial-header p {
            margin: 6px 0 0;
            color: #475569;
            font-size: 13px;
            line-height: 1.7;
        }

        .reportorial-timeline-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .reportorial-timeline-card {
            background: #ffffff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.08);
        }

        .reportorial-timeline-card__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
            color: #1e3a8a;
        }

        .reportorial-timeline-card__badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .reportorial-timeline-card h3 {
            margin: 0 0 8px;
            color: #1e3a8a;
            font-size: 15px;
        }

        .reportorial-timeline-card p {
            margin: 0;
            color: #475569;
            font-size: 13px;
            line-height: 1.6;
        }

        .reportorial-timeline-card__items {
            display: grid;
            gap: 8px;
            margin-top: 14px;
        }

        .reportorial-timeline-card__item {
            border: 1px solid #dbeafe;
            border-radius: 10px;
            background: #f8fbff;
            padding: 10px 12px;
            color: #334155;
            font-size: 12px;
            line-height: 1.5;
        }

        .reportorial-timeline-card__item-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border: 1px solid #dbeafe;
            border-radius: 10px;
            background: #f8fbff;
            padding: 10px 12px;
            color: #334155;
            text-decoration: none;
            transition: background-color 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        }

        .reportorial-timeline-card__item-link:hover {
            background: #eef4ff;
            border-color: #93c5fd;
            transform: translateY(-1px);
        }

        .reportorial-timeline-card__item-main {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .reportorial-timeline-card__item-main i {
            color: #1d4ed8;
            width: 14px;
            text-align: center;
            flex: 0 0 auto;
        }

        .reportorial-timeline-card__item-main span {
            color: #334155;
            font-size: 12px;
            line-height: 1.5;
        }

        .reportorial-timeline-card__item-arrow {
            color: #94a3b8;
            font-size: 11px;
            flex: 0 0 auto;
        }

        .reportorial-timeline-card__item--empty {
            color: #64748b;
            font-style: italic;
            background: #f8fafc;
            border-style: dashed;
        }

        .reportorial-back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #93b7f3;
            background: #ffffff;
            color: #002c76;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
        }

        .reportorial-back-link:hover {
            background: #eef4ff;
        }

        @media (max-width: 640px) {
            .reportorial-shell {
                padding: 16px;
            }

            .reportorial-header {
                flex-direction: column;
            }
        }
    </style>
@endsection
