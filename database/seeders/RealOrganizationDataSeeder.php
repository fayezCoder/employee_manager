<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\OrganizationalUnit;
use App\Models\User;
use App\Models\UserUnitAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RealOrganizationDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Organizational Units
            $hq = OrganizationalUnit::firstOrCreate(
                ['code' => 'HQ-MAIN'],
                [
                    'name' => 'القيادة الرئيسية',
                    'type' => OrganizationalUnit::TYPE_MAIN_COMMAND,
                    'parent_id' => null,
                    'is_active' => true,
                ]
            );

            $divisionAdm = OrganizationalUnit::firstOrCreate(
                ['code' => 'HQ-ADM'],
                [
                    'name' => 'شعبة الإدارة',
                    'type' => OrganizationalUnit::TYPE_DIVISION,
                    'parent_id' => $hq->id,
                    'is_active' => true,
                ]
            );

            $deptFollowup = OrganizationalUnit::firstOrCreate(
                ['code' => 'HQ-ADM-FOL'],
                [
                    'name' => 'قسم المتابعة',
                    'type' => OrganizationalUnit::TYPE_DEPARTMENT,
                    'parent_id' => $divisionAdm->id,
                    'is_active' => true,
                ]
            );

            $divisionHr = OrganizationalUnit::firstOrCreate(
                ['code' => 'HQ-HR'],
                [
                    'name' => 'شعبة الموارد البشرية',
                    'type' => OrganizationalUnit::TYPE_DIVISION,
                    'parent_id' => $hq->id,
                    'is_active' => true,
                ]
            );

            $sub1 = OrganizationalUnit::firstOrCreate(
                ['code' => 'SUB-01'],
                [
                    'name' => 'القيادة الفرعية الأولى',
                    'type' => OrganizationalUnit::TYPE_BRANCH_COMMAND,
                    'parent_id' => $hq->id,
                    'is_active' => true,
                ]
            );

            $ctr1 = OrganizationalUnit::firstOrCreate(
                ['code' => 'CTR-01'],
                [
                    'name' => 'المركز الأول',
                    'type' => OrganizationalUnit::TYPE_CENTER,
                    'parent_id' => $sub1->id,
                    'is_active' => true,
                ]
            );

            $ctr2 = OrganizationalUnit::firstOrCreate(
                ['code' => 'CTR-02'],
                [
                    'name' => 'المركز الثاني',
                    'type' => OrganizationalUnit::TYPE_CENTER,
                    'parent_id' => $sub1->id,
                    'is_active' => true,
                ]
            );

            $sub2 = OrganizationalUnit::firstOrCreate(
                ['code' => 'SUB-02'],
                [
                    'name' => 'القيادة الفرعية الثانية',
                    'type' => OrganizationalUnit::TYPE_BRANCH_COMMAND,
                    'parent_id' => $hq->id,
                    'is_active' => true,
                ]
            );

            $ctr3 = OrganizationalUnit::firstOrCreate(
                ['code' => 'CTR-03'],
                [
                    'name' => 'المركز الثالث',
                    'type' => OrganizationalUnit::TYPE_CENTER,
                    'parent_id' => $sub2->id,
                    'is_active' => true,
                ]
            );

            // 2. System Users and Managers
            $defaultPassword = Hash::make('password');

            // 2.1 Super Admin
            $superAdmin = User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'اللواء عبد الله الشمري (المسؤول الأول)',
                    'password' => $defaultPassword,
                    'role' => 'admin',
                    'is_active' => true,
                    'force_password_change' => false,
                ]
            );
            $superAdmin->update(['name' => 'اللواء عبد الله الشمري (المسؤول الأول)']);

            UserUnitAssignment::firstOrCreate(
                [
                    'user_id' => $superAdmin->id,
                    'org_unit_id' => null,
                    'role' => UserUnitAssignment::ROLE_SUPER_ADMIN,
                ],
                [
                    'include_sub_branches' => true,
                    'can_edit_approved' => true,
                    'can_manage_employees' => true,
                ]
            );

            // 2.2 Branch Command Admin (القيادة الفرعية الأولى)
            $branchAdmin = User::firstOrCreate(
                ['email' => 'branch_admin@example.com'],
                [
                    'name' => 'العميد إبراهيم الغامدي (قائد القيادة الفرعية الأولى)',
                    'password' => $defaultPassword,
                    'role' => 'employee',
                    'is_active' => true,
                    'force_password_change' => false,
                ]
            );
            $branchAdmin->update(['name' => 'العميد إبراهيم الغامدي (قائد القيادة الفرعية الأولى)']);

            UserUnitAssignment::updateOrCreate(
                [
                    'user_id' => $branchAdmin->id,
                    'org_unit_id' => $sub1->id,
                ],
                [
                    'role' => UserUnitAssignment::ROLE_BRANCH_COMMAND_ADMIN,
                    'include_sub_branches' => true,
                    'can_edit_approved' => false,
                    'can_manage_employees' => true,
                ]
            );

            // 2.3 Center 1 Admin (مسؤول المركز الأول)
            $center1Admin = User::firstOrCreate(
                ['email' => 'unit_admin@example.com'],
                [
                    'name' => 'الرائد عبد الرحمن الزهراني (قائد المركز الأول)',
                    'password' => $defaultPassword,
                    'role' => 'employee',
                    'is_active' => true,
                    'force_password_change' => false,
                ]
            );
            $center1Admin->update(['name' => 'الرائد عبد الرحمن الزهراني (قائد المركز الأول)']);

            UserUnitAssignment::updateOrCreate(
                [
                    'user_id' => $center1Admin->id,
                    'org_unit_id' => $ctr1->id,
                ],
                [
                    'role' => UserUnitAssignment::ROLE_CENTER_ADMIN,
                    'include_sub_branches' => false,
                    'can_edit_approved' => false,
                    'can_manage_employees' => true,
                ]
            );

            // 2.4 Division Admin (مدير شعبة الإدارة)
            $divisionAdmin = User::firstOrCreate(
                ['email' => 'division_admin@example.com'],
                [
                    'name' => 'العقيد محمد القحطاني (مدير شعبة الإدارة)',
                    'password' => $defaultPassword,
                    'role' => 'employee',
                    'is_active' => true,
                    'force_password_change' => false,
                ]
            );
            UserUnitAssignment::updateOrCreate(
                [
                    'user_id' => $divisionAdmin->id,
                    'org_unit_id' => $divisionAdm->id,
                ],
                [
                    'role' => UserUnitAssignment::ROLE_DIVISION_ADMIN,
                    'include_sub_branches' => true,
                    'can_edit_approved' => false,
                    'can_manage_employees' => true,
                ]
            );

            // 2.5 Section Admin (رئيس قسم المتابعة)
            $deptAdmin = User::firstOrCreate(
                ['email' => 'dept_admin@example.com'],
                [
                    'name' => 'النقيب خالد السبيعي (رئيس قسم المتابعة)',
                    'password' => $defaultPassword,
                    'role' => 'employee',
                    'is_active' => true,
                    'force_password_change' => false,
                ]
            );
            UserUnitAssignment::updateOrCreate(
                [
                    'user_id' => $deptAdmin->id,
                    'org_unit_id' => $deptFollowup->id,
                ],
                [
                    'role' => UserUnitAssignment::ROLE_DEPARTMENT_ADMIN,
                    'include_sub_branches' => false,
                    'can_edit_approved' => false,
                    'can_manage_employees' => true,
                ]
            );

            // 2.6 Regular Employee (سارة محمد)
            $saraUser = User::firstOrCreate(
                ['email' => 'sara@example.com'],
                [
                    'name' => 'سارة محمد',
                    'password' => $defaultPassword,
                    'role' => 'employee',
                    'is_active' => true,
                    'force_password_change' => false,
                ]
            );
            // Ensure Sara has NO unit assignments so she is purely a regular employee
            UserUnitAssignment::where('user_id', $saraUser->id)->delete();

            // 3. Real Active Employees Distributed Across Units
            $employeesList = [
                // القيادة الرئيسية
                [
                    'name' => 'العقيد فهد العتيبي',
                    'national_id' => '1011122233',
                    'job_title' => 'مدير مكتب القيادة',
                    'org_unit_id' => $hq->id,
                ],
                [
                    'name' => 'الرائد سلطان الدوسري',
                    'national_id' => '1022233344',
                    'job_title' => 'ضابط التنسيق والمتابعة',
                    'org_unit_id' => $hq->id,
                ],
                [
                    'name' => 'الموظف تركي الحربي',
                    'national_id' => '1033344455',
                    'job_title' => 'مدخل بيانات',
                    'org_unit_id' => $hq->id,
                ],

                // شعبة الإدارة
                [
                    'name' => 'الموظف سعد المطيري',
                    'national_id' => '1044455566',
                    'job_title' => 'أخصائي شؤون إدارية',
                    'org_unit_id' => $divisionAdm->id,
                ],
                [
                    'name' => 'الموظف حمد القحطاني',
                    'national_id' => '1055566677',
                    'job_title' => 'سكرتير إداري',
                    'org_unit_id' => $divisionAdm->id,
                ],

                // قسم المتابعة
                [
                    'name' => 'الموظف أحمد علي',
                    'national_id' => '1066677788',
                    'job_title' => 'باحث متابعة ميدانية',
                    'org_unit_id' => $deptFollowup->id,
                ],
                [
                    'name' => 'الموظفة ريم الشمري',
                    'national_id' => '1077788899',
                    'job_title' => 'أخصائية تقارير ومتابعة',
                    'org_unit_id' => $deptFollowup->id,
                ],

                // شعبة الموارد البشرية
                [
                    'name' => 'سارة محمد',
                    'national_id' => '1088899900',
                    'job_title' => 'أخصائية شؤون موظفين',
                    'org_unit_id' => $divisionHr->id,
                    'user_id' => $saraUser->id,
                ],
                [
                    'name' => 'الموظفة فاطمة الزهراء',
                    'national_id' => '1099900011',
                    'job_title' => 'مسؤولة تدريب وتطوير',
                    'org_unit_id' => $divisionHr->id,
                ],

                // القيادة الفرعية الأولى (موظفو القيادة المباشرون)
                [
                    'name' => 'الرائد منصور الشهري',
                    'national_id' => '1100011122',
                    'job_title' => 'مساعد قائد القيادة الفرعية',
                    'org_unit_id' => $sub1->id,
                ],
                [
                    'name' => 'الموظف عبد العزيز العنزي',
                    'national_id' => '1111122233',
                    'job_title' => 'كاتب سجلات ومراسلات',
                    'org_unit_id' => $sub1->id,
                ],

                // المركز الأول (التابع للقيادة الفرعية الأولى)
                [
                    'name' => 'عمر الفاروق',
                    'national_id' => '1122233344',
                    'job_title' => 'مشغل أنظمة مناوب',
                    'org_unit_id' => $ctr1->id,
                ],
                [
                    'name' => 'يوسف إبراهيم',
                    'national_id' => '1133344455',
                    'job_title' => 'فني تشغيل وصيانة',
                    'org_unit_id' => $ctr1->id,
                ],
                [
                    'name' => 'مريم حسن',
                    'national_id' => '1144455566',
                    'job_title' => 'مراقبة إدارية',
                    'org_unit_id' => $ctr1->id,
                ],

                // المركز الثاني (التابع للقيادة الفرعية الأولى)
                [
                    'name' => 'خالد عبد الله',
                    'national_id' => '1155566677',
                    'job_title' => 'مناوب عمليات ومراقبة',
                    'org_unit_id' => $ctr2->id,
                ],
                [
                    'name' => 'نورة التميمي',
                    'national_id' => '1166677788',
                    'job_title' => 'مسؤولة دعم ومساندة',
                    'org_unit_id' => $ctr2->id,
                ],

                // المركز الثالث (التابع للقيادة الفرعية الثانية)
                [
                    'name' => 'مشعل الشمري',
                    'national_id' => '1177788899',
                    'job_title' => 'مناوب أمني',
                    'org_unit_id' => $ctr3->id,
                ],
                [
                    'name' => 'ريان الحربي',
                    'national_id' => '1188899900',
                    'job_title' => 'مساعد إداري',
                    'org_unit_id' => $ctr3->id,
                ],
            ];

            foreach ($employeesList as $empData) {
                Employee::updateOrCreate(
                    ['national_id' => $empData['national_id']],
                    [
                        'name' => $empData['name'],
                        'job_title' => $empData['job_title'],
                        'org_unit_id' => $empData['org_unit_id'],
                        'user_id' => $empData['user_id'] ?? null,
                        'is_active' => true,
                        'needs_review' => false,
                        'review_notes' => null,
                    ]
                );
            }
        });
    }
}
