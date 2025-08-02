<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'name',
                'value' => '',
            ],
            [
                'key' => 'email',
                'value' => '',
            ],
            [
                'key' => 'phone',
                'value' => '',
            ],
            [
                'key' => 'address',
                'value' => '',
            ],
            [
                'key' => 'link',
                'value' => '',
            ],
            [
                'key' => 'register_point',
                'value' => '4',
            ],
            [
                'key' => 'view_point',
                'value' => '5',
            ],
            [
                'key' => 'share_point',
                'value' => '5',
            ],
            [
                'key' => 'invitation_point',
                'value' => '5',
            ],
            [
                'key' => 'complete_profile_point',
                'value' => '5',
            ],
        ];
        Setting::insert($settings);
    }
}
