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
            'view companies',
            'view users',
            'view posts',
            'delete post',

            // employee
            'delete opportunity',
            'block user',
            'news control',
            'view reports user', 'delete report user',
            'admin report', 'view admin reports',

            // Company
            'opportunity control',
            'edit request',
            'company profile control',

            // Seeker
            'post control',
            'request control',
            'seeker profile control'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $owner = Role::create(['name' => 'owner'])->givePermissionTo($permissions);

        $techSupportTeam = Role::create(['name' => 'tech_support_team'])->givePermissionTo([
            'delete post',
            'delete opportunity'
        ]);

        $userRole = Role::create(['name' => 'user'])->givePermissionTo([
            'report user', 'view news'
        ]);

        $companyRole = Role::create(['name' => 'company'])->givePermissionTo([
            'opportunity control', 'edit request',
            'delete opportunity',
            'company profile control'
        ]);

        $jobSeekerRole = Role::create(['name' => 'job_seeker'])->givePermissionTo([
            'post control',
            'request control',
            'delete post',
            'seeker profile control'
        ]);

        $employeeRole = Role::create(['name' => 'employee'])->givePermissionTo([
            'employee control',
            'view employees',
            'view opportunities',
            'view users',
            'view posts',
            'view companies',
        ]);
    }
}
