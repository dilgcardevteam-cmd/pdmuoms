@extends('layouts.dashboard')

@section('title', 'Project Details')
@section('page-title', 'Project Details')

@section('styles')
    <style>
        .lfp-summary-card {
            margin-bottom: 24px;
            padding: 18px 20px 16px;
            background: linear-gradient(180deg, #f4f6f8 0%, #ffffff 100%);
            border: 1px solid #00267c;
            border-radius: 10px;
            color: #00267c;
            font-size: 15px;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(0, 44, 118, 0.14);
        }

        .lfp-summary-row {
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom: 2px solid rgba(17, 24, 39, 0.14);
        }

        .lfp-summary-row:last-child {
            padding-bottom: 0;
            margin-bottom: 0;
            border-bottom: none;
        }

        .lfp-summary-label {
            display: block;
            margin-bottom: 4px;
            color: inherit;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.04em;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .lfp-summary-code {
            color: #111827;
            font-size: 18px;
            font-weight: inherit;
            line-height: 1.08;
            text-decoration: none;
            word-break: break-word;
        }

        .lfp-summary-name {
            color: #111827;
            font-size: 20px;
            font-weight: inherit;
            line-height: 1.05;
            word-break: break-word;
        }

        .lfp-summary-funding {
            display: grid;
            grid-template-columns: repeat(2, max-content);
            gap: 16px 24px;
            align-items: start;
            justify-content: start;
        }

        .lfp-summary-value {
            color: #111827;
            font-size: 18px;
            font-weight: inherit;
            line-height: 1.05;
        }

        .lfp-inline-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.56);
            z-index: 1290;
        }

        .lfp-inline-modal-backdrop.is-visible {
            display: block;
        }

        .lfp-inline-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            width: min(1180px, calc(100vw - 32px));
            max-height: min(90vh, 960px);
            transform: translate(-50%, -50%);
            border-radius: 16px;
            background-color: #ffffff;
            border: 1px solid #dbeafe;
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.24);
            z-index: 1300;
        }

        .lfp-inline-modal.is-visible {
            display: flex !important;
            flex-direction: column;
            overflow: hidden;
        }

        .lfp-inline-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%);
        }

        .lfp-inline-modal-body {
            flex: 1 1 auto;
            min-height: 0;
            max-height: none;
            overflow-y: auto;
            padding: 20px;
            background-color: #ffffff;
        }

        .lfp-inline-modal-close {
            border: none;
            background: #e2e8f0;
            color: #0f172a;
            width: 32px;
            height: 32px;
            border-radius: 999px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            line-height: 1;
        }

        .lfp-inline-modal-section-close {
            display: none;
        }

        @media (max-width: 768px) {
            .lfp-summary-card {
                padding: 16px;
            }

            .lfp-summary-funding {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .lfp-summary-name {
                font-size: clamp(1.75rem, 7vw, 2.4rem);
            }

            .lfp-summary-value {
                font-size: clamp(1.45rem, 6vw, 2rem);
            }

            .lfp-inline-modal-body {
                padding: 16px;
            }

            .lfp-mobile-canvas .lfp-inline-modal-backdrop.is-visible {
                display: block !important;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.56);
                z-index: 1290;
            }

            #editProfileFormBackdrop.is-visible,
            #editContractFormBackdrop.is-visible {
                display: block !important;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.56);
                z-index: 1290;
            }

            .lfp-mobile-canvas .lfp-inline-modal {
                margin-top: 0;
                top: auto;
                left: auto;
                width: auto;
                max-height: none;
                transform: none;
                background-color: #ffffff;
                border: 1px solid #dbeafe;
                border-radius: 16px;
                box-shadow: 0 24px 48px rgba(15, 23, 42, 0.24);
            }

            #editProfileFormWrapper,
            #editContractFormWrapper {
                margin-top: 0;
                top: auto;
                left: auto;
                width: auto;
                max-height: none;
                transform: none;
                background-color: #ffffff;
                border: 1px solid #dbeafe;
                border-radius: 16px;
                box-shadow: 0 24px 48px rgba(15, 23, 42, 0.24);
            }

            .lfp-mobile-canvas .lfp-inline-modal.is-visible {
                position: fixed;
                top: max(12px, env(safe-area-inset-top));
                right: 12px;
                bottom: max(12px, env(safe-area-inset-bottom));
                left: 12px;
                display: flex !important;
                flex-direction: column;
                min-height: 0;
                overflow: hidden;
                z-index: 1300;
            }

            #editProfileFormWrapper.is-visible,
            #editContractFormWrapper.is-visible {
                position: fixed;
                top: max(12px, env(safe-area-inset-top));
                right: 12px;
                bottom: max(12px, env(safe-area-inset-bottom));
                left: 12px;
                display: flex !important;
                flex-direction: column;
                min-height: 0;
                overflow: hidden;
                z-index: 1300;
            }

            .lfp-mobile-canvas .lfp-inline-modal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 14px 16px;
                border-bottom: 1px solid #e5e7eb;
                background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%);
            }

            #editProfileFormWrapper .lfp-inline-modal-header,
            #editContractFormWrapper .lfp-inline-modal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 14px 16px;
                border-bottom: 1px solid #e5e7eb;
                background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%);
            }

            .lfp-mobile-canvas .lfp-inline-modal-body {
                flex: 1 1 auto;
                min-height: 0;
                max-height: none;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                background-color: #ffffff;
            }

            .lfp-mobile-canvas .lfp-inline-modal-body form {
                min-height: 100%;
            }

            #editProfileFormWrapper .lfp-inline-modal-body,
            #editContractFormWrapper .lfp-inline-modal-body {
                flex: 1 1 auto;
                min-height: 0;
                max-height: none;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                background-color: #ffffff;
            }

            #editProfileFormWrapper .lfp-inline-modal-body form,
            #editContractFormWrapper .lfp-inline-modal-body form {
                min-height: 100%;
            }

            #editProfileForm > div:first-of-type,
            #editContractForm > div:first-of-type {
                grid-template-columns: 1fr !important;
            }

        }
    </style>
@endsection

