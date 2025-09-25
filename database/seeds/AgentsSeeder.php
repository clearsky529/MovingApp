<?php

use Illuminate\Database\Seeder;
use App\Agents;

class AgentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Agents::insert([
        	["id" => 1, "agent_name" => "Controlling Agent"],
        	["id" => 2, "agent_name" => "Origin Agent"],
        	["id" => 3, "agent_name" => "Destination Agent"]
        ]);
    }
}
