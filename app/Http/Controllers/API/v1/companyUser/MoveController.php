<?php

namespace App\Http\Controllers\API\v1\companyUser;

use PDF;
use File;
use Mail;
use Crypt;
use App\Move;
use App\User;
use Exception;
use Validator;
use App\CompanyUser;
use App\UpliftMoves;
use Aws\S3\S3Client;
use App\MoveComments;
use App\TransitMoves;
use App\DeliveryMoves;
use App\ScreeningMoves;
use App\TransloadMoves;
use App\PackageSignature;
use App\Helpers\GetIcrData;
use App\TermsAndConditions;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\CompanyAdmin;
use Illuminate\Validation\Rule;
use App\TermsAndConditionsChecked;
use App\Helpers\CompanyUserDetails;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Notification;
use App\{CommentImages, Companies, CompanyAgent, MoveContact, MoveItemCondition, RiskAssessment, RiskAssessmentDetail, RiskTitles, student};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as FacadesFile;
use Mpdf\Mpdf;

class MoveController extends BaseController
{
    public function getMoves(Request $request)
    {
        $company_id = (int) CompanyAdmin::getCompanyUserCompany();
        // $move_number = $request->move_number;
        $move_details = Move::select('id', 'move_number', 'company_id as created_company_id', 'assign_destination_company_id', 'foreign_origin_agent', 'foreign_controlling_agent', 'foreign_origin_contractor', 'foreign_destination_contractor', 'reference_number')
            ->whereRaw("BINARY `move_number`= ?", [$request->move_number])
            ->where(function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
                $query->orWhere('foreign_origin_agent', $company_id);
                $query->orWhere('assign_destination_company_id', $company_id);
                $query->orWhere('foreign_controlling_agent', $company_id);
                $query->orWhere('foreign_origin_contractor', $company_id);
                $query->orWhere('foreign_destination_contractor', $company_id);
            });
        $move = $move_details->get();
        foreach ($move as $key => $mv) {
            $moves = $mv->contact->contact_name;
        }
        $this->data = $move;
        // dd($this->data);