@section('content')
    <div class="lfp-mobile-shell">
        <div class="lfp-mobile-canvas">
    <div class="content-header" style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
        <div>
            <h1 style="font-weight: 700; color: #002C76; font-size: 32px;">Project Details</h1>
            <p>Full record for the selected locally funded project.</p>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
            <a href="{{ route('projects.locally-funded') }}" style="padding: 8px 16px; background-color: #002C76; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px;">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
            <button id="activityLogFab" type="button" aria-controls="activityLogSection" aria-expanded="false" data-state="closed">
                <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                <span style="font-weight: 600; font-size: 13px;">Activity Logs</span>
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 16px; border-radius: 8px; margin: 16px 0;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div id="success-alert" style="background-color: #d1fae5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 16px; border-radius: 8px; margin: 16px 0;">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function () {
                const successAlert = document.getElementById('success-alert');
                if (successAlert) {
                    successAlert.style.display = 'none';
                }
            }, 3000);
        </script>
    @endif

    @php
        $userAgency = strtoupper(trim((string) (Auth::user()->agency ?? '')));
        $userProvince = trim((string) (Auth::user()->province ?? ''));
        $userRole = strtolower(trim((string) (Auth::user()->role ?? '')));
        $isLguAgencyUser = $userAgency === 'LGU';
        $canEditProjectProfile = $userAgency === 'DILG'
            && $userProvince === 'Regional Office'
            && $userRole === 'superadmin';
    @endphp

    <div style="background: #f8fafc; padding: 24px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        
        <h1 class="project-main-title" style="font-weight: 700; color: #002C76; font-size: 32px;">{{ $project->project_name }}</h1>
        <div style="
            display: flex;
            flex-direction: row;
            width: 100%;
            ">
            <div style="display: flex; flex-direction: column; gap: 6px; color: #374151; font-family: 'Facebook Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                <span style="display: inline-flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span>
                    <span style="font-weight: 700; color: #000000;">
                        Project Code:
                    </span>
                    {{ $project->subaybayan_project_code }}
                    </span>
                    <button type="button" class="project-copy-button" data-copy-text="{{ $project->subaybayan_project_code }}" aria-label="Copy project code" title="Copy project code">
                        <i class="fas fa-copy" aria-hidden="true"></i>
                    </button>
                </span>

                <span>
                    <span style="font-weight: 700; color: #000000;">
                        Funding Year:
                    </span>
                    {{ $project->funding_year }}
                </span>
                <span>
                <span style="font-weight: 700; color: #000000;">
                        Funding Source:
                    </span>
                    {{ $project->fund_source }}
                </span>
            </div>
        </div>

        <!-- <br>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 24px;">
            <div style="padding: 16px; border: 1px solid #002C76; border-radius: 8px;">
                <div style="font-size: 12px; color: #002C76; font-weight: 700; text-transform: uppercase;">Project Code</div>
                <div style="font-size: 16px; font-weight: 700; color: #111827; margin-top: 6px;">{{ $project->subaybayan_project_code }}</div>
            </div>
            <div style="padding: 16px; border: 1px solid #002C76; border-radius: 8px;">
                <div style="font-size: 12px; color: #002C76; font-weight: 700; text-transform: uppercase;">Project Name</div>
                <div style="font-size: 16px; font-weight: 700; color: #111827; margin-top: 6px;">{{ $project->project_name }}</div>
            </div>
            <div style="padding: 16px; border: 1px solid #002C76; border-radius: 8px;">
                <div style="font-size: 12px; color: #002C76; font-weight: 700; text-transform: uppercase;">Funding</div>
                <div style="font-size: 14px; color: #111827; margin-top: 6px;">Year: <strong>{{ $project->funding_year }}</strong></div>
                <div style="font-size: 14px; color: #111827;">Source: <strong>{{ $project->fund_source }}</strong></div>
            </div>
        </div> -->

        <hr style="margin: 10px">

        <!-- <br>
        <div class="lfp-summary-card">
            <div class="lfp-summary-row">
                <span class="lfp-summary-label">Project Code</span>
                <div class="lfp-summary-code">{{ $project->subaybayan_project_code }}</div>
            </div>
            <div class="lfp-summary-row">
                <span class="lfp-summary-label">Project Name</span>
                <div class="lfp-summary-name">{{ $project->project_name }}</div>
            </div>
            <div class="lfp-summary-row">
                <div class="lfp-summary-funding">
                    <div>
                        <span class="lfp-summary-label">Funding Year</span>
                        <div class="lfp-summary-value">{{ $project->funding_year }}</div>
                    </div>
                    <div>
                        <span class="lfp-summary-label">Fund Source</span>
                        <div class="lfp-summary-value">{{ $project->fund_source }}</div>
                    </div>
                </div>
            </div>
        </div> -->

        <div class="project-tabs" role="tablist" aria-label="Project detail sections" style="margin-top: 40px;">
            <button type="button" class="project-tab is-active" id="tab-project-profile" data-project-tab-target="projectProfileSection" role="tab" aria-controls="projectProfileSection" aria-selected="true">Project Profile</button>
            <button type="button" class="project-tab" id="tab-contract-info" data-project-tab-target="contractInfoSection" role="tab" aria-controls="contractInfoSection" aria-selected="false">Contract Information</button>
            <button type="button" class="project-tab" id="tab-physical-accomplishment" data-project-tab-target="physicalAccomplishmentSection" role="tab" aria-controls="physicalAccomplishmentSection" aria-selected="false">Physical Accomplishment</button>
            <button type="button" class="project-tab" id="tab-financial-accomplishment" data-project-tab-target="financialAccomplishmentSection" role="tab" aria-controls="financialAccomplishmentSection" aria-selected="false">Financial Accomplishment</button>
            <button type="button" class="project-tab" id="tab-monitoring-inspection" data-project-tab-target="monitoringInspectionSection" role="tab" aria-controls="monitoringInspectionSection" aria-selected="false">Monitoring/Inspection Activities</button>
            <button type="button" class="project-tab" id="tab-post-implementation" data-project-tab-target="postImplementationSection" role="tab" aria-controls="postImplementationSection" aria-selected="false">Post Implementation</button>
        </div>

        <div id="projectProfileSection" class="project-tab-panel is-active" data-tab-key="profile" role="tabpanel" aria-labelledby="tab-project-profile" style="margin-bottom: 24px; padding: 20px; border: 1px solid #00267C; border-radius: 10px; background-color: #ffffff;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; border-bottom: 2px solid #00267C; padding-bottom: 10px;">
                <h3 style="color: #00267C; font-size: 15px; font-weight: 700; margin: 0;">Project Profile</h3>
                @if($canEditProjectProfile)
                    <a href="#" data-toggle="inline-edit" data-target="editProfileForm" style="padding: 6px 12px; background-color: #002C76; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 12px;"><i class="fas fa-edit" style="margin-right: 6px;"></i>Update</a>
                @endif
            </div>
            <div class="project-profile-grid" style="display: grid; grid-template-columns: repeat(2, minmax(260px, 1fr)); gap: 14px;">
                <div style="grid-column: 1 / -1;">
                    <span style="font-weight: 700; color: #000000;">Project Description:</span>
                    </br>
                    <span class="text-[#374151]" style="margin-top: 6px; color: #374151;">{!! nl2br(e($project->project_description)) !!}</span>
                </div>

                    <!-- SITE / LOCATION -->
                    <div class="flex flex-col text-[#374151]" style="gap: 8px;">
                        <span class=""><strong>Province:</strong> {{ $project->province }}</span>
                        <span class=""><strong>City/Municipality:</strong> {{ $project->city_municipality }}</span>
                        @php
                            $barangays = array_filter(array_map('trim', explode(',', (string) $project->barangay)));
                        @endphp
                        <span class="">
                            <strong>Barangay:</strong>
                            @if(count($barangays))
                                <ul style="margin: 4px 0 0 16px; padding: 0;">
                                    @foreach($barangays as $barangay)
                                        <li style="margin: 0; list-style: disc;">{{ $barangay }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </span>
                        <span><strong>LGSF Allocation:</strong> ₱ {{ number_format($project->lgsf_allocation, 2) }}</span>
                        <span><strong>LGU Counterpart:</strong> ₱ {{ number_format($project->lgu_counterpart, 2) }}</span>
                    </div>

                    <!-- OTHER INFO -->
                    <div class="flex flex-col text-[#374151]" style="gap: 8px;">
                        <span><strong>Project Type:</strong> {{ $project->project_type }}</span>
                        <span><strong>Date of NADAI:</strong> {{ $project->date_nadai ? $project->date_nadai->format('F j, Y') : '' }}</span>
                        <span><strong>No. of Beneficiaries:</strong> {{ number_format($project->no_of_beneficiaries) }}</span>
                        <span><strong>Rainwater Collection System:</strong> {{ $project->rainwater_collection_system }}</span>
                        <span><strong>Date of Confirmation Fund Receipt:</strong> {{ $project->date_confirmation_fund_receipt ? $project->date_confirmation_fund_receipt->format('F j, Y') : '' }}</span>
                    </div>
            </div>
            @if($canEditProjectProfile)
            <div id="editProfileFormBackdrop" class="lfp-inline-modal-backdrop{{ old('section') === 'profile' ? ' is-visible' : '' }}" aria-hidden="{{ old('section') === 'profile' ? 'false' : 'true' }}"></div>
            <div id="editProfileFormWrapper" class="lfp-inline-modal{{ old('section') === 'profile' ? ' is-visible' : '' }}" data-inline-modal="true" role="dialog" aria-modal="true" aria-labelledby="editProfileModalTitle" aria-hidden="{{ old('section') === 'profile' ? 'false' : 'true' }}" style="display: {{ old('section') === 'profile' ? 'block' : 'none' }};">
                <div class="lfp-inline-modal-header">
                    <h3 id="editProfileModalTitle" style="color: #00267C; font-size: 15px; font-weight: 700; margin: 0;">Edit Project Profile</h3>
                    <button type="button" class="lfp-inline-modal-close" data-toggle="inline-cancel" data-target="editProfileForm" aria-label="Close project profile editor">&times;</button>
                </div>
                <div class="lfp-inline-modal-body">
            <form id="editProfileForm" action="{{ route('locally-funded-project.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="section" value="profile">

                <div class="project-profile-form-grid" style="display: grid; grid-template-columns: repeat(3, minmax(260px, 1fr)); gap: 20px; padding: 20px; border: 1px solid #cbd5e1; border-radius: 10px; background-color: white;">
                    @php
                        $selectedProvince = old('province', $project->province);
                        $selectedProvinceNorm = strtolower(trim((string) $selectedProvince));
                        $hasProvinceInOptions = collect($provinces)->contains(function ($item) use ($selectedProvinceNorm) {
                            return strtolower(trim((string) $item)) === $selectedProvinceNorm;
                        });

                        $selectedFundingYear = (string) old('funding_year', $project->funding_year);
                        $hasFundingYearInOptions = collect($fundingYears)->contains(function ($item) use ($selectedFundingYear) {
                            return (string) $item === $selectedFundingYear;
                        });

                        $selectedFundSource = old('fund_source', $project->fund_source);
                        $selectedFundSourceNorm = strtolower(trim((string) $selectedFundSource));
                        $hasFundSourceInOptions = collect($fundSources)->contains(function ($item) use ($selectedFundSourceNorm) {
                            return strtolower(trim((string) $item)) === $selectedFundSourceNorm;
                        });
                    @endphp
                    <div style="grid-column: 1 / -1;">
                        <label for="project_description" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Project Description <span class="asterisk">*</span></label>
                        <textarea id="project_description" name="project_description" required rows="3"
                                  style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box; resize: vertical;">{{ old('project_description', $project->project_description) }}</textarea>
                    </div>

                    <div>
                        <label for="province" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Province <span class="asterisk">*</span></label>
                        <select id="province" name="province" required
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box; background-color: white;">
                            <option value="">-- Select Province --</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province }}" {{ strtolower(trim((string) $province)) === $selectedProvinceNorm ? 'selected' : '' }}>{{ $province }}</option>
                            @endforeach
                            @if(!$hasProvinceInOptions && trim((string) $selectedProvince) !== '')
                                <option value="{{ $selectedProvince }}" selected>{{ $selectedProvince }}</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <label for="city_municipality" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">City/Municipality <span class="asterisk">*</span></label>
                        <select id="city_municipality" name="city_municipality" required data-selected="{{ old('city_municipality', $project->city_municipality) }}"
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box; background-color: white;">
                            <option value="">-- Select Province First --</option>
                        </select>
                    </div>

                    <div>
                        <label for="barangay" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Barangay <span class="asterisk">*</span></label>
                        <div style="position: relative;">
                            <div id="barangay_badges" role="button" tabindex="0" aria-controls="barangay" aria-expanded="false" style="display: flex; flex-wrap: wrap; gap: 6px; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; min-height: 44px; background-color: white; margin-bottom: 8px; align-content: flex-start; cursor: pointer;">
                                <span style="color: #9ca3af; font-size: 14px; align-self: center;">Click here or dropdown to add barangays</span>
                            </div>
                            <select id="barangay" name="barangay[]" multiple hidden
                                    style="width: 100%; margin-top: 8px; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box; background-color: white; min-height: 120px;">
                            </select>
                        </div>
                        <input type="hidden" id="barangay_hidden" name="barangay_json" value="{{ old('barangay_json', json_encode(array_values(array_filter(array_map('trim', explode(',', $project->barangay)))))) }}">
                    </div>

                    <div>
                        <label for="funding_year" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Funding Year <span class="asterisk">*</span></label>
                        <select id="funding_year" name="funding_year" required
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box; background-color: white;">
                            <option value="">-- Select Funding Year --</option>
                            @foreach($fundingYears as $year)
                                <option value="{{ $year }}" {{ (string) $year === $selectedFundingYear ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                            @if(!$hasFundingYearInOptions && trim($selectedFundingYear) !== '')
                                <option value="{{ $selectedFundingYear }}" selected>{{ $selectedFundingYear }}</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <label for="fund_source" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Fund Source <span class="asterisk">*</span></label>
                        <select id="fund_source" name="fund_source" required
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box; background-color: white;">
                            <option value="">-- Select Fund Source --</option>
                            @foreach($fundSources as $source)
                                <option value="{{ $source }}" {{ strtolower(trim((string) $source)) === $selectedFundSourceNorm ? 'selected' : '' }}>{{ $source }}</option>
                            @endforeach
                            @if(!$hasFundSourceInOptions && trim((string) $selectedFundSource) !== '')
                                <option value="{{ $selectedFundSource }}" selected>{{ $selectedFundSource }}</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <label for="subaybayan_project_code" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">SubayBayan Project Code <span class="asterisk">*</span></label>
                        <input type="text" id="subaybayan_project_code" name="subaybayan_project_code" value="{{ old('subaybayan_project_code', $project->subaybayan_project_code) }}" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="project_name" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Project Name <span class="asterisk">*</span></label>
                        <input type="text" id="project_name" name="project_name" value="{{ old('project_name', $project->project_name) }}" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="project_type" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Project Type <span class="asterisk">*</span></label>
                        <select id="project_type" name="project_type" required
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box; background-color: white;">
                            <option value="">-- Select Project Type --</option>
                            <option value="Evacuation Center / Multi-Purpose Hall" {{ old('project_type', $project->project_type) === 'Evacuation Center / Multi-Purpose Hall' ? 'selected' : '' }}>Evacuation Center / Multi-Purpose Hall</option>
                            <option value="Water Supply and Sanitation" {{ old('project_type', $project->project_type) === 'Water Supply and Sanitation' ? 'selected' : '' }}>Water Supply and Sanitation</option>
                            <option value="Local Roads and Bridges" {{ old('project_type', $project->project_type) === 'Local Roads and Bridges' ? 'selected' : '' }}>Local Roads and Bridges</option>
                            <option value="Others" {{ old('project_type', $project->project_type) === 'Others' ? 'selected' : '' }}>Others</option>
                        </select>
                    </div>

                    <div>
                        <label for="date_nadai" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Date of NADAI <span class="asterisk">*</span></label>
                        <input type="date" id="date_nadai" name="date_nadai" value="{{ old('date_nadai', $project->date_nadai ? $project->date_nadai->format('Y-m-d') : '') }}" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="lgsf_allocation" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">LGSF Allocation (based on NADAI) <span class="asterisk">*</span></label>
                        <input type="text" id="lgsf_allocation" name="lgsf_allocation" value="{{ old('lgsf_allocation', number_format((float)$project->lgsf_allocation, 2, '.', ',')) }}" placeholder="0.00" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="lgu_counterpart" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">LGU Counterpart <span class="asterisk">*</span></label>
                        <input type="text" id="lgu_counterpart" name="lgu_counterpart" value="{{ old('lgu_counterpart', number_format((float)$project->lgu_counterpart, 2, '.', ',')) }}" placeholder="0.00" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="no_of_beneficiaries" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">No. of Beneficiaries <span class="asterisk">*</span></label>
                        <input type="number" id="no_of_beneficiaries" name="no_of_beneficiaries" value="{{ old('no_of_beneficiaries', $project->no_of_beneficiaries) }}" min="0" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="rainwater_collection_system" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 13px;">With Rainwater Collection System (for Govt buildings)</label>
                        <select id="rainwater_collection_system" name="rainwater_collection_system"
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box; background-color: white;">
                            <option value="">-- Select --</option>
                            <option value="Yes" {{ old('rainwater_collection_system', $project->rainwater_collection_system) === 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('rainwater_collection_system', $project->rainwater_collection_system) === 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <div>
                        <label for="date_confirmation_fund_receipt" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Date of Confirmation Fund Receipt <span class="asterisk">*</span></label>
                        <input type="date" id="date_confirmation_fund_receipt" name="date_confirmation_fund_receipt" value="{{ old('date_confirmation_fund_receipt', $project->date_confirmation_fund_receipt ? $project->date_confirmation_fund_receipt->format('Y-m-d') : '') }}" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                </div>

                <div style="margin-top: 16px; display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="submit" style="padding: 8px 16px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;"><i class="fas fa-check" style="margin-right: 8px;"></i>Save Changes</button>
                    <button type="button" data-toggle="inline-cancel" data-target="editProfileForm" style="padding: 8px 16px; background-color: #6b7280; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;"><i class="fas fa-times" style="margin-right: 8px;"></i>Cancel</button>
                </div>
            </form>
                </div>
            </div>
            @endif
        </div>

        <div id="contractInfoSection" class="project-tab-panel" data-tab-key="contract" role="tabpanel" aria-labelledby="tab-contract-info" style="margin-bottom: 24px; padding: 20px; border: 1px solid #00267C; border-radius: 10px; background-color: #ffffff;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; border-bottom: 2px solid #00267C; padding-bottom: 10px;">
                <h3 style="color: #00267C; font-size: 15px; font-weight: 700; margin: 0;">Contract Information</h3>
                @if(!$isLguAgencyUser)
                    <a href="#" data-toggle="inline-edit" data-target="editContractForm" style="padding: 6px 12px; background-color: #002C76; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 12px;"><i class="fas fa-edit" style="margin-right: 6px;"></i>Update</a>
                @endif
            </div>
            <div class="contract-info-grid" style="display: grid; grid-template-columns: repeat(3, minmax(260px, 1fr)); gap: 14px;">
                <div><strong>Mode of Procurement:</strong> {{ $project->mode_of_procurement }}</div>
                <div><strong>Implementing Unit:</strong> {{ $project->implementing_unit }}</div>
                <div><strong>Date of Posting (ITB):</strong> {{ $project->date_posting_itb ? $project->date_posting_itb->format('F j, Y') : '' }}</div>
                <div><strong>Date of Bid Opening:</strong> {{ $project->date_bid_opening ? $project->date_bid_opening->format('F j, Y') : '' }}</div>
                <div><strong>Date of NOA:</strong> {{ $project->date_noa ? $project->date_noa->format('F j, Y') : '' }}</div>
                <div><strong>Date of NTP:</strong> {{ $project->date_ntp ? $project->date_ntp->format('F j, Y') : '' }}</div>
                <div><strong>Contractor:</strong> {{ $project->contractor }}</div>
                <div><strong>Contract Amount:</strong> ₱ {{ number_format($project->contract_amount, 2) }}</div>
                <div><strong>Project Duration:</strong> {{ $project->project_duration }}</div>
                <div><strong>Actual Start Date:</strong> {{ $project->actual_start_date ? $project->actual_start_date->format('F j, Y') : '' }}</div>
                <div><strong>Target Date of Completion:</strong> {{ $project->target_date_completion ? $project->target_date_completion->format('F j, Y') : '' }}</div>
                <div><strong>Revised Target Date:</strong> {{ $project->revised_target_date_completion ? $project->revised_target_date_completion->format('F j, Y') : 'N/A' }}</div>
            </div>
            <div id="editContractFormBackdrop" class="lfp-inline-modal-backdrop{{ old('section') === 'contract' ? ' is-visible' : '' }}" aria-hidden="{{ old('section') === 'contract' ? 'false' : 'true' }}"></div>
            <div id="editContractFormWrapper" class="lfp-inline-modal{{ old('section') === 'contract' ? ' is-visible' : '' }}" data-inline-modal="true" role="dialog" aria-modal="true" aria-labelledby="editContractModalTitle" aria-hidden="{{ old('section') === 'contract' ? 'false' : 'true' }}" style="display: {{ old('section') === 'contract' ? 'block' : 'none' }};">
                <div class="lfp-inline-modal-header">
                    <h3 id="editContractModalTitle" style="color: #00267C; font-size: 15px; font-weight: 700; margin: 0;">Edit Contract Information</h3>
                    <button type="button" class="lfp-inline-modal-close" data-toggle="inline-cancel" data-target="editContractForm" aria-label="Close contract information editor">&times;</button>
                </div>
                <div class="lfp-inline-modal-body">
            <form id="editContractForm" action="{{ route('locally-funded-project.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="section" value="contract">

                <div class="contract-form-grid" style="display: grid; grid-template-columns: repeat(3, minmax(260px, 1fr)); gap: 20px;">
                    @php
                        $selectedModeOfProcurement = old('mode_of_procurement', $project->mode_of_procurement);
                        $selectedModeOfProcurementNorm = strtolower(trim((string) $selectedModeOfProcurement));
                        $knownModes = ['admin', 'contract'];
                        $hasModeOption = in_array($selectedModeOfProcurementNorm, $knownModes, true);

                        $selectedImplementingUnit = old('implementing_unit', $project->implementing_unit);
                        $selectedImplementingUnitNorm = strtolower(trim((string) $selectedImplementingUnit));
                        $knownImplementingUnits = ['provincial lgu', 'municipal lgu', 'barangay lgu'];
                        $hasImplementingOption = in_array($selectedImplementingUnitNorm, $knownImplementingUnits, true);
                    @endphp
                    <div>
                        <label for="mode_of_procurement" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Mode of Procurement *</label>
                        <select id="mode_of_procurement" name="mode_of_procurement" required
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box; background-color: white;">
                            <option value="">-- Select Mode of Procurement --</option>
                            <option value="admin" {{ $selectedModeOfProcurementNorm === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="contract" {{ $selectedModeOfProcurementNorm === 'contract' ? 'selected' : '' }}>Contract</option>
                            @if(!$hasModeOption && trim((string) $selectedModeOfProcurement) !== '')
                                <option value="{{ $selectedModeOfProcurement }}" selected>{{ $selectedModeOfProcurement }}</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <label for="implementing_unit" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Implementing Unit *</label>
                        <select id="implementing_unit" name="implementing_unit" required
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box; background-color: white;">
                            <option value="">-- Select Implementing Unit --</option>
                            <option value="Provincial LGU" {{ $selectedImplementingUnitNorm === 'provincial lgu' ? 'selected' : '' }}>Provincial LGU</option>
                            <option value="Municipal LGU" {{ $selectedImplementingUnitNorm === 'municipal lgu' ? 'selected' : '' }}>Municipal LGU</option>
                            <option value="Barangay LGU" {{ $selectedImplementingUnitNorm === 'barangay lgu' ? 'selected' : '' }}>Barangay LGU</option>
                            @if(!$hasImplementingOption && trim((string) $selectedImplementingUnit) !== '')
                                <option value="{{ $selectedImplementingUnit }}" selected>{{ $selectedImplementingUnit }}</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <label for="date_posting_itb" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Date of Posting (ITB) *</label>
                        <input type="date" id="date_posting_itb" name="date_posting_itb" value="{{ old('date_posting_itb', $project->date_posting_itb ? $project->date_posting_itb->format('Y-m-d') : '') }}" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="date_bid_opening" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Date of Bid Opening *</label>
                        <input type="date" id="date_bid_opening" name="date_bid_opening" value="{{ old('date_bid_opening', $project->date_bid_opening ? $project->date_bid_opening->format('Y-m-d') : '') }}" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="date_noa" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Date of NOA *</label>
                        <input type="date" id="date_noa" name="date_noa" value="{{ old('date_noa', $project->date_noa ? $project->date_noa->format('Y-m-d') : '') }}" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="date_ntp" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Date of NTP *</label>
                        <input type="date" id="date_ntp" name="date_ntp" value="{{ old('date_ntp', $project->date_ntp ? $project->date_ntp->format('Y-m-d') : '') }}" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="contractor" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Contractor *</label>
                        <input type="text" id="contractor" name="contractor" value="{{ old('contractor', $project->contractor) }}" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="contract_amount" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Contract Amount *</label>
                        <input type="text" id="contract_amount" name="contract_amount" value="{{ old('contract_amount', number_format((float)$project->contract_amount, 2, '.', ',')) }}" placeholder="0.00" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="project_duration" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Project Duration *</label>
                        <input type="text" id="project_duration" name="project_duration" value="{{ old('project_duration', $project->project_duration) }}" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="actual_start_date" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Actual Start Date *</label>
                        <input type="date" id="actual_start_date" name="actual_start_date" value="{{ old('actual_start_date', $project->actual_start_date ? $project->actual_start_date->format('Y-m-d') : '') }}" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="target_date_completion" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Target Date of Completion *</label>
                        <input type="date" id="target_date_completion" name="target_date_completion" value="{{ old('target_date_completion', $project->target_date_completion ? $project->target_date_completion->format('Y-m-d') : '') }}" required
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="revised_target_date_completion" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Revised Target Date of Completion</label>
                        <input type="date" id="revised_target_date_completion" name="revised_target_date_completion" value="{{ old('revised_target_date_completion', $project->revised_target_date_completion ? $project->revised_target_date_completion->format('Y-m-d') : '') }}"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>

                    <div>
                        <label for="actual_date_completion" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Actual Date of Completion</label>
                        <input type="date" id="actual_date_completion" name="actual_date_completion" value="{{ old('actual_date_completion', $project->actual_date_completion ? $project->actual_date_completion->format('Y-m-d') : '') }}"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.3s ease; box-sizing: border-box;">
                    </div>
                </div>

                <div style="margin-top: 16px; display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="submit" style="padding: 8px 16px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;"><i class="fas fa-check" style="margin-right: 8px;"></i>Save Changes</button>
                    <button type="button" data-toggle="inline-cancel" data-target="editContractForm" style="padding: 8px 16px; background-color: #6b7280; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;"><i class="fas fa-times" style="margin-right: 8px;"></i>Cancel</button>
                </div>
            </form>
                </div>
            </div>
        </div>

        @php
            $months = [
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
            ];
            $statusOptions = [
                ['value' => 'COMPLETED', 'label' => 'Completed'],
                ['value' => 'ONGOING', 'label' => 'On-going'],
                ['value' => 'BID EVALUATION/OPENING', 'label' => 'Bid Evaluation/Opening'],
                ['value' => 'NOA ISSUANCE', 'label' => 'NOA Issuance'],
                ['value' => 'DED PREPARATION', 'label' => 'DED Preparation'],
                ['value' => 'NOT YET STARTED', 'label' => 'Not Yet Started'],
                ['value' => 'ITB/AD POSTED', 'label' => 'ITB/AD Posted'],
                ['value' => 'TERMINATED', 'label' => 'Terminated'],
                ['value' => 'CANCELLED', 'label' => 'Cancelled'],
            ];
            $statusOptionValues = array_column($statusOptions, 'value');
            $statusLabelMap = [
                'COMPLETED' => 'Completed',
                'ONGOING' => 'On-going',
                'BID EVALUATION/OPENING' => 'Bid Evaluation/Opening',
                'NOA ISSUANCE' => 'NOA Issuance',
                'DED PREPARATION' => 'DED Preparation',
                'NOT YET STARTED' => 'Not Yet Started',
                'ITB/AD POSTED' => 'ITB/AD Posted',
                'TERMINATED' => 'Terminated',
                'CANCELLED' => 'Cancelled',
                'TERMINATED/CANCEL' => 'Terminated/Cancelled',
                'PROCUREMENT' => 'Procurement',
            ];
            $statusLabel = function ($value) use ($statusLabelMap) {
                return $statusLabelMap[$value] ?? $value;
            };
            $statusBadge = function ($value) use ($statusLabel) {
                if ($value === null || $value === '') {
                    return '<span style="color: #6b7280;">-</span>';
                }
                $colors = [
                    'COMPLETED' => ['#dcfce7', '#166534'],
                    'ONGOING' => ['#dbeafe', '#1d4ed8'],
                    'BID EVALUATION/OPENING' => ['#fef3c7', '#92400e'],
                    'NOA ISSUANCE' => ['#ede9fe', '#6b21a8'],
                    'DED PREPARATION' => ['#e0f2fe', '#0369a1'],
                    'NOT YET STARTED' => ['#f3f4f6', '#374151'],
                    'ITB/AD POSTED' => ['#d1fae5', '#065f46'],
                    'TERMINATED' => ['#fee2e2', '#991b1b'],
                    'CANCELLED' => ['#fecaca', '#7f1d1d'],
                    'TERMINATED/CANCEL' => ['#fee2e2', '#991b1b'],
                    'PROCUREMENT' => ['#e0f2fe', '#0369a1'],
                ];
                $color = $colors[$value] ?? ['#e5e7eb', '#374151'];
                return '<span style="display: inline-block; padding: 3px 8px; border-radius: 999px; background-color: ' . $color[0] . '; color: ' . $color[1] . '; font-size: 11px; font-weight: 600;">' . e($statusLabel($value)) . '</span>';
            };
        @endphp

        <div id="editPhysicalFormBackdrop" class="lfp-inline-modal-backdrop" aria-hidden="true"></div>
        <div id="physicalAccomplishmentSection" class="project-tab-panel lfp-inline-modal-section" data-tab-key="physical" role="tabpanel" aria-labelledby="tab-physical-accomplishment" data-inline-modal-section="true" data-inline-target="editPhysicalForm" style="margin-bottom: 24px; padding: 20px; border: 1px solid #00267C; border-radius: 10px; background-color: #ffffff;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; border-bottom: 2px solid #00267C; padding-bottom: 10px;">
                <h3 style="color: #00267C; font-size: 15px; font-weight: 700; margin: 0;">Physical Accomplishment</h3>
                <div style="display: flex; gap: 8px; align-items: center;">
                    @if(!$isLguAgencyUser)
                        <a href="#" data-toggle="inline-edit" data-target="editPhysicalForm" data-physical-toggle="true" style="padding: 6px 12px; background-color: #002C76; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 12px;"><i class="fas fa-edit" style="margin-right: 6px;"></i>Update</a>
                    @endif
                    <button type="button" class="lfp-inline-modal-close lfp-inline-modal-section-close" data-toggle="inline-cancel" data-target="editPhysicalForm" aria-label="Close physical accomplishment editor">&times;</button>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(2, minmax(300px, 1fr)); gap: 16px;">
                <div>
                    <strong>STATUS OF PROJECT (for FOU updating):</strong>
                    {!! $statusBadge($currentPhysical['status_project_fou'] ?? null) !!}
                    <details class="monthly-details" style="margin-top: 8px;">
                        <summary class="monthly-summary" style="cursor: pointer; color: #1d4ed8; background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">View monthly Status</summary>
                        <div style="margin-top: 10px;">
                            <div style="display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px; font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                <div>Month</div>
                                <div>Status</div>
                                <div>Date & Time</div>
                                <div>Updated By</div>
                            </div>
                            <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="physical">
                                <div style="margin-top: 6px; color: #6b7280; display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px;">
                                    @foreach($months as $monthNumber => $monthName)
                                        @php
                                            $row = $physicalByMonth[$monthNumber] ?? null;
                                            $value = $row['status_project_fou'] ?? '';
                                            $updatedAt = $row && $row['status_project_fou_updated_at']
                                                ? \Illuminate\Support\Carbon::parse($row['status_project_fou_updated_at'])->format('M d, Y h:i A')
                                                : '-';
                                            $updatedBy = $row['status_project_fou_updated_by_name'] ?? '-';
                                        @endphp
                                        <div>{{ $monthName }}</div>
                                        <div>
                                            <select name="status_project_fou[{{ $monthNumber }}]" data-physical-edit="true" data-month="{{ $monthNumber }}" disabled
                                                    style="width: 100%; min-width: 0; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: #f3f4f6;">
                                                <option value="">-- Select --</option>
                                                @if($value && !in_array($value, $statusOptionValues, true))
                                                    <option value="{{ $value }}" selected>{{ $statusLabel($value) }}</option>
                                                @endif
                                                @foreach($statusOptions as $option)
                                                    <option value="{{ $option['value'] }}" {{ $value === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #f3f4f6; color: #374151; font-size: 11px; font-weight: 600;">{{ $updatedAt }}</span></div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 600;">{{ $updatedBy }}</span></div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </details>
                </div>
                <div>
                    <strong>STATUS OF PROJECT PER SUBAYBAYAN (for RO updating):</strong>
                    {!! $statusBadge($currentPhysical['status_project_ro'] ?? null) !!}
                    <details class="monthly-details" style="margin-top: 8px;">
                        <summary class="monthly-summary" style="cursor: pointer; color: #1d4ed8; background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">View monthly Status</summary>
                        <div style="margin-top: 10px;">
                            <div style="display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px; font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                <div>Month</div>
                                <div>Status</div>
                                <div>Date & Time</div>
                                <div>Updated By</div>
                            </div>
                            <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="physical">
                                <div style="margin-top: 6px; color: #6b7280; display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px;">
                                    @foreach($months as $monthNumber => $monthName)
                                        @php
                                            $row = $physicalByMonth[$monthNumber] ?? null;
                                            $value = $row['status_project_ro'] ?? '';
                                            $updatedAt = $row && $row['status_project_ro_updated_at']
                                                ? \Illuminate\Support\Carbon::parse($row['status_project_ro_updated_at'])->format('M d, Y h:i A')
                                                : '-';
                                            $updatedBy = $row['status_project_ro_updated_by_name'] ?? '-';
                                        @endphp
                                        <div>{{ $monthName }}</div>
                                        <div>
                                            <select name="status_project_ro[{{ $monthNumber }}]" data-physical-edit="true" data-month="{{ $monthNumber }}" data-ro-only="true" {{ !(Auth::user()->agency === 'DILG' && Auth::user()->province === 'Regional Office') ? 'disabled' : '' }}
                                                    style="width: 100%; min-width: 0; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: #f3f4f6;">
                                                <option value="">-- Select --</option>
                                                @if($value && !in_array($value, $statusOptionValues, true))
                                                    <option value="{{ $value }}" selected>{{ $statusLabel($value) }}</option>
                                                @endif
                                                @foreach($statusOptions as $option)
                                                    <option value="{{ $option['value'] }}" {{ $value === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #f3f4f6; color: #374151; font-size: 11px; font-weight: 600;">{{ $updatedAt }}</span></div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 600;">{{ $updatedBy }}</span></div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </details>
                </div>

                <div>
                    <strong>% of Accomplishment (for FOU updating):</strong>
                    {!! $statusBadge(isset($currentPhysical['accomplishment_pct']) ? number_format((float)$currentPhysical['accomplishment_pct'], 2) . '%' : null) !!}
                    <details class="monthly-details" style="margin-top: 8px;">
                        <summary class="monthly-summary" style="cursor: pointer; color: #1d4ed8; background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">View monthly Status</summary>
                        <div style="margin-top: 10px;">
                            <div style="display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px; font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                <div>Month</div>
                                <div>Status</div>
                                <div>Date & Time</div>
                                <div>Updated By</div>
                            </div>
                            <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="physical">
                                <div style="margin-top: 6px; color: #6b7280; display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px;">
                                    @foreach($months as $monthNumber => $monthName)
                                        @php
                                            $row = $physicalByMonth[$monthNumber] ?? null;
                                            $value = $row['accomplishment_pct'] ?? '';
                                            $updatedAt = $row && $row['accomplishment_pct_updated_at']
                                                ? \Illuminate\Support\Carbon::parse($row['accomplishment_pct_updated_at'])->format('M d, Y h:i A')
                                                : '-';
                                            $updatedBy = $row['accomplishment_pct_updated_by_name'] ?? '-';
                                        @endphp
                                        <div>{{ $monthName }}</div>
                                        <div>
                                            <input type="number" step="0.01" min="0" max="100" name="accomplishment_pct[{{ $monthNumber }}]" value="{{ $value }}" data-physical-edit="true" data-month="{{ $monthNumber }}" disabled style="width: 100%; min-width: 0; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: #f3f4f6;">
                                        </div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #f3f4f6; color: #374151; font-size: 11px; font-weight: 600;">{{ $updatedAt }}</span></div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 600;">{{ $updatedBy }}</span></div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </details>
                </div>
                <div>
                    <strong>% of Accomplishment per Subaybayan (for RO updating):</strong>
                    {!! $statusBadge(isset($currentPhysical['accomplishment_pct_ro']) ? number_format((float)$currentPhysical['accomplishment_pct_ro'], 2) . '%' : null) !!}
                    <details class="monthly-details" style="margin-top: 8px;">
                        <summary class="monthly-summary" style="cursor: pointer; color: #1d4ed8; background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">View monthly Status</summary>
                        <div style="margin-top: 10px;">
                            <div style="display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px; font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                <div>Month</div>
                                <div>Status</div>
                                <div>Date & Time</div>
                                <div>Updated By</div>
                            </div>
                            <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="physical">
                                <div style="margin-top: 6px; color: #6b7280; display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px;">
                                    @foreach($months as $monthNumber => $monthName)
                                        @php
                                            $row = $physicalByMonth[$monthNumber] ?? null;
                                            $value = $row['accomplishment_pct_ro'] ?? '';
                                            $updatedAt = $row && $row['accomplishment_pct_ro_updated_at']
                                                ? \Illuminate\Support\Carbon::parse($row['accomplishment_pct_ro_updated_at'])->format('M d, Y h:i A')
                                                : '-';
                                            $updatedBy = $row['accomplishment_pct_ro_updated_by_name'] ?? '-';
                                        @endphp
                                        <div>{{ $monthName }}</div>
                                        <div>
                                            <input type="number" step="0.01" min="0" max="100" name="accomplishment_pct_ro[{{ $monthNumber }}]" value="{{ $value }}" data-physical-edit="true" data-month="{{ $monthNumber }}" data-ro-only="true" {{ !(Auth::user()->agency === 'DILG' && Auth::user()->province === 'Regional Office') ? 'disabled' : '' }} style="width: 100%; min-width: 0; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: #f3f4f6;">
                                        </div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #f3f4f6; color: #374151; font-size: 11px; font-weight: 600;">{{ $updatedAt }}</span></div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 600;">{{ $updatedBy }}</span></div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </details>
                </div>

                <div>
                    <strong>Slippage (for FOU updating):</strong>
                    {!! $statusBadge(isset($currentPhysical['slippage']) ? number_format((float)$currentPhysical['slippage'], 2) . '%' : null) !!}
                    <details class="monthly-details" style="margin-top: 8px;">
                        <summary class="monthly-summary" style="cursor: pointer; color: #1d4ed8; background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">View monthly Status</summary>
                        <div style="margin-top: 10px;">
                            <div style="display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px; font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                <div>Month</div>
                                <div>Status</div>
                                <div>Date & Time</div>
                                <div>Updated By</div>
                            </div>
                            <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="physical">
                                <div style="margin-top: 6px; color: #6b7280; display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px;">
                                    @foreach($months as $monthNumber => $monthName)
                                        @php
                                            $row = $physicalByMonth[$monthNumber] ?? null;
                                            $value = $row['slippage'] ?? '';
                                            $updatedAt = $row && $row['slippage_updated_at']
                                                ? \Illuminate\Support\Carbon::parse($row['slippage_updated_at'])->format('M d, Y h:i A')
                                                : '-';
                                            $updatedBy = $row['slippage_updated_by_name'] ?? '-';
                                        @endphp
                                        <div>{{ $monthName }}</div>
                                        <div>
                                            <input type="number" step="0.01" min="0" max="100" name="slippage[{{ $monthNumber }}]" value="{{ $value }}" data-physical-edit="true" data-month="{{ $monthNumber }}" disabled style="width: 100%; min-width: 0; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: #f3f4f6;">
                                        </div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #f3f4f6; color: #374151; font-size: 11px; font-weight: 600;">{{ $updatedAt }}</span></div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 600;">{{ $updatedBy }}</span></div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </details>
                </div>
                <div>
                    <strong>Slippage as to SubayBAYAN (for RO updating):</strong>
                    {!! $statusBadge(isset($currentPhysical['slippage_ro']) ? number_format((float)$currentPhysical['slippage_ro'], 2) . '%' : null) !!}
                    <details class="monthly-details" style="margin-top: 8px;">
                        <summary class="monthly-summary" style="cursor: pointer; color: #1d4ed8; background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">View monthly Status</summary>
                        <div style="margin-top: 10px;">
                            <div style="display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px; font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                <div>Month</div>
                                <div>Status</div>
                                <div>Date & Time</div>
                                <div>Updated By</div>
                            </div>
                            <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="physical">
                                <div style="margin-top: 6px; color: #6b7280; display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px;">
                                    @foreach($months as $monthNumber => $monthName)
                                        @php
                                            $row = $physicalByMonth[$monthNumber] ?? null;
                                            $value = $row['slippage_ro'] ?? '';
                                            $updatedAt = $row && $row['slippage_ro_updated_at']
                                                ? \Illuminate\Support\Carbon::parse($row['slippage_ro_updated_at'])->format('M d, Y h:i A')
                                                : '-';
                                            $updatedBy = $row['slippage_ro_updated_by_name'] ?? '-';
                                        @endphp
                                        <div>{{ $monthName }}</div>
                                        <div>
                                            <input type="number" step="0.01" min="0" max="100" name="slippage_ro[{{ $monthNumber }}]" value="{{ $value }}" data-physical-edit="true" data-month="{{ $monthNumber }}" data-ro-only="true" {{ !(Auth::user()->agency === 'DILG' && Auth::user()->province === 'Regional Office') ? 'disabled' : '' }} style="width: 100%; min-width: 0; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: #f3f4f6;">
                                        </div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #f3f4f6; color: #374151; font-size: 11px; font-weight: 600;">{{ $updatedAt }}</span></div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 600;">{{ $updatedBy }}</span></div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </details>
                </div>

                <form method="POST" action="{{ route('locally-funded-project.update', $project) }}" style="display: flex; flex-direction: column; align-items: flex-start; gap: 6px; margin-top: 6px;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="physical">
                      <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; font-size: 12px; color: #6b7280;">
                        <strong style="font-size: 14px; font-weight: 600; color: #000000;">Actual Date of Completion:</strong>
                        <span>{{ $project->actual_date_completion ? $project->actual_date_completion->format('F j, Y') : 'N/A' }}</span>
                        <span>Updated by: {{ $actualCompletionUpdatedByName ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                        <input type="date" id="actual_date_completion_physical" name="actual_date_completion" value="{{ old('actual_date_completion', $project->actual_date_completion ? $project->actual_date_completion->format('Y-m-d') : '') }}"
                               data-physical-edit="true" data-month="{{ $currentMonth }}" disabled
                               style="padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: #f3f4f6;">
                    </div>
                </form>

                <div>
                    <strong>
                        Risk as to aging:
                        <span title="Ahead (+ value of slippage)&#10;On Schedule (0%)&#10;No Risk (-0.01% to -4.99% slippage)&#10;Low Risk (-5% to -9.99% slippage)&#10;Moderate Risk (-10% to -14.99% slippage)&#10;High Risk (-15% and higher slippage)" style="display: inline-flex; align-items: center; justify-content: center; width: 16px; height: 16px; margin-left: 6px; border-radius: 999px; background-color: #e5e7eb; color: #374151; font-size: 11px; font-weight: 700; cursor: help;">i</span>
                    </strong>
                    {!! $statusBadge($currentPhysical['risk_aging'] ?? null) !!}
                    @if((int) $project->id === 25)
                        <span style="margin-left: 6px; color: #6b7280; font-size: 12px; font-weight: 600;">No Update</span>
                    @endif
                    <details class="monthly-details" style="margin-top: 8px;">
                        <summary class="monthly-summary" style="cursor: pointer; color: #1d4ed8; background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">View monthly Status</summary>
                        <div style="margin-top: 10px;">
                            <div style="display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px; font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                <div>Month</div>
                                <div>Status</div>
                                <div>Date & Time</div>
                                <div>Updated By</div>
                            </div>
                            <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="physical">
                                <div style="margin-top: 6px; color: #6b7280; display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px;">
                                    @foreach($months as $monthNumber => $monthName)
                                        @php
                                            $row = $physicalByMonth[$monthNumber] ?? null;
                                            $value = $row['risk_aging'] ?? '';
                                            $updatedAt = $row && $row['risk_aging_updated_at']
                                                ? \Illuminate\Support\Carbon::parse($row['risk_aging_updated_at'])->format('M d, Y h:i A')
                                                : '-';
                                            $updatedBy = $row['risk_aging_updated_by_name'] ?? '-';
                                        @endphp
                                        <div>{{ $monthName }}</div>
                                        <div>
                                            <select name="risk_aging[{{ $monthNumber }}]" data-physical-edit="true" data-month="{{ $monthNumber }}" data-ro-only="true" {{ !(Auth::user()->agency === 'DILG' && Auth::user()->province === 'Regional Office') ? 'disabled' : '' }}
                                                    style="width: 100%; min-width: 0; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: #f3f4f6;">
                                                <option value="">-- Select --</option>
                                                @if($value !== '' && !in_array($value, ['Ahead', 'On Schedule', 'No Risk', 'Low Risk', 'Moderate Risk', 'High Risk'], true))
                                                    <option value="{{ $value }}" selected>{{ $value }}</option>
                                                @endif
                                                <option value="Ahead" {{ $value === 'Ahead' ? 'selected' : '' }}>Ahead</option>
                                                <option value="On Schedule" {{ $value === 'On Schedule' ? 'selected' : '' }}>On Schedule</option>
                                                <option value="No Risk" {{ $value === 'No Risk' ? 'selected' : '' }}>No Risk</option>
                                                <option value="Low Risk" {{ $value === 'Low Risk' ? 'selected' : '' }}>Low Risk</option>
                                                <option value="Moderate Risk" {{ $value === 'Moderate Risk' ? 'selected' : '' }}>Moderate Risk</option>
                                                <option value="High Risk" {{ $value === 'High Risk' ? 'selected' : '' }}>High Risk</option>
                                            </select>
                                        </div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #f3f4f6; color: #374151; font-size: 11px; font-weight: 600;">{{ $updatedAt }}</span></div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 600;">{{ $updatedBy }}</span></div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </details>
                </div>

                <div>
                    <strong>Issued with Non-Compliance (NC) Letters:</strong>
                    {!! $statusBadge($currentPhysical['nc_letters'] ?? null) !!}
                    <details class="monthly-details" style="margin-top: 8px;">
                        <summary class="monthly-summary" style="cursor: pointer; color: #1d4ed8; background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">View monthly Status</summary>
                        <div style="margin-top: 10px;">
                            <div style="display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px; font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                <div>Month</div>
                                <div>Status</div>
                                <div>Date & Time</div>
                                <div>Updated By</div>
                            </div>
                            <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="physical">
                                <div style="margin-top: 6px; color: #6b7280; display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px;">
                                    @foreach($months as $monthNumber => $monthName)
                                        @php
                                            $row = $physicalByMonth[$monthNumber] ?? null;
                                            $value = $row['nc_letters'] ?? '';
                                            $updatedAt = $row && $row['nc_letters_updated_at']
                                                ? \Illuminate\Support\Carbon::parse($row['nc_letters_updated_at'])->format('M d, Y h:i A')
                                                : '-';
                                            $updatedBy = $row['nc_letters_updated_by_name'] ?? '-';
                                            $ncColors = [
                                                'NC No. 1' => '#fef3c7',
                                                'NC No. 2' => '#fde68a',
                                                'NC No. 3' => '#fecaca',
                                                'No' => '#dcfce7',
                                            ];
                                            $ncTextColors = [
                                                'NC No. 1' => '#92400e',
                                                'NC No. 2' => '#78350f',
                                                'NC No. 3' => '#991b1b',
                                                'No' => '#166534',
                                            ];
                                            $bgColor = $ncColors[$value] ?? '#f3f4f6';
                                            $textColor = $ncTextColors[$value] ?? '#374151';
                                        @endphp
                                        <div>{{ $monthName }}</div>
                                        <div>
                                            <select name="nc_letters[{{ $monthNumber }}]" data-physical-edit="true" data-month="{{ $monthNumber }}" disabled
                                                    style="width: 100%; min-width: 0; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                                <option value="">-- Select --</option>
                                                <option value="NC No. 1" {{ $value === 'NC No. 1' ? 'selected' : '' }}>NC No. 1</option>
                                                <option value="NC No. 2" {{ $value === 'NC No. 2' ? 'selected' : '' }}>NC No. 2</option>
                                                <option value="NC No. 3" {{ $value === 'NC No. 3' ? 'selected' : '' }}>NC No. 3</option>
                                                <option value="No" {{ $value === 'No' ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #f3f4f6; color: #374151; font-size: 11px; font-weight: 600;">{{ $updatedAt }}</span></div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 600;">{{ $updatedBy }}</span></div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </details>
                </div>
                <div>
                    <strong>Remarks:</strong>
                    <form method="POST" action="{{ route('locally-funded-project.update', $project) }}" style="margin-top: 8px;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="physical">
                        <textarea name="physical_remarks" rows="3" data-physical-edit="true" data-month="{{ $currentMonth }}" disabled
                                  style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; resize: vertical; background-color: #f3f4f6;">{{ old('physical_remarks', $project->physical_remarks) }}</textarea>
                        <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                            <span><strong>Updated By:</strong> {{ $physicalRemarksUpdatedByName ?? '-' }}</span>
                            <span><strong>Date & Time:</strong> {{ $project->physical_remarks_updated_at ? $project->physical_remarks_updated_at->format('M d, Y h:i A') : '-' }}</span>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>

        <div id="editFinancialFormBackdrop" class="lfp-inline-modal-backdrop" aria-hidden="true"></div>
        <div id="financialAccomplishmentSection" class="project-tab-panel lfp-inline-modal-section" data-inline-modal-section="true" data-inline-target="editFinancialForm" data-tab-key="financial" role="tabpanel" aria-labelledby="tab-financial-accomplishment" style="margin-bottom: 24px; padding: 20px; border: 1px solid #00267C; border-radius: 10px; background-color: #ffffff;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; border-bottom: 2px solid #00267C; padding-bottom: 10px;">
                <h3 style="color: #00267C; font-size: 15px; font-weight: 700; margin: 0;">Financial Accomplishment (based on Subaybayan)</h3>
                <div style="display: flex; gap: 8px; align-items: center;">
                    @if(!$isLguAgencyUser)
                        <a href="#" data-toggle="inline-edit" data-target="editFinancialForm" data-financial-toggle="true" style="padding: 6px 12px; background-color: #002C76; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 12px;"><i class="fas fa-edit" style="margin-right: 6px;"></i>Update</a>
                    @endif
                    <button type="button" class="lfp-inline-modal-close lfp-inline-modal-section-close" data-toggle="inline-cancel" data-target="editFinancialForm" aria-label="Close financial accomplishment editor">&times;</button>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                <div>
                    <strong>Obligated Amount:</strong>
                    <span id="financialSum-obligation">{{ number_format((float) ($financialTotals['obligation'] ?? 0), 2) }}</span>
                    <details class="monthly-details" style="margin-top: 8px;">
                        <summary class="monthly-summary" style="cursor: pointer; color: #1d4ed8; background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">View monthly Status</summary>
                        <div style="margin-top: 10px;">
                            <div style="display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px; font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                <div>Month</div>
                                <div>Value</div>
                                <div>Date & Time</div>
                                <div>Updated By</div>
                            </div>
                            <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="financial">
                                <div style="margin-top: 6px; color: #6b7280; display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px;">
                                    @foreach($months as $monthNumber => $monthName)
                                        @php
                                            $row = $financialByMonth[$monthNumber] ?? null;
                                            $value = $row['obligation'] ?? '';
                                            $updatedAt = $row && $row['obligation_updated_at']
                                                ? \Illuminate\Support\Carbon::parse($row['obligation_updated_at'])->format('M d, Y h:i A')
                                                : '-';
                                            $updatedBy = $row && $row['obligation_updated_by']
                                                ? \Illuminate\Support\Facades\DB::table('tbusers')->where('idno', $row['obligation_updated_by'])->value(\Illuminate\Support\Facades\DB::raw("concat(fname, ' ', lname)"))
                                                : '-';
                                        @endphp
                                        <div>{{ $monthName }}</div>
                                        <div>
                                            <input type="number" step="0.01" min="0" name="obligation[{{ $monthNumber }}]" value="{{ $value }}" placeholder="-" data-financial-field="obligation" data-financial-edit="true" data-month="{{ $monthNumber }}" data-ro-only="true" disabled style="width: 100%; min-width: 0; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: #f3f4f6;">
                                        </div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #f3f4f6; color: #374151; font-size: 11px; font-weight: 600;">{{ $updatedAt }}</span></div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 600;">{{ $updatedBy }}</span></div>
                                    @endforeach
                                </div>
                                <div style="margin-top: 10px;">
                                    <button type="submit" data-financial-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                                </div>
                            </form>
                        </div>
                    </details>
                </div>

                <div>
                    <strong>Disbursed Amount:</strong>
                    <span id="financialSum-disbursed_amount">{{ number_format((float) ($financialTotals['disbursed_amount'] ?? 0), 2) }}</span>
                    <details class="monthly-details" style="margin-top: 8px;">
                        <summary class="monthly-summary" style="cursor: pointer; color: #1d4ed8; background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">View monthly Status</summary>
                        <div style="margin-top: 10px;">
                            <div style="display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px; font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                <div>Month</div>
                                <div>Value</div>
                                <div>Date & Time</div>
                                <div>Updated By</div>
                            </div>
                            <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="financial">
                                <div style="margin-top: 6px; color: #6b7280; display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px;">
                                    @foreach($months as $monthNumber => $monthName)
                                        @php
                                            $row = $financialByMonth[$monthNumber] ?? null;
                                            $value = $row['disbursed_amount'] ?? '';
                                            $updatedAt = $row && $row['disbursed_amount_updated_at']
                                                ? \Illuminate\Support\Carbon::parse($row['disbursed_amount_updated_at'])->format('M d, Y h:i A')
                                                : '-';
                                            $updatedBy = $row && $row['disbursed_amount_updated_by']
                                                ? \Illuminate\Support\Facades\DB::table('tbusers')->where('idno', $row['disbursed_amount_updated_by'])->value(\Illuminate\Support\Facades\DB::raw("concat(fname, ' ', lname)"))
                                                : '-';
                                        @endphp
                                        <div>{{ $monthName }}</div>
                                        <div>
                                            <input type="number" step="0.01" min="0" name="disbursed_amount[{{ $monthNumber }}]" value="{{ $value }}" placeholder="-" data-financial-field="disbursed_amount" data-financial-edit="true" data-month="{{ $monthNumber }}" data-ro-only="true" disabled style="width: 100%; min-width: 0; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: #f3f4f6;">
                                        </div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #f3f4f6; color: #374151; font-size: 11px; font-weight: 600;">{{ $updatedAt }}</span></div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 600;">{{ $updatedBy }}</span></div>
                                    @endforeach
                                </div>
                                <div style="margin-top: 10px;">
                                    <button type="submit" data-financial-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                                </div>
                            </form>
                        </div>
                    </details>
                </div>

                <div>
                    <strong>Reverted Amount:</strong>
                    <span id="financialSum-reverted_amount">{{ number_format((float) ($financialTotals['reverted_amount'] ?? 0), 2) }}</span>
                    <details class="monthly-details" style="margin-top: 8px;">
                        <summary class="monthly-summary" style="cursor: pointer; color: #1d4ed8; background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">View monthly Status</summary>
                        <div style="margin-top: 10px;">
                            <div style="display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px; font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                <div>Month</div>
                                <div>Value</div>
                                <div>Date & Time</div>
                                <div>Updated By</div>
                            </div>
                            <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="financial">
                                <div style="margin-top: 6px; color: #6b7280; display: grid; grid-template-columns: 120px 1fr 180px 140px; gap: 8px;">
                                    @foreach($months as $monthNumber => $monthName)
                                        @php
                                            $row = $financialByMonth[$monthNumber] ?? null;
                                            $value = $row['reverted_amount'] ?? '';
                                            $updatedAt = $row && $row['reverted_amount_updated_at']
                                                ? \Illuminate\Support\Carbon::parse($row['reverted_amount_updated_at'])->format('M d, Y h:i A')
                                                : '-';
                                            $updatedBy = $row && $row['reverted_amount_updated_by']
                                                ? \Illuminate\Support\Facades\DB::table('tbusers')->where('idno', $row['reverted_amount_updated_by'])->value(\Illuminate\Support\Facades\DB::raw("concat(fname, ' ', lname)"))
                                                : '-';
                                        @endphp
                                        <div>{{ $monthName }}</div>
                                        <div>
                                            <input type="number" step="0.01" min="0" name="reverted_amount[{{ $monthNumber }}]" value="{{ $value }}" placeholder="-" data-financial-field="reverted_amount" data-financial-edit="true" data-month="{{ $monthNumber }}" data-ro-only="true" disabled style="width: 100%; min-width: 0; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; background-color: #f3f4f6;">
                                        </div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #f3f4f6; color: #374151; font-size: 11px; font-weight: 600;">{{ $updatedAt }}</span></div>
                                        <div><span style="display: inline-block; padding: 3px 8px; border-radius: 999px; border: 1px solid #e5e7eb; background-color: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 600;">{{ $updatedBy }}</span></div>
                                    @endforeach
                                </div>
                                <div style="margin-top: 10px;">
                                    <button type="submit" data-financial-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                                </div>
                            </form>
                        </div>
                    </details>
                </div>

                <div>
                    <strong>Balance:</strong>
                    <span id="financialBalance">{{ number_format((float) $financialBalance, 2) }}</span>
                </div>

                <div>
                    <strong>Utilization Rate:</strong>
                    <span id="financialUtilizationRate" style="color: {{ (float) $financialUtilizationRate < 100 ? '#dc2626' : '#111827' }};">{{ number_format((float) $financialUtilizationRate, 2) . '%' }}</span>
                </div>

                <div>
                    <strong>Remarks:</strong>
                    <form method="POST" action="{{ route('locally-funded-project.update', $project) }}" style="margin-top: 8px;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="financial">
                        <textarea name="financial_remarks" rows="3" data-financial-edit="true" data-month="{{ $currentMonth }}" data-ro-only="true" disabled
                                  style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; resize: vertical; background-color: #f3f4f6;">{{ old('financial_remarks', $project->financial_remarks) }}</textarea>
                        <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                            <span><strong>Updated By:</strong> {{ $financialRemarksUpdatedByName ?? '-' }}</span>
                            <span><strong>Date & Time:</strong> {{ $project->financial_remarks_updated_at ? $project->financial_remarks_updated_at->format('M d, Y h:i A') : '-' }}</span>
                        </div>
                        <div style="margin-top: 8px;">
                            <button type="submit" data-financial-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="editMonitoringFormBackdrop" class="lfp-inline-modal-backdrop" aria-hidden="true"></div>
        <div id="monitoringInspectionSection" class="project-tab-panel lfp-inline-modal-section" data-inline-modal-section="true" data-inline-target="editMonitoringForm" data-tab-key="monitoring" role="tabpanel" aria-labelledby="tab-monitoring-inspection" style="margin-bottom: 24px; padding: 20px; border: 1px solid #00267C; border-radius: 10px; background-color: #ffffff;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; border-bottom: 2px solid #00267C; padding-bottom: 10px;">
                <h3 style="color: #00267C; font-size: 15px; font-weight: 700; margin: 0;">Monitoring/Inspection Activities</h3>
                <div style="display: flex; gap: 8px; align-items: center;">
                    @if(!$isLguAgencyUser)
                        <a href="#" data-toggle="inline-edit" data-target="editMonitoringForm" data-monitoring-toggle="true" style="padding: 6px 12px; background-color: #002C76; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 12px;"><i class="fas fa-edit" style="margin-right: 6px;"></i>Update</a>
                    @endif
                    <button type="button" class="lfp-inline-modal-close lfp-inline-modal-section-close" data-toggle="inline-cancel" data-target="editMonitoringForm" aria-label="Close monitoring editor">&times;</button>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
                <div style="padding: 16px; border: 1px solid #00267C; border-radius: 10px; background-color: #ffffff;">
                    <h4 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 700; color: #00267C;">DILG Provincial Office Activity</h4>
                    <div style="display: grid; gap: 12px;">
                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="po_monitoring_date" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Date of Monitoring by PO</label>
                            <input type="date" id="po_monitoring_date" name="po_monitoring_date" value="{{ old('po_monitoring_date', $project->po_monitoring_date ? $project->po_monitoring_date->format('Y-m-d') : '') }}"
                                   data-monitoring-edit="true" disabled
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $poMonitoringDateUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->po_monitoring_date_updated_at ? $project->po_monitoring_date_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            <div style="margin-top: 8px;">
                                <button type="submit" data-monitoring-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="po_final_inspection" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">PO Conducted Final Inspection?</label>
                            <select id="po_final_inspection" name="po_final_inspection" data-monitoring-edit="true" disabled
                                    style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                                <option value="">-- Select --</option>
                                <option value="Yes" {{ old('po_final_inspection', $project->po_final_inspection) === 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('po_final_inspection', $project->po_final_inspection) === 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $poFinalInspectionUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->po_final_inspection_updated_at ? $project->po_final_inspection_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            <div style="margin-top: 8px;">
                                <button type="submit" data-monitoring-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="po_remarks" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Remarks</label>
                            <textarea id="po_remarks" name="po_remarks" rows="3" data-monitoring-edit="true" disabled
                                      style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; resize: vertical; background-color: #f3f4f6;">{{ old('po_remarks', $project->po_remarks) }}</textarea>
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $poRemarksUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->po_remarks_updated_at ? $project->po_remarks_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            <div style="margin-top: 8px;">
                                <button type="submit" data-monitoring-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div style="padding: 16px; border: 1px solid #00267C; border-radius: 10px; background-color: #ffffff;">
                    <h4 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 700; color: #00267C;">DILG Regional Office Activity</h4>
                    <div style="display: grid; gap: 12px;">
                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="ro_monitoring_date" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Date of Monitoring by RO</label>
                            <input type="date" id="ro_monitoring_date" name="ro_monitoring_date" value="{{ old('ro_monitoring_date', $project->ro_monitoring_date ? $project->ro_monitoring_date->format('Y-m-d') : '') }}"
                                   data-monitoring-edit="true" data-ro-only="true" disabled
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $roMonitoringDateUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->ro_monitoring_date_updated_at ? $project->ro_monitoring_date_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            <div style="margin-top: 8px;">
                                <button type="submit" data-monitoring-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="ro_final_inspection" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">RO Conducted Final Inspection?</label>
                            <select id="ro_final_inspection" name="ro_final_inspection" data-monitoring-edit="true" data-ro-only="true" disabled
                                    style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                                <option value="">-- Select --</option>
                                <option value="Yes" {{ old('ro_final_inspection', $project->ro_final_inspection) === 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('ro_final_inspection', $project->ro_final_inspection) === 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $roFinalInspectionUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->ro_final_inspection_updated_at ? $project->ro_final_inspection_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            <div style="margin-top: 8px;">
                                <button type="submit" data-monitoring-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="ro_remarks" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Remarks</label>
                            <textarea id="ro_remarks" name="ro_remarks" rows="3" data-monitoring-edit="true" data-ro-only="true" disabled
                                      style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; resize: vertical; background-color: #f3f4f6;">{{ old('ro_remarks', $project->ro_remarks) }}</textarea>
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $roRemarksUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->ro_remarks_updated_at ? $project->ro_remarks_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            <div style="margin-top: 8px;">
                                <button type="submit" data-monitoring-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="editPostImplementationFormBackdrop" class="lfp-inline-modal-backdrop" aria-hidden="true"></div>
        <div id="postImplementationSection" class="project-tab-panel lfp-inline-modal-section" data-inline-modal-section="true" data-inline-target="editPostImplementationForm" data-tab-key="post-implementation" role="tabpanel" aria-labelledby="tab-post-implementation" style="margin-bottom: 24px; padding: 20px; border: 1px solid #00267C; border-radius: 10px; background-color: #ffffff;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; border-bottom: 2px solid #00267C; padding-bottom: 10px;">
                <h3 style="color: #00267C; font-size: 15px; font-weight: 700; margin: 0;">Post Implementation Requirements</h3>
                <div style="display: flex; gap: 8px; align-items: center;">
                    @if(!$isLguAgencyUser)
                        <a href="#" data-toggle="inline-edit" data-target="editPostImplementationForm" data-post-implementation-toggle="true" style="padding: 6px 12px; background-color: #002C76; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 12px;"><i class="fas fa-edit" style="margin-right: 6px;"></i>Update</a>
                    @endif
                    <button type="button" class="lfp-inline-modal-close lfp-inline-modal-section-close" data-toggle="inline-cancel" data-target="editPostImplementationForm" aria-label="Close post implementation editor">&times;</button>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
                <div style="padding: 14px; border: 1px solid #00267C; border-radius: 10px; background-color: #ffffff;">
                    <h4 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 700; color: #00267C;">PCR Submission</h4>
                    <div style="display: grid; gap: 12px;">
                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="pcr_submission_deadline" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Deadline of PCR Submission</label>
                            @php
                                $computedPcrDeadline = $project->target_date_completion
                                    ? $project->target_date_completion->copy()->addDays(30)
                                    : $project->pcr_submission_deadline;
                            @endphp
                            <input type="date" id="pcr_submission_deadline" name="pcr_submission_deadline" value="{{ old('pcr_submission_deadline', $computedPcrDeadline ? $computedPcrDeadline->format('Y-m-d') : '') }}"
                                   data-post-implementation-edit="true" data-ro-only="true" disabled
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                            @if($pcrSubmissionDeadlineUpdatedByName || $project->pcr_submission_deadline_updated_at)
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $pcrSubmissionDeadlineUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->pcr_submission_deadline_updated_at ? $project->pcr_submission_deadline_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            @endif
                            <div style="margin-top: 8px;">
                                <button type="submit" data-post-implementation-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="pcr_date_submitted_to_po" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Date Submitted to PO</label>
                            <input type="date" id="pcr_date_submitted_to_po" name="pcr_date_submitted_to_po" value="{{ old('pcr_date_submitted_to_po', $project->pcr_date_submitted_to_po ? $project->pcr_date_submitted_to_po->format('Y-m-d') : '') }}"
                                   data-post-implementation-edit="true" disabled
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                            @if($pcrDateSubmittedToPoUpdatedByName || $project->pcr_date_submitted_to_po_updated_at)
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $pcrDateSubmittedToPoUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->pcr_date_submitted_to_po_updated_at ? $project->pcr_date_submitted_to_po_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            @endif
                            <div style="margin-top: 8px;">
                                <button type="submit" data-post-implementation-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="pcr_mov_file" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Upload PCR MOV</label>
                            <input type="file" id="pcr_mov_file" name="pcr_mov_file" accept="application/pdf,image/*"
                                   data-post-implementation-edit="true" disabled
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                            @if($project->pcr_mov_file_path)
                                <div style="display: flex; gap: 8px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                    <a href="{{ route('locally-funded-project.view-pcr-mov', $project) }}" target="_blank" style="padding: 4px 8px; background-color: #0369a1; color: white; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: 600;">
                                        <i class="fas fa-eye" style="margin-right: 4px;"></i>View
                                    </a>
                                    <span>Uploaded: {{ basename($project->pcr_mov_file_path) }}</span>
                                </div>
                            @endif
                            @if($project->pcr_mov_uploaded_at)
                                <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                    <span><strong>Submitted By:</strong> {{ $pcrMovUploadedByName ?? '-' }}</span>
                                    <span><strong>Date & Time:</strong> {{ $project->pcr_mov_uploaded_at->format('M d, Y h:i A') }}</span>
                                </div>
                            @endif
                            <div style="margin-top: 8px;">
                                <button type="submit" data-post-implementation-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="pcr_date_received_by_ro" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Date Received by RO</label>
                            <input type="date" id="pcr_date_received_by_ro" name="pcr_date_received_by_ro" value="{{ old('pcr_date_received_by_ro', $project->pcr_date_received_by_ro ? $project->pcr_date_received_by_ro->format('Y-m-d') : '') }}"
                                   data-post-implementation-edit="true" data-ro-only="true" disabled
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                            @if($pcrDateReceivedByRoUpdatedByName || $project->pcr_date_received_by_ro_updated_at)
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $pcrDateReceivedByRoUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->pcr_date_received_by_ro_updated_at ? $project->pcr_date_received_by_ro_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            @endif
                            <div style="margin-top: 8px;">
                                <button type="submit" data-post-implementation-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="pcr_remarks" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Remarks</label>
                            <textarea id="pcr_remarks" name="pcr_remarks" rows="3" data-post-implementation-edit="true" disabled
                                      style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; resize: vertical; background-color: #f3f4f6;">{{ old('pcr_remarks', $project->pcr_remarks) }}</textarea>
                            @if($pcrRemarksUpdatedByName || $project->pcr_remarks_updated_at)
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $pcrRemarksUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->pcr_remarks_updated_at ? $project->pcr_remarks_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            @endif
                            <div style="margin-top: 8px;">
                                <button type="submit" data-post-implementation-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div style="padding: 14px; border: 1px solid #00267C; border-radius: 10px; background-color: #ffffff;">
                    <h4 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 700; color: #00267C;">RSSA Report</h4>
                    <div style="display: grid; gap: 12px;">
                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="rssa_report_deadline" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Deadline of RSSA Report</label>
                            @php
                                $computedRssaDeadline = $project->target_date_completion
                                    ? $project->target_date_completion->copy()->addDays(395)
                                    : $project->rssa_report_deadline;
                            @endphp
                            <input type="date" id="rssa_report_deadline" name="rssa_report_deadline" value="{{ old('rssa_report_deadline', $computedRssaDeadline ? $computedRssaDeadline->format('Y-m-d') : '') }}"
                                   data-post-implementation-edit="true" data-ro-only="true" disabled
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                            @if($rssaReportDeadlineUpdatedByName || $project->rssa_report_deadline_updated_at)
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $rssaReportDeadlineUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->rssa_report_deadline_updated_at ? $project->rssa_report_deadline_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            @endif
                            <div style="margin-top: 8px;">
                                <button type="submit" data-post-implementation-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="rssa_submission_status" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Status of Submission</label>
                            <select id="rssa_submission_status" name="rssa_submission_status" data-post-implementation-edit="true" disabled
                                    style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                                <option value="">-- Select Status --</option>
                                <option value="Not yet assessed" {{ old('rssa_submission_status', $project->rssa_submission_status) === 'Not yet assessed' ? 'selected' : '' }}>Not yet assessed</option>
                                <option value="Draft" {{ old('rssa_submission_status', $project->rssa_submission_status) === 'Draft' ? 'selected' : '' }}>Draft</option>
                                <option value="Returned" {{ old('rssa_submission_status', $project->rssa_submission_status) === 'Returned' ? 'selected' : '' }}>Returned</option>
                                <option value="Submitted to C/MLGOO" {{ old('rssa_submission_status', $project->rssa_submission_status) === 'Submitted to C/MLGOO' ? 'selected' : '' }}>Submitted to C/MLGOO</option>
                                <option value="Submitted to PO" {{ old('rssa_submission_status', $project->rssa_submission_status) === 'Submitted to PO' ? 'selected' : '' }}>Submitted to PO</option>
                                <option value="Submitted to RO" {{ old('rssa_submission_status', $project->rssa_submission_status) === 'Submitted to RO' ? 'selected' : '' }}>Submitted to RO</option>
                                <option value="Submitted to PMED" {{ old('rssa_submission_status', $project->rssa_submission_status) === 'Submitted to PMED' ? 'selected' : '' }}>Submitted to PMED</option>
                                <option value="Vetted" {{ old('rssa_submission_status', $project->rssa_submission_status) === 'Vetted' ? 'selected' : '' }}>Vetted</option>
                            </select>
                            @if($rssaSubmissionStatusUpdatedByName || $project->rssa_submission_status_updated_at)
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $rssaSubmissionStatusUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->rssa_submission_status_updated_at ? $project->rssa_submission_status_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            @endif
                            <div style="margin-top: 8px;">
                                <button type="submit" data-post-implementation-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="rssa_date_submitted_to_po" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Date Submitted to PO</label>
                            <input type="date" id="rssa_date_submitted_to_po" name="rssa_date_submitted_to_po" value="{{ old('rssa_date_submitted_to_po', $project->rssa_date_submitted_to_po ? $project->rssa_date_submitted_to_po->format('Y-m-d') : '') }}"
                                   data-post-implementation-edit="true" disabled
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                            @if($rssaDateSubmittedToPoUpdatedByName || $project->rssa_date_submitted_to_po_updated_at)
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $rssaDateSubmittedToPoUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->rssa_date_submitted_to_po_updated_at ? $project->rssa_date_submitted_to_po_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            @endif
                            <div style="margin-top: 8px;">
                                <button type="submit" data-post-implementation-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="rssa_date_received_by_ro" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Date Received by RO</label>
                            <input type="date" id="rssa_date_received_by_ro" name="rssa_date_received_by_ro" value="{{ old('rssa_date_received_by_ro', $project->rssa_date_received_by_ro ? $project->rssa_date_received_by_ro->format('Y-m-d') : '') }}"
                                   data-post-implementation-edit="true" data-ro-only="true" disabled
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                            @if($rssaDateReceivedByRoUpdatedByName || $project->rssa_date_received_by_ro_updated_at)
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $rssaDateReceivedByRoUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->rssa_date_received_by_ro_updated_at ? $project->rssa_date_received_by_ro_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            @endif
                            <div style="margin-top: 8px;">
                                <button type="submit" data-post-implementation-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="rssa_date_submitted_to_co" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Date Submitted to CO</label>
                            <input type="date" id="rssa_date_submitted_to_co" name="rssa_date_submitted_to_co" value="{{ old('rssa_date_submitted_to_co', $project->rssa_date_submitted_to_co ? $project->rssa_date_submitted_to_co->format('Y-m-d') : '') }}"
                                   data-post-implementation-edit="true" data-ro-only="true" disabled
                                   style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; background-color: #f3f4f6;">
                            @if($rssaDateSubmittedToCoUpdatedByName || $project->rssa_date_submitted_to_co_updated_at)
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $rssaDateSubmittedToCoUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->rssa_date_submitted_to_co_updated_at ? $project->rssa_date_submitted_to_co_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            @endif
                            <div style="margin-top: 8px;">
                                <button type="submit" data-post-implementation-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('locally-funded-project.update', $project) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="monitoring">
                            <label for="rssa_remarks" style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px;">Remarks</label>
                            <textarea id="rssa_remarks" name="rssa_remarks" rows="3" data-post-implementation-edit="true" disabled
                                      style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; box-sizing: border-box; resize: vertical; background-color: #f3f4f6;">{{ old('rssa_remarks', $project->rssa_remarks) }}</textarea>
                            @if($rssaRemarksUpdatedByName || $project->rssa_remarks_updated_at)
                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px; font-size: 12px; color: #6b7280; flex-wrap: wrap;">
                                <span><strong>Updated By:</strong> {{ $rssaRemarksUpdatedByName ?? '-' }}</span>
                                <span><strong>Date & Time:</strong> {{ $project->rssa_remarks_updated_at ? $project->rssa_remarks_updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            @endif
                            <div style="margin-top: 8px;">
                                <button type="submit" data-post-implementation-save="true" style="display: none; padding: 6px 12px; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;"><i class="fas fa-check" style="margin-right: 4px;"></i>Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>
        <div id="activityLogSection" role="dialog" aria-modal="true" aria-labelledby="activityLogTitle" aria-hidden="true" style="margin-bottom: 24px; padding: 20px; border: 1px solid #e5e7eb; border-radius: 10px; background-color: #f9fafb;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; border-bottom: 2px solid #00267C; padding-bottom: 10px; position: relative;">
                <h3 id="activityLogTitle" style="color: #002C76; font-size: 15px; font-weight: 700; margin: 0;">Activity Logs</h3>
                <button type="button" id="activityLogClose" aria-label="Close activity logs" style="border: none; background: #e2e8f0; color: #0f172a; width: 28px; height: 28px; border-radius: 999px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 16px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @if(empty($activityLogs))
                <div style="padding: 16px; background-color: #f9fafb; border: 1px dashed #d1d5db; border-radius: 8px; text-align: center; color: #6b7280; font-size: 13px;">
                    No activity logs found for this project.
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr style="background-color: #f3f4f6; border-bottom: 2px solid #d1d5db;">
                                <th style="padding: 8px 10px; text-align: left;">Date/Time</th>
                                <th style="padding: 8px 10px; text-align: left;">User</th>
                                <th style="padding: 8px 10px; text-align: left;">Section</th>
                                <th style="padding: 8px 10px; text-align: left;">Field</th>
                                <th style="padding: 8px 10px; text-align: left;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityLogs as $log)
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 8px 10px; vertical-align: top;">{{ $log['timestamp']->format('M d, Y h:i A') }}</td>
                                    <td style="padding: 8px 10px; vertical-align: top;">
                                        {{ $log['user_name'] ?? 'Unknown' }}@if(!empty($log['user_agency'])) ({{ $log['user_agency'] }})@endif
                                    </td>
                                    <td style="padding: 8px 10px; vertical-align: top;">{{ $log['section'] }}</td>
                                    <td style="padding: 8px 10px; vertical-align: top;">{{ $log['field'] }}</td>
                                    <td style="padding: 8px 10px; vertical-align: top;">{{ $log['details'] ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div id="activityLogBackdrop" aria-hidden="true"></div>

    <style>
        .asterisk {
            color: #dc2626;
        }
        .form-label{
            font-size: 12px; color: #002C76; font-weight: 700; text-transform: uppercase;
        }
        .project-copy-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            padding: 0;
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            background: #eff6ff;
            color: #002C76;
            cursor: pointer;
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        .project-copy-button:hover {
            background: #dbeafe;
            border-color: #93c5fd;
        }

        .project-copy-button.is-copied {
            background: #dcfce7;
            border-color: #86efac;
            color: #166534;
        }

        .project-copy-button i {
            font-size: 11px;
        }
        .content-header {
            flex-wrap: wrap;
        }

        .content-header > div:last-child {
            flex-wrap: wrap;
        }

        .project-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .project-tab {
            border: 1px solid #c8d8f0;
            background-color: #ffffff;
            color: #002c76;
            border-radius: 999px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            line-height: 1.1;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .project-tab:hover {
            background-color: #eff6ff;
            border-color: #9bb7e3;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.12);
        }

        .project-tab.is-active {
            background-color: #002c76;
            border-color: #002c76;
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(0, 44, 118, 0.28);
        }

        .project-tab-panel {
            display: none;
        }

        .project-tab-panel.is-active {
            display: block;
        }

        #projectProfileSection,
        #contractInfoSection,
        #physicalAccomplishmentSection,
        #financialAccomplishmentSection,
        #monitoringInspectionSection,
        #postImplementationSection,
        #activityLogSection {
            font-size: 0.9em;
        }

        #activityLogBackdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            z-index: 1190;
        }

        #activityLogBackdrop.is-visible {
            opacity: 1;
            visibility: visible;
        }

        #activityLogSection {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) scale(0.98);
            width: min(900px, 92vw);
            max-height: 80vh;
            overflow: auto;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.25);
            display: none;
            z-index: 1200;
        }

        #activityLogSection.is-visible {
            display: block;
            transform: translate(-50%, -50%) scale(1);
        }

        body.modal-open {
            overflow: hidden;
        }

        #activityLogFab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background-color: #002C76;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.18);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        #activityLogFab:hover {
            background-color: #0b3b84;
            transform: translateY(-2px);
            box-shadow: 0 14px 22px rgba(15, 23, 42, 0.22);
        }

        #activityLogFab:active {
            transform: translateY(0);
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.2);
        }

        #activityLogFab[data-state="open"] {
            background-color: #0f172a;
        }

        #activityLogFab span {
            white-space: nowrap;
        }

        #physicalAccomplishmentSection [style*="font-size"] {
            font-size: inherit !important;
        }

        .monthly-details {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 12px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .monthly-details[open] {
            border-color: #c7d2fe;
            background-color: #eef2ff;
            box-shadow: 0 6px 14px rgba(30, 64, 175, 0.12);
        }

        .monthly-details > summary {
            list-style: none;
        }

        .monthly-summary::marker {
            content: '';
        }

        .monthly-summary::after {
            content: '+';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
            border-radius: 999px;
            background-color: #c7d2fe;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 700;
            transition: transform 0.2s ease;
        }

        details[open] > .monthly-summary::after {
            transform: rotate(45deg);
        }

        #financialAccomplishmentSection .monthly-details {
            width: 50%;
            min-width: 320px;
            box-sizing: border-box;
        }

        @media (max-width: 1024px) {
            div[style*="grid-template-columns: repeat(3"] {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 768px) {
            .content-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .content-header > div:last-child {
                width: 100%;
                justify-content: flex-start;
            }

            .project-tabs {
                gap: 6px;
            }

            .project-tab {
                border-radius: 999px;
                padding: 8px 12px;
                font-size: 11px;
            }

            #financialAccomplishmentSection .monthly-details {
                width: 100%;
                min-width: 0;
            }

            table {
                min-width: 720px;
            }
        }

        @media (max-width: 480px) {
            .content-header h1 {
                font-size: 20px;
            }

            .content-header p {
                font-size: 12px;
            }

            #activityLogSection {
                width: 94vw;
                max-height: 85vh;
            }
        }

        @media (max-width: 1024px) {
            .lfp-mobile-shell {
                width: 100%;
                overflow-x: hidden;
                overflow-y: visible;
                padding-bottom: 0;
            }

            .lfp-mobile-canvas {
                min-width: 100%;
                font-size: 11px;
            }

            .lfp-mobile-canvas .content-header {
                flex-direction: row;
                align-items: center;
                flex-wrap: wrap;
            }

            .lfp-mobile-canvas .content-header h1 {
                font-size: 22px;
            }

            .lfp-mobile-canvas .project-main-title {
                font-size: 20px !important;
            }

            .lfp-mobile-canvas .content-header > div:last-child {
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
            }

            .lfp-mobile-canvas .project-tabs {
                flex-wrap: nowrap;
                gap: 6px;
                overflow-x: auto;
                overflow-y: hidden;
                width: 100%;
                padding-bottom: 4px;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                scroll-snap-type: x proximity;
            }

            .lfp-mobile-canvas .project-tabs::-webkit-scrollbar {
                display: none;
            }

            .lfp-mobile-canvas .project-tab {
                flex: 0 0 auto;
                padding: 6px 10px;
                font-size: 10px;
                white-space: nowrap;
                text-align: center;
                scroll-snap-align: start;
            }

            .lfp-mobile-canvas div[style*="grid-template-columns: repeat(3"] {
                grid-template-columns: repeat(3, minmax(110px, 1fr)) !important;
                gap: 10px !important;
            }

            .lfp-mobile-canvas div[style*="grid-template-columns: repeat(2, minmax(260px, 1fr))"] {
                grid-template-columns: repeat(2, minmax(150px, 1fr)) !important;
                gap: 10px !important;
            }

            .lfp-mobile-canvas div[style*="grid-template-columns: repeat(2, minmax(300px, 1fr))"] {
                grid-template-columns: repeat(2, minmax(150px, 1fr)) !important;
                gap: 10px !important;
            }

            .lfp-mobile-canvas div[style*="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr))"] {
                grid-template-columns: repeat(2, minmax(150px, 1fr)) !important;
                gap: 12px !important;
            }

            .lfp-mobile-canvas div[style*="grid-template-columns: 120px 1fr 180px 140px"] {
                grid-template-columns: 46px minmax(72px, 1fr) 62px 54px !important;
                gap: 4px !important;
                font-size: 10px !important;
            }

            .lfp-mobile-canvas #editProfileForm > div:first-of-type,
            .lfp-mobile-canvas #editContractForm > div:first-of-type {
                grid-template-columns: repeat(3, minmax(110px, 1fr)) !important;
                gap: 10px !important;
            }

            .lfp-mobile-canvas #financialAccomplishmentSection .monthly-details {
                width: 100%;
                min-width: 0;
            }

            .lfp-mobile-canvas #projectProfileSection,
            .lfp-mobile-canvas #contractInfoSection,
            .lfp-mobile-canvas #physicalAccomplishmentSection,
            .lfp-mobile-canvas #financialAccomplishmentSection,
            .lfp-mobile-canvas #monitoringInspectionSection,
            .lfp-mobile-canvas #postImplementationSection {
                font-size: 0.78em;
            }

            .lfp-mobile-canvas > div[style*="background: #f8fafc; padding: 24px"] {
                padding: 14px !important;
            }

            .lfp-mobile-canvas table {
                min-width: 100% !important;
                font-size: 10px !important;
            }

            .lfp-mobile-canvas th,
            .lfp-mobile-canvas td {
                white-space: normal;
                word-break: break-word;
            }
        }

        @media (max-width: 768px) {
            .lfp-mobile-canvas #projectProfileSection {
                padding: 16px !important;
                font-size: 0.95em;
            }

            .lfp-mobile-canvas .project-tab-panel > div:first-child {
                flex-direction: row !important;
                align-items: flex-start !important;
            }

            .lfp-mobile-canvas .project-tab-panel > div:first-child a[data-toggle="inline-edit"] {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 36px !important;
                min-width: 36px;
                height: 36px !important;
                padding: 0 !important;
                border-radius: 999px !important;
                font-size: 0 !important;
                line-height: 1;
                flex: 0 0 auto;
                overflow: hidden;
            }

            .lfp-mobile-canvas .project-tab-panel > div:first-child a[data-toggle="inline-edit"] i {
                margin-right: 0 !important;
                font-size: 14px !important;
            }

            .lfp-mobile-canvas .project-profile-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 14px !important;
            }

            .lfp-mobile-canvas .project-profile-form-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
            }

            #editProfileFormWrapper .project-profile-form-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
            }

            .lfp-mobile-canvas .project-profile-grid > div,
            .lfp-mobile-canvas .project-profile-form-grid > div {
                min-width: 0;
                width: 100%;
            }

            #editProfileFormWrapper .project-profile-form-grid > div {
                min-width: 0;
                width: 100%;
            }

            .lfp-mobile-canvas #editProfileForm > div:last-child {
                flex-direction: column;
            }

            .lfp-mobile-canvas #editProfileForm > div:last-child button {
                width: 100%;
            }

            #editProfileForm > div:last-child {
                flex-direction: column;
            }

            #editProfileForm > div:last-child button {
                width: 100%;
            }

            .lfp-mobile-canvas #contractInfoSection {
                padding: 16px !important;
                font-size: 0.95em;
            }

            .lfp-mobile-canvas #contractInfoSection .contract-info-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
            }

            #editContractFormWrapper .contract-form-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
            }

            .lfp-mobile-canvas .contract-info-grid > div,
            .lfp-mobile-canvas .contract-form-grid > div,
            #editContractFormWrapper .contract-form-grid > div {
                min-width: 0;
            }

            .lfp-mobile-canvas .contract-info-grid > div {
                display: flex;
                flex-direction: column;
                gap: 4px;
                box-sizing: border-box;
                overflow-wrap: anywhere;
                word-break: break-word;
            }

            .lfp-mobile-canvas .contract-info-grid > div strong {
                display: block;
            }

        }

    </style>

    <script>
