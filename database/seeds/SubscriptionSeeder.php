<?php

use Illuminate\Database\Seeder;
use App\Subscription;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Subscription::insert([
        	["id" => 1, "title" => "Enterprise", "company_type" => "2","currency_id" => null ,"monthly_price"=>null,"addon_price"=>null,"monthly_max_moves"=>null,"free_users"=>null,"status"=>1,"created_by"=>2, 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' )],
        	["id" => 2, "title" => "Kika Direct", "company_type" => "2","currency_id" => null ,"monthly_price"=>null,"addon_price"=>null,"monthly_max_moves"=>null,"free_users"=>null,"status"=>1,"created_by"=>2, 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' )],
        	["id" => 3, "title" => "Kika Direct", "company_type" => "3","currency_id" => null ,"monthly_price"=>null,"addon_price"=>null,"monthly_max_moves"=>null,"free_users"=>null,"status"=>1,"created_by"=>2, 'created_at' => date( 'Y-m-d H:i:s' ), 'updated_at' => date( 'Y-m-d H:i:s' )],
        ]);
    }
}
