@extends('layouts.dashboard')

@section('title', 'View User')
@section('page-title', 'View User')

@section('content')
    @php($roleOptions = \App\Models\User::roleOptions())

    <div class="content-header">
        <h1>View User</h1>
        <p>Preview user information and account settings.</p>
    </div>

    @if (session('success'))
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <section>
        <form action="{{ route('users.update', $user->idno) }}" method="POST" id="userPreviewForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="{{ $user->status }}">

            <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; gap: 16px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #002C76 0%, #003d99 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 32px; margin-right: 20px;">
                            {{ strtoupper(substr($user->fname, 0, 1) . substr($user->lname, 0, 1)) }}
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 4px;">
                                <h2 style="margin: 0; color: #002C76; font-size: 20px;">{{ $user->fname }} {{ $user->lname }}</h2>
                                <div class="user-status-pill-group" data-status-group aria-label="User status" data-edit-state="inactive">
                                    <span class="user-status-pill" data-status-value="{{ strtolower((string) $user->status) }}" data-active="true">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </div>
                            </div>
                            <p style="margin: 0; color: #6b7280; font-size: 14px;">{{ $user->username }}</p>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <a href="{{ route('users.index') }}" class="user-preview-secondary-btn">
                            <i data-feather="arrow-left" style="width: 16px; height: 16px;"></i> Back
                        </a>
                        <button type="button" id="userEditToggleBtn" class="user-preview-primary-btn">
                            <i data-feather="edit-3" style="width: 16px; height: 16px;"></i> Edit
                        </button>
                        <button type="button" id="userSaveBtn" class="user-preview-primary-btn" style="display: none;" disabled>
                            <i data-feather="save" style="width: 16px; height: 16px;"></i> Save
                        </button>
                        <button type="button" id="userCancelBtn" class="user-preview-secondary-btn !bg-red-400 !text-white" style="display: none;">
                            <i data-feather="x" style="width: 16px; height: 16px;"></i> Cancel
                        </button>
                    </div>
                </div>

                <div style="border-top: 1px solid #e5e7eb; padding-top: 30px; margin-bottom: 30px;">
                    <h3 style="color: #002C76; font-size: 16px; margin: 0 0 20px; font-weight: 600;">Personal Information</h3>
                    <div class="user-preview-grid user-preview-grid--wide" style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px;">
                        <div>
                            <label class="user-preview-label">First Name <span class="user-preview-required">*</span></label>
                            <input type="text" name="fname" value="{{ $user->fname }}" required class="user-preview-input" data-editable>
                        </div>
                        <div>
                            <label class="user-preview-label">Last Name <span class="user-preview-required">*</span></label>
                            <input type="text" name="lname" value="{{ $user->lname }}" required class="user-preview-input" data-editable>
                        </div>
                        <div>
                            <label class="user-preview-label">Email Address <span class="user-preview-required">*</span></label>
                            <input type="email" name="emailaddress" value="{{ $user->emailaddress }}" required class="user-preview-input" data-editable>
                        </div>
                        <div>
                            <label class="user-preview-label">Mobile Number <span class="user-preview-required">*</span></label>
                            <input type="text" name="mobileno" value="{{ $user->mobileno }}" required maxlength="11" pattern="[0-9]{11}" inputmode="numeric" class="user-preview-input" data-editable>
                        </div>
                        <div>
                            <label class="user-preview-label">Username <span class="user-preview-required">*</span></label>
                            <input type="text" name="username" value="{{ $user->username }}" required class="user-preview-input" data-editable>
                        </div>
                        <div>
                            <label class="user-preview-label">Role <span class="user-preview-required">*</span></label>
                            <select id="roleSelect" name="role" required class="user-preview-input" data-editable>
                                @foreach($roleOptions as $roleValue => $roleLabel)
                                    <option value="{{ $roleValue }}" @selected($user->role === $roleValue)>{{ $roleLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="user-preview-label">Email Verified At</label>
                            <input type="text" value="{{ $user->email_verified_at ? $user->email_verified_at->format('Y-m-d h:i A') : '-' }}" class="user-preview-input user-preview-input--meta" disabled>
                        </div>
                    </div>
                </div>

                <div style="border-top: 1px solid #e5e7eb; padding-top: 30px; margin-bottom: 30px;">
                    <h3 style="color: #002C76; font-size: 16px; margin: 0 0 20px; font-weight: 600;">Organization Information</h3>
                    <div class="user-preview-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label class="user-preview-label">Agency/LGU <span class="user-preview-required">*</span></label>
                            <select id="agencySelect" name="agency" required class="user-preview-input" data-editable>
                                <option value="">Select Agency/LGU</option>
                                <option value="DILG" @selected($user->agency === 'DILG')>DILG</option>
                                <option value="LGU" @selected($user->agency === 'LGU')>LGU</option>
                            </select>
                        </div>
                        <div>
                            <label class="user-preview-label">Position <span class="user-preview-required">*</span></label>
                            <select id="positionSelect" name="position" required class="user-preview-input" data-editable>
                                <option value="{{ $user->position }}" selected>{{ $user->position }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="user-preview-label">Region <span class="user-preview-required">*</span></label>
                            <input type="text" name="region" value="{{ $user->region }}" required class="user-preview-input" data-editable>
                        </div>
                        <div>
                            <label class="user-preview-label">Province <span class="user-preview-required">*</span></label>
                            <select id="provinceSelect" name="province" required class="user-preview-input" data-editable>
                                <option value="{{ $user->province }}" selected>{{ $user->province }}</option>
                            </select>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label class="user-preview-label">Office</label>
                            <select id="officeSelect" name="office" class="user-preview-input" data-editable>
                                <option value="{{ $user->office }}" selected>{{ $user->office }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="border-top: 1px solid #e5e7eb; padding-top: 30px;">
                    <h3 style="color: #002C76; font-size: 16px; margin: 0 0 20px; font-weight: 600;">Account Information</h3>
                    <div class="user-preview-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label class="user-preview-label">Password <span style="color: #999; font-size: 12px;">(Leave blank to keep current)</span></label>
                            <input type="password" name="password" value="" class="user-preview-input" data-editable autocomplete="new-password">
                        </div>
                        <div>
                            <label class="user-preview-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" value="" class="user-preview-input" data-editable autocomplete="new-password">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <style>
        .user-preview-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            background: #f8fafc;
            color: #111827;
            transition: all 0.2s ease;
        }

        .user-preview-input:disabled {
            background: #f8fafc;
            color: #111827;
            opacity: 1;
            cursor: default;
        }

        .user-preview-input[data-edit-state="active"] {
            background: #ffffff;
        }

        .user-preview-label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
        }

        .user-preview-required {
            color: #dc2626;
        }

        .user-preview-primary-btn,
        .user-preview-secondary-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .user-preview-primary-btn {
            background-color: #002C76;
            color: white;
        }

        .user-preview-primary-btn:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .user-preview-secondary-btn {
            background-color: #e5e7eb;
            color: #374151;
        }

        .user-preview-primary-btn:hover:not(:disabled) {
            background-color: #001f59 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 44, 118, 0.2);
        }

        .user-preview-secondary-btn:hover {
            background-color: #d1d5db !important;
            transform: translateY(-2px);
        }

        .user-preview-input--meta {
            background: #f1f5f9;
        }

        .user-status-pill-group {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            min-height: 46px;
        }

        .user-status-pill {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            border-radius: 999px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
            cursor: default;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .user-status-pill[data-active="true"][data-status-value="active"] {
            background: #dcfce7;
            border-color: #86efac;
            color: #166534;
        }

        .user-status-pill[data-active="true"][data-status-value="inactive"] {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .user-preview-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.feather) {
                feather.replace();
            }

            const form = document.getElementById('userPreviewForm');
            const editToggleBtn = document.getElementById('userEditToggleBtn');
            const saveBtn = document.getElementById('userSaveBtn');
            const cancelBtn = document.getElementById('userCancelBtn');
            const editableFields = Array.from(form.querySelectorAll('[data-editable]'));
            const agencySelect = document.getElementById('agencySelect');
            const roleSelect = document.getElementById('roleSelect');
            const positionSelect = document.getElementById('positionSelect');
            const provinceSelect = document.getElementById('provinceSelect');
            const officeSelect = document.getElementById('officeSelect');
            let isEditMode = false;
            let initialSnapshot = '';

            const positions = {
                'DILG': [
                    'Engineer II',
                    'Engineer III',
                    'Unit Chief',
                    'Assistant Unit Chief',
                    'Financial Analyst II',
                    'Financial Analyst III',
                    'Project Evaluation Officer II',
                    'Project Evaluation Officer III',
                    'Information Systems Analyst III'
                ],
                'LGU': [
                    'Municipal Engineer I',
                    'Municipal Engineer II',
                    'Municipal Engineer III',
                    'Planning Officer II',
                    'Planning Officer III'
                ]
            };

            const provinces = [
                'Abra', 'Apayao', 'Benguet', 'City of Baguio', 'Ifugao', 'Kalinga', 'Mountain Province'
            ];

            const offices = {
                'Abra': ['PLGU Abra', 'Bangued', 'Boliney', 'Bucay', 'Bucloc', 'Daguioman', 'Danglas', 'Dolores', 'La Paz', 'Lacub', 'Lagangilang', 'Lagayan', 'Langiden', 'Licuan-Baay', 'Luba', 'Malibcong', 'Manabo', 'Peñarrubia', 'Pidigan', 'Pilar', 'Sallapadan', 'San Isidro', 'San Juan', 'San Quintin', 'Tayum', 'Tineg', 'Tubo', 'Villaviciosa'],
                'Apayao': ['PLGU Apayao', 'Calanasan', 'Conner', 'Flora', 'Kabugao', 'Luna', 'Pudtol', 'Santa Marcela'],
                'Benguet': ['PLGU Benguet', 'Atok', 'Bakun', 'Bokod', 'Buguias', 'Itogon', 'Kabayan', 'Kapangan', 'Kibungan', 'La Trinidad', 'Mankayan', 'Sablan', 'Tuba', 'Tublay'],
                'City of Baguio': ['PLGU City of Baguio', 'City of Baguio'],
                'Ifugao': ['PLGU Ifugao', 'Aguinaldo', 'Alfonso Lista', 'Asipulo', 'Banaue', 'Hingyon', 'Hungduan', 'Kiangan', 'Lagawe', 'Lamut', 'Mayoyao', 'Tinoc'],
                'Kalinga': ['PLGU Kalinga', 'Balbalan', 'Lubuagan', 'Pasil', 'Pinukpuk', 'Rizal', 'Tabuk', 'Tanudan'],
                'Mountain Province': ['PLGU Mountain Province', 'Barlig', 'Bauko', 'Besao', 'Bontoc', 'Natonin', 'Paracelis', 'Sabangan', 'Sadanga', 'Sagada', 'Tadian']
            };

            function setEditMode(active) {
                isEditMode = active;

                editableFields.forEach(function (field) {
                    field.disabled = !active;
                    field.readOnly = !active && field.tagName !== 'SELECT';
                    field.dataset.editState = active ? 'active' : 'inactive';
                });

                editToggleBtn.style.display = active ? 'none' : '';
                saveBtn.style.display = active ? '' : 'none';
                cancelBtn.style.display = active ? '' : 'none';

                if (active) {
                    editableFields[0]?.focus();
                }

                refreshSaveState();
            }

            function snapshotForm() {
                const data = {};
                editableFields.forEach(function (field) {
                    data[field.name] = field.value ?? '';
                });

                return JSON.stringify(data);
            }

            function restoreInitialValues() {
                const values = JSON.parse(initialSnapshot);
                editableFields.forEach(function (field) {
                    if (Object.prototype.hasOwnProperty.call(values, field.name)) {
                        field.value = values[field.name];
                    }
                });

                syncDependentFields(true);
                refreshSaveState();
            }

            function isDirty() {
                return snapshotForm() !== initialSnapshot;
            }

            function refreshSaveState() {
                saveBtn.disabled = !isEditMode || !isDirty();
            }

            function updatePositionDropdown(preserveCurrent) {
                const selectedValue = agencySelect.value;
                const currentPosition = preserveCurrent ? positionSelect.value : '';
                positionSelect.innerHTML = '<option value="" disabled>Select Position</option>';

                if (positions[selectedValue]) {
                    positions[selectedValue].forEach(function (position) {
                        const option = document.createElement('option');
                        option.value = position;
                        option.textContent = position;
                        if (position === currentPosition) {
                            option.selected = true;
                        }
                        positionSelect.appendChild(option);
                    });
                }
            }

            function syncAgencyWithRole() {
                if (!roleSelect) {
                    return;
                }

                const role = roleSelect.value;
                let nextAgency = '';

                if (role === 'user_lgu') {
                    nextAgency = 'LGU';
                } else if (role === 'user_regional' || role === 'user_provincial') {
                    nextAgency = 'DILG';
                }

                if (nextAgency !== '' && agencySelect.value !== nextAgency) {
                    agencySelect.value = nextAgency;
                }
            }

            function updateProvinceDropdown(preserveCurrent) {
                const selectedAgency = agencySelect.value;
                const currentProvince = preserveCurrent ? provinceSelect.value : '';
                provinceSelect.innerHTML = '<option value="" disabled>Select Province</option>';

                if (selectedAgency === 'DILG') {
                    const regionalOption = document.createElement('option');
                    regionalOption.value = 'Regional Office';
                    regionalOption.textContent = 'Regional Office';
                    if (currentProvince === 'Regional Office') {
                        regionalOption.selected = true;
                    }
                    provinceSelect.appendChild(regionalOption);
                }

                provinces.forEach(function (province) {
                    const option = document.createElement('option');
                    option.value = province;
                    option.textContent = province;
                    if (province === currentProvince) {
                        option.selected = true;
                    }
                    provinceSelect.appendChild(option);
                });
            }

            function updateOfficeDropdown(preserveCurrent) {
                const selectedAgency = agencySelect.value;
                const selectedProvince = provinceSelect.value;
                const currentOffice = preserveCurrent ? officeSelect.value : '';
                officeSelect.innerHTML = '<option value="">Select Office (Optional)</option>';

                if (selectedAgency === 'LGU' && offices[selectedProvince]) {
                    offices[selectedProvince].forEach(function (office) {
                        const option = document.createElement('option');
                        option.value = office;
                        option.textContent = office;
                        if (office === currentOffice) {
                            option.selected = true;
                        }
                        officeSelect.appendChild(option);
                    });
                } else if (currentOffice) {
                    const option = document.createElement('option');
                    option.value = currentOffice;
                    option.textContent = currentOffice;
                    option.selected = true;
                    officeSelect.appendChild(option);
                }
            }

            function syncDependentFields(preserveCurrent) {
                updatePositionDropdown(preserveCurrent);
                updateProvinceDropdown(preserveCurrent);
                updateOfficeDropdown(preserveCurrent);
            }

            editToggleBtn.addEventListener('click', function () {
                setEditMode(true);
            });

            cancelBtn.addEventListener('click', function () {
                if (!isDirty()) {
                    restoreInitialValues();
                    setEditMode(false);
                    return;
                }

                window.openConfirmationModal(
                    'Discard your unsaved changes to this user profile?',
                    function () {
                        restoreInitialValues();
                        setEditMode(false);
                    }
                );
            });

            saveBtn.addEventListener('click', function () {
                if (saveBtn.disabled) {
                    return;
                }

                window.openConfirmationModal(
                    'Save the changes you made to this user profile?',
                    function () {
                        HTMLFormElement.prototype.submit.call(form);
                    }
                );
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                if (saveBtn.disabled) {
                    return;
                }

                window.openConfirmationModal(
                    'Save the changes you made to this user profile?',
                    function () {
                        HTMLFormElement.prototype.submit.call(form);
                    }
                );
            });

            agencySelect.addEventListener('change', function () {
                updatePositionDropdown(false);
                updateProvinceDropdown(false);
                updateOfficeDropdown(false);
                refreshSaveState();
            });

            roleSelect?.addEventListener('change', function () {
                syncAgencyWithRole();
                updatePositionDropdown(false);
                updateProvinceDropdown(false);
                updateOfficeDropdown(false);
                refreshSaveState();
            });

            provinceSelect.addEventListener('change', function () {
                updateOfficeDropdown(false);
                refreshSaveState();
            });

            editableFields.forEach(function (field) {
                field.addEventListener('input', refreshSaveState);
                field.addEventListener('change', refreshSaveState);
            });

            syncAgencyWithRole();
            syncDependentFields(true);
            initialSnapshot = snapshotForm();
            setEditMode(false);
        });
    </script>
@endsection
