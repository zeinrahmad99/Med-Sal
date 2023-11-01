<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Api\V1\Role;
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles=[
            ['name'=>'super_admin','name_ar'=>'المالك'],
            ['name'=>'admin','name_ar'=>'المشرف'],
            ['name'=>'provider','name_ar'=>'مقدم الخدمة'],
        ];
        Role::insert($roles);
   }
}
