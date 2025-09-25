<?php

namespace App\Http\Controllers\API\v1\companyUser;

use App\Http\Controllers\Controller;
use App\RoomChoice;
use Illuminate\Http\Request;
use App\User;
use App\CartonChoice;
use App\CartonContent;
use App\CartonCondition;
use App\Companies;
use App\CompanyAgent;
use App\ConditionSide;
use App\ItemLabel;
use App\CompanyUser;
use App\DeliveryMoves;
use App\ScreeningCategories;
use App\TransloadCategories;
use App\PackerCode;
use App\OauthAccessToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Helpers\CompanyUserDetails;
use App\Move;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DataListingController extends BaseController
{
	public function getCartonChoice()
	{
		$carton_choice = CartonChoice::all();
		$this->data = $carton_choice;

		if (!empty($this->data)) {
			return $this->sendResponse($this->data, "Carton Choices retrived successfully.");
		} else {
			return $this->apiError("No carton choice found.");
		}
	}

	public function getRoomChoice()
	{
		$room_choice = RoomChoice::all();
		$this->data = $room_choice;

		if (!empty($this->data)) {
			return $this->sendResponse($this->data, "Room Choices retrived successfully.");
		} else {
			return $this->apiError("No room choice found.");
		}
	}

	public function getCartonConditionAndSide()
	{
		$carton['condition'] = CartonCondition::all();
		$carton['side'] = ConditionSide::all();
		$this->data = $carton;

		if (count($carton['condition']) > 0 && count($carton['side']) > 0) {
			return $this->sendResponse($this->data, "Carton Conditions retrieved successfully..");
		} else {
			return $this->apiError("Carton conditions not found.");
		}
	}

	public function getCartonItemLabel()
	{
		$this->data = ItemLabel::where('is_master', '1')->get();

		if (count($this->data) > 0) {
			return $this->sendResponse($this->data, "Item lable retrieved successfully..");
		} else {
			return $this->apiError("No item lable found.");
		}
	}

	public function getCartonPacker()
	{
		$this->data = PackerCode::select("*")
			->whereNotIn('code', ['LP', 'UP'])
			->orderBy('sort_order', 'asc')
			->get();
		// return $this->data;

		if (count($this->data) > 0) {
			return $this->sendResponse($this->data, "Packer code retrieved successfully..");
		} else {
			return $this->apiError("No packer code found.");
		}
	}

	public function getScreeningCategory(Request $request)
	{
		$this->data = ScreeningCategories::all();

		if (count($this->data) != 0) {
			return $this->sendResponse($this->data, "Screening Category list retrieved successfully.");
		} else {
			return $this->apiError("No Screening Category Data found.");
		}
	}

	public function getTransloadCategories()
	{
		$this->data = TransloadCategories::all();

		if (count($this->data) != 0) {
			return $this->sendResponse($this->data, "Screening Category list retrieved successfully.");
		} else {
			return $this->apiError("No Screening Category Data found.");
		}
	}

	public function listAllAgents(Request $request)
	{
		$company_id = (Auth::check() && isset(Auth::user()->companyUser->company->id) && !empty(Auth::user()->companyUser->company->id) ? Auth::user()->companyUser->company->id : 0);
		if ($company_id == 0) {
			return $this->apiError("No company found");
		}

		$query = CompanyAgent::select('id', 'company_name')->where('status', 1)->where('company_id', Auth::user()->companyUser->company->id);
		if ($request->has('search') && $request->filled('search')) {
			$query->where('company_name', 'like', '%' . $request->search . '%');
		}
		$this->data = $query->orderBy('id', 'desc')->get();

		if (count($this->data) == 0) {
			return $this->apiError("No agent found");
		}
		return $this->sendResponse($this->data, "Agents has been retrieved successfully.");
	}

	public function updateMoveAgent(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'move_id' => 'required|exists:moves,id',
			'agent_id' => 'required|gt:0|exists:company_agents,id',
		]);

		if ($validator->fails()) {
			return $this->apiError($validator->errors()->first());
		}

		$userId = (Auth::check() && Auth::user()->id ? Auth::user()->id : 0);
		if ($userId == 0) {
			return $this->apiError("Unauthorized access, please login to access.");
		}

		$company_id = (isset(Auth::user()->companyUser->company->id) && !empty(Auth::user()->companyUser->company->id) ? Auth::user()->companyUser->company->id : 0);
		if ($company_id == 0) {
			return $this->apiError("No company found");
		}

		$move = Move::where('company_id', $company_id)->where('id', $request->move_id)->first();
		$company_agent = CompanyAgent::select('kika_id', 'email', 'company_name')->where('id', $request->agent_id)->first();
		$destination_company = Companies::where('kika_id', $company_agent->kika_id)->value('id');
		$move->foreign_destination_agent = $destination_company;
		$move->destination_agent = $request->agent_id;
		$move->is_destination_agent_kika = 1;

		$delivery_move = DeliveryMoves::where('move_id', $request->move_id)->first();
		if (!empty($delivery_move)) {
			$delivery_move->delivery_agent_kika_id = $company_agent->kika_id;
			$delivery_move->delivery_agent = $company_agent->company_name;
			$delivery_move->delivery_agent_email = $company_agent->email;
			$delivery_move->save();
		}

		$get_company = Companies::where('id', $company_id)->first();
		if (isset($delivery_move->delivery_agent_kika_id) && !empty($get_company) && isset($get_company->kika_id) && $get_company->kika_id != $delivery_move->delivery_agent_kika_id) {
			$assignCompanyId = Companies::where('kika_id', $delivery_move->delivery_agent_kika_id)->value('id');
			if (!empty($assignCompanyId)) {
				$move->is_assign = 1;
				$move->assign_destination_company_id = $assignCompanyId;
			}
		} else {
			$move->is_assign = 1;
			$move->assign_destination_company_id = $company_id;
		}
		$move->save();
		return $this->sendResponse(array(), "Move agent has been updated successfully!");

	}
}
