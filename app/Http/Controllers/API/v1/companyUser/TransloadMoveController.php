<?php

namespace App\Http\Controllers\API\v1\companyUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Move;
use App\DeliveryMoves;
use App\ScreeningMoves;
use App\UpliftMoves;
use App\TransloadMoves;
use App\TransitMoves;
use App\PackageSignature;
use App\MoveContainer;
use App\ContainerItem;
use App\MoveItems;
use App\MoveItemCondition;
use App\MoveItemConditionSide;
use App\TransloadCondition;
use App\ContainerColorOrder;
use App\MoveConditionImage;
use App\Helpers\CompanyAdmin;
use Storage;

class TransloadMoveController extends BaseController
{
    public function classify(Request $request)
    {
    	$validator = Validator::make($request->all(), [
			'is_seprated'          => 'boolean',
			'required_screening'   => 'boolean',
			'move_id'              => 'required',
		]);

		if($validator->fails()){
            return $this->apiError($validator->errors()->first());
        }

        if ($move = Move::find($request->move_id)) {

    		$move->is_seprated = $request->is_seprated;
    		$move->required_screening = $request->required_screening;
    		$move->save();

        	return $this->msgResponse("move classified successfully!");
        }else{
        	return $this->apiError("No move found for give move ID!");
        }

    }

    public function addContainer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'          => 'integer',
            'move_id'              => 'required',
            'container_number'     => 'required',
        ]);

        if($validator->fails()){
            return $this->apiError($validator->errors()->first());
        }

        $company_id = CompanyAdmin::getCompanyUserCompany();

        if( Move::where('id',$request->move_id)->where(function ($query) use ($company_id){
                $query->where('company_id',$company_id)
                      ->orWhere('foreign_controlling_agent', '=', $company_id)
                      ->orWhere('foreign_origin_contractor',$company_id)
                      ->orWhere('foreign_destination_contractor',$company_id)
                      ->orWhere('foreign_origin_agent',$company_id)
                      ->orWhere('foreign_destination_agent',$company_id);
        })->exists())
        {
            $existing_container = MoveContainer::where('move_id',$request->move_id)->latest()->first();
            if ($existing_container) {
                $container = ContainerColorOrder::where('color_code',$existing_container->color_code)->first();

                if ($container->next()) {
                    $color_code = $container->next()->color_code;
                }else{
                    $color_code = ContainerColorOrder::where('sort_order',1)->value('color_code');
                }


            }else{

                $color_code = ContainerColorOrder::where('sort_order',1)->value('color_code');
            }

            $move_container                     = new MoveContainer();
            $move_container->category_id        = (int)$request->category_id;
            $move_container->move_id            = (int)$request->move_id;
            $move_container->container_number   = $request->container_number;
            $move_container->color_code         = $color_code;
            $move_container->save();

            $this->data = $move_container;

            return $this->sendResponse($this->data,"Transload move container added successfully!");
        }
        else{
            return $this->apiError("No move found for give move ID!");
        }
    }

    public function addItemContainer(Request $request)
    {
        foreach ($request->items as $itemKey => $item) {
            if($data = ContainerItem::where('move_item_id',$item['item_id'])->first()){
                    $data->delete();
            }

            $container_item                          = new ContainerItem();
            $container_item->container_id            = $item['container_id'];
            $container_item->move_item_id            = $item['item_id'];
            $container_item->save();

            $transload_condition = MoveItemCondition::where('move_item_id',$item['item_id'])->where('move_type',4);

            $transload_condition_id = $transload_condition->pluck('id');
            MoveItemConditionSide::whereIn('item_condition_id',$transload_condition_id)->delete();

            $existing_images =  MoveConditionImage::whereIn('move_condition_id',$transload_condition_id)->delete();

            if($transload_condition->exists()){
                $transload_condition->delete();
            }
            foreach ($item['conditions'] as $conditionkey => $condition) {

                $move_item_condition                 = new MoveItemCondition();
                $move_item_condition->move_id        = $item['move_id'];
                $move_item_condition->move_item_id   = $item['item_id'];
                $move_item_condition->condition_id   = $condition['id'];
                $move_item_condition->move_type      = 4;
                $move_item_condition->save();

                if (isset($condition['condition_images'])) {
                    foreach ($condition['condition_images'] as $imageKey => $conditionImage) {

                        $condition_image                    = new MoveConditionImage();
                        $condition_image->move_condition_id = $move_item_condition->id;
                        $condition_image->image             = $conditionImage['image'];
                        $condition_image->save();
                    }
                }

                foreach ($condition['condition_side'] as $condition_sidekey => $condition_side)
                {
                    $move_item_condition_side                     = new MoveItemConditionSide();
                    $move_item_condition_side->item_condition_id  = $move_item_condition->id;
                    $move_item_condition_side->condition_side_id  = $condition_side['id'];
                    $move_item_condition_side->save();

                }
            }
        }
        return $this->apiSuccess("Transload item added successfully in container!");
    }

    public function editItemCondition(Request $request)
    {
        foreach ($request->items as $itemKey => $item) {
            $moveItem = MoveItems::where('id',$item['item_id'])->first();
            $moveItemCondition = MoveItemCondition::where('move_item_id',$moveItem->id)->pluck('id');

            $existing_images =  MoveConditionImage::whereIn('move_condition_id',$moveItemCondition)->delete();

            MoveItemConditionSide::whereIn('item_condition_id',$moveItemCondition)->delete();
            MoveItemCondition::whereIn('id',$moveItemCondition)->delete();
            foreach ($item['condition'] as $conditionKey => $condition) {
                $move_item_condition_data                 = new MoveItemCondition();
                $move_item_condition_data->move_id        = $request->move_id;
                $move_item_condition_data->move_item_id   = $moveItem->id;
                $move_item_condition_data->condition_id   = $condition['id'];
                $move_item_condition_data->save();

                if (isset($condition['condition_images'])) {
                    foreach ($condition['condition_images'] as $imageKey => $conditionImage) {

                        $condition_image                    = new MoveConditionImage();
                        $condition_image->move_condition_id = $move_item_condition->id;
                        $condition_image->image             = $conditionImage['image'];
                        $condition_image->save();
                    }
                }


                foreach ($condition['condition_side'] as $sideKey => $side) {
                    $move_item_condition_side                     = new MoveItemConditionSide();
                    $move_item_condition_side->item_condition_id  = $move_item_condition_data->id;
                    $move_item_condition_side->condition_side_id  = $side['id'];
                    $move_item_condition_side->save();
                }
            }
        }

        $response['move_id'] = $request->move_id;
        $response['items'] = MoveItems::with([
                                            'condition.conditionDetails',
                                            'condition.conditionSides'
                                        ])
                                        ->where('move_id',$request->move_id)
                                        ->get();
        $this->data = $response;
        return $this->sendResponse($this->data,"Item conditions updated successfully!");
    }
}
