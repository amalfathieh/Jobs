<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // for owner
            'role control',
            'employee control',
            'delete user',

            // user
            'report user',
            'view news',

            // common
            'view employees',
            'view opportunities',
            'view users',
            'view posts',

            // employee
            'delete opportunity',
            'block user',
            'delete post',
            'news control',
            'view reports user', 'delete report user',
            'admin report', 'view admin reports',

            // Company
            'opportunity control',
            'edit request',

            // Seeker
            'post control',
            'request control',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $owner = Role::create(['name' => 'owner'])->givePermissionTo($permissions);

        $techSupportTeam = Role::create(['name' => 'tech_support_team'])->givePermissionTo([

        ]);

        $userRole = Role::create(['name' => 'user'])->givePermissionTo([
            'report user', 'view news'
        ]);

        $companyRole = Role::create(['name' => 'company'])->givePermissionTo([
            'opportunity control', 'edit request'
        ]);

        $jobSeekerRole = Role::create(['name' => 'job_seeker'])->givePermissionTo([
            'post control',
            'request control'
        ]);

        $employeeRole = Role::create(['name' => 'employee'])->givePermissionTo([
            'view employees',
            'view opportunities',
            'view users',
            'view posts',
        ]);
    }
}
