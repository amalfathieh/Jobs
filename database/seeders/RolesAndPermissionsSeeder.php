<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
          'setting create','setting view','setting edit','setting delete',
          'permission create','permission view','permission edit','permission delete',
          'role create','role view','role edit','role delete',
          'employee create','employee view','employee edit','employee delete',
          'user view','user edit','user delete',
          'post view','post edit','post delete',
          'opportunity view','opportunity edit','opportunity delete',
          'request view','request edit','request delete',
          'news create','news view','news edit','news delete',
          'report create','report view','report edit','report delete',
        ];

        $per = collect($permissions)->map(function ($permission){
            return [ 'name' => $permission, 'guard_name' => 'web'];
        });
        Permission::insert($per->toArray() );

        $adminRole = Role::create(['name'=>'super_admin'])->givePermissionTo($permissions);
        $account_manager = Role::create(['name'=>'account_manager'])->givePermissionTo([ 'user view','user edit','user delete']);
        $technical_support_manger = Role::create(['name'=>'technical_support_manger'])->givePermissionTo(['news create','news view','news edit','news delete',
            'report create','report view','report edit','report delete']);
    }
}
