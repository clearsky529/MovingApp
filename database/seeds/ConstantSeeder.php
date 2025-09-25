<?php

use Illuminate\Database\Seeder;
use App\Constant;

class ConstantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Constant::insert([
        	["name" => "referral_free_days", "value" => "30"],
        ]);
    }
}
