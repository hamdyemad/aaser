<?php

namespace Database\Seeders;

use App\Models\GuideType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GuideTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'المستشفيات',
            ],
            [
                'name' => 'المطاعم',
            ],
            [
                'name' => 'الكافيهات',
            ],
            [
                'name' => 'سفر وسياحه',
            ],
            [
                'name' => 'فنادق',
            ],
            [
                'name' => 'مراكز ترفيه',
            ],
            [
                'name' => 'محلات تجاريه',
            ],
            [
                'name' => 'انديه ولياقه',
            ],
            [
                'name' => 'تاجير سيارات',
            ],
            [
                'name' => 'خدمة صيانة المركبات',
            ],
        ];
        GuideType::insert($types);
    }
}
