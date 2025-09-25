<?php

use Illuminate\Database\Seeder;
use App\PackerCode;

class PackerCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PackerCode::insert([
        	["id" => 1, "package_status" => "Packed By Removalist", "code" => "PBR"],
        	["id" => 2, "package_status" => "Dismantled By Removalist", "code" => "DBR"],
        	["id" => 3, "package_status" => "Packed By Owner", "code" => "PBO"],
        	["id" => 4, "package_status" => "Dismantled By Owner", "code" => "DBO"],
        	["id" => 5, "package_status" => "Left Packed", "code" => "LP"],
        	["id" => 6, "package_status" => "Unpacked", "code" => "UP"],
        ]);
    }
}