const locationData = {
          "Abra": {
            "Bangued": [
              "Agtangao",
              "Angad",
              "Bañacao",
              "Bangbangar",
              "Cabuloan",
              "Calaba",
              "Tablac",
              "Cosili West",
              "Cosili East",
              "Dangdangla",
              "Lingtan",
              "Lipcan",
              "Lubong",
              "Macarcarmay",
              "Maoay",
              "Macray",
              "Malita",
              "Palao",
              "Patucannay",
              "Sagap",
              "San Antonio",
              "Santa Rosa",
              "Sao-atan",
              "Sappaac",
              "Zone 2 Pob.",
              "Zone 3 Pob.",
              "Zone 4 Pob.",
              "Zone 5 Pob.",
              "Zone 6 Pob.",
              "Zone 7 Pob.",
              "Zone 1 Pob."
            ],
            "Boliney": [
              "Amti",
              "Bao-yan",
              "Danac East",
              "Dao-angan",
              "Dumagas",
              "Kilong-Olao",
              "Poblacion",
              "Danac West"
            ],
            "Bucay": [
              "Abang",
              "Bangbangcag",
              "Bangcagan",
              "Banglolao",
              "Bugbog",
              "Calao",
              "Dugong",
              "Labon",
              "Layugan",
              "Madalipay",
              "Pagala",
              "Palaquio",
              "Pakiling",
              "Patoc",
              "North Poblacion",
              "South Poblacion",
              "Quimloong",
              "Salnec",
              "San Miguel",
              "Siblong",
              "Tabiog"
            ],
            "Bucloc": [
              "Ducligan",
              "Labaan",
              "Lingey",
              "Lamao"
            ],
            "Daguioman": [
              "Ableg",
              "Cabaruyan",
              "Pikek",
              "Tui"
            ],
            "Danglas": [
              "Abaquid",
              "Cabaruan",
              "Caupasan",
              "Nagaparan",
              "Padangitan",
              "Pangal"
            ],
            "Dolores": [
              "Bayaan",
              "Cabaroan",
              "Calumbaya",
              "Cardona",
              "Isit",
              "Kimmalaba",
              "Libtec",
              "Lub-lubba",
              "Mudiit",
              "Namit-ingan",
              "Pacac",
              "Poblacion",
              "Salucag",
              "Talogtog",
              "Taping"
            ],
            "La Paz": [
              "Benben",
              "Bulbulala",
              "Buli",
              "Canan",
              "Liguis",
              "Malabbaga",
              "Mudeng",
              "Pidipid",
              "Poblacion",
              "San Gregorio",
              "Toon",
              "Udangan"
            ],
            "Lacub": [
              "Bacag",
              "Buneg",
              "Guinguinabang",
              "Lan-ag",
              "Pacoc",
              "Poblacion"
            ],
            "Lagangilang": [
              "Aguet",
              "Bacooc",
              "Balais",
              "Cayapa",
              "Dalaguisen",
              "Laang",
              "Lagben",
              "Laguiben",
              "Nagtipulan",
              "Nagtupacan",
              "Paganao",
              "Pawa",
              "Poblacion",
              "Presentar"
            ],
            "San Isidro": [
              "Tagodtod",
              "Taping",
              "Cabayogan",
              "Dalimag",
              "Langbaban",
              "Manayday",
              "Pantoc",
              "Poblacion",
              "Sabtan-olo",
              "San Marcial",
              "Tangbao"
            ],
            "Lagayan": [
              "Ba-i",
              "Collago",
              "Pang-ot",
              "Poblacion",
              "Pulot"
            ],
            "Langiden": [
              "Baac",
              "Dalayap",
              "Mabungtot",
              "Malapaao",
              "Poblacion",
              "Quillat"
            ],
            "Licuan-Baay": [
              "Bonglo",
              "Bulbulala",
              "Cawayan",
              "Domenglay",
              "Lenneng",
              "Mapisla",
              "Mogao",
              "Nalbuan",
              "Poblacion",
              "Subagan",
              "Tumalip"
            ],
            "Luba": [
              "Ampalioc",
              "Barit",
              "Gayaman",
              "Lul-luno",
              "Luzong",
              "Nagbukel-Tuquipa",
              "Poblacion",
              "Sabnangan"
            ],
            "Malibcong": [
              "Bayabas",
              "Binasaran",
              "Buanao",
              "Dulao",
              "Duldulao",
              "Gacab",
              "Lat-ey",
              "Mataragan",
              "Pacgued",
              "Taripan",
              "Umnap"
            ],
            "Manabo": [
              "Catacdegan Viejo",
              "Luzong",
              "Ayyeng",
              "San Jose Norte",
              "San Jose Sur",
              "San Juan Norte",
              "San Juan Sur",
              "San Ramon East",
              "San Ramon West",
              "Santo Tomas",
              "Catacdegan Nuevo"
            ],
            "Peñarrubia": [
              "Dumayco",
              "Lusuac",
              "Namarabar",
              "Patiao",
              "Malamsit",
              "Poblacion",
              "Riang",
              "Santa Rosa",
              "Tattawa"
            ],
            "Pidigan": [
              "Alinaya",
              "Arab",
              "Garreta",
              "Immuli",
              "Laskig",
              "Naguirayan",
              "Monggoc",
              "Pamutic",
              "Pangtud",
              "Poblacion East",
              "Poblacion West",
              "San Diego",
              "Sulbec",
              "Suyo",
              "Yuyeng"
            ],
            "Pilar": [
              "Bolbolo",
              "Brookside",
              "Ocup",
              "Dalit",
              "Dintan",
              "Gapang",
              "Kinabiti",
              "Maliplipit",
              "Nagcanasan",
              "Nanangduan",
              "Narnara",
              "Pang-ot",
              "Patad",
              "Poblacion",
              "San Juan East",
              "San Juan West",
              "South Balioag",
              "Tikitik"
            ],
            "Villavieja": [],
            "Sallapadan": [
              "Bazar",
              "Bilabila",
              "Gangal",
              "Maguyepyep",
              "Naguilian",
              "Saccaang",
              "Subusob",
              "Ud-udiao"
            ],
            "San Juan": [
              "Abualan",
              "Ba-ug",
              "Badas",
              "Cabcaborao",
              "Colabaoan",
              "Culiong",
              "Daoidao",
              "Guimba",
              "Lam-ag",
              "Lumobang",
              "Nangobongan",
              "Pattaoig",
              "Poblacion North",
              "Poblacion South",
              "Quidaoen",
              "Sabangan",
              "Silet",
              "Supi-il",
              "Tagaytay"
            ],
            "San Quintin": [
              "Labaan",
              "Palang",
              "Pantoc",
              "Poblacion",
              "Tangadan",
              "Villa Mercedes"
            ],
            "Tayum": [
              "Bagalay",
              "Basbasa",
              "Budac",
              "Bumagcat",
              "Cabaroan",
              "Deet",
              "Gaddani",
              "Patucannay",
              "Pias",
              "Poblacion",
              "Velasco"
            ],
            "Tineg": [
              "Poblacion",
              "Alaoa",
              "Anayan",
              "Apao",
              "Belaat",
              "Caganayan",
              "Cogon",
              "Lanec",
              "Lapat-Balantay",
              "Naglibacan"
            ],
            "Tubo": [
              "Alangtin",
              "Amtuagan",
              "Dilong",
              "Kili",
              "Poblacion",
              "Supo",
              "Tiempo",
              "Tubtuba",
              "Wayangan",
              "Tabacda"
            ],
            "Villaviciosa": [
              "Ap-apaya",
              "Bol-lilising",
              "Cal-lao",
              "Lap-lapog",
              "Lumaba",
              "Poblacion",
              "Tamac",
              "Tuquib"
            ]
          },
          "Benguet": {
            "Atok": [
              "Abiang",
              "Caliking",
              "Cattubo",
              "Naguey",
              "Paoay",
              "Pasdong",
              "Poblacion",
              "Topdac"
            ],
            "Bakun": [
              "Ampusongan",
              "Bagu",
              "Dalipey",
              "Gambang",
              "Kayapa",
              "Poblacion",
              "Sinacbat"
            ],
            "Bokod": [
              "Ambuclao",
              "Bila",
              "Bobok-Bisal",
              "Daclan",
              "Ekip",
              "Karao",
              "Nawal",
              "Pito",
              "Poblacion",
              "Tikey"
            ],
            "Buguias": [
              "Abatan",
              "Amgaleyguey",
              "Amlimay",
              "Baculongan Norte",
              "Bangao",
              "Buyacaoan",
              "Calamagan",
              "Catlubong",
              "Loo",
              "Natubleng",
              "Poblacion",
              "Baculongan Sur",
              "Lengaoan",
              "Sebang"
            ],
            "Itogon": [
              "Ampucao",
              "Dalupirip",
              "Gumatdang",
              "Loacan",
              "Poblacion",
              "Tinongdan",
              "Tuding",
              "Ucab",
              "Virac"
            ],
            "Kabayan": [
              "Adaoay",
              "Anchukey",
              "Ballay",
              "Bashoy",
              "Batan",
              "Duacan",
              "Eddet",
              "Gusaran",
              "Kabayan Barrio",
              "Lusod",
              "Pacso",
              "Poblacion",
              "Tawangan"
            ],
            "Kapangan": [
              "Balakbak",
              "Beleng-Belis",
              "Boklaoan",
              "Cayapes",
              "Cuba",
              "Datakan",
              "Gadang",
              "Gaswiling",
              "Labueg",
              "Paykek",
              "Poblacion Central",
              "Pudong",
              "Pongayan",
              "Sagubo",
              "Taba-ao"
            ],
            "Kibungan": [
              "Badeo",
              "Lubo",
              "Madaymen",
              "Palina",
              "Poblacion",
              "Sagpat",
              "Tacadang"
            ],
            "La Trinidad": [
              "Alapang",
              "Alno",
              "Ambiong",
              "Bahong",
              "Balili",
              "Beckel",
              "Bineng",
              "Betag",
              "Cruz",
              "Lubas",
              "Pico",
              "Poblacion",
              "Puguis",
              "Shilan",
              "Tawang",
              "Wangal"
            ],
            "Mankayan": [
              "Balili",
              "Bedbed",
              "Bulalacao",
              "Cabiten",
              "Colalo",
              "Guinaoang",
              "Paco",
              "Palasaan",
              "Poblacion",
              "Sapid",
              "Tabio",
              "Taneg"
            ],
            "Sablan": [
              "Bagong",
              "Balluay",
              "Banangan",
              "Banengbeng",
              "Bayabas",
              "Kamog",
              "Pappa",
              "Poblacion"
            ],
            "Tuba": [
              "Ansagan",
              "Camp One",
              "Camp 3",
              "Camp 4",
              "Nangalisan",
              "Poblacion",
              "San Pascual",
              "Tabaan Norte",
              "Tabaan Sur",
              "Tadiangan",
              "Taloy Norte",
              "Taloy Sur",
              "Twin Peaks"
            ],
            "Tublay": [
              "Ambassador",
              "Ambongdolan",
              "Ba-ayan",
              "Basil",
              "Daclan",
              "Caponga",
              "Tublay Central",
              "Tuel"
            ]
          },
          "Ifugao": {
            "Banaue": [
              "Amganad",
              "Anaba",
              "Bangaan",
              "Batad",
              "Bocos",
              "Banao",
              "Cambulo",
              "Ducligan",
              "Gohang",
              "Kinakin",
              "Poblacion",
              "Poitan",
              "San Fernando",
              "Balawis",
              "Ohaj",
              "Tam-an",
              "View Point",
              "Pula"
            ],
            "Hungduan": [
              "Abatan",
              "Bangbang",
              "Maggok",
              "Poblacion",
              "Bokiawan",
              "Hapao",
              "Lubo-ong",
              "Nungulunan",
              "Ba-ang"
            ],
            "Kiangan": [
              "Ambabag",
              "Baguinge",
              "Bokiawan",
              "Dalligan",
              "Duit",
              "Hucab",
              "Julongan",
              "Lingay",
              "Mungayang",
              "Nagacadan",
              "Pindongan",
              "Poblacion",
              "Tuplac",
              "Bolog"
            ],
            "Lagawe": [
              "Abinuan",
              "Banga",
              "Boliwong",
              "Burnay",
              "Buyabuyan",
              "Caba",
              "Cudog",
              "Dulao",
              "Jucbong",
              "Luta",
              "Montabiong",
              "Olilicon",
              "Poblacion South",
              "Ponghal",
              "Pullaan",
              "Tungngod",
              "Tupaya",
              "Poblacion East",
              "Poblacion North",
              "Poblacion West"
            ],
            "Lamut": [
              "Ambasa",
              "Hapid",
              "Lawig",
              "Lucban",
              "Mabatobato",
              "Magulon",
              "Nayon",
              "Panopdopan",
              "Payawan",
              "Pieza",
              "Poblacion East",
              "Pugol",
              "Salamague",
              "Bimpal",
              "Holowon",
              "Poblacion West",
              "Sanafe",
              "Umilag"
            ],
            "Mayoyao": [
              "Aduyongan",
              "Alimit",
              "Ayangan",
              "Balangbang",
              "Banao",
              "Banhal",
              "Bongan",
              "Buninan",
              "Chaya",
              "Chumang",
              "Guinihon",
              "Inwaloy",
              "Langayan",
              "Liwo",
              "Maga",
              "Magulon",
              "Mapawoy",
              "Mayoyao Proper",
              "Mongol",
              "Nalbu",
              "Nattum",
              "Palaad",
              "Poblacion",
              "Talboc",
              "Tulaed",
              "Bato-Alatbang",
              "Epeng"
            ],
            "Alfonso Lista": [
              "Bangar",
              "Busilac",
              "Calimag",
              "Calupaan",
              "Caragasan",
              "Dolowog",
              "Kiling",
              "Namnama",
              "Namillangan",
              "Pinto",
              "Poblacion",
              "San Jose",
              "San Juan",
              "San Marcos",
              "San Quintin",
              "Santa Maria",
              "Santo Domingo",
              "Little Tadian",
              "Ngileb",
              "Laya"
            ],
            "Aguinaldo": [
              "Awayan",
              "Bunhian",
              "Butac",
              "Chalalo",
              "Damag",
              "Galonogon",
              "Halag",
              "Itab",
              "Jacmal",
              "Majlong",
              "Mongayang",
              "Posnaan",
              "Ta-ang",
              "Talite",
              "Ubao",
              "Buwag"
            ],
            "Hingyon": [
              "Anao",
              "Bangtinon",
              "Bitu",
              "Cababuyan",
              "Mompolia",
              "Namulditan",
              "O-ong",
              "Piwong",
              "Poblacion",
              "Ubuag",
              "Umalbong",
              "Northern Cababuyan"
            ],
            "Tinoc": [
              "Ahin",
              "Ap-apid",
              "Binablayan",
              "Danggo",
              "Eheb",
              "Gumhang",
              "Impugong",
              "Luhong",
              "Tukucan",
              "Tulludan",
              "Wangwang"
            ],
            "Asipulo": [
              "Amduntog",
              "Antipolo",
              "Camandag",
              "Cawayan",
              "Hallap",
              "Namal",
              "Nungawa",
              "Panubtuban",
              "Pula",
              "Liwon"
            ]
          },
          "Kalinga": {
            "Balbalan": [
              "Ababa-an",
              "Balantoy",
              "Balbalan Proper",
              "Balbalasang",
              "Buaya",
              "Dao-angan",
              "Gawa-an",
              "Mabaca",
              "Maling",
              "Pantikian",
              "Poswoy",
              "Poblacion",
              "Talalang",
              "Tawang"
            ],
            "Lubuagan": [
              "Dangoy",
              "Mabilong",
              "Mabongtot",
              "Poblacion",
              "Tanglag",
              "Lower Uma",
              "Upper Uma",
              "Antonio Canao",
              "Uma del Norte"
            ],
            "Pasil": [
              "Ableg",
              "Balatoc",
              "Balinciagao Norte",
              "Cagaluan",
              "Colayo",
              "Dalupa",
              "Dangtalan",
              "Galdang",
              "Guina-ang",
              "Magsilay",
              "Malucsad",
              "Pugong",
              "Balenciagao Sur",
              "Bagtayan"
            ],
            "Pinukpuk": [
              "Aciga",
              "Allaguia",
              "Ammacian",
              "Apatan",
              "Ba-ay",
              "Ballayangon",
              "Bayao",
              "Wagud",
              "Camalog",
              "Katabbogan",
              "Dugpa",
              "Cawagayan",
              "Asibanglan",
              "Limos",
              "Magaogao",
              "Malagnat",
              "Mapaco",
              "Pakawit",
              "Pinukpuk Junction",
              "Socbot",
              "Taga",
              "Pinococ",
              "Taggay"
            ],
            "Rizal": [
              "Babalag East",
              "Calaocan",
              "Kinama",
              "Liwan East",
              "Liwan West",
              "Macutay",
              "San Pascual",
              "San Quintin",
              "Santor",
              "Babalag West",
              "Bulbol",
              "Romualdez",
              "San Francisco",
              "San Pedro"
            ],
            "City of Tabuk": [
              "Agbannawag",
              "Amlao",
              "Appas",
              "Bagumbayan",
              "Balawag",
              "Balong",
              "Bantay",
              "Bulanao",
              "Cabaritan",
              "Cabaruan",
              "Calaccad",
              "Calanan",
              "Dilag",
              "Dupag",
              "Gobgob",
              "Guilayon",
              "Lanna",
              "Laya East",
              "Laya West",
              "Lucog",
              "Magnao",
              "Magsaysay",
              "Malalao",
              "Masablang",
              "Nambaran",
              "Nambucayan",
              "Naneng",
              "Dagupan Centro",
              "San Juan",
              "Suyang",
              "Tuga",
              "Bado Dangwa",
              "Bulo",
              "Casigayan",
              "Cudal",
              "Dagupan Weste",
              "Lacnog",
              "Malin-awa",
              "New Tanglag",
              "San Julian",
              "Bulanao Norte",
              "Ipil",
              "Lacnog West"
            ],
            "Tanudan": [
              "Anggacan",
              "Babbanoy",
              "Dacalan",
              "Gaang",
              "Lower Mangali",
              "Lower Taloctoc",
              "Lower Lubo",
              "Upper Lubo",
              "Mabaca",
              "Pangol",
              "Poblacion",
              "Upper Taloctoc",
              "Anggacan Sur",
              "Dupligan",
              "Lay-asan",
              "Mangali Centro"
            ],
            "Tinglayan": [
              "Ambato Legleg",
              "Bangad Centro",
              "Basao",
              "Belong Manubal",
              "Butbut",
              "Bugnay",
              "Buscalan",
              "Dananao",
              "Loccong",
              "Luplupa",
              "Mallango",
              "Poblacion",
              "Sumadel 1",
              "Sumadel 2",
              "Tulgao East",
              "Tulgao West",
              "Upper Bangad",
              "Ngibat",
              "Old Tinglayan",
              "Lower Bangad"
            ]
          },
          "Mountain Province": {
            "Barlig": [
              "Chupac",
              "Fiangtin",
              "Kaleo",
              "Latang",
              "Lias Kanluran",
              "Lingoy",
              "Lunas",
              "Macalana",
              "Ogo-og",
              "Gawana",
              "Lias Silangan"
            ],
            "Bauko": [
              "Abatan",
              "Bagnen Oriente",
              "Bagnen Proper",
              "Balintaugan",
              "Banao",
              "Bila",
              "Guinzadan Central",
              "Guinzadan Norte",
              "Guinzadan Sur",
              "Lagawa",
              "Leseb",
              "Mabaay",
              "Mayag",
              "Monamon Norte",
              "Monamon Sur",
              "Mount Data",
              "Otucan Norte",
              "Otucan Sur",
              "Poblacion",
              "Sadsadan",
              "Sinto",
              "Tapapan"
            ],
            "Besao": [
              "Agawa",
              "Ambaguio",
              "Banguitan",
              "Besao East",
              "Besao West",
              "Catengan",
              "Gueday",
              "Lacmaan",
              "Laylaya",
              "Padangan",
              "Payeo",
              "Suquib",
              "Tamboan",
              "Kin-iway"
            ],
            "Bontoc": [
              "Alab Proper",
              "Alab Oriente",
              "Balili",
              "Bayyo",
              "Bontoc Ili",
              "Caneo",
              "Dalican",
              "Gonogon",
              "Guinaang",
              "Mainit",
              "Maligcong",
              "Samoki",
              "Talubin",
              "Tocucan",
              "Poblacion",
              "Caluttit"
            ],
            "Natonin": [
              "Alunogan",
              "Balangao",
              "Banao",
              "Banawal",
              "Butac",
              "Maducayan",
              "Poblacion",
              "Saliok",
              "Sta. Isabel",
              "Tonglayan",
              "Pudo"
            ],
            "Paracelis": [
              "Anonat",
              "Bacarri",
              "Bananao",
              "Bantay",
              "Butigue",
              "Bunot",
              "Buringal",
              "Palitud",
              "Poblacion"
            ],
            "Sabangan": [
              "Bao-angan",
              "Bun-ayan",
              "Busa",
              "Camatagan",
              "Capinitan",
              "Data",
              "Gayang",
              "Lagan",
              "Losad",
              "Namatec",
              "Napua",
              "Pingad",
              "Poblacion",
              "Supang",
              "Tambingan"
            ],
            "Sadanga": [
              "Anabel",
              "Belwang",
              "Betwagan",
              "Bekigan",
              "Poblacion",
              "Sacasacan",
              "Saclit",
              "Demang"
            ],
            "Sagada": [
              "Aguid",
              "Tetepan Sur",
              "Ambasing",
              "Angkeling",
              "Antadao",
              "Balugan",
              "Bangaan",
              "Dagdag",
              "Demang",
              "Fidelisan",
              "Kilong",
              "Madongo",
              "Poblacion",
              "Pide",
              "Nacagang",
              "Suyo",
              "Taccong",
              "Tanulong",
              "Tetepan Norte"
            ],
            "Tadian": [
              "Balaoa",
              "Banaao",
              "Bantey",
              "Batayan",
              "Bunga",
              "Cadad-anan",
              "Cagubatan",
              "Duagan",
              "Dacudac",
              "Kayan East",
              "Lenga",
              "Lubon",
              "Mabalite",
              "Masla",
              "Pandayan",
              "Poblacion",
              "Sumadel",
              "Tue",
              "Kayan West"
            ]
          },
          "Apayao": {
            "Calanasan": [
              "Butao",
              "Cadaclan",
              "Langnao",
              "Lubong",
              "Naguilian",
              "Namaltugan",
              "Poblacion",
              "Sabangan",
              "Santa Filomena",
              "Tubongan",
              "Tanglagan",
              "Tubang",
              "Don Roque Ablan Sr.",
              "Eleazar",
              "Eva Puzon",
              "Kabugawan",
              "Macalino",
              "Santa Elena"
            ],
            "Conner": [
              "Allangigan",
              "Buluan",
              "Caglayan",
              "Calafug",
              "Cupis",
              "Daga",
              "Guinamgaman",
              "Karikitan",
              "Katablangan",
              "Malama",
              "Manag",
              "Nabuangan",
              "Paddaoan",
              "Puguin",
              "Ripang",
              "Sacpil",
              "Talifugo",
              "Banban",
              "Guinaang",
              "Ili",
              "Mawigue"
            ],
            "Flora": [
              "Allig",
              "Anninipan",
              "Atok",
              "Bagutong",
              "Balasi",
              "Balluyan",
              "Malayugan",
              "Malubibit Norte",
              "Poblacion East",
              "Tamalunog",
              "Mallig",
              "Malubibit Sur",
              "Poblacion West",
              "San Jose",
              "Santa Maria",
              "Upper Atok"
            ],
            "Kabugao": [
              "Badduat",
              "Baliwanan",
              "Bulu",
              "Dagara",
              "Dibagat",
              "Cabetayan",
              "Karagawan",
              "Kumao",
              "Laco",
              "Lenneng",
              "Lucab",
              "Luttuacan",
              "Madatag",
              "Madduang",
              "Magabta",
              "Maragat",
              "Musimut",
              "Nagbabalayan",
              "Poblacion",
              "Tuyangan",
              "Waga"
            ],
            "Luna": [
              "Bacsay",
              "Capagaypayan",
              "Dagupan",
              "Lappa",
              "Marag",
              "Poblacion",
              "Quirino",
              "Salvacion",
              "San Francisco",
              "San Isidro Norte",
              "San Sebastian",
              "Santa Lina",
              "Tumog",
              "Zumigui",
              "Cagandungan",
              "Calabigan",
              "Cangisitan",
              "Luyon",
              "San Gregorio",
              "San Isidro Sur",
              "Shalom",
              "Turod"
            ],
            "Pudtol": [
              "Aga",
              "Alem",
              "Cabatacan",
              "Cacalaggan",
              "Capannikian",
              "Lower Maton",
              "Malibang",
              "Mataguisi",
              "Poblacion",
              "San Antonio",
              "Swan",
              "Upper Maton",
              "Amado",
              "Aurora",
              "Doña Loreta",
              "Emilia",
              "Imelda",
              "Lt. Balag",
              "Lydia",
              "San Jose",
              "San Luis",
              "San Mariano"
            ],
            "Santa Marcela": [
              "Barocboc",
              "Consuelo",
              "Imelda",
              "Malekkeg",
              "Marcela",
              "Nueva",
              "Panay",
              "San Antonio",
              "Sipa Proper",
              "Emiliana",
              "San Carlos",
              "San Juan",
              "San Mariano"
            ]
          },
          "City of Baguio": {
            "City of Baguio": [
              "Apugan-Loakan",
              "Asin Road",
              "Atok Trail",
              "Bakakeng Central",
              "Bakakeng North",
              "Happy Hollow",
              "Balsigan",
              "Bayan Park West",
              "Bayan Park East",
              "Brookspoint",
              "Brookside",
              "Cabinet Hill-Teacher's Camp",
              "Camp Allen",
              "Camp 7",
              "Camp 8",
              "Campo Filipino",
              "City Camp Central",
              "City Camp Proper",
              "Country Club Village",
              "Cresencia Village",
              "Dagsian, Upper",
              "DPS Area",
              "Dizon Subdivision",
              "Quirino Hill, East",
              "Engineers' Hill",
              "Fairview Village",
              "Fort del Pilar",
              "General Luna, Upper",
              "General Luna, Lower",
              "Gibraltar",
              "Greenwater Village",
              "Guisad Central",
              "Guisad Sorong",
              "Hillside",
              "Holy Ghost Extension",
              "Holy Ghost Proper",
              "Imelda Village",
              "Irisan",
              "Kayang Extension",
              "Kias",
              "Kagitingan",
              "Loakan Proper",
              "Lopez Jaena",
              "Lourdes Subdivision Extension",
              "Dagsian, Lower",
              "Lourdes Subdivision, Lower",
              "Quirino Hill, Lower",
              "General Emilio F. Aguinaldo",
              "Lualhati",
              "Lucnab",
              "Magsaysay, Lower",
              "Magsaysay Private Road",
              "Aurora Hill Proper",
              "Bal-Marcoville",
              "Quirino Hill, Middle",
              "Military Cut-off",
              "Mines View Park",
              "Modern Site, East",
              "Modern Site, West",
              "New Lucban",
              "Aurora Hill, North Central",
              "Sanitary Camp, North",
              "Outlook Drive",
              "Pacdal",
              "Pinget",
              "Pinsao Pilot Project",
              "Pinsao Proper",
              "Poliwes",
              "Pucsusan",
              "MRR-Queen Of Peace",
              "Rock Quarry, Lower",
              "Salud Mitra",
              "San Antonio Village",
              "San Luis Village",
              "San Roque Village",
              "San Vicente",
              "Santa Escolastica",
              "Santo Rosario",
              "Santo Tomas School Area",
              "Santo Tomas Proper",
              "Scout Barrio",
              "Session Road Area",
              "Slaughter House Area",
              "Sanitary Camp, South",
              "Saint Joseph Village",
              "Teodora Alonzo",
              "Trancoville",
              "Rock Quarry, Upper",
              "Victoria Village",
              "Quirino Hill, West",
              "Andres Bonifacio",
              "Legarda-Burnham-Kisad",
              "Imelda R. Marcos",
              "Lourdes Subdivision, Proper",
              "Quirino-Magsaysay, Upper",
              "A. Bonifacio-Caguioa-Rimando",
              "Ambiong",
              "Aurora Hill, South Central",
              "Abanao-Zandueta-Kayong-Chugum-Otek",
              "Bagong Lipunan",
              "BGH Compound",
              "Bayan Park Village",
              "Camdas Subdivision",
              "Palma-Urbano",
              "Dominican Hill-Mirador",
              "Alfonso Tabora",
              "Dontogan",
              "Ferdinand",
              "Happy Homes",
              "Harrison-Claudio Carantes",
              "Honeymoon",
              "Kabayanihan",
              "Kayang-Hilltop",
              "Gabriela Silang",
              "Liwanag-Loakan",
              "Malcolm Square-Perfecto",
              "Manuel A. Roxas",
              "Padre Burgos",
              "Quezon Hill, Upper",
              "Rock Quarry, Middle",
              "Phil-Am",
              "Quezon Hill Proper",
              "Middle Quezon Hill Subdivision",
              "Rizal Monument Area",
              "SLU-SVP Housing Village",
              "South Drive",
              "Magsaysay, Upper",
              "Market Subdivision, Upper",
              "Padre Zamora"
            ]
          }
        };
        function formatMoney(value) {
            return (Math.round((value + Number.EPSILON) * 100) / 100).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        const financialAllocation = Number(@json((float) $project->lgsf_allocation)) || 0;

        function getFinancialFieldSum(field) {
            let sum = 0;
            document.querySelectorAll('[data-financial-field="' + field + '"]').forEach((input) => {
                const raw = input.value ? input.value.toString() : '';
                const cleaned = raw.replace(/,/g, '');
                const num = parseFloat(cleaned);
                if (!isNaN(num)) {
                    sum += num;
                }
            });
            return sum;
        }

        function updateFinancialSums() {
            const fields = ['obligation', 'disbursed_amount', 'reverted_amount'];
            fields.forEach((field) => {
                const el = document.getElementById('financialSum-' + field);
                if (el) {
                    el.textContent = formatMoney(getFinancialFieldSum(field));
                }
            });

            const disbursedTotal = getFinancialFieldSum('disbursed_amount');
            const revertedTotal = getFinancialFieldSum('reverted_amount');
            const balance = financialAllocation - (disbursedTotal + revertedTotal);
            const utilizationRate = financialAllocation > 0
                ? ((financialAllocation - balance) / financialAllocation) * 100
                : 0;

            const balanceEl = document.getElementById('financialBalance');
            if (balanceEl) {
                balanceEl.textContent = formatMoney(balance);
            }

            const utilizationEl = document.getElementById('financialUtilizationRate');
            if (utilizationEl) {
                utilizationEl.textContent = formatMoney(utilizationRate) + '%';
                utilizationEl.style.color = utilizationRate < 100 ? '#dc2626' : '#111827';
            }
        }

        document.addEventListener('input', (event) => {
            if (event.target && event.target.hasAttribute('data-financial-field')) {
                updateFinancialSums();
            }
        });

        updateFinancialSums();

        const inlineSectionTargetMap = {
            editPhysicalForm: 'physicalAccomplishmentSection',
            editFinancialForm: 'financialAccomplishmentSection',
            editMonitoringForm: 'monitoringInspectionSection',
            editPostImplementationForm: 'postImplementationSection',
        };

        function getInlineEditElements(targetId) {
            const mappedTargetId = inlineSectionTargetMap[targetId] || targetId;
            return {
                target: document.getElementById(mappedTargetId),
                wrapper: document.getElementById(targetId + 'Wrapper'),
                backdrop: document.getElementById(targetId + 'Backdrop'),
            };
        }

        function isInlineSectionTarget(targetId, inlineElements) {
            const elements = inlineElements || getInlineEditElements(targetId);

            return !elements.wrapper
                && Object.prototype.hasOwnProperty.call(inlineSectionTargetMap, targetId)
                && !!elements.target;
        }

        function isInlineEditOpen(targetId) {
            const inlineElements = getInlineEditElements(targetId);

            if (inlineElements.wrapper) {
                return inlineElements.wrapper.style.display !== 'none'
                    && inlineElements.wrapper.getAttribute('aria-hidden') !== 'true';
            }

            if (isInlineSectionTarget(targetId, inlineElements)) {
                return inlineElements.target.classList.contains('is-inline-editing');
            }

            return false;
        }

        function syncBodyModalState() {
            const activityLogModal = document.getElementById('activityLogSection');
            const hasActivityLogModal = activityLogModal ? activityLogModal.classList.contains('is-visible') : false;
            const hasInlineModal = Array.from(document.querySelectorAll('.lfp-inline-modal[data-inline-modal="true"]')).some((modal) => {
                return modal.style.display !== 'none' && modal.getAttribute('aria-hidden') !== 'true';
            });

            document.body.classList.toggle('modal-open', hasActivityLogModal || hasInlineModal);
        }

        function initializeMobileInlineModalPortal(wrapperId, backdropId) {
            const wrapper = document.getElementById(wrapperId);
            const backdrop = document.getElementById(backdropId);

            if (!wrapper || !backdrop || !wrapper.parentNode) {
                return;
            }

            const originalParent = wrapper.parentNode;
            const anchor = document.createElement('span');
            anchor.hidden = true;
            originalParent.insertBefore(anchor, backdrop);

            const syncPortal = () => {
                const isMobile = window.matchMedia('(max-width: 768px)').matches;

                if (isMobile) {
                    if (backdrop.parentNode !== document.body) {
                        document.body.appendChild(backdrop);
                    }

                    if (wrapper.parentNode !== document.body) {
                        document.body.appendChild(wrapper);
                    }
                } else {
                    if (backdrop.parentNode !== originalParent) {
                        originalParent.insertBefore(backdrop, anchor.nextSibling);
                    }

                    if (wrapper.parentNode !== originalParent) {
                        originalParent.insertBefore(wrapper, backdrop.nextSibling);
                    }
                }

                syncBodyModalState();
            };

            syncPortal();
            window.addEventListener('resize', syncPortal);
        }

        function getInlineToggleMarkup(label, iconClass) {
            return '<i class="' + iconClass + '" style="margin-right: 6px;" aria-hidden="true"></i>' + label;
        }

        function setInlineToggleState(button, isEditing) {
            if (!button) return;
            if (!button.dataset.originalText) {
                button.dataset.originalText = button.textContent.trim();
            }
            if (!button.dataset.originalHtml) {
                button.dataset.originalHtml = button.innerHTML;
            }
            if (!button.dataset.originalBg) {
                button.dataset.originalBg = button.style.backgroundColor;
            }
            if (!button.dataset.originalColor) {
                button.dataset.originalColor = button.style.color;
            }
            button.innerHTML = isEditing
                ? getInlineToggleMarkup('Cancel', 'fas fa-times')
                : (button.dataset.originalHtml || getInlineToggleMarkup(button.dataset.originalText || 'Update', 'fas fa-edit'));
            button.dataset.inlineState = isEditing ? 'editing' : 'idle';
            button.style.backgroundColor = isEditing ? '#dc2626' : (button.dataset.originalBg || '');
            button.style.color = isEditing ? '#ffffff' : (button.dataset.originalColor || '#ffffff');
            button.setAttribute('aria-label', isEditing ? 'Cancel editing' : (button.dataset.originalText || 'Update'));
        }

        function openInlineEdit(button) {
            const targetId = button.getAttribute('data-target');
            const inlineElements = getInlineEditElements(targetId);
            const { target, wrapper, backdrop } = inlineElements;
            if (wrapper) {
                wrapper.style.display = 'block';
                wrapper.classList.add('is-visible');
                wrapper.setAttribute('aria-hidden', 'false');

                const focusTarget = wrapper.querySelector('button, input, select, textarea');
                if (focusTarget) {
                    setTimeout(() => focusTarget.focus(), 0);
                }
            }

            if (isInlineSectionTarget(targetId, inlineElements)) {
                target.classList.add('is-inline-editing');
            }

            if (backdrop && wrapper) {
                backdrop.classList.add('is-visible');
                backdrop.setAttribute('aria-hidden', 'false');
            }

            syncBodyModalState();

            if (button.hasAttribute('data-physical-toggle')) {
                const currentMonth = {{ $currentMonth }};
                const userAgency = '{{ Auth::user()->agency }}';
                const userProvince = '{{ Auth::user()->province }}';
                const isROUser = userAgency === 'DILG' && userProvince === 'Regional Office';
                
                document.querySelectorAll('[data-physical-edit="true"]').forEach((input) => {
                    const inputMonth = parseInt(input.getAttribute('data-month'), 10);
                    const isROOnly = input.hasAttribute('data-ro-only');
                    
                    if (inputMonth === currentMonth) {
                        // For RO-only fields, only enable if user is RO user
                        if (isROOnly && !isROUser) {
                            input.disabled = true;
                            input.style.backgroundColor = '#f3f4f6';
                        } else {
                            input.disabled = false;
                            input.style.backgroundColor = '#ffffff';
                        }
                    } else {
                        input.disabled = true;
                        input.style.backgroundColor = '#f3f4f6';
                    }
                });
            }

            if (button.hasAttribute('data-financial-toggle')) {
                const currentMonth = {{ $currentMonth }};
                const userAgency = '{{ Auth::user()->agency }}';
                const userProvince = '{{ Auth::user()->province }}';
                const isROUser = userAgency === 'DILG' && userProvince === 'Regional Office';
                
                document.querySelectorAll('[data-financial-edit="true"]').forEach((input) => {
                    const inputMonth = parseInt(input.getAttribute('data-month'), 10);
                    const isROOnly = input.hasAttribute('data-ro-only');
                    
                    if (inputMonth === currentMonth) {
                        // For RO-only fields, only enable if user is RO user
                        if (isROOnly && !isROUser) {
                            input.disabled = true;
                            input.style.backgroundColor = '#f3f4f6';
                        } else {
                            input.disabled = false;
                            input.style.backgroundColor = '#ffffff';
                        }
                    } else {
                        input.disabled = true;
                        input.style.backgroundColor = '#f3f4f6';
                    }
                });
                // Only show save button if user is RO user
                document.querySelectorAll('[data-financial-save="true"]').forEach((saveBtn) => {
                    if (isROUser) {
                        saveBtn.style.display = 'inline-block';
                    } else {
                        saveBtn.style.display = 'none';
                    }
                });
            }

            if (button.hasAttribute('data-monitoring-toggle')) {
                const userAgency = '{{ Auth::user()->agency }}';
                const userProvince = '{{ Auth::user()->province }}';
                const isROUser = userAgency === 'DILG' && userProvince === 'Regional Office';
                
                document.querySelectorAll('[data-monitoring-edit="true"]').forEach((input) => {
                    const isROOnly = input.hasAttribute('data-ro-only');
                    
                    // For RO-only fields, only enable if user is RO user
                    if (isROOnly && !isROUser) {
                        input.disabled = true;
                        input.style.backgroundColor = '#f3f4f6';
                    } else {
                        input.disabled = false;
                        input.style.backgroundColor = '#ffffff';
                    }
                });
                // Show save button only for fields that are not disabled
                document.querySelectorAll('[data-monitoring-save="true"]').forEach((saveBtn) => {
                    // Find the corresponding input field
                    const form = saveBtn.closest('form');
                    const input = form ? form.querySelector('[data-monitoring-edit="true"]') : null;
                    
                    if (input && !input.disabled) {
                        saveBtn.style.display = 'inline-block';
                    } else {
                        saveBtn.style.display = 'none';
                    }
                });
            }

            if (button.hasAttribute('data-post-implementation-toggle')) {
                const userAgency = '{{ Auth::user()->agency }}';
                const userProvince = '{{ Auth::user()->province }}';
                const isROUser = userAgency === 'DILG' && userProvince === 'Regional Office';
                
                document.querySelectorAll('[data-post-implementation-edit="true"]').forEach((input) => {
                    const isROOnly = input.hasAttribute('data-ro-only');
                    
                    // For RO-only fields, only enable if user is RO user
                    if (isROOnly && !isROUser) {
                        input.disabled = true;
                        input.style.backgroundColor = '#f3f4f6';
                    } else {
                        input.disabled = false;
                        input.style.backgroundColor = '#ffffff';
                    }
                });
                // Show save button only for fields that are not disabled
                document.querySelectorAll('[data-post-implementation-save="true"]').forEach((saveBtn) => {
                    // Find the corresponding input field (previous sibling)
                    const form = saveBtn.closest('form');
                    const input = form ? form.querySelector('[data-post-implementation-edit="true"]') : null;
                    
                    if (input && !input.disabled) {
                        saveBtn.style.display = 'inline-block';
                    } else {
                        saveBtn.style.display = 'none';
                    }
                });
            }
        }

        initializeMobileInlineModalPortal('editProfileFormWrapper', 'editProfileFormBackdrop');
        initializeMobileInlineModalPortal('editContractFormWrapper', 'editContractFormBackdrop');

        function closeInlineEdit(targetId) {
            const inlineElements = getInlineEditElements(targetId);
            const { target, wrapper, backdrop } = inlineElements;
            if (wrapper) {
                wrapper.style.display = 'none';
                wrapper.classList.remove('is-visible');
                wrapper.setAttribute('aria-hidden', 'true');
            }

            if (isInlineSectionTarget(targetId, inlineElements)) {
                target.classList.remove('is-inline-editing');
            }

            if (backdrop) {
                backdrop.classList.remove('is-visible');
                backdrop.setAttribute('aria-hidden', 'true');
            }

            if (targetId === 'editPhysicalForm') {
                document.querySelectorAll('[data-physical-edit="true"]').forEach((input) => {
                    input.disabled = true;
                    input.style.backgroundColor = '#f3f4f6';
                });
            }

            if (targetId === 'editFinancialForm') {
                document.querySelectorAll('[data-financial-edit="true"]').forEach((input) => {
                    input.disabled = true;
                    input.style.backgroundColor = '#f3f4f6';
                });
                document.querySelectorAll('[data-financial-save="true"]').forEach((saveBtn) => {
                    saveBtn.style.display = 'none';
                });
            }

            if (targetId === 'editMonitoringForm') {
                document.querySelectorAll('[data-monitoring-edit="true"]').forEach((input) => {
                    input.disabled = true;
                    input.style.backgroundColor = '#f3f4f6';
                });
                document.querySelectorAll('[data-monitoring-save="true"]').forEach((saveBtn) => {
                    saveBtn.style.display = 'none';
                });
            }

            if (targetId === 'editPostImplementationForm') {
                document.querySelectorAll('[data-post-implementation-edit="true"]').forEach((input) => {
                    input.disabled = true;
                    input.style.backgroundColor = '#f3f4f6';
                });
                document.querySelectorAll('[data-post-implementation-save="true"]').forEach((saveBtn) => {
                    saveBtn.style.display = 'none';
                });
            }

            syncBodyModalState();
        }

        function disableAllEditableControlsOnLoad() {
            const editableSelectors = [
                '[data-physical-edit="true"]',
                '[data-financial-edit="true"]',
                '[data-monitoring-edit="true"]',
                '[data-post-implementation-edit="true"]',
            ];

            document.querySelectorAll(editableSelectors.join(',')).forEach((input) => {
                input.disabled = true;
                input.style.backgroundColor = '#f3f4f6';
            });

            const saveSelectors = [
                '[data-financial-save="true"]',
                '[data-monitoring-save="true"]',
                '[data-post-implementation-save="true"]',
            ];

            document.querySelectorAll(saveSelectors.join(',')).forEach((saveBtn) => {
                saveBtn.style.display = 'none';
            });
        }

        disableAllEditableControlsOnLoad();

        function submitFieldChangeForm(field) {
            if (!field) {
                return false;
            }

            const form = field.closest('form');
            if (!form) {
                return false;
            }

            const submitterCandidates = Array.from(form.querySelectorAll(
                '[data-financial-save="true"], [data-monitoring-save="true"], [data-post-implementation-save="true"], button[type="submit"], input[type="submit"]'
            ));
            const submitter = submitterCandidates.find((button) => {
                if (!button || button.disabled) {
                    return false;
                }
                const style = window.getComputedStyle(button);
                return style.display !== 'none' && style.visibility !== 'hidden' && button.offsetParent !== null;
            }) || null;

            if (!submitter) {
                return false;
            }

            if (submitter && submitter.dataset) {
                submitter.dataset.confirmSkip = 'true';
                submitter.dataset.confirmed = 'true';
            }

            try {
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit(submitter);
                } else {
                    form.submit();
                }
            } finally {
                if (submitter && submitter.dataset) {
                    setTimeout(() => {
                        delete submitter.dataset.confirmSkip;
                        delete submitter.dataset.confirmed;
                    }, 0);
                }
            }

            return true;
        }

        function handleFieldSubmitFailure(restoreCallback) {
            if (typeof restoreCallback === 'function') {
                restoreCallback();
            }

            if (typeof window.showSystemErrorModal === 'function') {
                window.showSystemErrorModal('Unable to save this field change right now.');
            }
        }

        function getEditableFieldValue(field) {
            if (!field) {
                return '';
            }

            if (field.type === 'checkbox' || field.type === 'radio') {
                return field.checked ? '1' : '0';
            }

            return field.value;
        }

        function setEditableFieldValue(field, value) {
            if (!field) {
                return;
            }

            if (field.type === 'checkbox' || field.type === 'radio') {
                field.checked = value === '1';
                return;
            }

            field.value = value;
        }

        function openReusableConfirmation(message, onConfirm, onCancel) {
            if (typeof window.openConfirmationModal === 'function') {
                window.openConfirmationModal(message, onConfirm, onCancel);
                return;
            }

            if (typeof window.showSystemErrorModal === 'function') {
                window.showSystemErrorModal('Confirmation dialog is unavailable right now.');
            }

            if (typeof onCancel === 'function') {
                onCancel();
            }
        }

        function initializeFieldChangeConfirmation() {
            const editableFieldSelectors = [
                'select[data-physical-edit="true"]',
                'input[data-physical-edit="true"]:not([type="hidden"]):not([type="file"]):not([type="submit"]):not([type="button"]):not([type="reset"])',
                'textarea[data-physical-edit="true"]',
                'select[data-financial-edit="true"]',
                'input[data-financial-edit="true"]:not([type="hidden"]):not([type="file"]):not([type="submit"]):not([type="button"]):not([type="reset"])',
                'textarea[data-financial-edit="true"]',
                'select[data-monitoring-edit="true"]',
                'input[data-monitoring-edit="true"]:not([type="hidden"]):not([type="file"]):not([type="submit"]):not([type="button"]):not([type="reset"])',
                'textarea[data-monitoring-edit="true"]',
                'select[data-post-implementation-edit="true"]',
                'input[data-post-implementation-edit="true"]:not([type="hidden"]):not([type="file"]):not([type="submit"]):not([type="button"]):not([type="reset"])',
                'textarea[data-post-implementation-edit="true"]',
            ];

            document.querySelectorAll(editableFieldSelectors.join(',')).forEach((field) => {
                const rememberCurrentValue = () => {
                    field.dataset.previousValue = getEditableFieldValue(field);
                };

                rememberCurrentValue();

                field.addEventListener('focus', rememberCurrentValue);
                field.addEventListener('mousedown', rememberCurrentValue);
                field.addEventListener('touchstart', rememberCurrentValue, { passive: true });
                field.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ' || event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                        rememberCurrentValue();
                    }
                });

                field.addEventListener('change', () => {
                    if (field.disabled) {
                        field.dataset.previousValue = getEditableFieldValue(field);
                        return;
                    }

                    const previousValue = Object.prototype.hasOwnProperty.call(field.dataset, 'previousValue')
                        ? field.dataset.previousValue
                        : '';
                    const currentValue = getEditableFieldValue(field);

                    if (previousValue === currentValue) {
                        return;
                    }

                    const confirmMessage = field.tagName === 'SELECT'
                        ? 'Dropdown value changed. Do you want to save this change?'
                        : 'Field value changed. Do you want to save this change?';
                    const restorePreviousValue = () => {
                        setEditableFieldValue(field, previousValue);
                        field.dataset.previousValue = previousValue;
                    };

                    openReusableConfirmation(
                        confirmMessage,
                        () => {
                            field.dataset.previousValue = currentValue;
                            const submitted = submitFieldChangeForm(field);
                            if (!submitted) {
                                handleFieldSubmitFailure(restorePreviousValue);
                            }
                        },
                        () => {
                            restorePreviousValue();
                        }
                    );
                });
            });
        }

        initializeFieldChangeConfirmation();

        document.querySelectorAll('[data-toggle="inline-edit"]').forEach((button) => {
            const targetId = button.getAttribute('data-target');
            setInlineToggleState(button, isInlineEditOpen(targetId));

            button.addEventListener('click', (event) => {
                event.preventDefault();
                if (button.dataset.inlineState === 'editing') {
                    closeInlineEdit(targetId);
                    setInlineToggleState(button, false);
                    return;
                }

                openInlineEdit(button);
                setInlineToggleState(button, true);
            });
        });

        syncBodyModalState();

        document.querySelectorAll('[data-toggle="inline-cancel"]').forEach((button) => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                closeInlineEdit(targetId);
                const editButton = document.querySelector('[data-toggle="inline-edit"][data-target="' + targetId + '"]');
                setInlineToggleState(editButton, false);
            });
        });

        document.querySelectorAll('.lfp-inline-modal-backdrop').forEach((backdrop) => {
            backdrop.addEventListener('click', () => {
                const targetId = backdrop.id.replace(/Backdrop$/, '');
                closeInlineEdit(targetId);
                const editButton = document.querySelector('[data-toggle="inline-edit"][data-target="' + targetId + '"]');
                setInlineToggleState(editButton, false);
            });
        });

        window.addEventListener('resize', syncBodyModalState);

        document.addEventListener('submit', (event) => {
            const submitter = event.submitter;
            if (!submitter) return;
            let targetId = event.target && event.target.getAttribute('id');
            if (submitter.hasAttribute('data-financial-save')) {
                targetId = 'editFinancialForm';
            }
            if (submitter.hasAttribute('data-monitoring-save')) {
                targetId = 'editMonitoringForm';
            }
            if (submitter.hasAttribute('data-post-implementation-save')) {
                targetId = 'editPostImplementationForm';
            }
            if (!targetId) return;
            const editButton = document.querySelector('[data-toggle="inline-edit"][data-target="' + targetId + '"]');
            setInlineToggleState(editButton, false);
        }, true);

        // Location/Barangay selection for profile edit (match create form)
        let selectedBarangays = {};
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city_municipality');
        const barangaySelect = document.getElementById('barangay');
        const barangayBadges = document.getElementById('barangay_badges');
        const barangayHidden = document.getElementById('barangay_hidden');
        let availableBarangays = [];

        function normalizeBarangayList(items) {
            return Array.from(new Set((items || [])
                .map((item) => (item || '').toString().trim())
                .filter(Boolean)));
        }

        function renderBarangayOptions() {
            if (!barangaySelect) {
                return;
            }

            const remainingBarangays = availableBarangays.filter((barangay) => !selectedBarangays[barangay]);
            barangaySelect.innerHTML = '';

            remainingBarangays.forEach((barangay) => {
                const option = document.createElement('option');
                option.value = barangay;
                option.textContent = barangay;
                barangaySelect.appendChild(option);
            });
        }

        function syncBarangayPickerState() {
            if (!barangayBadges || !barangaySelect) {
                return;
            }

            barangayBadges.setAttribute('aria-expanded', barangaySelect.hidden ? 'false' : 'true');
        }

        function openBarangayPicker() {
            if (!barangaySelect) {
                return;
            }

            renderBarangayOptions();
            barangaySelect.hidden = false;
            syncBarangayPickerState();

            setTimeout(() => barangaySelect.focus(), 0);
        }

        function closeBarangayPicker() {
            if (!barangaySelect) {
                return;
            }

            barangaySelect.hidden = true;
            syncBarangayPickerState();
        }

        function isWithinBarangayPicker(target) {
            if (!target || !barangayBadges || !barangaySelect) {
                return false;
            }

            return barangayBadges.contains(target) || barangaySelect.contains(target);
        }

        function hideBarangayPickerIfFocusLeaves() {
            setTimeout(() => {
                if (!barangaySelect || barangaySelect.hidden) {
                    return;
                }

                if (isWithinBarangayPicker(document.activeElement)) {
                    return;
                }

                closeBarangayPicker();
            }, 0);
        }

        function updateBadges() {
            if (!barangayBadges || !barangayHidden) {
                return;
            }

            const selectedList = Object.keys(selectedBarangays);
            if (selectedList.length === 0) {
                barangayBadges.innerHTML = '<span style="color: #9ca3af; font-size: 14px; align-self: center;">Click here or dropdown to add barangays</span>';
                barangayHidden.value = '';
                renderBarangayOptions();
                return;
            }

            let badgesHTML = '';
            selectedList.forEach((barangay) => {
                badgesHTML += `
                    <span style="display: inline-flex; align-items: center; gap: 6px; background-color: #002C76; color: white; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 500;">
                        ${barangay}
                        <button type="button" onclick="removeBarangay('${barangay}')" style="background: none; border: none; color: white; cursor: pointer; font-size: 16px; padding: 0; line-height: 1;">×</button>
                    </span>
                `;
            });

            barangayBadges.innerHTML = badgesHTML;
            barangayHidden.value = JSON.stringify(selectedList);
            renderBarangayOptions();
        }

        window.removeBarangay = function(barangay) {
            delete selectedBarangays[barangay];
            updateBadges();
            if (barangaySelect) {
                setTimeout(() => {
                    openBarangayPicker();
                }, 0);
            }
        };

        function resetBarangaySelection() {
            selectedBarangays = {};
            if (barangayBadges) {
                barangayBadges.innerHTML = '<span style="color: #9ca3af; font-size: 14px; align-self: center;">Click here or dropdown to add barangays</span>';
            }
            if (barangayHidden) {
                barangayHidden.value = '';
            }
            renderBarangayOptions();
        }

        if (provinceSelect && citySelect && barangaySelect && barangayBadges && barangayHidden) {
            syncBarangayPickerState();

            barangayBadges.addEventListener('click', function(event) {
                if (event.target.closest('button')) {
                    return;
                }

                openBarangayPicker();
            });

            barangayBadges.addEventListener('keydown', function(event) {
                if (event.key !== 'Enter' && event.key !== ' ') {
                    return;
                }

                if (event.target.closest('button')) {
                    return;
                }

                event.preventDefault();
                openBarangayPicker();
            });

            barangayBadges.addEventListener('focusout', hideBarangayPickerIfFocusLeaves);
            barangaySelect.addEventListener('blur', hideBarangayPickerIfFocusLeaves);

            provinceSelect.addEventListener('change', function() {
                const selectedProvince = this.value;
                citySelect.innerHTML = '<option value="">-- Select City/Municipality --</option>';
                availableBarangays = [];
                resetBarangaySelection();
                closeBarangayPicker();

                if (selectedProvince && locationData[selectedProvince]) {
                    Object.keys(locationData[selectedProvince]).forEach((city) => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                }
            });

            citySelect.addEventListener('change', function() {
                const selectedProvince = provinceSelect.value;
                const selectedCity = this.value;
                availableBarangays = [];
                resetBarangaySelection();
                closeBarangayPicker();

                if (selectedProvince && selectedCity && locationData[selectedProvince] && locationData[selectedProvince][selectedCity]) {
                    availableBarangays = normalizeBarangayList(locationData[selectedProvince][selectedCity]);
                }
                renderBarangayOptions();
            });

            barangaySelect.addEventListener('change', function() {
                Array.from(this.selectedOptions).forEach((option) => {
                    const selectedValue = option.value;
                    if (selectedValue && !selectedBarangays[selectedValue]) {
                        selectedBarangays[selectedValue] = true;
                    }
                });
                updateBadges();
                this.value = '';
            });

            document.addEventListener('pointerdown', function(event) {
                if (barangaySelect.hidden) {
                    return;
                }

                if (isWithinBarangayPicker(event.target)) {
                    return;
                }

                closeBarangayPicker();
            });

            document.addEventListener('focusin', function(event) {
                if (barangaySelect.hidden) {
                    return;
                }

                if (isWithinBarangayPicker(event.target)) {
                    return;
                }

                closeBarangayPicker();
            });

            // Initialize city and barangay lists based on current values
            const initialProvince = provinceSelect.value;
            const initialCity = citySelect.dataset.selected || citySelect.value;
            let initialBarangays = [];
            try {
                initialBarangays = JSON.parse(barangayHidden.value || '[]');
                if (!Array.isArray(initialBarangays)) {
                    initialBarangays = [];
                }
            } catch (err) {
                initialBarangays = [];
            }

            if (initialProvince && locationData[initialProvince]) {
                citySelect.innerHTML = '<option value="">-- Select City/Municipality --</option>';
                Object.keys(locationData[initialProvince]).forEach((city) => {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    citySelect.appendChild(option);
                });
                if (initialCity) {
                    const matchedCity = Object.keys(locationData[initialProvince]).find((city) => {
                        return city.toLowerCase() === initialCity.toLowerCase();
                    });
                    if (matchedCity) {
                        citySelect.value = matchedCity;
                    } else {
                        const customCityOption = document.createElement('option');
                        customCityOption.value = initialCity;
                        customCityOption.textContent = initialCity;
                        citySelect.appendChild(customCityOption);
                        citySelect.value = initialCity;
                    }
                }
            } else if (initialCity) {
                citySelect.innerHTML = '<option value="">-- Select City/Municipality --</option>';
                const customCityOption = document.createElement('option');
                customCityOption.value = initialCity;
                customCityOption.textContent = initialCity;
                citySelect.appendChild(customCityOption);
                citySelect.value = initialCity;
            }

            const resolvedCity = citySelect.value || initialCity;
            if (initialProvince && resolvedCity && locationData[initialProvince]) {
                const matchedCityKey = Object.keys(locationData[initialProvince]).find((city) => {
                    return city.toLowerCase() === resolvedCity.toLowerCase();
                });
                if (matchedCityKey && locationData[initialProvince][matchedCityKey]) {
                    availableBarangays = normalizeBarangayList(locationData[initialProvince][matchedCityKey]);
                }
            }

            if (!availableBarangays.length && initialBarangays.length) {
                availableBarangays = normalizeBarangayList(initialBarangays);
            }

            selectedBarangays = {};
            initialBarangays.forEach((barangay) => {
                if (barangay) {
                    selectedBarangays[barangay] = true;
                }
            });
            updateBadges();
            closeBarangayPicker();
        }

        const profileForm = document.getElementById('editProfileForm');
        if (profileForm) {
            profileForm.addEventListener('submit', (event) => {
                const hiddenField = document.getElementById('barangay_hidden');
                if (hiddenField) {
                    const selectedList = Object.keys(selectedBarangays);
                    if (selectedList.length === 0) {
                        event.preventDefault();
                        if (typeof window.showSystemErrorModal === 'function') {
                            window.showSystemErrorModal('Please select at least one barangay.');
                        } else {
                            alert('Please select at least one barangay.');
                        }
                        return;
                    }
                    hiddenField.value = JSON.stringify(selectedList);
                }

                ['lgsf_allocation', 'lgu_counterpart'].forEach((fieldId) => {
                    const field = profileForm.querySelector('#' + fieldId);
                    if (field) {
                        const cleaned = field.value.replace(/[^\d.]/g, '');
                        field.value = cleaned === '' ? '0' : cleaned;
                    }
                });
            });
        }

        const contractForm = document.getElementById('editContractForm');
        if (contractForm) {
            contractForm.addEventListener('submit', () => {
                const field = contractForm.querySelector('#contract_amount');
                if (field) {
                    const cleaned = field.value.replace(/[^\d.]/g, '');
                    field.value = cleaned === '' ? '0' : cleaned;
                }
            });
        }

        function submitFormWithAutoSave(form, submitter) {
            if (!form || form.dataset.autoSaveSubmitting === 'true') {
                return;
            }

            if (typeof form.reportValidity === 'function' && !form.reportValidity()) {
                return;
            }

            form.dataset.autoSaveSubmitting = 'true';

            if (submitter && typeof form.requestSubmit === 'function') {
                form.requestSubmit(submitter);
                return;
            }

            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
                return;
            }

            form.submit();
        }

        function bindInlineAutoSave(selector) {
            document.querySelectorAll(selector).forEach((field) => {
                field.addEventListener('change', () => {
                    if (field.disabled) {
                        return;
                    }

                    const form = field.closest('form');
                    if (!form) {
                        return;
                    }

                    const saveButton = form.querySelector(
                        '[data-financial-save="true"], [data-monitoring-save="true"], [data-post-implementation-save="true"]'
                    );

                    submitFormWithAutoSave(form, saveButton);
                });
            });
        }

        bindInlineAutoSave('[data-physical-edit="true"]');
        bindInlineAutoSave('[data-financial-edit="true"]');
        bindInlineAutoSave('[data-monitoring-edit="true"]');
        bindInlineAutoSave('[data-post-implementation-edit="true"]');

        async function copyTextToClipboard(text) {
            if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                await navigator.clipboard.writeText(text);
                return;
            }

            const fallbackField = document.createElement('textarea');
            fallbackField.value = text;
            fallbackField.setAttribute('readonly', '');
            fallbackField.style.position = 'absolute';
            fallbackField.style.left = '-9999px';
            document.body.appendChild(fallbackField);
            fallbackField.select();
            document.execCommand('copy');
            document.body.removeChild(fallbackField);
        }

        document.querySelectorAll('.project-copy-button').forEach((button) => {
            let resetTimer;

            button.addEventListener('click', async () => {
                const label = button.querySelector('[data-copy-label="true"]');
                const originalLabel = label ? label.textContent : 'Copy';

                try {
                    await copyTextToClipboard(button.dataset.copyText || '');
                    button.classList.add('is-copied');
                    if (label) {
                        label.textContent = 'Copied';
                    }

                    window.clearTimeout(resetTimer);
                    resetTimer = window.setTimeout(() => {
                        button.classList.remove('is-copied');
                        if (label) {
                            label.textContent = originalLabel;
                        }
                    }, 1800);
                } catch (error) {
                    if (typeof window.showSystemErrorModal === 'function') {
                        window.showSystemErrorModal('Unable to copy the project code.');
                    } else {
                        alert('Unable to copy the project code.');
                    }
                }
            });
        });

        const projectTabs = Array.from(document.querySelectorAll('.project-tab'));
        const projectPanels = Array.from(document.querySelectorAll('.project-tab-panel'));
        const oldSectionKey = @json(old('section'));
        const sectionKeyToPanelId = {
            profile: 'projectProfileSection',
            contract: 'contractInfoSection',
            physical: 'physicalAccomplishmentSection',
            financial: 'financialAccomplishmentSection',
            monitoring: 'monitoringInspectionSection',
            'post-implementation': 'postImplementationSection',
        };

        function setActiveProjectPanel(panelId) {
            projectPanels.forEach((panel) => {
                const isActive = panel.id === panelId;
                panel.classList.toggle('is-active', isActive);
                panel.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            });

            projectTabs.forEach((tab) => {
                const isActive = tab.dataset.projectTabTarget === panelId;
                tab.classList.toggle('is-active', isActive);
                tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });
        }

        if (projectTabs.length > 0 && projectPanels.length > 0) {
            projectTabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    const panelId = tab.dataset.projectTabTarget;
                    if (panelId) {
                        setActiveProjectPanel(panelId);
                    }
                });
            });

            const hashPanelId = window.location.hash ? window.location.hash.replace('#', '') : '';
            const hasHashPanel = projectPanels.some((panel) => panel.id === hashPanelId);
            const oldPanelId = oldSectionKey ? sectionKeyToPanelId[oldSectionKey] : '';
            const initialPanelId = hasHashPanel
                ? hashPanelId
                : (oldPanelId || 'projectProfileSection');

            setActiveProjectPanel(initialPanelId);
        }

        const activityLogSection = document.getElementById('activityLogSection');
        const activityLogBackdrop = document.getElementById('activityLogBackdrop');
        const activityLogFab = document.getElementById('activityLogFab');
        const activityLogClose = document.getElementById('activityLogClose');

        function setActivityLogVisibility(isVisible) {
            if (!activityLogSection || !activityLogFab || !activityLogBackdrop) {
                return;
            }

            activityLogSection.classList.toggle('is-visible', isVisible);
            activityLogBackdrop.classList.toggle('is-visible', isVisible);
            activityLogFab.setAttribute('aria-expanded', isVisible ? 'true' : 'false');
            activityLogFab.dataset.state = isVisible ? 'open' : 'closed';
            activityLogSection.setAttribute('aria-hidden', isVisible ? 'false' : 'true');
            activityLogBackdrop.setAttribute('aria-hidden', isVisible ? 'false' : 'true');
            syncBodyModalState();

            const labelSpan = activityLogFab.querySelector('span');
            if (labelSpan) {
                labelSpan.textContent = isVisible ? 'Activity Logs' : 'Activity Logs';
            }

            if (isVisible && activityLogClose) {
                activityLogClose.focus();
            }
        }

        if (activityLogFab && activityLogSection && activityLogBackdrop) {
            activityLogFab.addEventListener('click', () => {
                const isOpen = activityLogSection.classList.contains('is-visible');
                setActivityLogVisibility(!isOpen);
            });

            activityLogBackdrop.addEventListener('click', () => {
                setActivityLogVisibility(false);
            });

            if (activityLogClose) {
                activityLogClose.addEventListener('click', () => {
                    setActivityLogVisibility(false);
                });
            }

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') {
                    return;
                }

                if (activityLogSection.classList.contains('is-visible')) {
                    setActivityLogVisibility(false);
                }
            });

            if (window.location.hash === '#activityLogSection') {
                setActivityLogVisibility(true);
            }
        }

        // Monthly accordion uses native <details> only; no JS needed.
    </script>
@endsection
