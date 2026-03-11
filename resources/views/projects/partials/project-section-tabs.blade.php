@php
    $activeTab = $activeTab ?? 'locally-funded';
    $projectTabs = [
        [
            'key' => 'locally-funded',
            'label' => 'Locally Funded Projects',
            'icon' => 'fa-project-diagram',
            'url' => route('dashboard'),
        ],
        [
            'key' => 'rssa',
            'label' => 'Rapid Subproject Sustainability Assessment',
            'icon' => 'fa-clipboard-check',
            'url' => route('projects.rssa'),
        ],
        [
            'key' => 'sglgif',
            'label' => 'SGLG Incentive Fund',
            'icon' => 'fa-award',
            'url' => route('projects.sglgif'),
        ],
        [
            'key' => 'rlip-lime',
            'label' => 'RLIP / LIME 20% Development Fund',
            'icon' => 'fa-road',
            'url' => route('projects.rlip-lime'),
        ],
    ];
@endphp

@once
    <style>
        .project-section-tabs {
            --project-tab-accent: #002C76;
            --project-tab-accent-dark: #001f54;
            --project-tab-accent-soft: rgba(0, 44, 118, 0.18);
            position: relative;
            display: flex;
            align-items: flex-end;
            gap: 18px;
            margin: 0 0 24px;
            padding: 0 0 3px;
            overflow-x: auto;
            scrollbar-width: thin;
        }

        .project-section-tabs::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            height: 3px;
            background: var(--project-tab-accent);
            border-radius: 999px;
        }

        .project-section-tab {
            position: relative;
            z-index: 1;
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: fit-content;
            max-width: 100%;
            padding: 15px 22px 13px;
            border: 1px solid #cfd4da;
            border-bottom: none;
            border-radius: 14px 14px 0 0;
            background: linear-gradient(180deg, #f1f2f4 0%, #d7dade 100%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.72);
            color: var(--project-tab-accent);
            text-decoration: none;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.25;
            white-space: nowrap;
            transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
        }

        .project-section-tab-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 999px;
            background: rgba(0, 44, 118, 0.1);
            color: var(--project-tab-accent);
            font-size: 11px;
            flex: 0 0 auto;
        }

        .project-section-tab:hover {
            transform: translateY(-2px);
            border-color: var(--project-tab-accent);
            background: linear-gradient(180deg, #f8fbff 0%, #dde8f6 100%);
            color: var(--project-tab-accent);
        }

        .project-section-tab:focus-visible {
            outline: 3px solid var(--project-tab-accent-soft);
            outline-offset: 2px;
        }

        .project-section-tab.is-active {
            background: linear-gradient(180deg, #0b4fae 0%, var(--project-tab-accent) 55%, var(--project-tab-accent-dark) 100%);
            border-color: var(--project-tab-accent-dark);
            box-shadow: 0 12px 20px var(--project-tab-accent-soft);
            color: #ffffff;
        }

        .project-section-tab.is-active .project-section-tab-icon {
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
        }

        @media (max-width: 700px) {
            .project-section-tabs {
                gap: 12px;
                margin-bottom: 20px;
                padding-bottom: 4px;
            }

            .project-section-tab {
                padding: 14px 18px 12px;
                font-size: 13px;
                letter-spacing: 0.04em;
            }
        }
    </style>
@endonce

<nav class="project-section-tabs" aria-label="Project pages">
    @foreach ($projectTabs as $tab)
        <a
            href="{{ $tab['url'] }}"
            class="project-section-tab{{ $activeTab === $tab['key'] ? ' is-active' : '' }}"
            @if ($activeTab === $tab['key']) aria-current="page" @endif
        >
            <span class="project-section-tab-icon" aria-hidden="true">
                <i class="fas {{ $tab['icon'] }}"></i>
            </span>
            <span>{{ $tab['label'] }}</span>
        </a>
    @endforeach
</nav>
