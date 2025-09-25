<?php

use Illuminate\Database\Seeder;
use App\Currencies;

class CurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Currencies::insert([
        	["id" => 1, "currency_name" => "Dollars", "currency_symbol" => "$", "currency_code" => "USD", "exchange_rate" => 1],
        	["id" => 2, "currency_name" => "Pounds", "currency_symbol" => "£", "currency_code" => "GBP", "exchange_rate" => 0.76704],
        	["id" => 3, "currency_name" => "Euros", "currency_symbol" => "€", "currency_code" => "EUR", "exchange_rate" => 0.88421],
        	["id" => 4, "currency_name" => "Rupee", "currency_symbol" => "₹", "currency_code" => "INR", "exchange_rate" => 69.395042],
        	["id" => 5, "currency_name" => "Australian Dollars", "currency_symbol" => "$", "currency_code" => "AUD", "exchange_rate" => 1.38917],
        	["id" => 6, "currency_name" => "Singapore Dollars", "currency_symbol" => "$", "currency_code" => "SGD", "exchange_rate" => 1.352498],
        	["id" => 7, "currency_name" => "Canadian Dolar", "currency_symbol" => "$", "currency_code" => "CAD", "exchange_rate" => 1.332535],
        	["id" => 8, "currency_name" => "New Zealand Dollar", "currency_symbol" => "$", "currency_code" => "NZD", "exchange_rate" => 1.48392],
        ]);
    }
}
