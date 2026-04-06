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
                            @php
                                $opensDeadlineModal = !empty($item['route']);
                            @endphp
                            @if (!empty($item['route']))
                                <a
                                    href="{{ $item['route'] }}"
                                    class="reportorial-timeline-card__item-link"
                                    @if ($opensDeadlineModal)
                                        data-deadline-modal-trigger="true"
                                        data-deadline-aspect="{{ $item['aspect'] }}"
                                        data-deadline-label="{{ $item['label'] }}"
                                        data-deadline-timeline="{{ $timelineCard['badge'] }}"
                                    @endif
                                >
                                    <div class="reportorial-timeline-card__item-main">
                                        <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
                                        <div class="reportorial-timeline-card__item-copy">
                                            <span class="reportorial-timeline-card__item-title">{{ $item['label'] }}</span>
                                            @if ($opensDeadlineModal)
                                                <span class="reportorial-timeline-card__item-status" data-deadline-status-for="{{ $item['aspect'] }}">
                                                    Click to set deadline
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <i class="fas fa-arrow-right reportorial-timeline-card__item-arrow" aria-hidden="true"></i>
                                </a>
                            @else
                                <div class="reportorial-timeline-card__item">
                                    <div class="reportorial-timeline-card__item-main">
                                        <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
                                        <div class="reportorial-timeline-card__item-copy">
                                            <span class="reportorial-timeline-card__item-title">{{ $item['label'] }}</span>
                                        </div>
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

    <div class="deadline-modal" id="deadlineDraftModal" hidden>
        <div class="deadline-modal__backdrop" data-deadline-close></div>
        <div class="deadline-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="deadlineDraftModalTitle">
            <div class="deadline-modal__header">
                <div>
                    <span class="deadline-modal__eyebrow">Deadline Draft</span>
                    <h2 id="deadlineDraftModalTitle">Set Deadline</h2>
                    <p id="deadlineDraftModalDescription">Configure a frontend-only deadline draft for this requirement.</p>
                </div>
                <button type="button" class="deadline-modal__close" data-deadline-close aria-label="Close deadline modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form class="deadline-modal__form" id="deadlineDraftForm">
                <div class="deadline-modal__field-grid">
                    <div class="deadline-modal__field">
                        <label for="deadlineDraftRequirement">Requirement</label>
                        <input type="text" id="deadlineDraftRequirement" readonly>
                    </div>
                    <div class="deadline-modal__field">
                        <label for="deadlineDraftTimeline">Timeline</label>
                        <input type="text" id="deadlineDraftTimeline" readonly>
                    </div>
                    <div class="deadline-modal__field">
                        <label for="deadlineDraftYear">Reporting Year</label>
                        <select id="deadlineDraftYear" required>
                            @for ($year = now()->year + 3; $year >= 2020; $year--)
                                <option value="{{ $year }}" @selected($year === (int) now()->year)>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="deadline-modal__field">
                        <label for="deadlineDraftDate">Deadline Date</label>
                        <input type="date" id="deadlineDraftDate" required>
                    </div>
                </div>

                <div class="deadline-modal__notice">
                    This modal is UI-only for now. Saving will update the on-page draft preview only and will not write to the database yet.
                </div>

                <div class="deadline-modal__footer">
                    <a href="#" class="deadline-modal__link" id="deadlineDraftOpenRoute">
                        <i class="fas fa-up-right-from-square"></i>
                        <span>Open Report Page</span>
                    </a>
                    <div class="deadline-modal__actions">
                        <button type="button" class="deadline-modal__button deadline-modal__button--secondary" data-deadline-close>Cancel</button>
                        <button type="submit" class="deadline-modal__button deadline-modal__button--primary">Save Draft</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
            align-items: flex-start;
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

        .reportorial-timeline-card__item-copy {
            display: grid;
            gap: 3px;
        }

        .reportorial-timeline-card__item-title {
            color: #334155;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.5;
        }

        .reportorial-timeline-card__item-status {
            color: #1d4ed8;
            font-size: 11px;
            line-height: 1.4;
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

        .deadline-modal[hidden] {
            display: none;
        }

        .deadline-modal {
            position: fixed;
            inset: 0;
            z-index: 1100;
        }

        .deadline-modal__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(2px);
        }

        .deadline-modal__dialog {
            position: relative;
            width: min(100%, 680px);
            margin: 7vh auto 0;
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #bfdbfe;
            box-shadow: 0 28px 60px rgba(15, 23, 42, 0.28);
            padding: 24px;
            z-index: 1;
        }

        .deadline-modal__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .deadline-modal__eyebrow {
            display: inline-block;
            margin-bottom: 6px;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .deadline-modal__header h2 {
            margin: 0;
            color: #002c76;
            font-size: 22px;
            line-height: 1.25;
        }

        .deadline-modal__header p {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }

        .deadline-modal__close {
            width: 38px;
            height: 38px;
            border: 1px solid #dbeafe;
            border-radius: 999px;
            background: #f8fbff;
            color: #1d4ed8;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .deadline-modal__form {
            display: grid;
            gap: 16px;
        }

        .deadline-modal__field-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .deadline-modal__field {
            display: grid;
            gap: 6px;
        }

        .deadline-modal__field label {
            color: #334155;
            font-size: 12px;
            font-weight: 700;
        }

        .deadline-modal__field input,
        .deadline-modal__field select {
            width: 100%;
            height: 42px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #ffffff;
            color: #0f172a;
            padding: 0 12px;
            font-size: 13px;
        }

        .deadline-modal__field input[readonly] {
            background: #f8fafc;
            color: #475569;
        }

        .deadline-modal__notice {
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            background: #eff6ff;
            color: #1e40af;
            padding: 12px 14px;
            font-size: 12px;
            line-height: 1.6;
        }

        .deadline-modal__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .deadline-modal__actions {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .deadline-modal__button,
        .deadline-modal__link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            border-radius: 10px;
            padding: 0 14px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }

        .deadline-modal__button {
            cursor: pointer;
            border: 1px solid transparent;
        }

        .deadline-modal__button--secondary {
            background: #e2e8f0;
            border-color: #cbd5e1;
            color: #334155;
        }

        .deadline-modal__button--primary {
            background: linear-gradient(180deg, #0a4cb3 0%, #002c76 100%);
            border-color: #002c76;
            color: #ffffff;
        }

        .deadline-modal__link {
            border: 1px solid #bfdbfe;
            background: #ffffff;
            color: #1d4ed8;
        }

        @media (max-width: 640px) {
            .reportorial-shell {
                padding: 16px;
            }

            .reportorial-header {
                flex-direction: column;
            }

            .deadline-modal__dialog {
                width: calc(100% - 20px);
                margin-top: 4vh;
                padding: 18px;
            }

            .deadline-modal__field-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        (() => {
            const modal = document.getElementById('deadlineDraftModal');
            const form = document.getElementById('deadlineDraftForm');
            const requirementInput = document.getElementById('deadlineDraftRequirement');
            const timelineInput = document.getElementById('deadlineDraftTimeline');
            const yearInput = document.getElementById('deadlineDraftYear');
            const dateInput = document.getElementById('deadlineDraftDate');
            const description = document.getElementById('deadlineDraftModalDescription');
            const openRouteLink = document.getElementById('deadlineDraftOpenRoute');
            const triggers = document.querySelectorAll('[data-deadline-modal-trigger="true"]');

            if (!modal || !form || triggers.length === 0) {
                return;
            }

            let activeTrigger = null;

            const closeModal = () => {
                modal.hidden = true;
                document.body.style.overflow = '';
                activeTrigger = null;
            };

            const openModal = (trigger) => {
                activeTrigger = trigger;
                requirementInput.value = trigger.dataset.deadlineLabel || '';
                timelineInput.value = trigger.dataset.deadlineTimeline || '';
                description.textContent = 'Configure a frontend-only deadline draft for ' + (trigger.dataset.deadlineLabel || 'this requirement') + '.';
                openRouteLink.href = trigger.getAttribute('href') || '#';
                modal.hidden = false;
                document.body.style.overflow = 'hidden';
            };

            const formatDate = (value) => {
                const date = new Date(value + 'T00:00:00');
                if (Number.isNaN(date.getTime())) {
                    return value;
                }

                return date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                });
            };

            triggers.forEach((trigger) => {
                trigger.addEventListener('click', (event) => {
                    event.preventDefault();
                    openModal(trigger);
                });
            });

            modal.querySelectorAll('[data-deadline-close]').forEach((element) => {
                element.addEventListener('click', () => closeModal());
            });

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.hidden) {
                    closeModal();
                }
            });

            form.addEventListener('submit', (event) => {
                event.preventDefault();

                if (!activeTrigger) {
                    closeModal();
                    return;
                }

                if (!dateInput.value) {
                    dateInput.focus();
                    return;
                }

                const aspect = activeTrigger.dataset.deadlineAspect || '';
                const status = document.querySelector('[data-deadline-status-for="' + aspect + '"]');
                if (status) {
                    status.textContent = 'Draft deadline: ' + formatDate(dateInput.value) + ' (CY ' + yearInput.value + ')';
                }

                closeModal();
            });
        })();
    </script>
@endsection
