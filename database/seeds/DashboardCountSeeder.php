<?php

use Illuminate\Database\Seeder;
use App\DashboardCountSetting;

class DashboardCountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DashboardCountSetting::insert([
        	["id" => 1, "duration" => "all"],
        ]);
    }
}
