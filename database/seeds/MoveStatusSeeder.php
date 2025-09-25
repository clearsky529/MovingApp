<?php

use App\MoveType;
use Illuminate\Database\Seeder;

class MoveStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MoveType::insert([
        	["id" => 1, "type" => "Uplift"],
        	["id" => 2, "type" => "Transit"],
            ["id" => 3, "type" => "Screening"],
        	["id" => 4, "type" => "Transload"],
        	["id" => 5, "type" => "Delivery"]
        ]);
    }
}
