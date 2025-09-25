<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\RoomChoice;

class RoomChoicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        RoomChoice::insert([
            ["id" => 1, "room_choice" => "Kitchen", "room_code" => "KT", "comment" => "Kitchen Label"],
            ["id" => 2, "room_choice" => "Dining Room", "room_code" => "DR", "comment" => "Dining Room Label"],
            ["id" => 3, "room_choice" => "Lounge Room", "room_code" => "LNG", "comment" => "Lounge Room Label"],
            ["id" => 4, "room_choice" => "Rumpus Room", "room_code" => "RP", "comment" => "Rumpus Room Label"],
            ["id" => 5, "room_choice" => "Entrance", "room_code" => "ET", "comment" => "Entrance Label"],
            ["id" => 6, "room_choice" => "Hallway", "room_code" => "HW", "comment" => "Hallway Label"],
            ["id" => 7, "room_choice" => "Front Patio", "room_code" => "FP", "comment" => "Front Patio Label"],
            ["id" => 8, "room_choice" => "Garage", "room_code" => "GR", "comment" => "Garage Label"],
            ["id" => 9, "room_choice" => "Shed", "room_code" => "SD", "comment" => "Shed Label"],
            ["id" => 10, "room_choice" => "Laundry", "room_code" => "LD", "comment" => "Laundry Label"],
            ["id" => 11, "room_choice" => "Formal Lounge", "room_code" => "FL", "comment" => "Formal Lounge Label"],
            ["id" => 12, "room_choice" => "Bathroom", "room_code" => "BT", "comment" => "Bathroom Label"],
            ["id" => 13, "room_choice" => "Under Stairs", "room_code" => "US", "comment" => "Under Stairs Label"],
            ["id" => 14, "room_choice" => "Storage", "room_code" => "SG", "comment" => "Storage Label"],
            ["id" => 15, "room_choice" => "Master Bedroom", "room_code" => "MBR", "comment" => "Master Bedroom Label"],
            ["id" => 16, "room_choice" => "Nursery", "room_code" => "NS", "comment" => "Nursery Label"],
            ["id" => 17, "room_choice" => "Boys Rm", "room_code" => "BY", "comment" => "Boys Rm Label"],
            ["id" => 18, "room_choice" => "Girls Room", "room_code" => "GL", "comment" => "Girls Room Label"],
            ["id" => 19, "room_choice" => "Bedroom 2", "room_code" => "BR2", "comment" => "Bedroom 2 Label"],
            ["id" => 20, "room_choice" => "Bedroom 3", "room_code" => "BR3", "comment" => "Bedroom 3 Label"],
            ["id" => 21, "room_choice" => "Bedroom 4", "room_code" => "BR4", "comment" => "Bedroom 4 Label"],
            ["id" => 22, "room_choice" => "Guest Room", "room_code" => "GT", "comment" => "Guest Room Label"],
            ["id" => 23, "room_choice" => "Spare Room", "room_code" => "SP", "comment" => "Spare Room Label"],
            ["id" => 24, "room_choice" => "Bathroom 2", "room_code" => "BT2", "comment" => "Bathroom 2 Label"],
            ["id" => 25, "room_choice" => "Study", "room_code" => "ST", "comment" => "Study Label"],
            ["id" => 26, "room_choice" => "Office", "room_code" => "OF", "comment" => "Office Label"],
            ["id" => 27, "room_choice" => "Basement", "room_code" => "BST", "comment" => "Basement Label"],
            ["id" => 28, "room_choice" => "Den", "room_code" => "DN", "comment" => "Den Label"],
            ["id" => 29, "room_choice" => "Guest Room 2", "room_code" => "GT2", "comment" => "Guest Room 2 Label"]
        ]);
    }
}
