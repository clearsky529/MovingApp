<?php

use Illuminate\Database\Seeder;
use App\ConditionSide;

class ConditionSideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConditionSide::insert([
        	["id" => 1, "side" => "Top", "side_code" => "1"],
        	["id" => 2, "side" => "Front", "side_code" => "2"],
        	["id" => 3, "side" => "Side", "side_code" => "3"],
        	["id" => 4, "side" => "Corner", "side_code" => "4"],
        	["id" => 5, "side" => "Arm", "side_code" => "5"],
        	["id" => 6, "side" => "Leg", "side_code" => "6"],
        	["id" => 7, "side" => "Edge", "side_code" => "7"],
        	["id" => 8, "side" => "Bottom", "side_code" => "8"],
        	["id" => 9, "side" => "Left", "side_code" => "9"],
        	["id" => 10, "side" => "Right", "side_code" => "10"],
        	["id" => 11, "side" => "Surface", "side_code" => "11"],
        	["id" => 12, "side" => "Rear", "side_code" => "12"],
        ]);
    }
}
