<?php

use Illuminate\Database\Seeder;
use App\CartonChoice;

class CartonChoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		CartonChoice::insert([
			["id" => 1, "cartoon_choice" => "Furniture Blanket", "cartoon_code" => "FB", "comment" => "Furniture Label", "item_type" => "FC", "good_type" => null],
        	["id" => 2, "cartoon_choice" => "Package", "cartoon_code" => "PKG", "comment" => "Furniture Label", "item_type" => "FC", "good_type" => "PKG"],
        	["id" => 3, "cartoon_choice" => "Standard Carton", "cartoon_code" => "STD", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 4, "cartoon_choice" => "Dishpack Carton", "cartoon_code" => "DP", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 5, "cartoon_choice" => "6 cft Carton", "cartoon_code" => "6", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 6, "cartoon_choice" => "5 cft Carton", "cartoon_code" => "5", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 7, "cartoon_choice" => "Crystal Carton", "cartoon_code" => "CRC", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 8, "cartoon_choice" => "Medium Carton", "cartoon_code" => "MC", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 9, "cartoon_choice" => "4 cft Carton", "cartoon_code" => "4", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 10, "cartoon_choice" => "Book Carton", "cartoon_code" => "BC", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 11, "cartoon_choice" => "2 cft Carton", "cartoon_code" => "2", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 12, "cartoon_choice" => "Portorobe", "cartoon_code" => "PR", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 13, "cartoon_choice" => "Wardrobe Carton", "cartoon_code" => "WR", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 14, "cartoon_choice" => "Flat Pack Carton", "cartoon_code" => "FP", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 15, "cartoon_choice" => "Clothes Carton", "cartoon_code" => "CC", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 16, "cartoon_choice" => "Carton", "cartoon_code" => "CTN", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 17, "cartoon_choice" => "Owner Carton", "cartoon_code" => "OC", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 18, "cartoon_choice" => "Plastic Covers", "cartoon_code" => "PC", "comment" => "Furniture Label", "item_type" => "FC", "good_type" => "PKG"],
        	["id" => 19, "cartoon_choice" => "Large Carton", "cartoon_code" => "LC", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 20, "cartoon_choice" => "Picture Pack", "cartoon_code" => "PP", "comment" => "Furniture Label - 2nd Level - Art/Mirror", "item_type" => "2L26", "good_type" => "PKG"],
        	["id" => 21, "cartoon_choice" => "Wine Carton", "cartoon_code" => "WC", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 22, "cartoon_choice" => "TV Carton", "cartoon_code" => "TC", "comment" => "Furniture Label - 2nd Level - Television", "item_type" => "2L31", "good_type" => "CRT"],
        	["id" => 23, "cartoon_choice" => "Bike Carton", "cartoon_code" => "BKC", "comment" => "Furniture Label - 2nd Level - Bike", "item_type" => "2L25", "good_type" => "CRT"],
        	["id" => 24, "cartoon_choice" => "Crate", "cartoon_code" => "CR", "comment" => "Furniture Label", "item_type" => "FC", "good_type" => "PKG"],
            ["id" => 25, "cartoon_choice" => "Original Carton", "cartoon_code" => "ORC", "comment" => "Furniture Label", "item_type" => "FC", "good_type" => "CRT"],
        	["id" => 26, "cartoon_choice" => "Plastic Tub", "cartoon_code" => "PT", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 27, "cartoon_choice" => "Plastic Tote", "cartoon_code" => "TT", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 28, "cartoon_choice" => "Archive Box", "cartoon_code" => "AB", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
			["id" => 29, "cartoon_choice" => "Document Box", "cartoon_code" => "DB", "comment" => "Contents of Cartons Labels", "item_type" => "CC", "good_type" => "CRT"],
        	["id" => 30, "cartoon_choice" => "Custom Type", "cartoon_code" => "CT", "comment" => "Custom Label", "item_type" => "CST", "good_type" => "PKG"],
			
        ]);
    }
}
