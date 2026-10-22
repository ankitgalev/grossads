<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class GrossAdsUserSeeder extends Seeder
{
    public function run()
    {
        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => 'admin@grossads.com'
            ],
            [
                'name' => 'GrossAds Admin',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'company_name' => 'GrossAds',
                'phone' => '9999999999',
                'status' => 1,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Publisher Demo
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => 'publisher@grossads.com'
            ],
            [
                'name' => 'Demo Publisher',
                'password' => Hash::make('Publisher@123'),
                'role' => 'publisher',
                'company_name' => 'Demo Publisher Pvt Ltd',
                'phone' => '9999999998',
                'status' => 1,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Advertiser Demo
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => 'advertiser@grossads.com'
            ],
            [
                'name' => 'Demo Advertiser',
                'password' => Hash::make('Advertiser@123'),
                'role' => 'advertiser',
                'company_name' => 'Demo Finance Pvt Ltd',
                'phone' => '9999999997',
                'status' => 1,
            ]
        );
    }
}