        if (count($this->data) != 0) {
            return $this->sendResponse($this->data, "Move Details retrive successfully.");
        } else {
            return $this->apiError("No move found for given move number.");
        }

    }

    public function searchMove(Request $request)
    {
        $company_id = (int) CompanyAdmin::getCompanyUserCompany();
        $id = $request->move_id;
        $company = Companies::where('id', $company_id)->first();
        // // dd($company);
        $companyAgent = CompanyAgent::where('kika_id', $company->kika_id)->latest()->first();
        // // dd($companyAgent);

        $move = Move::where('id', $id)
            ->where(function ($query) use ($company_id) {
                $query->where('company_id', 'LIKE', '%' . $company_id . '%');
                $query->orWhere('foreign_origin_agent', 'LIKE', '%' . $company_id . '%');
                $query->orWhere('assign_destination_company_id', 'LIKE', '%' . $company_id . '%');
                $query->orWhere('foreign_controlling_agent', 'LIKE', '%' . $company_id . '%');
                $query->orWhere('foreign_origin_contractor', 'LIKE', '%' . $company_id . '%');
                $query->orWhere('foreign_destination_contractor', 'LIKE', '%' . $company_id . '%');
            });

        //right
        // $move = Move::where('id',$id)
        // 			->where('company_id','LIKE','%'.$company_id.'%')
        // 			->orWhere(function ($query) use ($company_id) {
        // 			$query->orWhere('foreign_controlling_agent', 'LIKE','%'.$company_id.'%');
        // 			// $query->orWhere('foreign_origin_contractor','LIKE','%'.$company_id.'%');
        // 			// $query->orWhere('foreign_destination_contractor','LIKE','%'.$company_id.'%');
        // 			// $query->orWhere('foreign_origin_agent','LIKE','%'.$company_id.'%');
        // 			$query->orWhere('assign_destination_company_id','LIKE','%'.$company_id.'%');
        // 	});

        $move_id = $move->value('id');
        // dd($move_id);


        if (is_null($move_id)) {
            return $this->apiError("No move found for given move id.");
        } else {

            $move_data = Move::find($id);
            // dd($move_data);

            switch ($move_data->type_id) {
                case 3:
                    if ($move_data && !($move_data->required_screening == 1)) {
                        return $this->apiError("Screening is not required for this move.");
                    }
                    break;

                case 4:
                    if ($move_data && !($move_data->required_storage == 1)) {
                        return $this->apiError("Storage is not required for this move.");
                    }
                    break;

                case 5:
                    if ($move_data->delivery) {
                        if ($move_data && ($move_data->delivery->status == 2)) {
                            return $this->apiError("Delivery for this move is completed.");
                        }
                    }
                    // if (!Move::where('id',$id)->exists()) {
                    // 		return $this->apiError("Only Destination Agents have access to the delivery.");
                    // }
                    $agent_move = Move::where('id', $id)->first();
                    // if($agent_move->destination_agent == $companyAgent->id){
                    // 	$comId = $companyAgent->company_id;
                    // 	if (!Move::where('id',$move_id)->where(function ($query) use ($comId) {
                    // 		$query->where('company_id', '=', $comId)
                    // 			->orWhere('foreign_destination_contractor',$comId)
                    // 			->orWhere('foreign_destination_agent',$comId);
                    // 	})->exists()) {
                    // 		return $this->apiError("Only Destination Agents have access to the delivery.");
                    // 	}
                    // }else{
                    if (
                        !Move::where('id', $id)->where(function ($query) use ($company_id) {
                            $query->where('company_id', '=', $company_id)
                                ->orWhere('foreign_destination_contractor', $company_id)
                                ->orWhere('foreign_destination_agent', $company_id);
                        })->exists()
                    ) {
                        return $this->apiError("Only Destination Agents have access to the delivery.");
                    }
                    // }


                    break;
            }

            if (
                Move::where(['id' => $move_id, 'type_id' => 1])->orWhere(['foreign_origin_contractor' => $company_id, 'foreign_controlling_agent' => $company_id])->exists() && UpliftMoves::where([['move_id', '=', $move_id], ['status', '=', 0]]) || UpliftMoves::where([['move_id', '=', $move_id], ['status', '=', 1]])->exists() ||
                Move::where('id', $move_id)->where('type_id', 3)->exists() && ScreeningMoves::where([['move_id', '=', $move_id], ['status', '=', 1]])->exists() ||
                Move::where('id', $move_id)->where('type_id', 4)->exists() && TransloadMoves::where([['move_id', '=', $move_id], ['status', '=', 1]])->exists() ||
                Move::where('id', $move_id)->where('type_id', 5)->orWhere(['foreign_destination_contractor' => $company_id, 'foreign_destination_agent' => $company_id])->exists() && DeliveryMoves::where([['move_id', '=', $move_id]])->exists()
            ) {

                $nonkika_move_data = Move::with('uplift', 'screening', 'transload')->where('id', $move_data->id)->first();
                $item_count = $nonkika_move_data->uplift->item_count;
                if ($item_count !== null && $nonkika_move_data->required_storage == 0 && $nonkika_move_data->required_screening == 0) {
                    $move = Move::with([
                        'uplift',
                        'roomChoice',
                        'transloadContainer',
                        'items.itemUpliftCategory',
                        'items.itemScreeningCategory.Category',
                        'items.itemPacker',
                        'items.itemDetails',
                        'items.container',
                        'items.roomChoice',
                        'items.subItems.subItemDetails',
                        'items.cartoonItem.cartoonItemDetails',
                        'items.condition.conditionDetails',
                        'items.condition.conditionImage',
                        'items.condition.conditionSides.sideDetails',
                        'items.transloadCondition.conditionDetails',
                        'items.transloadCondition.conditionImage',
                        'items.transloadCondition.conditionSides.sideDetails',
                        'items.deliveryCondition.conditionDetails',
                        'items.deliveryCondition.conditionImage',
                        'items.deliveryCondition.conditionSides.sideDetails',
                    ])
                        // ->where('company_id',$company_id)
                        ->where('id', $id)
                        ->first();

                    $move['customer'] = $move->contact->contact_name;
                    $move['customer_email'] = $move->contact->email;

                    if ($move['items']) {

                        foreach ($move['items'] as $itemKey => $item) {

                            if ($item['item_id'] == null) {
                                $item['item_id'] = 0;
                            }

                            if ($item->is_unpacked == null && $item->is_unpacked !== 0) {
                                $item->is_unpacked = 2;
                            }

                            $item['container_id'] = $item['container'] ? $item['container']['container_id'] : null;
                            unset($item['container']);

                            if (isset($item['subItems']) && $item['subItems'] != null) {
                                $sub_items = $item['subItems']['subItemDetails'];
                                unset($item['subItems']);
                                $item['sub_item'] = $sub_items;
                            } else {
                                unset($item['subItems']);
                                $item['sub_item'] = null;
                            }

                            if ($item['item_id'] != 0) {

                                $item['cartoon_item_details'] = null;
                            } else {
                                if (isset($item['cartoonItem']) && $item['cartoonItem'] != null) {
                                    $foodArray = [];
                                    foreach ($item['cartoonItem'] as $cartoonItems) {
                                        array_push($foodArray, json_decode(json_encode($cartoonItems['cartoonItemDetails']), true));
                                    }
                                    $item['cartoon_item_details'] = $foodArray;
                                }

                            }

                            if ($item['itemScreeningCategory']) {
                                $item['screening_category'] = $item['itemScreeningCategory']['Category'];
                                unset($item['itemScreeningCategory']);
                            }

                            foreach ($item['condition'] as $conditionKey => $condition) {
                                $imageArray = $condition['conditionImage'];
                                $condition_details = $condition['conditionDetails'];
                                $sideArray = array();
                                foreach ($condition['conditionSides'] as $sideKey => $side) {
                                    array_push($sideArray, $side['sideDetails']);
                                    unset($condition['conditionSides'][$sideKey]);
                                }
                                $item['condition'][$conditionKey]['id'] = $condition_details['id'];
                                $item['condition'][$conditionKey]['condition'] = $condition_details['condition'];
                                $item['condition'][$conditionKey]['condition_code'] = $condition_details['condition_code'];
                                $item['condition'][$conditionKey]['move_stage'] = $condition_details['move_stage'];
                                $item['condition'][$conditionKey]['color_code'] = $condition_details['color_code'];
                                $item['condition'][$conditionKey]['is_side_required'] = $condition_details['is_side_required'];
                                $item['condition'][$conditionKey]['condition_side'] = $sideArray;
                                $item['condition'][$conditionKey]['condition_images'] = $imageArray;
                            }

                            foreach ($item['transloadCondition'] as $transloadConditionKey => $transloadCondition) {
                                $imageArray = $transloadCondition['conditionImage'];
                                $transloadConditionDetails = $transloadCondition['conditionDetails'];
                                $transloadSideArray = array();
                                foreach ($transloadCondition['conditionSides'] as $transloadSideKey => $transloadSide) {
                                    array_push($transloadSideArray, $transloadSide['sideDetails']);
                                    unset($transloadCondition['conditionSides'][$transloadSideKey]);
                                }
                                $item['transloadCondition'][$transloadConditionKey]['id'] = $transloadConditionDetails['id'];
                                $item['transloadCondition'][$transloadConditionKey]['condition'] = $transloadConditionDetails['condition'];
                                $item['transloadCondition'][$transloadConditionKey]['condition_code'] = $transloadConditionDetails['condition_code'];
                                $item['transloadCondition'][$transloadConditionKey]['move_stage'] = $transloadConditionDetails['move_stage'];
                                $item['transloadCondition'][$transloadConditionKey]['color_code'] = $transloadConditionDetails['color_code'];
                                $item['transloadCondition'][$transloadConditionKey]['is_side_required'] = $transloadConditionDetails['is_side_required'];
                                $item['transloadCondition'][$transloadConditionKey]['condition_side'] = $transloadSideArray;
                                $item['transloadCondition'][$transloadConditionKey]['condition_images'] = $imageArray;
                            }

                            foreach ($item['deliveryCondition'] as $deliveryConditionKey => $deliveryCondition) {
                                $imageArray = $deliveryCondition['conditionImage'];
                                $deliveryConditionDetails = $deliveryCondition['conditionDetails'];
                                $deliverySideArray = array();
                                foreach ($deliveryCondition['conditionSides'] as $deliverySideKey => $deliverySide) {
                                    array_push($deliverySideArray, $deliverySide['sideDetails']);
                                    unset($deliveryCondition['conditionSides'][$deliverySideKey]);
                                }
                                $item['deliveryCondition'][$deliveryConditionKey]['id'] = $deliveryConditionDetails['id'];
                                $item['deliveryCondition'][$deliveryConditionKey]['condition'] = $deliveryConditionDetails['condition'];
                                $item['deliveryCondition'][$deliveryConditionKey]['condition_code'] = $deliveryConditionDetails['condition_code'];
                                $item['deliveryCondition'][$deliveryConditionKey]['move_stage'] = $deliveryConditionDetails['move_stage'];
                                $item['deliveryCondition'][$deliveryConditionKey]['color_code'] = $deliveryConditionDetails['color_code'];
                                $item['deliveryCondition'][$deliveryConditionKey]['is_side_required'] = $deliveryConditionDetails['is_side_required'];
                                $item['deliveryCondition'][$deliveryConditionKey]['condition_side'] = $deliverySideArray;
                                $item['deliveryCondition'][$deliveryConditionKey]['condition_images'] = $imageArray;
                            }
                        }
                        $this->data = $move;
                        return $this->sendResponse($this->data, "Move retrieved successfully!");
                    }
                } else {
                    $move = Move::with([
                        'uplift',
                        'roomChoice',
                        'transloadContainer',
                        'items.itemUpliftCategory',
                        'items.itemScreeningCategory.Category',
                        'items.itemPacker',
                        'items.itemDetails',
                        'items.container',
                        'items.roomChoice',
                        'items.subItems.subItemDetails',
                        'items.cartoonItem.cartoonItemDetails',
                        'items.condition.conditionDetails',
                        'items.condition.conditionImage',
                        'items.condition.conditionSides.sideDetails',
                        'items.transloadCondition.conditionDetails',
                        'items.transloadCondition.conditionImage',
                        'items.transloadCondition.conditionSides.sideDetails',
                        'items.deliveryCondition.conditionDetails',
                        'items.deliveryCondition.conditionImage',
                        'items.deliveryCondition.conditionSides.sideDetails',
                    ])
                        ->where('id', $id)
                        ->first();
                    // dd($move);
                    // }
                    // if($agent_move->own_created_company_id ==  $company_id){
                    // 	$move['is_created_move'] = $move->is_created_move = 0;
                    // }else{
                    // 	$move['is_created_move'] = $move->is_created_move = 1;
                    // }

                    $move['customer'] = $move->contact->contact_name;
                    $move['customer_email'] = $move->contact->email;

                    if ($move['items']) {

                        foreach ($move['items'] as $itemKey => $item) {

                            if ($item['item_id'] == null) {
                                $item['item_id'] = 0;
                            }

                            if ($item->is_unpacked == null && $item->is_unpacked !== 0) {
                                $item->is_unpacked = 2;
                            }

                            $item['container_id'] = $item['container'] ? $item['container']['container_id'] : null;
                            unset($item['container']);

                            if ($item['item_id'] == 0) {
                                $item['sub_item'] = null;
                            } else {
                                if (isset($item['subItems']) && $item['subItems'] != null) {
                                    $sub_items = $item['subItems']['subItemDetails'];
                                    unset($item['subItems']);
                                    $item['sub_item'] = $sub_items;
                                } else {
                                    unset($item['subItems']);
                                    $item['sub_item'] = null;
                                }
                            }

                            if ($item['item_id'] != 0) {

                                $item['cartoon_item_details'] = null;
                            } else {
                                if (isset($item['cartoonItem']) && $item['cartoonItem'] != null) {
                                    $foodArray = [];
                                    foreach ($item['cartoonItem'] as $cartoonItems) {
                                        array_push($foodArray, json_decode(json_encode($cartoonItems['cartoonItemDetails']), true));
                                    }
                                    $item['cartoon_item_details'] = $foodArray;
                                }

                            }

                            if ($item['itemScreeningCategory']) {
                                $item['screening_category'] = $item['itemScreeningCategory']['Category'];
                                unset($item['itemScreeningCategory']);
                            }

                            foreach ($item['condition'] as $conditionKey => $condition) {
                                $imageArray = $condition['conditionImage'];
                                $condition_details = $condition['conditionDetails'];
                                $sideArray = array();
                                foreach ($condition['conditionSides'] as $sideKey => $side) {
                                    array_push($sideArray, $side['sideDetails']);
                                    unset($condition['conditionSides'][$sideKey]);
                                }
                                $item['condition'][$conditionKey]['id'] = $condition_details['id'];
                                $item['condition'][$conditionKey]['condition'] = $condition_details['condition'];
                                $item['condition'][$conditionKey]['condition_code'] = $condition_details['condition_code'];
                                $item['condition'][$conditionKey]['move_stage'] = $condition_details['move_stage'];
                                $item['condition'][$conditionKey]['color_code'] = $condition_details['color_code'];
                                $item['condition'][$conditionKey]['is_side_required'] = $condition_details['is_side_required'];
                                $item['condition'][$conditionKey]['condition_side'] = $sideArray;
                                $item['condition'][$conditionKey]['condition_images'] = $imageArray;
                            }

                            foreach ($item['transloadCondition'] as $transloadConditionKey => $transloadCondition) {
                                $imageArray = $transloadCondition['conditionImage'];
                                $transloadConditionDetails = $transloadCondition['conditionDetails'];
                                $transloadSideArray = array();
                                foreach ($transloadCondition['conditionSides'] as $transloadSideKey => $transloadSide) {
                                    array_push($transloadSideArray, $transloadSide['sideDetails']);
                                    unset($transloadCondition['conditionSides'][$transloadSideKey]);
                                }
                                $item['transloadCondition'][$transloadConditionKey]['id'] = $transloadConditionDetails['id'];
                                $item['transloadCondition'][$transloadConditionKey]['condition'] = $transloadConditionDetails['condition'];
                                $item['transloadCondition'][$transloadConditionKey]['condition_code'] = $transloadConditionDetails['condition_code'];
                                $item['transloadCondition'][$transloadConditionKey]['move_stage'] = $transloadConditionDetails['move_stage'];
                                $item['transloadCondition'][$transloadConditionKey]['color_code'] = $transloadConditionDetails['color_code'];
                                $item['transloadCondition'][$transloadConditionKey]['is_side_required'] = $transloadConditionDetails['is_side_required'];
                                $item['transloadCondition'][$transloadConditionKey]['condition_side'] = $transloadSideArray;
                                $item['transloadCondition'][$transloadConditionKey]['condition_images'] = $imageArray;
                            }

                            foreach ($item['deliveryCondition'] as $deliveryConditionKey => $deliveryCondition) {
                                $imageArray = $deliveryCondition['conditionImage'];
                                $deliveryConditionDetails = $deliveryCondition['conditionDetails'];
                                $deliverySideArray = array();
                                foreach ($deliveryCondition['conditionSides'] as $deliverySideKey => $deliverySide) {
                                    array_push($deliverySideArray, $deliverySide['sideDetails']);
                                    unset($deliveryCondition['conditionSides'][$deliverySideKey]);
                                }
                                $item['deliveryCondition'][$deliveryConditionKey]['id'] = $deliveryConditionDetails['id'];
                                $item['deliveryCondition'][$deliveryConditionKey]['condition'] = $deliveryConditionDetails['condition'];
                                $item['deliveryCondition'][$deliveryConditionKey]['condition_code'] = $deliveryConditionDetails['condition_code'];
                                $item['deliveryCondition'][$deliveryConditionKey]['move_stage'] = $deliveryConditionDetails['move_stage'];
                                $item['deliveryCondition'][$deliveryConditionKey]['color_code'] = $deliveryConditionDetails['color_code'];
                                $item['deliveryCondition'][$deliveryConditionKey]['is_side_required'] = $deliveryConditionDetails['is_side_required'];
                                $item['deliveryCondition'][$deliveryConditionKey]['condition_side'] = $deliverySideArray;
                                $item['deliveryCondition'][$deliveryConditionKey]['condition_images'] = $imageArray;
                            }
                        }
                    }
                    $this->data = $move;
                    return $this->sendResponse($this->data, "Move retrieved successfully!");
                }
            } else {
                return $this->apiError("Move status must be in-progress. Please wait for Admin approval.");
            }
        }

    }

    public function changeMoveStatusTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'move_id' => 'required',
            'move_type' => 'required|gte:1|lte:5',
            'status' => 'required|gte:0|lte:2',
            'customer_name' => Rule::requiredIf($request->move_type == '1' && $request->status == '2'),
            'customer_signature' => [Rule::requiredIf($request->move_type == '1' && $request->status == '2'), 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'employee_name' => Rule::requiredIf($request->move_type == '1' && $request->status == '2' || $request->move_type == '4' && $request->status == '2' || $request->move_type == '5' && $request->status == '2'),
            'employee_signature' => [Rule::requiredIf($request->move_type == '1' && $request->status == '2' || $request->move_type == '5' && $request->status == '2'), 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'left_package' => [Rule::requiredIf($request->move_type == '5' && $request->status == '2'), 'integer'],
            'left_carton' => [Rule::requiredIf($request->move_type == '5' && $request->status == '2'), 'integer'],
            // 'email' => [Rule::requiredIf($request->move_type == '5' && $request->status == '2' || $request->move_type == '1' && $request->status == '2'),'email'],
            'device' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }

        $company_id = CompanyAdmin::getCompanyUserCompany();
        $move = Move::where('id', $request->move_id)->first();

        if ($move) {

            ini_set("pcre.backtrack_limit", "5000000");
            switch ($request->move_type) {
                case '1':
                    UpliftMoves::where('move_id', $request->move_id)->update(['status' => $request->status]);

                    $send_email_device = [];

                    $company_email = Companies::where('id', $move->company_id)->value('email');
                    $uplift_device_email = UpliftMoves::where('move_id', $move->id)->value('device_email');


                    //sening email for uplift ICR
                    if ($move->is_assign == 1) {
                        if ($uplift_device_email != $company_email) {
                            $send_email_device = [$uplift_device_email, $company_email];
                        } else {
                            $send_email_device = [$uplift_device_email];
                        }
                    } else {
                        $send_email_device = [$uplift_device_email];
                    }

                    if ($request->status == 2) {
                        $company_id = CompanyAdmin::getCompanyUserCompany();
                        if (Companies::where('id', $company_id)->where('kika_direct', 1)->exists()) {
                            Move::where('id', $request->move_id)->update(['is_completed_icr_uplift' => 1]);
                        }

                        if ($move->transit) {
                            TransitMoves::where('id', $move->transit->id)->delete();
                        } elseif (!($move->transit)) {
                            $transit_move = new TransitMoves();
                            $transit_move->move_id = $move->id;
                            $transit_move->volume = $move->uplift->volume;
                            $transit_move->status = 1;
                            $transit_move->save();
                        }

                        if ($move->required_screening == 1 && !($move->screening)) {
                            $screening_move = new ScreeningMoves();
                            $screening_move->move_id = $move->id;
                            $screening_move->volume = $move->uplift->volume;
                            $screening_move->status = 0;
                            $screening_move->save();
                        }

                        if ($move->required_storage == 1 && !($move->transload)) {
                            $transload_move = new TransloadMoves();
                            $transload_move->move_id = $move->id;
                            $transload_move->volume = $move->uplift->volume;
                            $transload_move->status = 0;
                            $transload_move->save();
                        }

                        CompanyUserDetails::changeConditionalStatus($move->id);

                        if (TermsAndConditionsChecked::where(['move_id' => $request->move_id, 'move_status' => 1, 'move_type' => 1])->exists()) {

                            TermsAndConditionsChecked::where(['move_id' => $request->move_id, 'move_status' => 1, 'move_type' => 1])->delete();
                        }

                        $checked_condition = explode(',', $request->accept_condition);
                        for ($pointer = 5; $pointer <= 8; $pointer++) {
                            $termsandcondition = new TermsAndConditionsChecked();
                            $termsandcondition->move_id = $request->move_id;
                            $termsandcondition->move_type = 1;
                            $termsandcondition->tnc_id = $pointer;

                            in_array($pointer, $checked_condition) ? $termsandcondition->is_checked = 1 : $termsandcondition->is_checked = 0;

                            $termsandcondition->move_status = 1;
                            $termsandcondition->save();
                        }

                        if ($existingPackageSignature = PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 1, 'status' => 1])->first()) {

                            if (\Storage::has('/clientsignature/' . $existingPackageSignature->customer_signature)) {
                                \Storage::delete('/clientsignature/' . $existingPackageSignature->customer_signature);
                            }
                            if (\Storage::has('/clientsignature/' . $existingPackageSignature->employee_signature)) {
                                \Storage::delete('/clientsignature/' . $existingPackageSignature->employee_signature);
                            }

                            PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 1, 'status' => 1])->delete();
                        }

                        $customer_signature = "CSTM_SIGN_UPL_POST_MOVE_" . $move->reference_number . '.' . $request->customer_signature->extension();
                        $employee_signature = "EMP_SIGN_UPL_POST_MOVE_" . $move->reference_number . '.' . $request->customer_signature->extension();

                        $filePath_customer = '/clientsignature/' . $customer_signature;
                        // dd($filePath_customer);
                        \Storage::put($filePath_customer, file_get_contents($request->customer_signature));
                        $filePath_employee = '/clientsignature/' . $employee_signature;
                        \Storage::put($filePath_employee, file_get_contents($request->employee_signature));

                        // $request->customer_signature->move(public_path('storage/image/company-admin/signature'), $customer_signature);
                        // $request->employee_signature->move(public_path('storage/image/company-admin/signature'), $employee_signature);

                        $packageSignature = new PackageSignature();
                        $packageSignature->move_id = $request->move_id;
                        $packageSignature->move_type = 1;
                        $packageSignature->client_name = $request->customer_name;
                        $packageSignature->client_signature = $customer_signature;
                        $packageSignature->employee_name = $request->employee_name;
                        $packageSignature->employee_signature = $employee_signature;
                        $packageSignature->status = 1;
                        $packageSignature->save();

                        $data['move'] = $move;
                        $data['move_mode'] = "Uplift Post";
                        $move_type = 1;
                        $comment_type = 1;
                        $data['link'] = 'company-admin/move/getdata/' . Crypt::encrypt($move->id) . '/' . $move_type . '/' . $comment_type;


                        $companyAdmin = Companies::where('id', $company_id)->value('email');


                        if ($move->foreign_destination_agent) {
                            $company_agent = CompanyAgent::where('company_id', $move->foreign_destination_agent)->value('email');
                            if ($company_agent) {
                                array_push($customer, $company_agent);
                            }
                        }

                        $check_condition_image = DB::table('move_item_conditions')
                            ->join('move_condition_images', 'move_condition_images.move_condition_id', 'move_item_conditions.id')
                            ->select('move_condition_images.image')
                            ->where([
                                ['move_id', $request->move_id],
                                ['move_type', $request->move_type],
                            ]);
                        if ($check_condition_image->exists()) {
                            $data['move_type'] = 'Uplift';
                            $data['condition_image_pdf_link'] = 'company-admin/move/uplift/icrimage/pdf/' . Crypt::encrypt($move->id);
                        }



                        // 	$arr_email = [];
                        // 	$final_email_customer = '';
                        if ($move->is_overflow == 1) {
                            // return $this->apiError("111");
                        } else {
                            // return $this->apiError("222");
                        }

                        $nonkika_move = UpliftMoves::where('move_id', $request->move_id)->first();
                        if ($nonkika_move->item_count != null) {
                            Move::where('id', $request->move_id)->update(['type_id' => 5]);
                        } else {
                            if (!ScreeningMoves::where('move_id', $request->move_id)->exists()) {
                                if (DeliveryMoves::where('move_id', $request->move_id)->exists()) {
                                    Move::where('id', $request->move_id)->update(['type_id' => 5]);
                                } else {
                                    Move::where('id', $request->move_id)->update(['type_id' => 1]);
                                }
                            } else {
                                Move::where('id', $request->move_id)->update(['type_id' => 3]);
                            }
                        }
                    } else {
                        Move::where('id', $request->move_id)->update(['type_id' => 1]);
                    }

                    if (ScreeningMoves::where('move_id', $request->move_id)->exists()) {
                        ScreeningMoves::where('move_id', $request->move_id)->update(['status' => 1]);
                    }

                    return $this->msgResponse("Uplift status changed successfully");

                case '2':
                    return $this->apiError("You cant change status of this move type!");

                case '3':
                    ScreeningMoves::where('move_id', $request->move_id)->update(['status' => $request->status]);
                    if ($request->status == 2) {

                        if ($move->required_storage == 1 && !($move->transload)) {
                            $transload_move = new TransloadMoves();
                            $transload_move->move_id = $move->id;
                            $transload_move->volume = $move->uplift->volume;
                            $transload_move->status = 0;
                            $transload_move->save();
                        }

                        CompanyUserDetails::changeConditionalStatus($move->id);

                        Move::where('id', $request->move_id)->update(['type_id' => 4]);
                    } else {
                        Move::where('id', $request->move_id)->update(['type_id' => 3]);
                    }
                    if (TransloadMoves::where('move_id', $request->move_id)->exists()) {
                        TransloadMoves::where('move_id', $request->move_id)->update(['status' => 1]);
                    }
                    return $this->msgResponse("Screening status changed successfully");

                case '4':
                    TransloadMoves::where('move_id', $request->move_id)->update(['status' => $request->status]);
                    // dd($move);
                    $data['move'] = $move;
                    $data['transloadlink'] = 'company-admin/move/gettransload/' . Crypt::encrypt($move->id);

                    $destinationAgent = DeliveryMoves::where('move_id', $request->move_id)->first();
                    // dd($destinationAgent);
                    // $receiver = $move->uplift->origin_agent_email;
                    $receiver_email = Companies::where('id', $move->company_id)->value('email');
                    $customer = [$destinationAgent->delivery_agent_email, $receiver_email];

                    $device_email = User::where('username', $request->device)->value('email');
                    $company_email = Companies::where('id', $move->company_id)->value('email');
                    $send_email_address = "";

                    if ($device_email != "" && $device_email != null) {
                        $send_email_address = $device_email;
                    } else {
                        $send_email_address = $company_email;
                    }

                    Mail::send('mails.transload', $data, function ($message) use ($move, $send_email_address) {
                        $message->to($send_email_address)
                            ->subject($move->contact->contact_name . " - " . $move->move_number . " : Bingo Sheet");
                    });

                    if ($request->status == 2) {

                        CompanyUserDetails::changeConditionalStatus($move->id);

                        if (PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 4, 'status' => 1])->exists()) {
                            PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 4, 'status' => 1])->delete();
                        }

                        $packageSignature = new PackageSignature();
                        $packageSignature->move_id = $request->move_id;
                        $packageSignature->move_type = 4;
                        $packageSignature->client_name = null;
                        $packageSignature->client_signature = null;
                        $packageSignature->employee_name = $request->employee_name;
                        $packageSignature->employee_signature = null;
                        $packageSignature->status = 1;
                        $packageSignature->save();

                        Move::where('id', $request->move_id)->update(['type_id' => 5]);
                    } else {
                        Move::where('id', $request->move_id)->update(['type_id' => 4]);
                    }
                    return $this->msgResponse("Transload status changed successfully");

                case '5':
                    DeliveryMoves::where('move_id', $request->move_id)->update(['status' => $request->status]);

                    $send_email_device = [];

                    $uplift_device_email = UpliftMoves::where('move_id', $move->id)->value('device_email'); //Uplift Device email
                    $delivery_device_email = DeliveryMoves::where('move_id', $move->id)->value('device_email'); //Delivery Device Email


                    // sening email for uplift ICR
                    if ($move->is_assign == 1) {
                        if ($uplift_device_email != $delivery_device_email) {
                            $send_email_device = [$uplift_device_email, $delivery_device_email];
                        } else {
                            $send_email_device = [$delivery_device_email];
                        }
                    } else {
                        $send_email_device = [$delivery_device_email];
                    }

                    return $this->apiError($send_email_device[0]);
                // if ($request->status == 2) {
                //     $company_id = CompanyAdmin::getCompanyUserCompany();

                //     if (Companies::where('id', $company_id)->where('kika_direct', 1)->exists()) {
                //         Move::where('id', $request->move_id)->update(['is_completed_icr_delivery' => 1]);
                //     }
                //     CompanyUserDetails::changeConditionalStatus($move->id);

                //     DeliveryMoves::where('move_id', $request->move_id)->update(['lp_package' => $request->left_package, 'lp_carton' => $request->left_carton]);

                //     if (TermsAndConditionsChecked::where(['move_id' => $request->move_id, 'move_status' => 1, 'move_type' => 5])->exists()) {
                //         TermsAndConditionsChecked::where(['move_id' => $request->move_id, 'move_status' => 1, 'move_type' => 5])->delete();
                //     }

                //     $checked_condition = explode(',', $request->accept_condition);

                //     for ($pointer = 13; $pointer <= 18; $pointer++) {
                //         $termsandcondition = new TermsAndConditionsChecked();
                //         $termsandcondition->move_id = $request->move_id;
                //         $termsandcondition->move_type = $request->move_type;
                //         $termsandcondition->tnc_id = $pointer;
                //         $termsandcondition->is_checked = in_array($pointer, $checked_condition) ? 1 : 0;
                //         $termsandcondition->move_status = 1;
                //         $termsandcondition->save();
                //     }

                //     if ($existingPackageSignature = PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 5, 'status' => 1])->first()) {

                //         if (\Storage::has('/clientsignature/' . $existingPackageSignature->customer_signature)) {
                //             \Storage::delete('/clientsignature/' . $existingPackageSignature->customer_signature);
                //         }
                //         if (\Storage::has('/clientsignature/' . $existingPackageSignature->employee_signature)) {
                //             \Storage::delete('/clientsignature/' . $existingPackageSignature->employee_signature);
                //         }

                //         PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 5, 'status' => 1])->delete();
                //     }

                //     $customer_signature = "CSTM_SIGN_DL_POST_MOVE_" . $move->reference_number . '.' . $request->customer_signature->extension();
                //     $employee_signature = "EMP_SIGN_DL_POST_MOVE_" . $move->reference_number . '.' . $request->customer_signature->extension();

                //     $filePath_customer = '/clientsignature/' . $customer_signature;
                //     // dd($filePath_customer);
                //     \Storage::put($filePath_customer, file_get_contents($request->customer_signature));
                //     $filePath_employee = '/clientsignature/' . $employee_signature;
                //     \Storage::put($filePath_employee, file_get_contents($request->employee_signature));
                //     // $request->customer_signature->move(public_path('storage/image/company-admin/signature'), $customer_signature);
                //     // $request->employee_signature->move(public_path('storage/image/company-admin/signature'), $employee_signature);

                //     $packageSignature = new PackageSignature();
                //     $packageSignature->move_id = $request->move_id;
                //     $packageSignature->move_type = 5;
                //     $packageSignature->client_name = $request->customer_name;
                //     $packageSignature->client_signature = $customer_signature;
                //     $packageSignature->employee_name = $request->employee_name;
                //     $packageSignature->employee_signature = $employee_signature;
                //     $packageSignature->status = 1;
                //     $packageSignature->save();

                //     $data['move'] = $move;
                //     $data['move_mode'] = "Delivery Post";
                //     $move_type = 5;
                //     $comment_type = 1;
                //     $data['link'] = 'company-admin/move/getdata/' . Crypt::encrypt($move->id) . '/' . $move_type . '/' . $comment_type;

                //     $icrpdfData = GetIcrData::getIcrData($move->id, 5);

                //     // $bg_color = '#d5d5d5';
                //     // $pdf_margin_top = 40;
                //     // $div_html = '<div class="company-name text-black mb-10">' . $move->uplift->origin_agent . '</div>';
                //     // $company_admin = Companies::select('id', 'kika_id', 'icr_title_toggle', 'icr_title_image', 'title_bar_color_code')->where('kika_id', $icrpdfData['move']['origin_agent_kika_id'])->first();
                //     // if ($company_admin && $company_admin->icr_title_toggle == 0) {
                //     //     if ($company_admin->title_bar_color_code != null) {
                //     //         $bg_color = $company_admin->title_bar_color_code;
                //     //     }

                //     //     if ($company_admin->icr_title_image != null) {
                //     //         $pdf_margin_top = 46;
                //     //         $s3_base_url = config('filesystems.disks.s3.url');
                //     //         $s3_image_path = $s3_base_url . 'icrtitle/';
                //     //         $title_image_path = '';
                //     //         if ($company_admin->icr_title_image != '') {
                //     //             $title_image_path = $s3_image_path . $company_admin->icr_title_image;
                //     //         }
                //     //         $div_html = '<div class="mb-10">
                //     //             <img style="max-width: 720px; max-height: 80px;" src="' . $title_image_path . '" alt="">
                //     //         </div>';
                //     //     }
                //     // }

                //     $check_condition_image = DB::table('move_item_conditions')
                //         ->join('move_condition_images', 'move_condition_images.move_condition_id', 'move_item_conditions.id')
                //         ->select('move_condition_images.image')
                //         ->where([
                //             ['move_id', $request->move_id],
                //             ['move_type', $request->move_type],
                //         ]);
                //     if ($check_condition_image->exists()) {
                //         $data['move_type'] = 'Delivery';
                //         $data['condition_image_pdf_link'] = 'company-admin/move/delivery/icrimage/pdf/' . Crypt::encrypt($move->id);
                //     }

                //     if ($icrpdfData['move']['item_count'] != null && $icrpdfData['move_type'] == 5) {
                //         // $icr_pdf = PDF::loadView('theme.company-admin.pdf.nonkika-delivery-icr', $icrpdfData)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);

                //         return $this->msgResponse("1111");
                //         //Sending email to Uplift Device & Delivery Device email
                //         // Mail::send('mails.customer-icr-pdf', $data, function ($message) use ($mpdf, $move, $fileName, $sub_title) {
                //         //     $message->to("clearsky5290@gmail.com")
                //         //         ->subject($move->conct->contact_name . " : " . $move->move_number . " - " . $sub_title)
                //         //         ->attachData($mpdf->Output(null, 'S'), $fileName);
                //         // });

                //     } else {
                //         // $icr_pdf = PDF::loadView('theme.company-admin.pdf.icr', $icrpdfData)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);
                //         return $this->msgResponse($send_email_device);
                //         //Sending email to Uplift Device & Delivery Device email
                //         // Mail::send('mails.customer-icr-pdf', $data, function ($message) use ($mpdf, $move, $send_email_device, $fileName, $sub_title) {
                //         //     $message->to(array_unique($send_email_device))
                //         //         ->subject($move->conct->contact_name . " : " . $move->move_number . " - " . $sub_title)
                //         //         ->attachData($mpdf->Output(null, 'S'), $fileName);
                //         // });

                //     }
                // } else {
                //     Move::where('id', $request->move_id)->update(['type_id' => 5]);
                // }

                // return $this->msgResponse("Delivery status changed successfully");

                default:
                    return $this->apiError("Invalid post data!");
            }
        } else {
            return $this->apiError("No move found for given move number.");
        }
    }
    public function changeMoveStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'move_id' => 'required',
            'move_type' => 'required|gte:1|lte:5',
            'status' => 'required|gte:0|lte:2',
            'customer_name' => Rule::requiredIf($request->move_type == '1' && $request->status == '2'),
            'customer_signature' => [Rule::requiredIf($request->move_type == '1' && $request->status == '2'), 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'employee_name' => Rule::requiredIf($request->move_type == '1' && $request->status == '2' || $request->move_type == '4' && $request->status == '2' || $request->move_type == '5' && $request->status == '2'),
            'employee_signature' => [Rule::requiredIf($request->move_type == '1' && $request->status == '2' || $request->move_type == '5' && $request->status == '2'), 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'left_package' => [Rule::requiredIf($request->move_type == '5' && $request->status == '2'), 'integer'],
            'left_carton' => [Rule::requiredIf($request->move_type == '5' && $request->status == '2'), 'integer'],
            'email' => 'nullable|email|max:255',
            'device' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }

        $company_id = CompanyAdmin::getCompanyUserCompany();
        $move = Move::where('id', $request->move_id)->first();

        if ($move) {

            ini_set("pcre.backtrack_limit", "5000000");
            switch ($request->move_type) {
                case '1':
                    UpliftMoves::where('move_id', $request->move_id)->update(['status' => $request->status]);

                    $send_email_device = [];

                    //Delivery company Email
                    $company_email = Companies::where('id', $move->foreign_destination_agent)->value('email');
                    $uplift_device_email = UpliftMoves::where('move_id', $move->id)->value('device_email');
                    //optional email address


                    //sening email for uplift ICR
                    if ($move->is_assign == 1) {
                        if ($request->email != "" && $request->email != null) {
                            if ($uplift_device_email != $company_email) {
                                $send_email_device = [$uplift_device_email, $company_email, $request->email];
                            } else {
                                $send_email_device = [$uplift_device_email, $request->email];
                            }
                        } else {
                            if ($uplift_device_email != $company_email) {
                                $send_email_device = [$uplift_device_email, $company_email];
                            } else {
                                $send_email_device = [$uplift_device_email];
                            }
                        }

                    } else {
                        if ($request->email != "" && $request->email != null) {
                            $send_email_device = [$uplift_device_email, $request->email];
                        } else {
                            $send_email_device = [$uplift_device_email];
                        }
                    }

                    if ($request->status == 2) {
                        $company_id = CompanyAdmin::getCompanyUserCompany();
                        if (Companies::where('id', $company_id)->where('kika_direct', 1)->exists()) {
                            Move::where('id', $request->move_id)->update(['is_completed_icr_uplift' => 1]);
                        }

                        if ($move->transit) {
                            TransitMoves::where('id', $move->transit->id)->delete();
                        } elseif (!($move->transit)) {
                            $transit_move = new TransitMoves();
                            $transit_move->move_id = $move->id;
                            $transit_move->volume = $move->uplift->volume;
                            $transit_move->status = 1;
                            $transit_move->save();
                        }

                        if ($move->required_screening == 1 && !($move->screening)) {
                            $screening_move = new ScreeningMoves();
                            $screening_move->move_id = $move->id;
                            $screening_move->volume = $move->uplift->volume;
                            $screening_move->status = 0;
                            $screening_move->save();
                        }

                        if ($move->required_storage == 1 && !($move->transload)) {
                            $transload_move = new TransloadMoves();
                            $transload_move->move_id = $move->id;
                            $transload_move->volume = $move->uplift->volume;
                            $transload_move->status = 0;
                            $transload_move->save();
                        }

                        CompanyUserDetails::changeConditionalStatus($move->id);

                        if (TermsAndConditionsChecked::where(['move_id' => $request->move_id, 'move_status' => 1, 'move_type' => 1])->exists()) {

                            TermsAndConditionsChecked::where(['move_id' => $request->move_id, 'move_status' => 1, 'move_type' => 1])->delete();
                        }

                        $checked_condition = explode(',', $request->accept_condition);
                        for ($pointer = 5; $pointer <= 8; $pointer++) {
                            $termsandcondition = new TermsAndConditionsChecked();
                            $termsandcondition->move_id = $request->move_id;
                            $termsandcondition->move_type = 1;
                            $termsandcondition->tnc_id = $pointer;

                            in_array($pointer, $checked_condition) ? $termsandcondition->is_checked = 1 : $termsandcondition->is_checked = 0;

                            $termsandcondition->move_status = 1;
                            $termsandcondition->save();
                        }

                        if ($existingPackageSignature = PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 1, 'status' => 1])->first()) {

                            if (\Storage::has('/clientsignature/' . $existingPackageSignature->customer_signature)) {
                                \Storage::delete('/clientsignature/' . $existingPackageSignature->customer_signature);
                            }
                            if (\Storage::has('/clientsignature/' . $existingPackageSignature->employee_signature)) {
                                \Storage::delete('/clientsignature/' . $existingPackageSignature->employee_signature);
                            }

                            PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 1, 'status' => 1])->delete();
                        }

                        $customer_signature = "CSTM_SIGN_UPL_POST_MOVE_" . $move->reference_number . '.' . $request->customer_signature->extension();
                        $employee_signature = "EMP_SIGN_UPL_POST_MOVE_" . $move->reference_number . '.' . $request->customer_signature->extension();

                        $filePath_customer = '/clientsignature/' . $customer_signature;
                        // dd($filePath_customer);
                        \Storage::put($filePath_customer, file_get_contents($request->customer_signature));
                        $filePath_employee = '/clientsignature/' . $employee_signature;
                        \Storage::put($filePath_employee, file_get_contents($request->employee_signature));

                        // $request->customer_signature->move(public_path('storage/image/company-admin/signature'), $customer_signature);
                        // $request->employee_signature->move(public_path('storage/image/company-admin/signature'), $employee_signature);

                        $packageSignature = new PackageSignature();
                        $packageSignature->move_id = $request->move_id;
                        $packageSignature->move_type = 1;
                        $packageSignature->client_name = $request->customer_name;
                        $packageSignature->client_signature = $customer_signature;
                        $packageSignature->employee_name = $request->employee_name;
                        $packageSignature->employee_signature = $employee_signature;
                        $packageSignature->status = 1;
                        $packageSignature->save();

                        $data['move'] = $move;
                        $data['move_mode'] = "Uplift Post";
                        $move_type = 1;
                        $comment_type = 1;
                        $data['link'] = 'company-admin/move/getdata/' . Crypt::encrypt($move->id) . '/' . $move_type . '/' . $comment_type;

                        $icrpdfData = GetIcrData::getIcrData($move->id, 1);
                        // $icr_pdf = PDF::loadView('theme.company-admin.pdf.icr', $icrpdfData);
                        $bg_color = '#d5d5d5';
                        $pdf_margin_top = 40;
                        $div_html = '<div class="company-name text-black mb-10">' . $move->uplift->origin_agent . '</div>';
                        $company_admin = Companies::select('id', 'kika_id', 'icr_title_toggle', 'icr_title_image', 'title_bar_color_code')->where('kika_id', $icrpdfData['move']['origin_agent_kika_id'])->first();
                        if ($company_admin && $company_admin->icr_title_toggle == 0) {
                            if ($company_admin->title_bar_color_code != null) {
                                $bg_color = $company_admin->title_bar_color_code;
                            }

                            if ($company_admin->icr_title_image != null) {
                                $pdf_margin_top = 46;
                                $s3_base_url = config('filesystems.disks.s3.url');
                                $s3_image_path = $s3_base_url . 'icrtitle/';
                                $title_image_path = '';
                                if ($company_admin->icr_title_image != '') {
                                    $title_image_path = $s3_image_path . $company_admin->icr_title_image;
                                }
                                $div_html = '<div class="mb-10">
                                    <img style="max-width: 720px; max-height: 80px;" src="' . $title_image_path . '" alt="">
                                </div>';
                            }
                        }

                        $mpdf = new Mpdf([
                            'fontDir' => str_replace('\\', '/', base_path('public/fonts/arial')),
                            'fontdata' => [
                                'customarialfont' => [
                                    'R' => 'Arial.ttf',
                                    'B' => 'Arial-bold.ttf',
                                ]
                            ],
                            'mode' => 'utf-8',
                            'format' => 'A4',
                            'margin_left' => 12,
                            'margin_right' => 12,
                            'margin_top' => $pdf_margin_top,
                            'margin_bottom' => 16,
                            'margin_header' => 12,
                            'margin_footer' => 12,
                        ]);

                        $mpdf->SetHTMLHeader(
                            '<div class="main-wrapper">
                            ' . $div_html . '
                                    <div class="mb-10 report-delivery" style="background-color: ' . $bg_color . '">
                                        <table class="border-0">
                                            <tr>
                                                <td class="text-black f-14">Inventory And Condition Report - ' . $move->contact->contact_name . ' : ' . $move->move_number . ' - ' . ucfirst($move_type == 1 ? "Uplift" : "Delivery") . '</td>
                                                <td class="text-black f-14" style="text-align: right">Page {PAGENO} of {nbpg}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>'
                        );
                        $mpdf->AddPage('P');
                        $html = view('theme.company-admin.pdf.icr', $icrpdfData)->render();
                        $mpdf->WriteHTML($html);

                        $companyAdmin = Companies::where('id', $company_id)->value('email');
                        if ($move->uplift->sub_contactor_email != null) {
                            if ($request->email) {
                                if ($move->is_overflow == 1) {
                                    $customer = [$request->email, $move->contact->email, $move->controlling_agent_email, $uplift_device_email, $move->uplift->sub_contactor_email, $companyAdmin];
                                } else {
                                    $customer = [$request->email, $move->contact->email, $move->controlling_agent_email, $uplift_device_email, $move->uplift->sub_contactor_email];
                                }

                            } else {
                                if ($move->is_overflow == 1) {
                                    $customer = [$move->contact->email, $move->controlling_agent_email, $uplift_device_email, $move->uplift->sub_contactor_email, $companyAdmin];
                                } else {
                                    $customer = [$move->contact->email, $move->controlling_agent_email, $uplift_device_email, $move->uplift->sub_contactor_email];
                                }
                            }

                        } else {
                            if ($request->email) {
                                if ($move->is_overflow == 1) {
                                    $customer = [$request->email, $move->contact->email, $move->controlling_agent_email, $uplift_device_email, $companyAdmin];
                                } else {
                                    $customer = [$request->email, $move->contact->email, $move->controlling_agent_email, $uplift_device_email];
                                }

                            } else {
                                if ($move->is_overflow == 1) {
                                    $customer = [$move->contact->email, $move->controlling_agent_email, $uplift_device_email, $companyAdmin];
                                } else {
                                    $customer = [$move->contact->email, $move->controlling_agent_email, $uplift_device_email];
                                }
                            }
                        }

                        if ($move->foreign_destination_agent) {
                            $company_agent = CompanyAgent::where('company_id', $move->foreign_destination_agent)->value('email');
                            if ($company_agent) {
                                array_push($customer, $company_agent);
                            }
                        }

                        $check_condition_image = DB::table('move_item_conditions')
                            ->join('move_condition_images', 'move_condition_images.move_condition_id', 'move_item_conditions.id')
                            ->select('move_condition_images.image')
                            ->where([
                                ['move_id', $request->move_id],
                                ['move_type', $request->move_type],
                            ]);
                        if ($check_condition_image->exists()) {
                            $data['move_type'] = 'Uplift';
                            $data['condition_image_pdf_link'] = 'company-admin/move/uplift/icrimage/pdf/' . Crypt::encrypt($move->id);
                        }



                        // 	$arr_email = [];
                        // 	$final_email_customer = '';
                        if ($move->is_overflow == 1) {
                            $overFlowIcr_data = GetIcrData::getOverflowIcrData($move->id, 1);
                            // $overFolw_pdf = PDF::loadView('theme.company-admin.pdf.uplift-overflowIcr', $overFlowIcr_data)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);
                            $fileName = $move->contact->contact_name . ' - ' . $move->move_number . ' - Uplift.pdf';
                            $overFlowfileName = $move->contact->contact_name . ' - ' . $move->move_number . ' - Overflow.pdf';

                            $overflow_mpdf = new Mpdf([
                                'fontDir' => str_replace('\\', '/', base_path('public/fonts/arial')),
                                'fontdata' => [
                                    'customarialfont' => [
                                        'R' => 'Arial.ttf',
                                        'B' => 'Arial-bold.ttf',
                                    ]
                                ],
                                'mode' => 'utf-8',
                                'format' => 'A4',
                                'margin_left' => 12,
                                'margin_right' => 12,
                                'margin_top' => $pdf_margin_top,
                                'margin_bottom' => 16,
                                'margin_header' => 12,
                                'margin_footer' => 12,
                            ]);

                            $overflow_mpdf->SetHTMLHeader(
                                '<div class="main-wrapper">
                                ' . $div_html . '
                                        <div class="mb-10 report-delivery" style="background-color: ' . $bg_color . '">
                                            <table class="border-0">
                                                <tr>
                                                    <td class="text-black f-14">Inventory And Condition Report - ' . $move->contact->contact_name . ' : ' . $move->move_number . ' - ' . ucfirst("Overflow") . '</td>
                                                    <td class="text-black f-14" style="text-align: right">Page {PAGENO} of {nbpg}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>'
                            );
                            $overflow_mpdf->AddPage('P');
                            $overflow_html = view('theme.company-admin.pdf.uplift-overflowIcr', $overFlowIcr_data)->render();
                            $overflow_mpdf->WriteHTML($overflow_html);

                            // 	$companyAdmin = Companies::where('id',$company_id)->value('email');
                            // 	array_push($arr_email,$companyAdmin);
                            // 	$final_email_customer .= $companyAdmin.',';

                            $comment_image = MoveComments::where(['move_id' => $move->id, 'move_type' => $move_type, 'move_status' => $comment_type])->with('image')->first();
                            if ($comment_image && $comment_image->image->isNotEmpty()) {
                                $data['image_pdf_link'] = 'company-admin/move/post-move-comment-image/' . Crypt::encrypt($move->id) . '/' . $move_type;
                            }

                            /////////in case local Server/////////////
                            Mail::send('mails.customer-icr-pdf', $data, function ($message) use ($mpdf, $move, $send_email_device, $fileName, $overflow_mpdf, $overFlowfileName) {
                                $message->to(array_unique($send_email_device))
                                    ->subject($move->contact->contact_name . " : " . $move->move_number . " - Uplift")
                                    ->attachData($mpdf->Output(null, 'S'), $fileName)
                                    ->attachData($overflow_mpdf->Output(null, 'S'), $overFlowfileName);
                            });

                        } else {
                            $fileName = $move->contact->contact_name . ' - ' . $move->move_number . ' - Uplift.pdf';
                            $comment_image = MoveComments::where(['move_id' => $move->id, 'move_type' => $move_type, 'move_status' => $comment_type])->with('image')->first();
                            if ($comment_image && $comment_image->image->isNotEmpty()) {
                                $data['image_pdf_link'] = 'company-admin/move/post-move-comment-image/' . Crypt::encrypt($move->id) . '/' . $move_type;
                            }

                            /////////in case local Server/////////////
                            Mail::send('mails.customer-icr-pdf', $data, function ($message) use ($mpdf, $move, $send_email_device, $fileName) {
                                $message->to(array_unique($send_email_device))
                                    ->subject($move->contact->contact_name . " : " . $move->move_number . " - Uplift")
                                    // ->attachData($icr_pdf->output(), $fileName);
                                    ->attachData($mpdf->Output(null, 'S'), $fileName);
                            });
                        }

                        $nonkika_move = UpliftMoves::where('move_id', $request->move_id)->first();
                        if ($nonkika_move->item_count != null) {
                            Move::where('id', $request->move_id)->update(['type_id' => 5]);
                        } else {
                            if (!ScreeningMoves::where('move_id', $request->move_id)->exists()) {
                                if (DeliveryMoves::where('move_id', $request->move_id)->exists()) {
                                    Move::where('id', $request->move_id)->update(['type_id' => 5]);
                                } else {
                                    Move::where('id', $request->move_id)->update(['type_id' => 1]);
                                }
                            } else {
                                // Move::where('id', $request->move_id)->update(['type_id' => 3]);
                                Move::where('id', $request->move_id)->update(['type_id' => 3]);
                            }
                        }
                    } else {
                        Move::where('id', $request->move_id)->update(['type_id' => 1]);
                    }

                    if (ScreeningMoves::where('move_id', $request->move_id)->exists()) {
                        ScreeningMoves::where('move_id', $request->move_id)->update(['status' => 1]);
                    }

                    return $this->msgResponse("Uplift status changed successfully");

                case '2':
                    return $this->apiError("You cant change status of this move type!");

                case '3':
                    ScreeningMoves::where('move_id', $request->move_id)->update(['status' => $request->status]);
                    if ($request->status == 2) {

                        if ($move->required_storage == 1 && !($move->transload)) {
                            $transload_move = new TransloadMoves();
                            $transload_move->move_id = $move->id;
                            $transload_move->volume = $move->uplift->volume;
                            $transload_move->status = 0;
                            $transload_move->save();
                        }

                        CompanyUserDetails::changeConditionalStatus($move->id);

                        Move::where('id', $request->move_id)->update(['type_id' => 4]);
                    } else {
                        Move::where('id', $request->move_id)->update(['type_id' => 3]);
                    }
                    if (TransloadMoves::where('move_id', $request->move_id)->exists()) {
                        TransloadMoves::where('move_id', $request->move_id)->update(['status' => 1]);
                    }
                    return $this->msgResponse("Screening status changed successfully");

                case '4':
                    TransloadMoves::where('move_id', $request->move_id)->update(['status' => $request->status]);
                    // dd($move);
                    $data['move'] = $move;
                    $data['transloadlink'] = 'company-admin/move/gettransload/' . Crypt::encrypt($move->id);

                    $destinationAgent = DeliveryMoves::where('move_id', $request->move_id)->first();
                    // dd($destinationAgent);
                    // $receiver = $move->uplift->origin_agent_email;
                    $receiver_email = Companies::where('id', $move->company_id)->value('email');
                    $customer = [$destinationAgent->delivery_agent_email, $receiver_email];

                    $device_email = User::where('username', $request->device)->value('email');
                    $company_email = Companies::where('id', $move->company_id)->value('email');
                    $send_email_address = "";

                    if ($device_email != "" && $device_email != null) {
                        $send_email_address = $device_email;
                    } else {
                        $send_email_address = $company_email;
                    }

                    Mail::send('mails.transload', $data, function ($message) use ($move, $send_email_address) {
                        $message->to($send_email_address)
                            ->subject($move->contact->contact_name . " - " . $move->move_number . " : Bingo Sheet");
                    });

                    if ($request->status == 2) {

                        CompanyUserDetails::changeConditionalStatus($move->id);

                        if (PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 4, 'status' => 1])->exists()) {
                            PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 4, 'status' => 1])->delete();
                        }

                        $packageSignature = new PackageSignature();
                        $packageSignature->move_id = $request->move_id;
                        $packageSignature->move_type = 4;
                        $packageSignature->client_name = null;
                        $packageSignature->client_signature = null;
                        $packageSignature->employee_name = $request->employee_name;
                        $packageSignature->employee_signature = null;
                        $packageSignature->status = 1;
                        $packageSignature->save();

                        Move::where('id', $request->move_id)->update(['type_id' => 5]);
                    } else {
                        Move::where('id', $request->move_id)->update(['type_id' => 4]);
                    }
                    return $this->msgResponse("Transload status changed successfully");

                case '5':
                    DeliveryMoves::where('move_id', $request->move_id)->update(['status' => $request->status]);

                    $send_email_device = [];

                    $uplift_device_email = UpliftMoves::where('move_id', $move->id)->value('device_email'); //Uplift Device email
                    $delivery_device_email = DeliveryMoves::where('move_id', $move->id)->value('device_email'); //Delivery Device Email


                    // Send  Delivery ICR 
                    // if ($move->is_assign == 1) {
                    if ($request->email != "" && $request->email != null) {
                        if ($uplift_device_email != $delivery_device_email) {
                            $send_email_device = [$uplift_device_email, $delivery_device_email, $request->email];
                        } else {
                            $send_email_device = [$delivery_device_email, $request->email];
                        }
                    } else {
                        if ($uplift_device_email != $delivery_device_email) {
                            $send_email_device = [$uplift_device_email, $delivery_device_email];
                        } else {
                            $send_email_device = [$delivery_device_email];
                        }
                    }

                    // } else {
                    //     $send_email_device = [$delivery_device_email];
                    // }

                    if ($request->status == 2) {
                        $company_id = CompanyAdmin::getCompanyUserCompany();

                        if (Companies::where('id', $company_id)->where('kika_direct', 1)->exists()) {
                            Move::where('id', $request->move_id)->update(['is_completed_icr_delivery' => 1]);
                        }
                        CompanyUserDetails::changeConditionalStatus($move->id);

                        DeliveryMoves::where('move_id', $request->move_id)->update(['lp_package' => $request->left_package, 'lp_carton' => $request->left_carton]);

                        if (TermsAndConditionsChecked::where(['move_id' => $request->move_id, 'move_status' => 1, 'move_type' => 5])->exists()) {
                            TermsAndConditionsChecked::where(['move_id' => $request->move_id, 'move_status' => 1, 'move_type' => 5])->delete();
                        }

                        $checked_condition = explode(',', $request->accept_condition);

                        for ($pointer = 13; $pointer <= 18; $pointer++) {
                            $termsandcondition = new TermsAndConditionsChecked();
                            $termsandcondition->move_id = $request->move_id;
                            $termsandcondition->move_type = $request->move_type;
                            $termsandcondition->tnc_id = $pointer;
                            $termsandcondition->is_checked = in_array($pointer, $checked_condition) ? 1 : 0;
                            $termsandcondition->move_status = 1;
                            $termsandcondition->save();
                        }

                        if ($existingPackageSignature = PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 5, 'status' => 1])->first()) {

                            if (\Storage::has('/clientsignature/' . $existingPackageSignature->customer_signature)) {
                                \Storage::delete('/clientsignature/' . $existingPackageSignature->customer_signature);
                            }
                            if (\Storage::has('/clientsignature/' . $existingPackageSignature->employee_signature)) {
                                \Storage::delete('/clientsignature/' . $existingPackageSignature->employee_signature);
                            }

                            PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 5, 'status' => 1])->delete();
                        }

                        $customer_signature = "CSTM_SIGN_DL_POST_MOVE_" . $move->reference_number . '.' . $request->customer_signature->extension();
                        $employee_signature = "EMP_SIGN_DL_POST_MOVE_" . $move->reference_number . '.' . $request->customer_signature->extension();

                        $filePath_customer = '/clientsignature/' . $customer_signature;
                        // dd($filePath_customer);
                        \Storage::put($filePath_customer, file_get_contents($request->customer_signature));
                        $filePath_employee = '/clientsignature/' . $employee_signature;
                        \Storage::put($filePath_employee, file_get_contents($request->employee_signature));
                        // $request->customer_signature->move(public_path('storage/image/company-admin/signature'), $customer_signature);
                        // $request->employee_signature->move(public_path('storage/image/company-admin/signature'), $employee_signature);

                        $packageSignature = new PackageSignature();
                        $packageSignature->move_id = $request->move_id;
                        $packageSignature->move_type = 5;
                        $packageSignature->client_name = $request->customer_name;
                        $packageSignature->client_signature = $customer_signature;
                        $packageSignature->employee_name = $request->employee_name;
                        $packageSignature->employee_signature = $employee_signature;
                        $packageSignature->status = 1;
                        $packageSignature->save();

                        $data['move'] = $move;
                        $data['move_mode'] = "Delivery Post";
                        $move_type = 5;
                        $comment_type = 1;
                        $data['link'] = 'company-admin/move/getdata/' . Crypt::encrypt($move->id) . '/' . $move_type . '/' . $comment_type;

                        $icrpdfData = GetIcrData::getIcrData($move->id, 5);

                        $bg_color = '#d5d5d5';
                        $pdf_margin_top = 40;
                        $div_html = '<div class="company-name text-black mb-10">' . $move->uplift->origin_agent . '</div>';
                        $company_admin = Companies::select('id', 'kika_id', 'icr_title_toggle', 'icr_title_image', 'title_bar_color_code')->where('kika_id', $icrpdfData['move']['origin_agent_kika_id'])->first();
                        if ($company_admin && $company_admin->icr_title_toggle == 0) {
                            if ($company_admin->title_bar_color_code != null) {
                                $bg_color = $company_admin->title_bar_color_code;
                            }

                            if ($company_admin->icr_title_image != null) {
                                $pdf_margin_top = 46;
                                $s3_base_url = config('filesystems.disks.s3.url');
                                $s3_image_path = $s3_base_url . 'icrtitle/';
                                $title_image_path = '';
                                if ($company_admin->icr_title_image != '') {
                                    $title_image_path = $s3_image_path . $company_admin->icr_title_image;
                                }
                                $div_html = '<div class="mb-10">
                                    <img style="max-width: 720px; max-height: 80px;" src="' . $title_image_path . '" alt="">
                                </div>';
                            }
                        }

                        $check_condition_image = DB::table('move_item_conditions')
                            ->join('move_condition_images', 'move_condition_images.move_condition_id', 'move_item_conditions.id')
                            ->select('move_condition_images.image')
                            ->where([
                                ['move_id', $request->move_id],
                                ['move_type', $request->move_type],
                            ]);
                        if ($check_condition_image->exists()) {
                            $data['move_type'] = 'Delivery';
                            $data['condition_image_pdf_link'] = 'company-admin/move/delivery/icrimage/pdf/' . Crypt::encrypt($move->id);
                        }

                        if ($icrpdfData['move']['item_count'] != null && $icrpdfData['move_type'] == 5) {
                            // $icr_pdf = PDF::loadView('theme.company-admin.pdf.nonkika-delivery-icr', $icrpdfData)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);

                            $mpdf = new Mpdf([
                                'fontDir' => str_replace('\\', '/', base_path('public/fonts/arial')),
                                'fontdata' => [
                                    'customarialfont' => [
                                        'R' => 'Arial.ttf',
                                        'B' => 'Arial-bold.ttf',
                                    ]
                                ],
                                'mode' => 'utf-8',
                                'format' => 'A4', // Paper size (A4, Letter, etc.)
                                'margin_left' => 12, // Left margin in millimeters
                                'margin_right' => 12, // Right margin in millimeters
                                'margin_top' => $pdf_margin_top, // Top margin in millimeters
                                'margin_bottom' => 16, // Bottom margin in millimeters
                                'margin_header' => 12, // Header margin
                                'margin_footer' => 12, // Footer margin
                            ]);

                            $mpdf->SetHTMLHeader(
                                '<div class="main-wrapper">
                                    ' . $div_html . '
                                        <div class="mb-10 report-delivery" style="background-color: ' . $bg_color . '">
                                            <table class="border-0">
                                                <tr>
                                                    <td class="text-black f-14">' . $move->contact->contact_name . ' : ' . $move->move_number . ' - Delivery Outturn Report</td>
                                                    <td class="text-black f-14" style="text-align: right">Page {PAGENO} of {nbpg}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>'
                            );
                            $mpdf->AddPage('P'); // You can specify 'A4', 'Letter', or custom dimensions
                            $mpdf->WriteHTML(view('theme.company-admin.pdf.nonkika-delivery-icr', $icrpdfData)->render());

                            if ($move->delivery->sub_contactor_email != null) {
                                if ($request->email) {
                                    $customer = [$request->email, $move->contact->email, $move->controlling_agent_email, $delivery_device_email, $move->delivery->delivery_agent_email, $move->delivery->sub_contactor_email];
                                } else {
                                    $customer = [$move->contact->email, $move->controlling_agent_email, $delivery_device_email, $move->delivery->delivery_agent_email, $move->delivery->sub_contactor_email];
                                }
                            } else {
                                if ($request->email) {
                                    $customer = [$request->email, $move->contact->email, $move->controlling_agent_email, $delivery_device_email, $move->delivery->delivery_agent_email];
                                } else {
                                    $customer = [$move->contact->email, $move->controlling_agent_email, $delivery_device_email, $move->delivery->delivery_agent_email];
                                }
                            }

                            // $customer = [$request->email,$move->contact->email,$move->controlling_agent_email,$origin_agent_email,$move->delivery->delivery_agent_email];
                            if ($icrpdfData['move']['item_count'] != null && $icrpdfData['move_type'] == 5) {
                                $fileName = $move->contact->contact_name . ' - ' . $move->move_number . ' - Delivery Outturn Report.pdf';
                                $sub_title = "Delivery Outturn Report";
                            } else {
                                $fileName = $move->contact->contact_name . ' - ' . $move->move_number . ' - Delivery.pdf';
                                $sub_title = "Delivery";
                            }
                            $comment_image = MoveComments::where(['move_id' => $move->id, 'move_type' => $move_type, 'move_status' => $comment_type])->with('image')->first();
                            if ($comment_image && $comment_image->image->isNotEmpty()) {
                                $data['image_pdf_link'] = 'company-admin/move/post-move-comment-image/' . Crypt::encrypt($move->id) . '/' . $move_type;
                            }

                            //Sending email to Uplift Device & Delivery Device email
                            // Mail::send('mails.customer-icr-pdf', $data, function ($message) use ($mpdf, $move, $send_email_device, $fileName, $sub_title) {
                            //     $message->to(array_unique($send_email_device))
                            //         ->subject($move->conct->contact_name . " : " . $move->move_number . " - " . $sub_title)
                            //         ->attachData($mpdf->Output(null, 'S'), $fileName);
                            // });

                            Mail::send('mails.customer-icr-pdf', $data, function ($message) use ($mpdf, $move, $send_email_device, $fileName, $sub_title) {
                                $message->to(array_unique($send_email_device))
                                    ->subject($move->contact->contact_name . " : " . $move->move_number . " - " . $sub_title)
                                    ->attachData($mpdf->Output(null, 'S'), $fileName);
                            });

                        } else {
                            // $icr_pdf = PDF::loadView('theme.company-admin.pdf.icr', $icrpdfData)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);

                            $mpdf = new Mpdf([
                                'fontDir' => str_replace('\\', '/', base_path('public/fonts/arial')),
                                'fontdata' => [
                                    'customarialfont' => [
                                        'R' => 'Arial.ttf',
                                        'B' => 'Arial-bold.ttf',
                                    ]
                                ],
                                'mode' => 'utf-8',
                                'format' => 'A4',
                                'margin_left' => 12,
                                'margin_right' => 12,
                                'margin_top' => $pdf_margin_top,
                                'margin_bottom' => 16,
                                'margin_header' => 12,
                                'margin_footer' => 12,
                            ]);
                            $mpdf->SetHTMLHeader(
                                '<div class="main-wrapper">
                                        ' . $div_html . '
                                        <div class="mb-10 report-delivery" style="background-color: ' . $bg_color . '">
                                            <table class="border-0">
                                                <tr>
                                                    <td class="text-black f-14">Inventory And Condition Report - ' . $move->contact->contact_name . ' : ' . $move->move_number . ' - ' . ucfirst($move_type == 1 ? "Uplift" : "Delivery") . '</td>
                                                    <td class="text-black f-14" style="text-align: right">Page {PAGENO} of {nbpg}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>'
                            );
                            $mpdf->AddPage('P');
                            $html = view('theme.company-admin.pdf.icr', $icrpdfData)->render();
                            $mpdf->WriteHTML($html);

                            if ($move->delivery->sub_contactor_email != null) {
                                if ($request->email) {
                                    $customer = [$request->email, $move->contact->email, $move->controlling_agent_email, $delivery_device_email, $move->delivery->delivery_agent_email, $move->delivery->sub_contactor_email];
                                } else {
                                    $customer = [$move->contact->email, $move->controlling_agent_email, $delivery_device_email, $move->delivery->delivery_agent_email, $move->delivery->sub_contactor_email];
                                }
                            } else {
                                if ($request->email) {
                                    $customer = [$request->email, $move->contact->email, $move->controlling_agent_email, $delivery_device_email, $move->delivery->delivery_agent_email];
                                } else {
                                    $customer = [$move->contact->email, $move->controlling_agent_email, $delivery_device_email, $move->delivery->delivery_agent_email];
                                }
                            }
                            // $customer = [$request->email,$move->contact->email,$move->controlling_agent_email,$origin_agent_email,$move->delivery->delivery_agent_email];

                            // $fileName = $move->contact->contact_name.' - '.$move->move_number.' - Delivery.pdf';
                            if ($icrpdfData['move']['item_count'] != null && $icrpdfData['move_type'] == 5) {
                                $fileName = $move->contact->contact_name . ' - ' . $move->move_number . ' - Delivery Outturn Report.pdf';
                                $sub_title = "Delivery Outturn Report";
                            } else {
                                $fileName = $move->contact->contact_name . ' - ' . $move->move_number . ' - Delivery.pdf';
                                $sub_title = "Delivery";
                            }
                            $comment_image = MoveComments::where(['move_id' => $move->id, 'move_type' => $move_type, 'move_status' => $comment_type])->with('image')->first();
                            if ($comment_image && $comment_image->image->isNotEmpty()) {
                                $data['image_pdf_link'] = 'company-admin/move/post-move-comment-image/' . Crypt::encrypt($move->id) . '/' . $move_type;
                            }

                            // array_unique($send_email_device)
                            $email = "clearsky5290@gmail.com";

                            // Sending email to Uplift Device & Delivery Device email
                            // Mail::send('mails.customer-icr-pdf', $data, function ($message) use ($mpdf, $move, $email, $fileName, $sub_title) {
                            //     $message->to($email)
                            //         ->subject($move->conct->contact_name . " : " . $move->move_number . " - " . $sub_title)
                            //         ->attachData($mpdf->Output(null, 'S'), $fileName);
                            // });
                            Mail::send('mails.customer-icr-pdf', $data, function ($message) use ($mpdf, $move, $send_email_device, $fileName, $sub_title) {
                                $message->to(array_unique($send_email_device))
                                    ->subject($move->contact->contact_name . " : " . $move->move_number . " - " . $sub_title)
                                    ->attachData($mpdf->Output(null, 'S'), $fileName);
                            });

                        }
                        Move::where('id', $request->move_id)->update(['type_id' => 5]);
                    } else {
                        Move::where('id', $request->move_id)->update(['type_id' => 5]);
                    }

                    return $this->msgResponse("Delivery status changed successfully");

                default:
                    return $this->apiError("Invalid post data!");
            }
        } else {
            return $this->apiError("No move found for given move number.");
        }
    }

    public function changeoverflowStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'move_id' => 'required',
            'is_overflow' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }

        $update_overflow_status = Move::where('id', $request->move_id)->update(['is_overflow' => $request->is_overflow]);
        if ($update_overflow_status) {
            return $this->msgResponse("Overflow status changed successfully");
        } else {
            return $this->apiError("Opps,Something went wrong");
        }

    }

    public function preMoveCheckTest(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'move_id' => 'required',
            'move_type' => 'required|gte:1|lte:5',
            'signature_status' => 'required',
            'customer_name' => Rule::requiredIf(($request->move_type == '1' && $request->signature_status == '1') || ($request->move_type == '5' && $request->signature_status == '1')),
            'customer_signature' => [Rule::requiredIf(($request->move_type == '1' && $request->signature_status == '1') || ($request->move_type == '5' && $request->signature_status == '1')), 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'employee_name' => Rule::requiredIf(($request->move_type == '1' && $request->signature_status == '1') || ($request->move_type == '4' && $request->signature_status == '1') || ($request->move_type == '5' && $request->signature_status == '1')),
            'employee_signature' => [Rule::requiredIf(($request->move_type == '1' && $request->signature_status == '1') || ($request->move_type == '5' && $request->signature_status == '1')), 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'movement' => Rule::requiredIf($request->move_type == '4'),
            'skip' => 'in:0,1',
            'device' => 'required|string|max:255'
        ]);


        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }


        switch ($request->move_type) {
            case '1':
                $company_id = CompanyAdmin::getCompanyUserCompany();
                // if ($move = Move::where('company_id',$company_id)->where('id',$request->move_id)->first()) {
                if ($move = Move::where('id', $request->move_id)->first()) {

                    if (TermsAndConditionsChecked::where(['move_id' => $request->move_id, 'move_status' => 0, 'move_type' => 1])->exists()) {

                        return $this->msgResponse("You have already checked uplift conditions");

                    } else {
                        $checked_condition = explode(',', $request->accept_condition);
                        $tnc_id = [1, 2, 3, 19, 4];
                        foreach ($tnc_id as $key => $tnc_value) {
                            // for ($pointer = 1; $pointer <= 4 ; $pointer++) {
                            $termsandcondition = new TermsAndConditionsChecked();
                            $termsandcondition->move_id = $request->move_id;
                            $termsandcondition->move_type = $request->move_type;
                            $termsandcondition->tnc_id = $tnc_value;

                            in_array($tnc_value, $checked_condition) ? $termsandcondition->is_checked = 1 : $termsandcondition->is_checked = 0;

                            $termsandcondition->move_status = 0;
                            $termsandcondition->save();

                        }
                    }

                    if (!PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 1, 'status' => 0])->exists()) {
                        $customer_signature = "";
                        $employee_signature = "";
                        if ($request->customer_signature && $request->employee_signature) {
                            $customer_signature = "CSTM_SIGN_UPL_PRE_MOVE_" . $move->reference_number . '.' . $request->customer_signature->extension();
                            $employee_signature = "EMP_SIGN_UPL_PRE_MOVE_" . $move->reference_number . '.' . $request->employee_signature->extension();

                            // $employee_signature = "EMP_SIGN_UPL_PRE_MOVE_".$move->reference_number.'.'.$request->customer_signature->extension();

                            $filePath_customer = '/clientsignature/' . $customer_signature;
                            // dd($filePath_customer);
                            \Storage::put($filePath_customer, file_get_contents($request->customer_signature));
                            $filePath_employee = '/clientsignature/' . $employee_signature;
                            \Storage::put($filePath_employee, file_get_contents($request->employee_signature));

                            // $request->customer_signature->move(public_path('storage/image/company-admin/signature'), $customer_signature);
                            // $request->employee_signature->move(public_path('storage/image/company-admin/signature'), $employee_signature);
                        }
                        $packageSignature = new PackageSignature();
                        $packageSignature->move_id = $request->move_id;
                        $packageSignature->move_type = 1;
                        $packageSignature->client_name = $request->customer_name ? $request->customer_name : null;
                        $packageSignature->client_signature = $customer_signature ? $customer_signature : null;
                        $packageSignature->employee_name = $request->employee_name ? $request->employee_name : null;
                        $packageSignature->employee_signature = $employee_signature ? $employee_signature : null;
                        $packageSignature->status = 0;
                        $packageSignature->save();

                    }

                    $move->is_tnc_checked = 1;
                    $move->save();

                    $data['move'] = $move;
                    $data['move_mode'] = "Uplift Pre";

                    $move_type = 1;
                    $comment_type = 0;
                    $data['link'] = 'company-admin/move/getdata/' . Crypt::encrypt($move->id) . '/' . $move_type . '/' . $comment_type;


                    $company_email = Companies::where('id', $move->company_id)->value('email');

                    $uplift_device_email = User::where('username', $request->device)->value('email');

                    //email address that send Uplift Pre Comments
                    $send_email_address = "";

                    if ($uplift_device_email != "" && $uplift_device_email != null) {
                        $send_email_address = $uplift_device_email;

                    } else {
                        $send_email_address = $company_email;
                    }

                    UpliftMoves::where('move_id', $request->move_id)->update(['device_email' => $send_email_address]);

                    if ($move->uplift->sub_contactor_email != null) {
                        $receiver = [$move->controlling_agent_email, $send_email_address, $move->uplift->sub_contactor_email];
                    } else {
                        $receiver = [$move->controlling_agent_email, $send_email_address];
                    }

                    // while skip = 0 - Submit & Begin ICR button API called, and while skip = 1 - Skip & Begin ICR button API called
                    $skip = ($request->has('skip') && $request->filled('skip') ? $request->input('skip') : 1);
                    if ($request->skip == 0) {
                        // $receiver_email = $move->uplift->origin_agent_email;
                        $receiver_email = $send_email_address;
                        if ($receiver_email) {


                        }
                    }
                } else {
                    return $this->apiError("No move found for given move ID!");
                }
                //change status of uplift pending to in-progress
                UpliftMoves::where('move_id', $request->move_id)->update(['status' => 1]);

                return $this->msgResponse("Uplift pre-checked successfully");

            case '2':
                return $this->apiError("You cant perform this operation for Transit moves!");

            case '4':
                if ($move = Move::where('id', $request->move_id)->first()) {

                    if (!PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 4, 'status' => 0])->exists()) {

                        $packageSignature = new PackageSignature();
                        $packageSignature->move_id = $request->move_id;
                        $packageSignature->move_type = 4;
                        $packageSignature->client_name = null;
                        $packageSignature->client_signature = null;
                        $packageSignature->employee_name = $request->employee_name;
                        $packageSignature->employee_signature = null;
                        $packageSignature->status = 0;
                        $packageSignature->save();

                        TransloadMoves::where('move_id', $request->move_id)->update(['movement' => $request->movement]);

                        $move->is_transload_tnc_checked = 1;
                        $move->save();

                    }
                    return $this->msgResponse("Transload pre-checked successfully");
                } else {
                    return $this->apiError("No move found for given move ID!");
                }

            case '5':
                if ($move = Move::where('id', $request->move_id)->first()) {
                    if (TermsAndConditionsChecked::where(['move_id' => $request->move_id, 'move_status' => 0, 'move_type' => 5])->exists()) {

                        return $this->msgResponse("You have already checked delivery conditions");

                    } else {
                        $checked_condition = explode(',', $request->accept_condition);
                        for ($pointer = 9; $pointer <= 13; $pointer++) {
                            $termsandcondition = new TermsAndConditionsChecked();
                            $termsandcondition->move_id = $request->move_id;
                            $termsandcondition->move_type = $request->move_type;
                            $termsandcondition->tnc_id = $pointer;

                            in_array($pointer, $checked_condition) ? $termsandcondition->is_checked = 1 : $termsandcondition->is_checked = 0;

                            $termsandcondition->move_status = 0;
                            $termsandcondition->save();

                        }
                    }


                    if (!PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 5, 'status' => 0])->exists()) {

                        $customer_signature = "";
                        $employee_signature = "";
                        if ($request->customer_signature && $request->employee_signature) {
                            $customer_signature = "CSTM_SIGN_DL_PRE_MOVE_" . $move->move_number . '.' . $request->customer_signature->extension();
                            $employee_signature = "EMP_SIGN_DL_PRE_MOVE_" . $move->move_number . '.' . $request->employee_signature->extension();

                            // $employee_signature = "EMP_SIGN_DL_PRE_MOVE_".$move->move_number.'.'.$request->customer_signature->extension();

                            $filePath_customer = '/clientsignature/' . $customer_signature;
                            // dd($filePath_customer);
                            \Storage::put($filePath_customer, file_get_contents($request->customer_signature));
                            $filePath_employee = '/clientsignature/' . $employee_signature;
                            \Storage::put($filePath_employee, file_get_contents($request->employee_signature));

                            // $request->customer_signature->move(public_path('storage/image/company-admin/signature'), $customer_signature);
                            // $request->employee_signature->move(public_path('storage/image/company-admin/signature'), $employee_signature);

                            $packageSignature = new PackageSignature();
                            $packageSignature->move_id = $request->move_id;
                            $packageSignature->move_type = 5;
                            $packageSignature->client_name = $request->customer_name ? $request->customer_name : null;
                            $packageSignature->client_signature = $customer_signature ? $customer_signature : null;
                            $packageSignature->employee_name = $request->employee_name ? $request->employee_name : null;
                            $packageSignature->employee_signature = $employee_signature ? $employee_signature : null;
                            $packageSignature->status = 0;
                            $packageSignature->save();
                        }
                    }

                    $move->is_dl_tnc_checked = 1;
                    $move->save();

                    $data['move'] = $move;
                    $data['move_mode'] = "Delivery Pre";

                    $move_type = 5;
                    $comment_type = 0;
                    $data['link'] = 'company-admin/move/getdata/' . Crypt::encrypt($move->id) . '/' . $move_type . '/' . $comment_type;

                    if ($move->delivery->sub_contactor_email != null) {
                        $receiver = [$move->controlling_agent_email, $move->delivery->delivery_agent_email, $move->delivery->sub_contactor_email];
                    } else {
                        $receiver = [$move->controlling_agent_email, $move->delivery->delivery_agent_email];

                    }

                    //email address that send Delivery Pre comments
                    $send_email_address = "";

                    $company_email = Companies::where('id', $move->company_id)->value('email');
                    $delivery_device_email = User::where('username', $request->device)->value('email');

                    if ($delivery_device_email != null && $delivery_device_email != "") {
                        $send_email_address = $delivery_device_email;
                    } else {
                        $send_email_address = $company_email;
                    }

                    DeliveryMoves::where('move_id', $request->move_id)->update(['device_email' => $send_email_address]);

                    // Mail::send('mails.move-notification', $data, function($message)use($move, $receiver) {
                    // 	$message->to($receiver)
                    // 	->subject($move->contact->contact_name." - ".$move->move_number." : Delivery Pre Move Comment");
                    // });

                    // $mail_reciever = $move->uplift->origin_agent_email;

                    // while skip = 0 - Submit & Begin ICR button API called, and while skip = 1 = Skip & Begin ICR button API called
                    $skip = ($request->has('skip') && $request->filled('skip') ? $request->input('skip') : 1);
                    if ($skip == 0) {
                        $destination_agant_email = CompanyAgent::where('id', $move->destination_agent)->value('email');
                        if (!empty($destination_agant_email)) {
                            // $comment_image = MoveComments::where([
                            //     'move_id' => $move->id,
                            //     'move_type' => $move_type,
                            //     'move_status' => $comment_type
                            // ])
                            //     ->with('image')
                            //     ->first();

                            // if ($comment_image && $comment_image->image->isNotEmpty()) {
                            //     $data['image_pdf_link'] = 'company-admin/move/pre-move-comment-image/' . Crypt::encrypt($move->id) . '/' . $move_type;
                            // }
                            // $data['move_type'] = $move_type;
                            // // $data['pdf_link'] = 'company-admin/move/pre-move-comment/' . Crypt::encrypt($move->id) . '/' . $move_type;

                            // $pdfData = GetIcrData::getPreMoveComment($move->id, $move_type);
                            // $pdf = PDF::loadView('api-pdf.pre-move-comment', $pdfData)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);
                            // $title = $move->contact->contact_name . " - $move->move_number : Delivery Pre Move Comments";
                            // $pdfPath = $title . '.pdf';
                            // /////////in case local Server/////////////

                            // Mail::send('mails.pre-move-comment', $data, function ($message) use ($move, $title, $send_email_address, $pdfPath, $pdf) {
                            //     $message->to($send_email_address)
                            //         ->subject($title)
                            //         ->attachData($pdf->output(), $pdfPath);
                            // });

                        }
                    }

                } else {
                    return $this->apiError("No move found for given move ID!");
                }
                //change status of delivery pending to in-progress
                DeliveryMoves::where('move_id', $request->move_id)->update(['status' => 1]);
                return $this->msgResponse("Delivery pre-checked successfully");

            default:
                return $this->apiError("Invalid post data!");
        }
    }

    public function preMoveCheck(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'move_id' => 'required',
            'move_type' => 'required|gte:1|lte:5',
            'signature_status' => 'required',
            'customer_name' => Rule::requiredIf(($request->move_type == '1' && $request->signature_status == '1') || ($request->move_type == '5' && $request->signature_status == '1')),
            'customer_signature' => [Rule::requiredIf(($request->move_type == '1' && $request->signature_status == '1') || ($request->move_type == '5' && $request->signature_status == '1')), 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'employee_name' => Rule::requiredIf(($request->move_type == '1' && $request->signature_status == '1') || ($request->move_type == '4' && $request->signature_status == '1') || ($request->move_type == '5' && $request->signature_status == '1')),
            'employee_signature' => [Rule::requiredIf(($request->move_type == '1' && $request->signature_status == '1') || ($request->move_type == '5' && $request->signature_status == '1')), 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'movement' => Rule::requiredIf($request->move_type == '4'),
            'skip' => 'in:0,1',
            'device' => 'required|string|max:255'
        ]);


        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }


        switch ($request->move_type) {
            case '1':
                $company_id = CompanyAdmin::getCompanyUserCompany();
                // if ($move = Move::where('company_id',$company_id)->where('id',$request->move_id)->first()) {
                if ($move = Move::where('id', $request->move_id)->first()) {

                    if (TermsAndConditionsChecked::where(['move_id' => $request->move_id, 'move_status' => 0, 'move_type' => 1])->exists()) {

                        return $this->msgResponse("You have already checked uplift conditions");

                    } else {
                        $checked_condition = explode(',', $request->accept_condition);
                        $tnc_id = [1, 2, 3, 19, 4];
                        foreach ($tnc_id as $key => $tnc_value) {
                            // for ($pointer = 1; $pointer <= 4 ; $pointer++) {
                            $termsandcondition = new TermsAndConditionsChecked();
                            $termsandcondition->move_id = $request->move_id;
                            $termsandcondition->move_type = $request->move_type;
                            $termsandcondition->tnc_id = $tnc_value;

                            in_array($tnc_value, $checked_condition) ? $termsandcondition->is_checked = 1 : $termsandcondition->is_checked = 0;

                            $termsandcondition->move_status = 0;
                            $termsandcondition->save();

                        }
                    }

                    if (!PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 1, 'status' => 0])->exists()) {
                        $customer_signature = "";
                        $employee_signature = "";
                        if ($request->customer_signature && $request->employee_signature) {
                            $customer_signature = "CSTM_SIGN_UPL_PRE_MOVE_" . $move->reference_number . '.' . $request->customer_signature->extension();
                            $employee_signature = "EMP_SIGN_UPL_PRE_MOVE_" . $move->reference_number . '.' . $request->employee_signature->extension();

                            // $employee_signature = "EMP_SIGN_UPL_PRE_MOVE_".$move->reference_number.'.'.$request->customer_signature->extension();

                            $filePath_customer = '/clientsignature/' . $customer_signature;
                            // dd($filePath_customer);
                            \Storage::put($filePath_customer, file_get_contents($request->customer_signature));
                            $filePath_employee = '/clientsignature/' . $employee_signature;
                            \Storage::put($filePath_employee, file_get_contents($request->employee_signature));

                            // $request->customer_signature->move(public_path('storage/image/company-admin/signature'), $customer_signature);
                            // $request->employee_signature->move(public_path('storage/image/company-admin/signature'), $employee_signature);
                        }
                        $packageSignature = new PackageSignature();
                        $packageSignature->move_id = $request->move_id;
                        $packageSignature->move_type = 1;
                        $packageSignature->client_name = $request->customer_name ? $request->customer_name : null;
                        $packageSignature->client_signature = $customer_signature ? $customer_signature : null;
                        $packageSignature->employee_name = $request->employee_name ? $request->employee_name : null;
                        $packageSignature->employee_signature = $employee_signature ? $employee_signature : null;
                        $packageSignature->status = 0;
                        $packageSignature->save();

                    }

                    $move->is_tnc_checked = 1;
                    $move->save();

                    $data['move'] = $move;
                    $data['move_mode'] = "Uplift Pre";

                    $move_type = 1;
                    $comment_type = 0;
                    $data['link'] = 'company-admin/move/getdata/' . Crypt::encrypt($move->id) . '/' . $move_type . '/' . $comment_type;


                    $company_email = Companies::where('id', $move->company_id)->value('email');

                    $uplift_device_email = User::where('username', $request->device)->value('email');

                    //email address that send Uplift Pre Comments
                    $send_email_address = "";

                    if ($uplift_device_email != "" && $uplift_device_email != null) {
                        $send_email_address = $uplift_device_email;

                    } else {
                        $send_email_address = $company_email;
                    }

                    UpliftMoves::where('move_id', $request->move_id)->update(['device_email' => $send_email_address]);

                    if ($move->uplift->sub_contactor_email != null) {
                        $receiver = [$move->controlling_agent_email, $send_email_address, $move->uplift->sub_contactor_email];
                    } else {
                        $receiver = [$move->controlling_agent_email, $send_email_address];
                    }

                    // while skip = 0 - Submit & Begin ICR button API called, and while skip = 1 - Skip & Begin ICR button API called
                    $skip = ($request->has('skip') && $request->filled('skip') ? $request->input('skip') : 1);
                    if ($request->skip == 0) {
                        // $receiver_email = $move->uplift->origin_agent_email;
                        $receiver_email = $send_email_address;
                        if ($receiver_email) {

                            $comment_image = MoveComments::where([
                                'move_id' => $move->id,
                                'move_type' => $move_type,
                                'move_status' => $comment_type
                            ])
                                ->with('image')
                                ->first();
                            if ($comment_image && $comment_image->image->isNotEmpty()) {
                                $data['image_pdf_link'] = 'company-admin/move/pre-move-comment-image/' . Crypt::encrypt($move->id) . '/' . $move_type;
                            }
                            $data['move_type'] = $move_type;

                            // $data['pdf_link'] = 'company-admin/move/pre-move-comment/' . Crypt::encrypt($move->id) . '/' . $move_type;

                            $pdfData = GetIcrData::getPreMoveComment($move->id, $move_type);
                            $pdf = PDF::loadView('api-pdf.pre-move-comment', $pdfData)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);
                            $title = $move->contact->contact_name . " - $move->move_number : Uplift Pre Move Comments";
                            $pdfPath = $title . '.pdf';
                            /////////in case local Server/////////////
                            Mail::send('mails.pre-move-comment', $data, function ($message) use ($move, $title, $send_email_address, $pdf, $pdfPath) {
                                $message->to($send_email_address)
                                    ->subject($title)
                                    ->attachData($pdf->output(), $pdfPath);
                            });
                        }
                    }
                    // Mail::send('mails.move-notification', $data, function($message)use($move, $receiver) {
                    // 	$message->to($receiver)
                    // 			->subject($move->contact->contact_name." - ".$move->move_number." : Uplift Pre Move Comment");
                    // });
                } else {
                    return $this->apiError("No move found for given move ID!");
                }
                //change status of uplift pending to in-progress
                UpliftMoves::where('move_id', $request->move_id)->update(['status' => 1]);

                return $this->msgResponse("Uplift pre-checked successfully");

            case '2':
                return $this->apiError("You cant perform this operation for Transit moves!");

            case '4':
                if ($move = Move::where('id', $request->move_id)->first()) {

                    if (!PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 4, 'status' => 0])->exists()) {

                        $packageSignature = new PackageSignature();
                        $packageSignature->move_id = $request->move_id;
                        $packageSignature->move_type = 4;
                        $packageSignature->client_name = null;
                        $packageSignature->client_signature = null;
                        $packageSignature->employee_name = $request->employee_name;
                        $packageSignature->employee_signature = null;
                        $packageSignature->status = 0;
                        $packageSignature->save();

                        TransloadMoves::where('move_id', $request->move_id)->update(['movement' => $request->movement]);

                        $move->is_transload_tnc_checked = 1;
                        $move->save();

                    }
                    return $this->msgResponse("Transload pre-checked successfully");
                } else {
                    return $this->apiError("No move found for given move ID!");
                }

            case '5':
                if ($move = Move::where('id', $request->move_id)->first()) {
                    if (TermsAndConditionsChecked::where(['move_id' => $request->move_id, 'move_status' => 0, 'move_type' => 5])->exists()) {

                        return $this->msgResponse("You have already checked delivery conditions");

                    } else {
                        $checked_condition = explode(',', $request->accept_condition);
                        for ($pointer = 9; $pointer <= 13; $pointer++) {
                            $termsandcondition = new TermsAndConditionsChecked();
                            $termsandcondition->move_id = $request->move_id;
                            $termsandcondition->move_type = $request->move_type;
                            $termsandcondition->tnc_id = $pointer;

                            in_array($pointer, $checked_condition) ? $termsandcondition->is_checked = 1 : $termsandcondition->is_checked = 0;

                            $termsandcondition->move_status = 0;
                            $termsandcondition->save();

                        }
                    }


                    if (!PackageSignature::where(['move_id' => $request->move_id, 'move_type' => 5, 'status' => 0])->exists()) {

                        $customer_signature = "";
                        $employee_signature = "";
                        if ($request->customer_signature && $request->employee_signature) {
                            $customer_signature = "CSTM_SIGN_DL_PRE_MOVE_" . $move->move_number . '.' . $request->customer_signature->extension();
                            $employee_signature = "EMP_SIGN_DL_PRE_MOVE_" . $move->move_number . '.' . $request->employee_signature->extension();

                            // $employee_signature = "EMP_SIGN_DL_PRE_MOVE_".$move->move_number.'.'.$request->customer_signature->extension();

                            $filePath_customer = '/clientsignature/' . $customer_signature;
                            // dd($filePath_customer);
                            \Storage::put($filePath_customer, file_get_contents($request->customer_signature));
                            $filePath_employee = '/clientsignature/' . $employee_signature;
                            \Storage::put($filePath_employee, file_get_contents($request->employee_signature));

                            // $request->customer_signature->move(public_path('storage/image/company-admin/signature'), $customer_signature);
                            // $request->employee_signature->move(public_path('storage/image/company-admin/signature'), $employee_signature);

                            $packageSignature = new PackageSignature();
                            $packageSignature->move_id = $request->move_id;
                            $packageSignature->move_type = 5;
                            $packageSignature->client_name = $request->customer_name ? $request->customer_name : null;
                            $packageSignature->client_signature = $customer_signature ? $customer_signature : null;
                            $packageSignature->employee_name = $request->employee_name ? $request->employee_name : null;
                            $packageSignature->employee_signature = $employee_signature ? $employee_signature : null;
                            $packageSignature->status = 0;
                            $packageSignature->save();
                        }
                    }

                    $move->is_dl_tnc_checked = 1;
                    $move->save();

                    $data['move'] = $move;
                    $data['move_mode'] = "Delivery Pre";

                    $move_type = 5;
                    $comment_type = 0;
                    $data['link'] = 'company-admin/move/getdata/' . Crypt::encrypt($move->id) . '/' . $move_type . '/' . $comment_type;

                    if ($move->delivery->sub_contactor_email != null) {
                        $receiver = [$move->controlling_agent_email, $move->delivery->delivery_agent_email, $move->delivery->sub_contactor_email];
                    } else {
                        $receiver = [$move->controlling_agent_email, $move->delivery->delivery_agent_email];

                    }

                    //email address that send Delivery Pre comments
                    $send_email_address = "";

                    $company_email = Companies::where('id', $move->company_id)->value('email');
                    $delivery_device_email = User::where('username', $request->device)->value('email');

                    if ($delivery_device_email != null && $delivery_device_email != "") {
                        $send_email_address = $delivery_device_email;
                    } else {
                        $send_email_address = $company_email;
                    }

                    DeliveryMoves::where('move_id', $request->move_id)->update(['device_email' => $send_email_address]);

                    // Mail::send('mails.move-notification', $data, function($message)use($move, $receiver) {
                    // 	$message->to($receiver)
                    // 	->subject($move->contact->contact_name." - ".$move->move_number." : Delivery Pre Move Comment");
                    // });

                    // $mail_reciever = $move->uplift->origin_agent_email;

                    // while skip = 0 - Submit & Begin ICR button API called, and while skip = 1 = Skip & Begin ICR button API called
                    $skip = ($request->has('skip') && $request->filled('skip') ? $request->input('skip') : 1);
                    if ($skip == 0) {
                        $destination_agant_email = CompanyAgent::where('id', $move->destination_agent)->value('email');
                        if (!empty($destination_agant_email)) {
                            $comment_image = MoveComments::where([
                                'move_id' => $move->id,
                                'move_type' => $move_type,
                                'move_status' => $comment_type
                            ])
                                ->with('image')
                                ->first();

                            if ($comment_image && $comment_image->image->isNotEmpty()) {
                                $data['image_pdf_link'] = 'company-admin/move/pre-move-comment-image/' . Crypt::encrypt($move->id) . '/' . $move_type;
                            }
                            $data['move_type'] = $move_type;
                            // $data['pdf_link'] = 'company-admin/move/pre-move-comment/' . Crypt::encrypt($move->id) . '/' . $move_type;

                            $pdfData = GetIcrData::getPreMoveComment($move->id, $move_type);
                            $pdf = PDF::loadView('api-pdf.pre-move-comment', $pdfData)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);
                            $title = $move->contact->contact_name . " - $move->move_number : Delivery Pre Move Comments";
                            $pdfPath = $title . '.pdf';
                            /////////in case local Server/////////////

                            Mail::send('mails.pre-move-comment', $data, function ($message) use ($move, $title, $send_email_address, $pdfPath, $pdf) {
                                $message->to($send_email_address)
                                    ->subject($title)
                                    ->attachData($pdf->output(), $pdfPath);
                            });

                        }
                    }

                } else {
                    return $this->apiError("No move found for given move ID!");
                }
                //change status of delivery pending to in-progress
                DeliveryMoves::where('move_id', $request->move_id)->update(['status' => 1]);
                return $this->msgResponse("Delivery pre-checked successfully");

            default:
                return $this->apiError("Invalid post data!");
        }
    }

    public function manageComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'move_id' => 'required',
            'comment' => 'sometimes|nullable|min:3',
            'pre_comment' => 'sometimes|nullable|min:3',
            'move_status' => 'required|boolean',
            'move_type' => 'required|gte:1|lte:5',
        ]);


        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }

        if (Move::where('id', $request->move_id)->exists()) {

            $existing_comment = MoveComments::where(['move_id' => $request->move_id, 'move_status' => $request->move_status, 'move_type' => $request->move_type])->first();

            if ($existing_comment) {

                $existing_comment->comment = $request->comment;
                $existing_comment->pre_comment = $request->pre_comment;
                $existing_comment->save();

                if ($request->images) {
                    CommentImages::where('comment_id', $existing_comment->id)->delete();
                    foreach ($request->images as $image) {

                        $comment_image = new CommentImages();
                        $comment_image->comment_id = $existing_comment->id;
                        $comment_image->image = $image['image'];
                        $comment_image->save();
                    }
                }

                $this->data = MoveComments::where('id', $existing_comment->id)->with('image')->first();
                ;

                return $this->sendResponse($this->data, "Comment updated successfully!");
            } else {

                $move_comment = new MoveComments();
                $move_comment->move_id = $request->move_id;
                $move_comment->move_type = $request->move_type;
                $move_comment->comment = $request->comment;
                $move_comment->pre_comment = $request->pre_comment;
                $move_comment->move_status = $request->move_status;

                $move_comment->save();

                if ($request->images) {
                    foreach ($request->images as $image) {
                        $comment_image = new CommentImages();
                        $comment_image->comment_id = $move_comment->id;
                        $comment_image->image = $image['image'];
                        $comment_image->save();
                    }
                }

                $this->data = MoveComments::where('id', $move_comment->id)->with('image')->first();

                return $this->sendResponse($this->data, "Comment added successfully!");
            }
        } else {
            return $this->apiError("No move found for given move id!");
        }
    }

    public function createMove(Request $request)
    {
        $companyId = CompanyAdmin::getCompanyUserCompany();
        $companyDetails = Companies::where('id', $companyId)->first();

        if (Move::where('company_id', $companyDetails->id)->where('move_number', '=', $request->move_number)->exists()) {
            $validator = Validator::make($request->all(), [
                'move_number' => 'required|unique:moves',
                'name' => 'required|string|min:2',
                //'contact_number' 	=> 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
                'volume' => 'required',
                'uplift_address' => 'required|string|min:5',
                'delivery_address' => 'required|string|min:5',
                'is_assign' => 'required'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'move_number' => 'required',
                'name' => 'required|string|min:2',
                //'contact_number' 	=> 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
                'volume' => 'required',
                'uplift_address' => 'required|string|min:5',
                'delivery_address' => 'required|string|min:5',
                'is_assign' => 'required'
            ]);
        }

        if (isset($request->contact_number)) {
            $validator->sometimes('contact_number', 'regex:/^([0-9\s\-\+\(\)]*)$/', function ($request) {
                if ($request->contact_number != null) {
                    return true;
                }
            });
            $validator->sometimes('contact_number', 'min:6', function ($request) {
                if ($request->contact_number != null) {
                    return true;
                }
            });
        }

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }


        $agent = CompanyAgent::where('kika_id', $companyDetails->kika_id)->first();

        $move = new Move();
        $move->company_id = $companyId;
        $move->move_number = $request->move_number;
        $move->reference_number = $companyDetails->kika_id . ' - ' . $request->move_number;
        $move->controlling_agent_kika_id = "self";
        $move->controlling_agent = $companyDetails->name;
        $move->controlling_agent_email = $companyDetails->email;
        $move->origin_agent = $agent->id;
        $move->destination_agent = $agent->id;
        $move->is_origin_agent_kika = 1;
        $move->is_destination_agent_kika = 1;
        $move->required_storage = 1;
        $move->required_screening = 1;
        $move->is_seprated = 2;
        $move->is_tnc_checked = 0;
        $move->is_assign = $request->is_assign;
        $move->status = 0;
        $move->is_dl_tnc_checked = 0;
        $move->is_transload_tnc_checked = 0;
        $move->type_id = 1;
        $move->archive_status = 0;
        $move->assign_destination_company_id = $companyId;
        $move->is_email_optional = 0;
        $move->created_by = null;

        // dd($move);

        if ($move->save()) {
            $contact_user = new MoveContact();
            $contact_user->move_id = $move->id;
            $contact_user->contact_name = $request->name;
            $contact_user->email = $request->email ? $request->email : $companyDetails->email;
            $contact_user->contact_number = (isset($request->contact_number)) ? $request->contact_number : '';
        }

        if ($contact_user->save()) {
            $uplift_move = new UpliftMoves();
            $uplift_move->move_id = $move->id;
            $uplift_move->volume = $request->volume;
            $uplift_move->uplift_address = $request->uplift_address;
            $uplift_move->origin_agent_kika_id = $agent->kika_id;
            $uplift_move->origin_agent = $agent->company_name;
            $uplift_move->origin_agent_email = $agent->email;
            $uplift_move->date = date('Y-m-d H:i:s');
            $uplift_move->is_icr_created = 0;
            $uplift_move->status = 0;
        }

        if ($uplift_move->save()) {
            $delivery_move = new DeliveryMoves();
            $delivery_move->move_id = $move->id;
            $delivery_move->volume = $request->volume;
            $delivery_move->delivery_address = $request->delivery_address;
            $delivery_move->delivery_agent_kika_id = $agent->kika_id;
            $delivery_move->delivery_agent = $agent->company_name;
            $delivery_move->delivery_agent_email = $agent->email;
            $delivery_move->date = date('Y-m-d H:i:s');
            $delivery_move->status = 0;
        }

        if ($delivery_move->save()) {
            $move = Move::with([
                'uplift',
            ])
                ->where('move_number', $request->move_number)
                ->where('company_id', $companyId)
                ->first();
            // dd($move);


            $move['is_created_move'] = 0;
            $move['customer'] = $move->contact->contact_name;
            $move['customer_email'] = $move->contact->email;

            $this->data = $move;
            return $this->sendResponse($this->data, "Move is created successfully!");
        } else {
            return $this->apiError("Opps! Something went wrong.");
        }

    }

    public function createNonkikaMove(Request $request)
    {
        $companyId = CompanyAdmin::getCompanyUserCompany();
        $companyDetails = Companies::where('id', $companyId)->first();

        if (Move::where('company_id', $companyDetails->id)->where('move_number', '=', $request->move_number)->exists()) {
            $validator = Validator::make($request->all(), [
                'move_number' => 'required|unique:moves',
                'name' => 'required|string|min:2',
                //'contact_number' 	=> 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
                'volume' => 'required',
                'uplift_address' => 'required|string|min:5',
                'delivery_address' => 'required|string|min:5',
                'item_count' => 'required|numeric|gt:0',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'move_number' => 'required',
                'name' => 'required|string|min:2',
                //'contact_number' 	=> 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
                'volume' => 'required',
                'uplift_address' => 'required|string|min:5',
                'delivery_address' => 'required|string|min:5',
                'item_count' => 'required|numeric|gte:1'
            ]);
        }

        if (isset($request->contact_number)) {
            $validator->sometimes('contact_number', 'regex:/^([0-9\s\-\+\(\)]*)$/', function ($request) {
                if ($request->contact_number != null) {
                    return true;
                }
            });
            $validator->sometimes('contact_number', 'min:6', function ($request) {
                if ($request->contact_number != null) {
                    return true;
                }
            });
        }

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }

        $agent = CompanyAgent::where('kika_id', $companyDetails->kika_id)->first();

        $move = new Move();
        $move->company_id = $companyId;
        $move->move_number = $request->move_number;
        $move->reference_number = $companyDetails->kika_id . ' - ' . $request->move_number;
        $move->controlling_agent_kika_id = "self";
        $move->controlling_agent = $companyDetails->name;
        $move->controlling_agent_email = $companyDetails->email;
        $move->origin_agent = $agent->id;
        $move->destination_agent = $agent->id;
        $move->is_origin_agent_kika = 1;
        $move->is_destination_agent_kika = 1;
        $move->required_storage = $request->tranship ? 1 : 0;
        $move->required_screening = $request->screen ? 1 : 0;
        $move->is_seprated = 2;
        $move->is_tnc_checked = 0;
        $move->status = 2;
        $move->is_dl_tnc_checked = 0;
        $move->is_transload_tnc_checked = 0;
        if ($request->screen) {
            $move->type_id = 3;
        } else {
            $move->type_id = 5;
        }
        $move->archive_status = 0;
        $move->assign_destination_company_id = $companyId;
        $move->is_email_optional = 0;
        $move->created_by = null;
        if ($move->save()) {
            $contact_user = new MoveContact();
            $contact_user->move_id = $move->id;
            $contact_user->contact_name = $request->name;
            $contact_user->email = $request->email ? $request->email : $companyDetails->email;
            $contact_user->contact_number = (isset($request->contact_number)) ? $request->contact_number : '';
        }
        if ($contact_user->save()) {
            $uplift_move = new UpliftMoves();
            $uplift_move->move_id = $move->id;
            $uplift_move->volume = $request->volume;
            $uplift_move->uplift_address = $request->uplift_address;
            $uplift_move->origin_agent_kika_id = $agent->kika_id;
            $uplift_move->origin_agent = $agent->company_name;
            $uplift_move->origin_agent_email = $agent->email;
            $uplift_move->date = date('Y-m-d H:i:s');
            $uplift_move->is_icr_created = 1;
            $uplift_move->item_count = $request->item_count;
            $uplift_move->status = 2;
        }
        if ($uplift_move->save()) {
            $delivery_move = new DeliveryMoves();
            $delivery_move->move_id = $move->id;
            $delivery_move->volume = $request->volume;
            $delivery_move->delivery_address = $request->delivery_address;
            $delivery_move->delivery_agent_kika_id = $agent->kika_id;
            $delivery_move->delivery_agent = $agent->company_name;
            $delivery_move->delivery_agent_email = $agent->email;
            $delivery_move->date = date('Y-m-d H:i:s');
            $delivery_move->status = 0;
        }
        if ($delivery_move->save()) {
            if ($request->screen) {
                $screening_move = new ScreeningMoves();
                $screening_move->move_id = $move->id;
                $screening_move->volume = $request->volume;
                $screening_move->status = 1;
                $screening_move->save();
            }
            $move = Move::with(['uplift', 'screening', 'transload', 'delivery', 'contact'])
                ->where('move_number', $request->move_number)
                ->where('company_id', $companyId)
                ->first();
            // dd($move);


            $move['is_created_move'] = 0;
            $move['customer'] = $move->contact->contact_name;
            $move['customer_email'] = $move->contact->email;

            $this->data = $move;
            return $this->sendResponse($this->data, "Move is created successfully!");

        } else {
            return $this->apiError("Opps! Something went wrong.");
        }
    }

    public function createTransloadMove(Request $request)
    {
        $companyId = CompanyAdmin::getCompanyUserCompany();
        $companyDetails = Companies::where('id', $companyId)->first();

        if (Move::where('company_id', $companyDetails->id)->where('move_number', '=', $request->move_number)->exists()) {
            $validator = Validator::make($request->all(), [
                'move_number' => 'required|unique:moves',
                'name' => 'required|string|min:2',
                //'contact_number' 	=> 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
                'volume' => 'required',
                'uplift_address' => 'required|string|min:5',
                'delivery_address' => 'required|string|min:5',
                'item_count' => 'required|numeric|gt:0',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'move_number' => 'required',
                'name' => 'required|string|min:2',
                //'contact_number' 	=> 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
                'volume' => 'required',
                'uplift_address' => 'required|string|min:5',
                'delivery_address' => 'required|string|min:5',
                'item_count' => 'required|numeric|gte:1'
            ]);
        }

        if (isset($request->contact_number)) {
            $validator->sometimes('contact_number', 'regex:/^([0-9\s\-\+\(\)]*)$/', function ($request) {
                if ($request->contact_number != null) {
                    return true;
                }
            });
            $validator->sometimes('contact_number', 'min:6', function ($request) {
                if ($request->contact_number != null) {
                    return true;
                }
            });
        }

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }

        $agent = CompanyAgent::where('kika_id', $companyDetails->kika_id)->first();

        $move = new Move();
        $move->company_id = $companyId;
        $move->move_number = $request->move_number;
        $move->reference_number = $companyDetails->kika_id . ' - ' . $request->move_number;
        $move->controlling_agent_kika_id = "self";
        $move->controlling_agent = $companyDetails->name;
        $move->controlling_agent_email = $companyDetails->email;
        $move->origin_agent = $agent->id;
        $move->destination_agent = $agent->id;
        $move->is_origin_agent_kika = 1;
        $move->is_destination_agent_kika = 1;
        $move->required_storage = $request->tranship ? 1 : 0;
        $move->required_screening = $request->screen ? 1 : 0;
        $move->is_seprated = 2;
        $move->is_tnc_checked = 0;
        $move->status = 2;
        $move->is_dl_tnc_checked = 0;
        $move->is_transload_tnc_checked = 0;
        $move->type_id = 1;
        // if ($request->screen) {
        //     $move->type_id = 3;
        // } else {
        //     $move->type_id = 5;
        // }
        $move->archive_status = 0;
        $move->assign_destination_company_id = $companyId;
        $move->is_email_optional = 0;
        $move->created_by = null;
        if ($move->save()) {
            $contact_user = new MoveContact();
            $contact_user->move_id = $move->id;
            $contact_user->contact_name = $request->name;
            $contact_user->email = $request->email ? $request->email : $companyDetails->email;
            $contact_user->contact_number = (isset($request->contact_number)) ? $request->contact_number : '';
        }
        if ($contact_user->save()) {
            $uplift_move = new UpliftMoves();
            $uplift_move->move_id = $move->id;
            $uplift_move->volume = $request->volume;
            $uplift_move->uplift_address = $request->uplift_address;
            $uplift_move->origin_agent_kika_id = $agent->kika_id;
            $uplift_move->origin_agent = $agent->company_name;
            $uplift_move->origin_agent_email = $agent->email;
            $uplift_move->date = date('Y-m-d H:i:s');
            $uplift_move->is_icr_created = 1;
            $uplift_move->item_count = $request->item_count;
            $uplift_move->status = 2;
        }
        if ($uplift_move->save()) {
            $delivery_move = new DeliveryMoves();
            $delivery_move->move_id = $move->id;
            $delivery_move->volume = $request->volume;
            $delivery_move->delivery_address = $request->delivery_address;
            $delivery_move->delivery_agent_kika_id = $agent->kika_id;
            $delivery_move->delivery_agent = $agent->company_name;
            $delivery_move->delivery_agent_email = $agent->email;
            $delivery_move->date = date('Y-m-d H:i:s');
            $delivery_move->status = 0;
        }
        if ($delivery_move->save()) {
            if ($request->screen) {
                $screening_move = new ScreeningMoves();
                $screening_move->move_id = $move->id;
                $screening_move->volume = $request->volume;
                $screening_move->status = 1;
                $screening_move->save();
            }
            $move = Move::with(['uplift', 'screening', 'transload', 'delivery', 'contact'])
                ->where('move_number', $request->move_number)
                ->where('company_id', $companyId)
                ->first();
            // dd($move);


            $move['is_created_move'] = 0;
            $move['customer'] = $move->contact->contact_name;
            $move['customer_email'] = $move->contact->email;

            $this->data = $move;
            return $this->sendResponse($this->data, "Move is created successfully!");

        } else {
            return $this->apiError("Opps! Something went wrong.");
        }
    }

    public function changeContainerNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'move_id' => 'required',
            'container_number' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }

        $move = Move::where('id', $request->move_id)->first();

        if ($move) {

            $move->container_number = $request->container_number;
            $move->save();

            $this->data = $move;
            return $this->sendResponse($this->data, "Container number updated successfully!");
        } else {
            return $this->apiError("No move found for given move number.");
        }
    }

    public function moveRiskAssessment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'move_id' => 'required',
            'move_type' => 'required|in:1,5',
            'team_leader' => 'required',
            'risk_assessment_data' => 'required',
            'device' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors());
        }

        $move = Move::where('id', $request->move_id)->first();
        if (!$move) {
            return $this->apiError("No move found for given move number.");
        }

        $risk_assessment_id = RiskAssessment::where('move_id', $request->move_id)->where('move_type', $request->move_type)->value('id');
        if ($risk_assessment_id) {
            return $this->apiError("You have already added risk assessment");
        }

        $risk_assessment = new RiskAssessment();
        $risk_assessment->move_id = $request->move_id;
        $risk_assessment->move_type = $request->move_type;
        $risk_assessment->team_leader = $request->team_leader;
        $risk_assessment->risk_comment = $request->comment;
        $risk_assessment->save();

        if (!$risk_assessment) {
            return $this->apiError("Risk assessment not found");
        }

        $risk_assessment_request = json_decode($request->risk_assessment_data);

        foreach ($risk_assessment_request as $row) {
            try {
                $assessment_detail = new RiskAssessmentDetail();
                $assessment_detail->risk_assessment_id = $risk_assessment->id;
                $assessment_detail->risk_title_id = $row->title;
                $assessment_detail->risk_priority = $row->priority;
                $assessment_detail->save();
            } catch (Exception $e) {
                Log::debug('MoveController::moveRiskAssessment, route - move/risk-assessment, risk_assessment_details - insert - Exception', ['exception' => $e->getMessage()]);
            }
        }

        $pdfData['move_agent'] = $move->uplift->origin_agent;
        $pdfData['risk_title'] = RiskTitles::get()->toArray();
        $pdfData['risk_assessment'] = RiskAssessment::with('riskAssessmentDetail')->where([
            'move_id' => $request->move_id,
            'move_type' => $request->move_type
        ])->first();

        switch ($request->move_type) {
            case '1':
                // $receiver_email = $move->uplift->origin_agent_email;
                $receiver_email = Companies::where('id', $move->company_id)->value('email');
                if ($receiver_email) {
                    $title = $move->contact->contact_name . " - $move->move_number : Uplift Risk Assessment";
                    $pdfData['move_name'] = "Uplift";
                    $pdfData['title'] = $move->contact->contact_name . " - $move->move_number : Uplift";

                    $pdf = PDF::loadView('api-pdf.risk-assessment', $pdfData)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);
                    $pdfPath = $title . '.pdf';
                    $data = array();

                    //getting device email
                    $device_email = User::where('username', $request->device)->value('email');


                    if ($device_email != null && $device_email != "") {
                        Mail::send('mails.risk-assessment', $data, function ($message) use ($pdfPath, $title, $device_email, $pdf) {
                            $message->to($device_email)
                                ->subject($title)
                                ->attachData($pdf->output(), $pdfPath);
                        });
                    }
                }
                return $this->msgResponse("Uplift risk assessment created successfully");
            case '5':
                $receiver_email = CompanyAgent::where('id', $move->destination_agent)->value('email');
                if ($receiver_email) {
                    $title = $move->contact->contact_name . " - $move->move_number : Delivery Risk Assessment";
                    $pdfData['move_name'] = "Delivery";
                    $pdfData['title'] = $move->contact->contact_name . " - $move->move_number : Delivery";

                    $pdf = PDF::loadView('api-pdf.risk-assessment', $pdfData)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);
                    $pdfPath = $title . '.pdf';
                    $data = array();

                    $device_email = User::where('username', $request->device)->value('email');

                    if ($device_email != null && $device_email != "") {
                        Mail::send('mails.risk-assessment', $data, function ($message) use ($pdfPath, $title, $device_email, $pdf) {
                            $message->to($device_email)
                                ->subject($title)
                                ->attachData($pdf->output(), $pdfPath);
                        });
                    }
                }
                return $this->msgResponse("Delivery risk assessment created successfully");
        }
    }

    public function setRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'move_id' => 'required',
            'room_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }

        $move = Move::where('id', $request->move_id)->first();
        if (!$move) {
            return $this->apiError("No move found for given move number.");
        }

        $move->room_id = $request->room_id;
        $move->save();

        $this->data = $move;
        return $this->sendResponse($this->data, "Set Room successfully!");
    }

}
