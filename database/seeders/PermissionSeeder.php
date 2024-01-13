<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Api\V1\Role;
use App\Models\Api\V1\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $provider=Role::where('name','provider')->first();
       $admin=Role::where('name','admin')->first();
       $permission=[
        ['role_id'=>$admin->id,'ability'=>'view products and services','ability_ar'=>' عرض المنتاجات والخدمات'],
        ['role_id'=>$admin->id,'ability'=>'accept provider register','ability_ar'=>'قبول مزود خدمة'],
        ['role_id'=>$admin->id,'ability'=>'reject provider register','ability_ar'=>'رفض مزود خدمة'],
        ['role_id'=>$admin->id,'ability'=>'accept product','ability_ar'=>'قبول منتج'],
        ['role_id'=>$admin->id,'ability'=>'remove product','ability_ar'=>'رفض منتج'],
        ['role_id'=>$admin->id,'ability'=>'accept service','ability_ar'=>'قبول خدمة'],
        ['role_id'=>$admin->id,'ability'=>'remove service','ability_ar'=>'رفض خدمة'],
        ['role_id'=>$provider->id,'ability'=>'view products and services','ability_ar'=>'عرض المنتجات و الخدمات'],
        ['role_id'=>$provider->id,'ability'=>'add product','ability_ar'=>'إضافة منتج'],
        ['role_id'=>$provider->id,'ability'=>'update product','ability_ar'=>'تعديل منتج'],
        ['role_id'=>$provider->id,'ability'=>'remove product','ability_ar'=>'حذف منتج'],
        ['role_id'=>$provider->id,'ability'=>'delete product','ability_ar'=>'حذف نهائي للمنتج'],
        ['role_id'=>$provider->id,'ability'=>'add service','ability_ar'=>'إضافة خدمة'],
        ['role_id'=>$provider->id,'ability'=>'update service','ability_ar'=>'تعديل خدمة'],
        ['role_id'=>$provider->id,'ability'=>'remove service','ability_ar'=>'حذف خدمة'],
        ['role_id'=>$provider->id,'ability'=>'delete service','ability_ar'=>'حذف نهائي للخدمة'],
        ['role_id'=>$provider->id,'ability'=>'edit personal data','ability_ar'=>'تعديل البيانات الخاصة'],
        ['role_id'=>$provider->id,'ability'=>'approve order','ability_ar'=>'قبول الطلب'],
        ['role_id'=>$provider->id,'ability'=>'reject order','ability_ar'=>'رفض الطلب'],
        ['role_id'=>$provider->id,'ability'=>'approve service','ability_ar'=>'قبول الخدمة'],
        ['role_id'=>$provider->id,'ability'=>'reject service','ability_ar'=>'رفض الخدمة'],
        ['role_id'=>$provider->id,'ability'=>'user management','ability_ar'=>'ادارة المستخدمين'],

    ];

       Permission::insert($permission);
    }
}
