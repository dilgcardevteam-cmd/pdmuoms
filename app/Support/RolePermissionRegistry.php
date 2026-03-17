<?php

namespace App\Support;

use App\Models\User;

class RolePermissionRegistry
{
    public const CRUD_ACTION_OPTIONS = [
        'view' => 'VIEW',
        'add' => 'ADD',
        'update' => 'UPDATE',
        'delete' => 'DELETE',
    ];

    public static function actionOptions(): array
    {
        return self::CRUD_ACTION_OPTIONS;
    }

    public static function modules(): array
    {
        return [
            [
                'module' => 'Project Monitoring',
                'description' => 'Project monitoring modules for project profiles, updates, and accomplishment tracking.',
                'items' => [
                    [
                        'aspect' => 'locally_funded_projects',
                        'label' => 'Locally Funded Projects',
                        'description' => 'View and manage locally funded project records, including profile details and monitoring updates within the role scope.',
                    ],
                ],
            ],
            [
                'module' => 'LGU Reportorial Requirements',
                'description' => 'Annual, quarterly, and monthly LGU reportorial submissions handled by the current system.',
                'items' => [
                    [
                        'aspect' => 'rbis_annual_certification',
                        'label' => 'Annual / RBIS Annual Certification',
                        'description' => 'Manage annual RBIS certification documents and related validation actions.',
                    ],
                    [
                        'aspect' => 'fund_utilization_reports',
                        'label' => 'Quarterly / Fund Utilization Report',
                        'description' => 'Manage quarterly fund utilization records, MOV uploads, notices, and supporting reportorial documents.',
                    ],
                    [
                        'aspect' => 'local_project_monitoring_committee',
                        'label' => 'Quarterly / Local Project Monitoring Committee',
                        'description' => 'Manage quarterly LPMC submissions, uploaded documents, and validation workflow.',
                    ],
                    [
                        'aspect' => 'road_maintenance_status_reports',
                        'label' => 'Quarterly / Road Maintenance Status Report',
                        'description' => 'Manage quarterly road maintenance status submissions, document uploads, and validation steps.',
                    ],
                    [
                        'aspect' => 'pd_no_pbbm_monthly_reports',
                        'label' => 'Monthly / PD No. PBBM-2025-1572-1573',
                        'description' => 'Manage monthly report submissions, uploaded files, and document approval actions.',
                    ],
                ],
            ],
            [
                'module' => 'Pre-Implementation Documents',
                'description' => 'Document requirements collected before implementation begins.',
                'items' => [
                    [
                        'aspect' => 'pre_implementation_documents',
                        'label' => 'SBDP Pre-Implementation Documents',
                        'description' => 'View and add the pre-implementation document set required for SBDP projects before project execution.',
                    ],
                ],
            ],
        ];
    }

    public static function validPermissionKeys(): array
    {
        return collect(self::modules())
            ->flatMap(function (array $module) {
                return collect($module['items'] ?? [])->pluck('aspect');
            })
            ->flatMap(function (string $aspect) {
                return collect(array_keys(self::actionOptions()))
                    ->map(fn (string $action) => $aspect . '.' . $action);
            })
            ->unique()
            ->values()
            ->all();
    }

    public static function configurableRoles(): array
    {
        return [
            User::ROLE_REGIONAL,
            User::ROLE_PROVINCIAL,
            User::ROLE_LGU,
        ];
    }

    public static function roleDescriptions(): array
    {
        return [
            User::ROLE_SUPERADMIN => 'Highest access. Superadmin keeps full access across all modules and system utilities.',
            User::ROLE_REGIONAL => 'Can oversee projects within the designated region, including its provinces and LGUs.',
            User::ROLE_PROVINCIAL => 'Can oversee projects within the designated province, including LGUs inside that province.',
            User::ROLE_LGU => 'Lowest operational scope. Access is limited to the assigned LGU and its submitted records.',
        ];
    }

    public static function defaultPermissionsByRole(): array
    {
        $reportorialPermissions = [
            'fund_utilization_reports.view',
            'fund_utilization_reports.add',
            'fund_utilization_reports.update',
            'fund_utilization_reports.delete',
            'local_project_monitoring_committee.view',
            'local_project_monitoring_committee.add',
            'local_project_monitoring_committee.update',
            'local_project_monitoring_committee.delete',
            'road_maintenance_status_reports.view',
            'road_maintenance_status_reports.add',
            'road_maintenance_status_reports.update',
            'road_maintenance_status_reports.delete',
            'rbis_annual_certification.view',
            'rbis_annual_certification.add',
            'rbis_annual_certification.update',
            'rbis_annual_certification.delete',
            'pd_no_pbbm_monthly_reports.view',
            'pd_no_pbbm_monthly_reports.add',
            'pd_no_pbbm_monthly_reports.update',
            'pd_no_pbbm_monthly_reports.delete',
        ];

        return [
            User::ROLE_SUPERADMIN => ['*'],
            User::ROLE_REGIONAL => array_merge($reportorialPermissions, [
                'locally_funded_projects.view',
                'locally_funded_projects.update',
            ]),
            User::ROLE_PROVINCIAL => array_merge($reportorialPermissions, [
                'locally_funded_projects.view',
                'locally_funded_projects.update',
                'pre_implementation_documents.view',
                'pre_implementation_documents.add',
            ]),
            User::ROLE_LGU => array_merge($reportorialPermissions, [
                'locally_funded_projects.view',
                'pre_implementation_documents.view',
                'pre_implementation_documents.add',
            ]),
        ];
    }

    public static function permissionsForRole(string $role, ?array $configuredPermissions = null): array
    {
        $normalizedRole = strtolower(trim($role));

        if ($normalizedRole === User::ROLE_SUPERADMIN) {
            return ['*'];
        }

        if (is_array($configuredPermissions)) {
            return self::normalizePermissions($configuredPermissions);
        }

        return self::normalizePermissions(self::defaultPermissionsByRole()[$normalizedRole] ?? []);
    }

    public static function normalizePermissions(array $permissions): array
    {
        return collect($permissions)
            ->map(fn ($permission) => strtolower(trim((string) $permission)))
            ->filter(fn ($permission) => $permission === '*' || in_array($permission, self::validPermissionKeys(), true))
            ->unique()
            ->values()
            ->all();
    }
}
