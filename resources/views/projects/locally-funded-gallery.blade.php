@extends('layouts.dashboard')

@section('title', 'Project Gallery')
@section('page-title', 'Project Gallery')

@section('styles')
    <style>
        .lfp-gallery-page .project-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 32px 0 14px;
        }

        .lfp-gallery-page .project-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            background-color: #ffffff;
            color: #1e293b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
            text-decoration: none;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }

        .lfp-gallery-page .project-tab:hover {
            border-color: #002c76;
            color: #002c76;
        }

        .lfp-gallery-page .project-tab.is-active {
            background-color: #002c76;
            border-color: #002c76;
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(0, 44, 118, 0.28);
        }

        @media (max-width: 768px) {
            .lfp-gallery-page .project-tabs {
                gap: 6px;
            }

            .lfp-gallery-page .project-tab {
                padding: 8px 12px;
                font-size: 12px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $detailUrl = route('locally-funded-project.show', $project);
        $galleryUrl = route('locally-funded-project.gallery', $project);
        $tabs = [
            ['label' => 'Project Profile', 'url' => $detailUrl . '#projectProfileSection'],
            ['label' => 'Contract Information', 'url' => $detailUrl . '#contractInfoSection'],
            ['label' => 'Physical Accomplishment', 'url' => $detailUrl . '#physicalAccomplishmentSection'],
            ['label' => 'Financial Accomplishment', 'url' => $detailUrl . '#financialAccomplishmentSection'],
            ['label' => 'Monitoring/Inspection Activities', 'url' => $detailUrl . '#monitoringInspectionSection'],
            ['label' => 'Post Implementation', 'url' => $detailUrl . '#postImplementationSection'],
        ];
    @endphp

    <div class="lfp-gallery-page">
        <div class="content-header" style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
            <div>
                <h1 style="font-weight: 700; color: #002C76; font-size: 32px;">Project Gallery</h1>
                <p>Gallery page for the selected locally funded project.</p>
            </div>
            <div style="display: flex; gap: 8px; align-items: center;">
                <a href="{{ $detailUrl }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; background-color: #ffffff; color: #002C76; border: 1px solid #002C76; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Project Details
                </a>
                <a href="{{ route('projects.locally-funded') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; background-color: #002C76; color: #ffffff; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;">
                    <i class="fas fa-list" aria-hidden="true"></i>
                    Back to List
                </a>
            </div>
        </div>

        <div style="background: #f8fafc; padding: 24px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
            <h1 style="font-weight: 700; color: #002C76; font-size: 32px; margin-bottom: 10px;">{{ $project->project_name }}</h1>
            <div style="display: flex; flex-direction: column; gap: 6px; color: #374151;">
                <span><strong>Project Code:</strong> {{ $project->subaybayan_project_code }}</span>
                <span><strong>Funding Year:</strong> {{ $project->funding_year }}</span>
                <span><strong>Funding Source:</strong> {{ $project->fund_source }}</span>
            </div>

            <div class="project-tabs" aria-label="Project detail pages">
                @foreach ($tabs as $tab)
                    <a href="{{ $tab['url'] }}" class="project-tab">{{ $tab['label'] }}</a>
                @endforeach
                <a href="{{ $galleryUrl }}" class="project-tab is-active" aria-current="page">Gallery</a>
            </div>

            <div style="margin-bottom: 24px; padding: 20px; border: 1px solid #00267C; border-radius: 10px; background-color: #ffffff;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; border-bottom: 2px solid #00267C; padding-bottom: 10px;">
                    <h3 style="color: #00267C; font-size: 15px; font-weight: 700; margin: 0;">Gallery</h3>
                </div>
            </div>
        </div>
    </div>
@endsection
