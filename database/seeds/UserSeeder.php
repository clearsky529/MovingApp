<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
        	["id" => 1, "role_id" => 4, "email" => "support@kikamoving.com", 'username' => 'Support-Admin','status' => '1', 'password' => '$2y$10$NXki6SQzEYDITDgl0GVeeeah8lKjg9TTRhp5MrwqylwkRvMGt4W4O', 'remember_token' => '5PML28IFHBpS1kFdoiC3im2l47Vmg1uf0d1nWK4AFIB00beRp4RIOyfRC2q9', 'profile_pic' => 'avatar.png', 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' )],
            ["id" => 2, "role_id" => 1, "email" => "damien@kikamoving.com", 'username' => 'Damien','status' => '1', 'password' => '$2y$10$NXki6SQzEYDITDgl0GVeeeah8lKjg9TTRhp5MrwqylwkRvMGt4W4O', 'remember_token' => '5PML28IFHBpS1kFdoiC3im2l47Vmg1uf0d1nWK4AFIB00beRp4RIOyfRC2q9', 'profile_pic' => 'avatar.png', 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' )]
       	]);
    }
}
