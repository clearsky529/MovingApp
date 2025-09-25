<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\RoomChoice;

class RoomChoicesSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        RoomChoice::insert([
            ["id" => 30, "room_choice" => "Garden Shed", "room_code" => "GS", "comment" => "Garden Shed Label"],
            ["id" => 31, "room_choice" => "Patio", "room_code" => "PT", "comment" => "Patio Label"],
            ["id" => 32, "room_choice" => "Balcony", "room_code" => "BL", "comment" => "Balcony Label"],
            ["id" => 33, "room_choice" => "Media Room", "room_code" => "MR", "comment" => "Media Room Label"],
        ]);
    }
}
