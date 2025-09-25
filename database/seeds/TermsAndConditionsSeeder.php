<?php

use Illuminate\Database\Seeder;
use App\TermsAndConditions;

class TermsAndConditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {	
        TermsAndConditions::insert([
            ["id" => 1, "terms_and_conditions" => "Client has given permission to park the truck in the driveway if needed.", "move_type" => 1, "move_status" => 0],
            ["id" => 2, "terms_and_conditions" => "An on site health and safety risk assessment has been carried out by the Team Leader.", "move_type" => 1, "move_status" => 0],
            ["id" => 3, "terms_and_conditions" => "The property has been inspected and any existing damages have been noted and shown to the client.", "move_type" => 1, "move_status" => 0],
            ["id" => 4, "terms_and_conditions" => "Client has read and understood the above statements.", "move_type" => 1, "move_status" => 0],
        	["id" => 5, "terms_and_conditions" => "The items listed on the ICR are a true and complete record of what has been uplifted.", "move_type" => 1, "move_status" => 1],
        	["id" => 6, "terms_and_conditions" => "I have made final check of the property and confirm that all the items have ben uplifted.", "move_type" => 1, "move_status" => 1],
        	["id" => 7, "terms_and_conditions" => "The property has NOT been damaged.", "move_type" => 1, "move_status" => 1],
        	["id" => 8, "terms_and_conditions" => "I have read and understood the statements listed above.", "move_type" => 1, "move_status" => 1],
        	["id" => 9, "terms_and_conditions" => "Client has given permission to park the truck in the driveway if needed.", "move_type" => 5, "move_status" => 0],
        	["id" => 10, "terms_and_conditions" => "An on site health and safety risk assessment has been carried out by the Team Leader.", "move_type" => 5, "move_status" => 0],
        	["id" => 11, "terms_and_conditions" => "The property has been inspected and any existing damages have been noted and shown to the client.", "move_type" => 5, "move_status" => 0],
        	["id" => 12, "terms_and_conditions" => "Client has read and understood the above statements.", "move_type" => 5, "move_status" => 0],
        	["id" => 13, "terms_and_conditions" => "All items have been delivered as per the inventory and condition report.", "move_type" => 5, "move_status" => 1],
        	["id" => 14, "terms_and_conditions" => "Any damages or items not delivered have been noted.", "move_type" => 5, "move_status" => 1],
        	["id" => 15, "terms_and_conditions" => "The unpack and generalset up has been completed to my satisfaction.", "move_type" => 5, "move_status" => 1],
        	["id" => 16, "terms_and_conditions" => "The property has NOT been damaged.", "move_type" => 5, "move_status" => 1],
        	["id" => 17, "terms_and_conditions" => "I have read and understood the statements listed above.", "move_type" => 5, "move_status" => 1],
            ["id" => 18, "terms_and_conditions" => "I waive my entitlement to have the cartons/packages noted as LP unpacked by the removalist.", "move_type" => 5, "move_status" => 1],
            ["id" => 19, "terms_and_conditions" => "The client/agent has had what an ICR is explained to them and been asked to accompany the removalist while it is being completed.", "move_type" => 1, "move_status" => 0],
        ]);
    }
}
