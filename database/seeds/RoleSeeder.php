<?php

use App\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::insert([
        	["id" => 1, "name" => "super-admin", "guard_name" => "web", 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' )],
        	["id" => 2, "name" => "company-admin", "guard_name" => "web", 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' )],
        	["id" => 3, "name" => "company-user", "guard_name" => "web", 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' )],
            ["id" => 4, "name" => "support-admin", "guard_name" => "web", 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' )]
       	]);
    }
}
