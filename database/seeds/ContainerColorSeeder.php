<?php

use Illuminate\Database\Seeder;
use App\ContainerColorOrder;

class ContainerColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ContainerColorOrder::insert([
        	["sort_order" => 1, "color_code" => "#00EEFF"],
        	["sort_order" => 2, "color_code" => "#90FF00"],
        	["sort_order" => 3, "color_code" => "#FF99F4"],
        	["sort_order" => 4, "color_code" => "#FFF600"],
        	["sort_order" => 5, "color_code" => "#F7A072"],
        	["sort_order" => 6, "color_code" => "#C2CADB"],
        	["sort_order" => 7, "color_code" => "#D4F164"],
        	["sort_order" => 8, "color_code" => "#70A7FF"],
        	["sort_order" => 9, "color_code" => "#CAFFDF"],
        	["sort_order" => 10, "color_code" => "#FFADAD"],
        	["sort_order" => 11, "color_code" => "#47FF91"],
        	["sort_order" => 12, "color_code" => "#D6FCFF"],
        	["sort_order" => 13, "color_code" => "#E4FFC2"],
        	["sort_order" => 14, "color_code" => "#FFD6FB"],
        	["sort_order" => 15, "color_code" => "#FFFB85"],
        	["sort_order" => 16, "color_code" => "#FF8585"],
        	["sort_order" => 17, "color_code" => "#FCD8C5"],
        	["sort_order" => 18, "color_code" => "#D6E6FF"],
        	["sort_order" => 19, "color_code" => "#F0FAC7"],
        	["sort_order" => 20, "color_code" => "#F2F4F7"],
        	["sort_order" => 21, "color_code" => "#D9329E"],
        	["sort_order" => 22, "color_code" => "#C2B8FF"],
        	["sort_order" => 23, "color_code" => "#D9DC31"],
        	["sort_order" => 24, "color_code" => "#B5B2A0"],
        	["sort_order" => 25, "color_code" => "#87C244"],
        	["sort_order" => 26, "color_code" => "#FF5E2B"],
        	["sort_order" => 27, "color_code" => "#D6B85C"],
        	["sort_order" => 28, "color_code" => "#00A1FF"],
        	["sort_order" => 29, "color_code" => "#CCEF43"],
        	["sort_order" => 30, "color_code" => "#9AA9C1"],
        ]);
    }
}
