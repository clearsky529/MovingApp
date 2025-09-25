<?php

use Illuminate\Database\Seeder;
use App\ScreeningCategories;

class ScreeningCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       ScreeningCategories::insert([
    		["id" => 1, "category_name" => "Quarantine", "color_code" => "#EE230D"],
    		["id" => 2, "category_name" => "Good", "color_code" => "#61D837"],
    		["id" => 3, "category_name" => "Storage", "color_code" => "#FEAE02"],
    		["id" => 4, "category_name" => "House", "color_code" => "#03A1FF"],
    		["id" => 5, "category_name" => "Location 2", "color_code" => "#FF41A1"],
    		//["id" => 6, "category_name" => "X Ray", "color_code" => "#D5D5D5"],
    	]);
    }
}
