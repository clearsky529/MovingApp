<?php

use App\CompanyType;
use Illuminate\Database\Seeder;

class CompanyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CompanyType::insert([
        	["id" => 1, "company_type" => "Mobility"],
        	["id" => 2, "company_type" => "Moving"],
        	["id" => 3, "company_type" => "Contractor"]
        ]);
    }
}
