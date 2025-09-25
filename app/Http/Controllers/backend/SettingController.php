<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DashboardCountSetting;
use App\Helpers\CompanyAdmin;
use App\{Constant,Currencies};
use Illuminate\Validation\Rule;
class SettingController extends Controller
{
    public function index(Request $request)
    {
		$this->dashboardCount = DashboardCountSetting::first();
		$this->free_trial_period = CompanyAdmin::getFreeTrialDay();
		$this->device_price = CompanyAdmin::getDevicePrice();
		$this->icr_price = CompanyAdmin::getIcrPrice();
		// dd($this->icr_price);
		$this->currencies = Currencies::all();
		$this->device_currency	= Constant::where('name','=',"device_price")->value('currency_id');
		$this->icr_currency_symbol = Constant::where('name','=',"icr_price")->value('currency_id');

    	if ($request->isMethod('post')) {

    		$this->dashboardCount->duration = $request->duration;
    		$this->dashboardCount->save();

    		echo(str_replace("_"," ",$this->dashboardCount->duration));
    	}else{
	    	return view('theme.admin.setting.index',$this->data);
    	}
	}
	
	public function changeFreeTrial(Request $request)
	{
		$validator = \Validator::make($request->all(), [
			'days'			 => 'required|numeric|min:1',
		]);
		if ($validator->fails())
		{
			return response()->json(['errors'=>$validator->errors()->all()]);
		}

		Constant::where('name','referral_free_days')->update(['value' => $request->days]);
	}

	public function changeDevicePrice(Request $request)
	{
		$validator = \Validator::make($request->all(), [
			'deviceprice'    => 'required|numeric',
			'device_currency' => 'required',
		]);
		if ($validator->fails())
		{
			return response()->json(['errors'=>$validator->errors()->all()]);
		}
		$device_currency = Currencies::where('id',$request->device_currency)->first();
		Constant::where('name','device_price')->update(['value' => $request->deviceprice, 'currency_id' => $device_currency->id]);
		return $device_currency;
	}

	public function changeIcrPrice(Request $request)
	{
		$validator = \Validator::make($request->all(), [
			'icrprice'    => 'required|numeric',
			'currency'    => 'required',
		]);
		if ($validator->fails())
		{
			return response()->json(['errors'=>$validator->errors()->all()]);
		}
		$currency = Currencies::where('id',$request->currency)->first();

		Constant::where('name','icr_price')->update(['value' => $request->icrprice, 'currency_id' => $currency->id]);
		return	$currency;
	}
}
