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
            'view settings', 'edit setting', 'delete setting',
            'add role', 'view roles', 'edit role', 'delete role',
            'add employee', 'view employees', 'edit employee', 'delete employee',
            'view users', 'edit user', 'delete user', 'block user',
            'add opportunity', 'view opportunities', 'edit opportunity', 'delete opportunity',
            'add request', 'view requests', 'edit request', 'delete request',
            'add news', 'view news', 'edit news', 'delete news',
            'adminReport create', 'adminReport view', 'adminReport edit', 'adminReport delete',
            'create userReport', 'view userReports', 'edit userReport', 'delete userReport',
            'add post', 'edit post', 'view posts', 'delete post',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $owner = Role::create(['name' => 'owner'])->givePermissionTo($permissions);

        $techSupportTeam = Role::create(['name' => 'tech_support_team'])->givePermissionTo([
            'adminReport create', 'adminReport view', 'adminReport edit', 'adminReport delete',
            'view settings', 'edit setting', 'delete setting',
            'add news', 'view news', 'edit news', 'delete news',
            'view roles',
            'block user',
            'view posts', 'delete post',
        ]);

        $userRole = Role::create(['name' => 'user'])->givePermissionTo([
            'create userReport'
        ]);

        $companyRole = Role::create(['name' => 'company'])->givePermissionTo([
            'add opportunity',
        ]);

        $jobSeekerRole = Role::create(['name' => 'job_seeker'])->givePermissionTo([
            'add post',
            'add request'
        ]);

        $employeeRole = Role::create(['name' => 'employee'])->givePermissionTo([
            'view employees'
        ]);
    }
}
