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
            'key' => 'rlip-lime',
            'label' => 'RLIP / LIME 20% Development Fund',
            'icon' => 'fa-road',
            'url' => route('projects.rlip-lime.dashboard'),
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
    ];
@endphp

@once
    <style>
        .project-section-tabs {
            display: flex;
<<<<<<< Updated upstream
            align-items: center;
            gap: 10px;
            margin: 0 0 20px;
            padding: 0 0 12px;
            overflow-x: auto;
            scrollbar-width: thin;
            border-bottom: 1px solid #0b3d91;
        }

        .project-section-tab {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border: 1px solid #a8c0e8;
            border-radius: 999px;
            background: #eef4ff;
            color: #0b3d91;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
            transition: background-color 0.18s ease, color 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .project-section-tab:hover {
            border-color: #7ea5df;
            background: #e1edff;
            color: #0b3d91;
        }

        .project-section-tab:focus-visible {
            outline: 3px solid rgba(37, 99, 235, 0.25);
=======
            flex-wrap: wrap;
            gap: 10px;
            margin: 0 0 24px;
        }

        .project-section-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            max-width: 100%;
            padding: 10px 18px;
            border: 1px solid #c8d8f0;
            border-radius: 999px;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
            color: #002c76;
            text-decoration: none;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.25;
            white-space: normal;
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .project-section-tab-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 999px;
            background: rgba(0, 44, 118, 0.08);
            color: #002c76;
            font-size: 11px;
            flex: 0 0 auto;
        }

        .project-section-tab:hover {
            background: #eff6ff;
            border-color: #9bb7e3;
            color: #002c76;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.12);
        }

        .project-section-tab:focus-visible {
            outline: 3px solid rgba(0, 44, 118, 0.18);
>>>>>>> Stashed changes
            outline-offset: 2px;
        }

        .project-section-tab.is-active {
<<<<<<< Updated upstream
            background: #0b3d91;
            border-color: #0b3d91;
=======
            background: #002c76;
            border-color: #002c76;
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(0, 44, 118, 0.28);
        }

        .project-section-tab.is-active .project-section-tab-icon {
            background: rgba(255, 255, 255, 0.16);
>>>>>>> Stashed changes
            color: #ffffff;
            box-shadow: 0 8px 16px rgba(11, 61, 145, 0.28);
        }

        @media (max-width: 700px) {
            .project-section-tabs {
                gap: 8px;
<<<<<<< Updated upstream
                margin-bottom: 16px;
                padding-bottom: 10px;
            }

            .project-section-tab {
                padding: 8px 13px;
                font-size: 12px;
=======
                margin-bottom: 20px;
            }

            .project-section-tab {
                width: 100%;
                padding: 11px 16px;
>>>>>>> Stashed changes
            }
        }
    </style>
@endonce

<nav class="project-tabs project-section-tabs" aria-label="Project pages">
    @foreach ($projectTabs as $tab)
        <a
            href="{{ $tab['url'] }}"
            class="project-tab project-section-tab{{ $activeTab === $tab['key'] ? ' is-active' : '' }}"
            @if ($activeTab === $tab['key']) aria-current="page" @endif
        >
            <span>{{ $tab['label'] }}</span>
        </a>
    @endforeach
</nav>

 
