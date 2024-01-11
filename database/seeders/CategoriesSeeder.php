<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            [
                'admin_id'=>1,
                'name' => 'Dr Dental',
                'name_ar' => 'طبيب أسنان',
                'description' => 'Category for dental doctors',
                'description_ar' => 'فئة لأطباء الأسنان',
                'status' => 'active',
            ],
            [
                'admin_id'=>2,
                'name' => 'Dr Optics',
                'name_ar' => 'طبيب بصريات',
                'description' => 'Category for optics doctors',
                'description_ar' => 'فئة لأطباء البصريات',
                'status' => 'active',
            ],
            [
                'admin_id'=>3,
                'name' => 'Dr Nutritionist',
                'name_ar' => 'طبيب اخصائي تغذية',
                'description' => 'Category for nutritionist doctors',
                'description_ar' => 'فئة لأطباء التغذية',
                'status' => 'active',
            ],
            [
                'admin_id'=>4,
                'name' => 'Dr Home Care',
                'name_ar' => 'طبيب الرعاية المنزلية ',
                'description' => 'Category for home care doctors',
                'description_ar' => 'فئة لأطباء الرعاية المنزلية',
                'status' => 'active',
            ],
            [
                'admin_id'=>5,
                'name' => 'Dr Plastic Surgery',
                'name_ar' => 'طبيب جراحة التجميل ',
                'description' => 'Category for plastic surgery doctors',
                'description_ar' => 'فئة لأطباء جراحة التجميل',
                'status' => 'active',
            ],
            [
                'admin_id'=>6,
                'name' => 'Dr Radiologist',
                'name_ar' => 'طبيب أشعة',
                'description' => 'Category for radiology doctors',
                'description_ar' => 'فئة لأطباء الأشعة',
                'status' => 'active',
            ],
            [
                'admin_id'=>7,
                'name' => 'Dr aesthetics',
                'name_ar' => 'طبيب تجميل',
                'description' => 'Category for aesthetic doctors',
                'description_ar' => 'فئة لأطباء التجميل',
                'status' => 'active',
            ],
            [
                'admin_id'=>8,
                'name' => 'Pharmacy',
                'name_ar' => 'صيدلية',
                'description' => 'Category for pharmacies',
                'description_ar' => 'فئة للصيدليات',
                'status' => 'active',
            ],
            [
                'admin_id'=>9,
                'name' => 'Hospital',
                'name_ar' => 'مستشفى',
                'description' => 'Category for hospitals',
                'description_ar' => 'فئة للمستشفيات',
                'status' => 'active',
            ],
            [
                'admin_id'=>10,
                'name' => 'Lab',
                'name_ar' => 'مختبر',
                'description' => 'Category for labs',
                'description_ar' => 'فئة للمختبرات',
                'status' => 'active',
            ],
            [
                'admin_id'=>12,
                'name' => 'Clinic',
                'name_ar' => 'عيادة',
                'description' => 'Category for clinics',
                'description_ar' => 'فئة للعيادات',
                'status' => 'active',
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}
