<?php

use Illuminate\Database\Seeder;
use App\TransloadCategories;

class TransloadCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function run()
    {
       TransloadCategories::insert([
    		["id" => 1, "category" => "Quarantine", "color_code" => "#EE230D"],
    		["id" => 2, "category" => "Good", "color_code" => "#61D837"],
    		["id" => 3, "category" => "Storage", "color_code" => "#FEAE02"],
    		["id" => 4, "category" => "House", "color_code" => "#03A1FF"],
    		["id" => 5, "category" => "Location 2", "color_code" => "#FF41A1"],
    		["id" => 6, "category" => "X Ray", "color_code" => "#D5D5D5"],
    	]);
    }
}
