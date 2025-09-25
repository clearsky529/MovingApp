<?php

use Illuminate\Database\Seeder;
use App\CartonCondition;

class CartonConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CartonCondition::insert([
        	["id" => 1, "condition" => "Scratched", "condition_code" => "SC", "move_stage" => "all", "color_code" => "#eb5f34", "is_side_required" => 1],
        	["id" => 2, "condition" => "Dented", "condition_code" => "D", "move_stage" => "all", "color_code" => "#eb4c34", "is_side_required" => 1],
        	["id" => 3, "condition" => "Loose", "condition_code" => "L", "move_stage" => "all", "color_code" => "#ebb734", "is_side_required" => 1],
        	["id" => 4, "condition" => "Rubbed", "condition_code" => "R", "move_stage" => "all", "color_code" => "#8feb34", "is_side_required" => 1],
        	["id" => 5, "condition" => "Gouged", "condition_code" => "G", "move_stage" => "all", "color_code" => "#a3c79f", "is_side_required" => 1],
        	["id" => 6, "condition" => "Chipped", "condition_code" => "CH", "move_stage" => "all", "color_code" => "#65e077", "is_side_required" => 1],
        	["id" => 7, "condition" => "Broken", "condition_code" => "BR", "move_stage" => "all", "color_code" => "#497a51", "is_side_required" => 1],
        	["id" => 8, "condition" => "Soiled", "condition_code" => "SO", "move_stage" => "all", "color_code" => "#02caed", "is_side_required" => 1],
        	["id" => 9, "condition" => "Cracked", "condition_code" => "CR", "move_stage" => "all", "color_code" => "#6db5c2", "is_side_required" => 1],
        	["id" => 10, "condition" => "Torn", "condition_code" => "T", "move_stage" => "all", "color_code" => "#cfe4e8", "is_side_required" => 1],
        	["id" => 11, "condition" => "Bent", "condition_code" => "BE", "move_stage" => "all", "color_code" => "#38a3f5", "is_side_required" => 1],
        	["id" => 12, "condition" => "Missing", "condition_code" => "MG", "move_stage" => "all", "color_code" => "#2287d4", "is_side_required" => 1],
        	["id" => 13, "condition" => "Peeling", "condition_code" => "PL", "move_stage" => "all", "color_code" => "#9458f5", "is_side_required" => 1],
        	["id" => 14, "condition" => "Owners Risk", "condition_code" => "OR", "move_stage" => "all", "color_code" => "#aa8ade", "is_side_required" => 1],
        	["id" => 15, "condition" => "Badly Worn", "condition_code" => "BW", "move_stage" => "all", "color_code" => "#d1c5e3", "is_side_required" => 1],
        	["id" => 16, "condition" => "Condition Unknown", "condition_code" => "CU", "move_stage" => "all", "color_code" => "#877d96", "is_side_required" => 1],
        	["id" => 17, "condition" => "Mildew", "condition_code" => "ML", "move_stage" => "all", "color_code" => "#f1c1f5", "is_side_required" => 1],
        	["id" => 18, "condition" => "Rust", "condition_code" => "RU", "move_stage" => "all", "color_code" => "#876b8a", "is_side_required" => 1],
        	["id" => 19, "condition" => "Water Damaged", "condition_code" => "WD", "move_stage" => "all", "color_code" => "#876b8a", "is_side_required" => 1],
        	["id" => 20, "condition" => "Stained", "condition_code" => "ST", "move_stage" => "all", "color_code" => "#876b8a", "is_side_required" => 1],
        	["id" => 21, "condition" => "Faded", "condition_code" => "F", "move_stage" => "all", "color_code" => "#876b8a", "is_side_required" => 1],
        ]);
    }
}