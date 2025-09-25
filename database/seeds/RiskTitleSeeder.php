<?php

use App\RiskTitles;
use Illuminate\Database\Seeder;

class RiskTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            RiskTitles::insert([
                ["id" => 1, "risk_title" => "Slip/ Trip/ Fall"],
                ["id" => 2, "risk_title" => "Awkward/ Irregular Items"],
                ["id" => 3, "risk_title" => "Traffic/ Parking/ Loading"],
                ["id" => 4, "risk_title" => "Power Lines/ Leaves/ Trees"],
                ["id" => 5, "risk_title" => "Fences/ Stairs/ Balcony/ Railings"],
                ["id" => 6, "risk_title" => "Other (Children/ Pets/ Biological Etc)"],
            ]);
    }
}
