<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            [
                'user_id' => 'tenant_202559826863ce90b8823',
                'firstname' => 'Admin',
                'middlename' => 'Admin',
                'lastname' => 'Admin',
                'extension_name' => 'Admin',
                'province' => null,
                'municipality' => null,
                'barangay' => null,
                'zipcode' => null,
                'email' => 'Admin@gmail.com',
                'mobile_number' => '639095416290',
                'dti_permit' => null,
                'location' => 'San Juan, irosin, Sorsogon',
                'profile' => null,
                'house_name' => 'Papyado',
                'date_registered' => '2025-07-01',
                'user_type' => 'Admin',
                'username' => 'Admin@123',
                'password' => Hash::make('password123'),
                'status' => 'Approved',
                'view' => 'No',
                'remember_token' => null,
            ],

            [
                'user_id' => 'owner_202527011980069007abd0fccb',
                'firstname' => 'Helen',
                'middlename' => 'Gelilio',
                'lastname' => 'Magtangob',
                'extension_name' => '',
                'province' => 'sorsogon',
                'municipality' => 'Bulan',
                'barangay' => 'Zone 1',
                'zipcode' => '4706',
                'email' => 'helengelilio14@gmail.com',
                'mobile_number' => '639168233260',
                'dti_permit' => 'DTI PERMIT.jpg',
                'location' => 'Zone 2 Poblacion Bulan Sorsogon',
                'profile' => 'profile_690084a6279918.78458188.jpg',
                'house_name' => 'Magtangob Lodging House',
                'date_registered' => '2025-10-28',
                'user_type' => 'Owner',
                'username' => '@helenGelilio2',
                'password' => Hash::make('password123'),
                'status' => 'Approved',
                'view' => 'Yes',
                'remember_token' => null,
            ],

        ];

        foreach ($data as $row) {
            Account::create($row);
        }
    }
}
