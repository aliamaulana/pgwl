<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // //create user
        // $user = new \App\Models\User();
        // $user->name = 'Admin';
        // $user->phone = '081234567890';
        // $user->email= 'admin@gmail.com';
        // $user->password= bcrypt('admin123');
        // $user->save();

        //create multiple user
        $user = [ [
            'name' => 'Admin',
            'phone' => '081234567890',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('1234'),
        ],
        [
            'name' => 'User',
            'phone' => '081234567891',
            'email' => 'user@gmail.com',
            'password' => bcrypt('1234'),

        ],
    ];

    //insert the user into the database
    DB::table('users')->insert($user);
    }
}
