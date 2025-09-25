<?php

namespace App\Http\Controllers\API\v1\companyUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Move;
use App\MoveItems;
use App\MoveItemCondition;
use App\MoveItemConditionSide;
use App\Helpers\CompanyAdmin;
use App\MoveConditionImage;
use Storage;

class DeliveryMoveController extends BaseController
{
    public function manageDeliveryItem(Request $request)
    {
        $company_id = CompanyAdmin::getCompanyUserCompany();
        if(Move::where('id',$request->move_id)->where(function ($query) use ($company_id) {
			$query->where('company_id', '=', $company_id)
				  ->orWhere('foreign_controlling_agent', '=', $company_id)
				  ->orWhere('foreign_origin_contractor',$company_id)
				  ->orWhere('foreign_destination_contractor',$company_id)
				  ->orWhere('foreign_origin_agent',$company_id)
				  ->orWhere('foreign_destination_agent',$company_id);
		})->exists()){
            foreach ($request->items as $itemKey => $item) {

                MoveItems::where('id',$item['item_id'])->update(['is_delivered' => 1]);

                $delivery_condition = MoveItemCondition::where('move_item_id',$item['item_id'])->where('move_type',5);

                $delivery_condition_id = $delivery_condition->pluck('id');

                $existing_images =  MoveConditionImage::whereIn('move_condition_id',$delivery_condition_id)->delete();

                MoveItemConditionSide::whereIn('item_condition_id',$delivery_condition_id)->delete();
    
                if($delivery_condition->exists()){
                    $delivery_condition->delete();
                }
                foreach ($item['conditions'] as $conditionkey => $condition) {
    
                    $move_item_condition                 = new MoveItemCondition();
                    $move_item_condition->move_id        = $request->move_id;
                    $move_item_condition->move_item_id   = $item['item_id'];
                    $move_item_condition->condition_id   = $condition['id'];
                    $move_item_condition->move_type      = 5;
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
            return $this->apiSuccess("Condition assigned to delivery items!");
        }else{
            return $this->apiError("No move found for given move number.");
        }
    }

    public function unpackItems(Request $request)
    {
        $moveItems = MoveItems::Where('move_id', $request->move_id)->get();
        foreach($moveItems as $moveItem){

            if (in_array($moveItem->id,$request->item_package_status['UP'])) {
                MoveItems::where('id',$moveItem->id)->update(['is_unpacked' => 1]);
            }elseif(in_array($moveItem->id,$request->item_package_status['LP'])) {
                MoveItems::where('id',$moveItem->id)->update(['is_unpacked' => 0]);
            }else{
                MoveItems::where('id',$moveItem->id)->update(['is_unpacked' => null]);
            }

        }

        return $this->apiSuccess("Items unpacked successfully!");
    }

    //   public function nonKikaItems(Request $request)
    // {
    //     $company_id = CompanyAdmin::getCompanyUserCompany();
    //     if(Move::where('id',$request->move_id)->where(function ($query) use ($company_id) {
    //         $query->where('company_id', '=', $company_id)
    //               ->orWhere('foreign_controlling_agent', '=', $company_id)
    //               ->orWhere('foreign_origin_contractor',$company_id)
    //               ->orWhere('foreign_destination_contractor',$company_id)
    //               ->orWhere('foreign_origin_agent',$company_id)
    //               ->orWhere('foreign_destination_agent',$company_id);
    //     })->exists())
    //     {
    //         $items = $request->items;
    //         if(MoveItems::where('move_id',$request->move_id)->exists())
    //         {
    //             $data = MoveItems::where('move_id',$request->move_id)->where('item_number',$items[0]['item_number'])->first();
    //             if(MoveItems::where('move_id',$request->move_id)->exists() && $data != null)
    //             {
    //                 $changed_move_item_number = MoveItems::where('move_id',$request->move_id)->where('item_number',$items[0]['item_number'])->update(['move_type' => 5, 'is_delivered' => $items[0]['is_delivered']]);
    //                 $move_item_number = MoveItems::where('move_id',$request->move_id)->where('item_number',$items[0]['item_number'])->first();
    //                 $delivery_condition = MoveItemCondition::where('move_item_id',$move_item_number->id)->where('move_type',5);
    //                 $delivery_condition_id = $delivery_condition->pluck('id');
    //                 $existing_images =  MoveConditionImage::whereIn('move_condition_id',$delivery_condition_id)->delete();
    //                 MoveItemConditionSide::whereIn('item_condition_id',$delivery_condition_id)->delete();
    //                 if($delivery_condition->exists()){
    //                     $delivery_condition->delete();
    //                 }
    //                 foreach ($items[0]['conditions'] as $conditionkey => $condition) {

    //                     $move_item_condition                 = new MoveItemCondition();
    //                     $move_item_condition->move_id        = $request->move_id;
    //                     $move_item_condition->move_item_id   = $move_item_number->id;
    //                     $move_item_condition->condition_id   = $condition['id'];
    //                     $move_item_condition->move_type      = 5;
    //                     $move_item_condition->save();
    
    //                     if (isset($condition['condition_images'])) {
    //                         foreach ($condition['condition_images'] as $imageKey => $conditionImage) {
                            
    //                             $condition_image                    = new MoveConditionImage();
    //                             $condition_image->move_condition_id = $move_item_condition->id;
    //                             $condition_image->image             = $conditionImage['image'];
    //                             $condition_image->save();
                                
    //                         }
    //                     }
                        
    //                     foreach ($condition['condition_side'] as $condition_sidekey => $condition_side)
    //                     {
    //                         $move_item_condition_side                     = new MoveItemConditionSide();
    //                         $move_item_condition_side->item_condition_id  = $move_item_condition->id;
    //                         $move_item_condition_side->condition_side_id  = $condition_side['id'];
    //                         $move_item_condition_side->save();
                                        
    //                     }
    //                 }
    //                 return $this->apiSuccess("Delivery items added successfully!");
    //             }
    //             else{
    //                 $move_items = new MoveItems();
    //                 $move_items->move_id = $items[0]['move_id'];
    //                 $move_items->item_id = null;
    //                 $move_items->item = null;
    //                 $move_items->screening_category_id = null;
    //                 $move_items->packer_id = null;
    //                 $move_items->item_number = $items[0]['item_number'];
    //                 $move_items->is_delivered = $items[0]['is_delivered'];
    //                 $move_items->is_unpacked = null;
    //                 $move_items->move_type = 5;
    //                 $move_items->description = null;
    //                 if($move_items->save())
    //                 {
    //                     $delivery_condition = MoveItemCondition::where('move_item_id',$move_items['id'])->where('move_type',5);
    //                     $delivery_condition_id = $delivery_condition->pluck('id');
    //                     $existing_images =  MoveConditionImage::whereIn('move_condition_id',$delivery_condition_id)->delete();
    //                     MoveItemConditionSide::whereIn('item_condition_id',$delivery_condition_id)->delete();
    //                     if($delivery_condition->exists()){
    //                         $delivery_condition->delete();
    //                     }
    //                     foreach ($items[0]['conditions'] as $conditionkey => $condition) {
    
    //                         $move_item_condition                 = new MoveItemCondition();
    //                         $move_item_condition->move_id        = $request->move_id;
    //                         $move_item_condition->move_item_id   = $move_items['id'];
    //                         $move_item_condition->condition_id   = $condition['id'];
    //                         $move_item_condition->move_type      = 5;
    //                         $move_item_condition->save();
        
    //                         if (isset($condition['condition_images'])) {
    //                             foreach ($condition['condition_images'] as $imageKey => $conditionImage) {
                                
    //                                 $condition_image                    = new MoveConditionImage();
    //                                 $condition_image->move_condition_id = $move_item_condition->id;
    //                                 $condition_image->image             = $conditionImage['image'];
    //                                 $condition_image->save();
                                    
    //                             }
    //                         }
                            
    //                         foreach ($condition['condition_side'] as $condition_sidekey => $condition_side)
    //                         {
    //                             $move_item_condition_side                     = new MoveItemConditionSide();
    //                             $move_item_condition_side->item_condition_id  = $move_item_condition->id;
    //                             $move_item_condition_side->condition_side_id  = $condition_side['id'];
    //                             $move_item_condition_side->save();
                                            
    //                         }
    //                     }
    //                 }
    //                 return $this->apiSuccess("Delivery items added successfully!");
    //             }
    //         }else{
    //             $move_items = new MoveItems();
    //             $move_items->move_id = $items[0]['move_id'];
    //             $move_items->item_id = null;
    //             $move_items->item = null;
    //             $move_items->screening_category_id = null;
    //             $move_items->packer_id = null;
    //             $move_items->item_number = $items[0]['item_number'];
    //             $move_items->is_delivered = $items[0]['is_delivered'];
    //             $move_items->is_unpacked = null;
    //             $move_items->move_type = 5;
    //             $move_items->description = null;
    //             if($move_items->save())
    //             {
    //                 $delivery_condition = MoveItemCondition::where('move_item_id',$move_items['id'])->where('move_type',5);
    //                 $delivery_condition_id = $delivery_condition->pluck('id');
    //                 $existing_images =  MoveConditionImage::whereIn('move_condition_id',$delivery_condition_id)->delete();
    //                 MoveItemConditionSide::whereIn('item_condition_id',$delivery_condition_id)->delete();
    //                 if($delivery_condition->exists()){
    //                     $delivery_condition->delete();
    //                 }
    //                 foreach ($items[0]['conditions'] as $conditionkey => $condition) {

    //                     $move_item_condition                 = new MoveItemCondition();
    //                     $move_item_condition->move_id        = $request->move_id;
    //                     $move_item_condition->move_item_id   = $move_items['id'];
    //                     $move_item_condition->condition_id   = $condition['id'];
    //                     $move_item_condition->move_type      = 5;
    //                     $move_item_condition->save();
    
    //                     if (isset($condition['condition_images'])) {
    //                         foreach ($condition['condition_images'] as $imageKey => $conditionImage) {
                            
    //                             $condition_image                    = new MoveConditionImage();
    //                             $condition_image->move_condition_id = $move_item_condition->id;
    //                             $condition_image->image             = $conditionImage['image'];
    //                             $condition_image->save();
                                
    //                         }
    //                     }
                        
    //                     foreach ($condition['condition_side'] as $condition_sidekey => $condition_side)
    //                     {
    //                         $move_item_condition_side                     = new MoveItemConditionSide();
    //                         $move_item_condition_side->item_condition_id  = $move_item_condition->id;
    //                         $move_item_condition_side->condition_side_id  = $condition_side['id'];
    //                         $move_item_condition_side->save();
                                        
    //                     }
    //                 }
    //             }
    //             return $this->apiSuccess("Delivery items added successfully!");
    //         }

        
    //     }else{
    //         return $this->apiError("No move found for given move number.");
    //     }

        
    // }

    public function nonKikaItems(Request $request)
    {
    	$company_id = CompanyAdmin::getCompanyUserCompany();
        if(Move::where('id',$request->move_id)->where(function ($query) use ($company_id) {
			$query->where('company_id', '=', $company_id)
				  ->orWhere('foreign_controlling_agent', '=', $company_id)
				  ->orWhere('foreign_origin_contractor',$company_id)
				  ->orWhere('foreign_destination_contractor',$company_id)
				  ->orWhere('foreign_origin_agent',$company_id)
				  ->orWhere('foreign_destination_agent',$company_id);
		})->exists())
        {
            $items = $request->items;
            if(MoveItems::where('move_id',$request->move_id)->exists())
            {
            	foreach($items as $item)
          		{
          			// echo "<pre>";
          			// print_r($item['item_number']);
          			// die;
	                $data = MoveItems::where('move_id',$request->move_id)->where('item_number',$item['item_number'])->first();
	                // dd($data);
	                if(MoveItems::where('move_id',$request->move_id)->exists() && $data != null)
	                {
	                    $changed_move_item_number = MoveItems::where('move_id',$request->move_id)->where('item_number',$item['item_number'])->update(['move_type' => 5, 'is_delivered' => $item['is_delivered']]);
	                    $move_item_number = MoveItems::where('move_id',$request->move_id)->where('item_number',$item['item_number'])->first();
	                    $delivery_condition = MoveItemCondition::where('move_item_id',$move_item_number->id)->where('move_type',5);
	                    $delivery_condition_id = $delivery_condition->pluck('id');
	                    $existing_images =  MoveConditionImage::whereIn('move_condition_id',$delivery_condition_id)->delete();
	                    MoveItemConditionSide::whereIn('item_condition_id',$delivery_condition_id)->delete();
	                    if($delivery_condition->exists()){
	                        $delivery_condition->delete();
	                    }
	                    foreach ($item['conditions'] as $conditionkey => $condition) {

	                        $move_item_condition                 = new MoveItemCondition();
	                        $move_item_condition->move_id        = $request->move_id;
	                        $move_item_condition->move_item_id   = $move_item_number->id;
	                        $move_item_condition->condition_id   = $condition['id'];
	                        $move_item_condition->move_type      = 5;
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
	                }else{
                    $move_items = new MoveItems();
                    $move_items->move_id = $item['move_id'];
                    $move_items->item_id = null;
                    $move_items->item = null;
                    $move_items->screening_category_id = null;
                    $move_items->packer_id = null;
                    $move_items->item_number = $item['item_number'];
                    $move_items->is_delivered = $item['is_delivered'];
                    $move_items->is_unpacked = null;
                    $move_items->move_type = 5;
                    $move_items->description = null;
                    if($move_items->save())
                    {
                        $delivery_condition = MoveItemCondition::where('move_item_id',$move_items['id'])->where('move_type',5);
                        $delivery_condition_id = $delivery_condition->pluck('id');
                        $existing_images =  MoveConditionImage::whereIn('move_condition_id',$delivery_condition_id)->delete();
                        MoveItemConditionSide::whereIn('item_condition_id',$delivery_condition_id)->delete();
                        if($delivery_condition->exists()){
                            $delivery_condition->delete();
                        }
                        foreach ($item['conditions'] as $conditionkey => $condition) {
    
                            $move_item_condition                 = new MoveItemCondition();
                            $move_item_condition->move_id        = $request->move_id;
                            $move_item_condition->move_item_id   = $move_items['id'];
                            $move_item_condition->condition_id   = $condition['id'];
                            $move_item_condition->move_type      = 5;
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
                    return $this->apiSuccess("Delivery items added successfully!");
                }
                    return $this->apiSuccess("Delivery items added successfully!");
                }
            }	
            else{
            	foreach($items as $item)
          		{
	                $move_items = new MoveItems();
	                $move_items->move_id = $item['move_id'];
	                $move_items->item_id = null;
	                $move_items->item = null;
	                $move_items->screening_category_id = null;
	                $move_items->packer_id = null;
	                $move_items->item_number = $item['item_number'];
	                $move_items->is_delivered = $item['is_delivered'];
	                $move_items->is_unpacked = null;
	                $move_items->move_type = 5;
	                $move_items->description = null;
	                if($move_items->save())
	                {
	                    $delivery_condition = MoveItemCondition::where('move_item_id',$move_items['id'])->where('move_type',5);
	                    $delivery_condition_id = $delivery_condition->pluck('id');
	                    $existing_images =  MoveConditionImage::whereIn('move_condition_id',$delivery_condition_id)->delete();
	                    MoveItemConditionSide::whereIn('item_condition_id',$delivery_condition_id)->delete();
	                    if($delivery_condition->exists()){
	                        $delivery_condition->delete();
	                    }
	                    foreach ($item['conditions'] as $conditionkey => $condition) {

	                        $move_item_condition                 = new MoveItemCondition();
	                        $move_item_condition->move_id        = $request->move_id;
	                        $move_item_condition->move_item_id   = $move_items['id'];
	                        $move_item_condition->condition_id   = $condition['id'];
	                        $move_item_condition->move_type      = 5;
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
	            }
	                return $this->apiSuccess("Delivery items added successfully!");
			}
        }else{
            return $this->apiError("No move found for given move number.");
        }
    }

   //  public function nonKikaItems(Request $request)
   //  {
   //     	$company_id = CompanyAdmin::getCompanyUserCompany();
   //    	if(Move::where('id',$request->move_id)->where(function ($query) use ($company_id) {
			//   $query->where('company_id', '=', $company_id)
			// 	  ->orWhere('foreign_controlling_agent', '=', $company_id)
			// 	  ->orWhere('foreign_origin_contractor',$company_id)
			// 	  ->orWhere('foreign_destination_contractor',$company_id)
			// 	  ->orWhere('foreign_origin_agent',$company_id)
			// 	  ->orWhere('foreign_destination_agent',$company_id);
		 //    })->exists())
   //    	{
   //      $items = $request->items;
   //      if(MoveItems::where('move_id',$request->move_id)->exists())
   //      {
   //        // $update_change_item_number_arr = [];
   //        $insert_change_item_number_arr = [];
   //        // $delete_existing_images_arr  = [];
   //        foreach($items as $item)
   //        { 
   //        	// dd($item['item_number'];
   //          $checkMoveItemAvailable = false;
   //          $data = MoveItems::where('move_id',$request->move_id)->where('item_number',$item['item_number'])->first();
   //          if(MoveItems::where('move_id',$request->move_id)->exists() && $data != null)
   //          {
   //            $changed_move_item_number = MoveItems::where('move_id',$request->move_id)->where('item_number',$item['item_number'])->update(['move_type' => 5, 'is_delivered' => $items[0]['is_delivered']]);
   //            $move_item_number = MoveItems::where('move_id',$request->move_id)->where('item_number',$item['item_number'])->first();
   //            $delivery_condition = MoveItemCondition::where('move_item_id',$move_item_number->id)->where('move_type',5);
   //            $delivery_condition_id = $delivery_condition->pluck('id');
   //            $existing_images =  MoveConditionImage::whereIn('move_condition_id',$delivery_condition_id)->delete();
   //            MoveItemConditionSide::whereIn('item_condition_id',$delivery_condition_id)->delete();
   //            if($delivery_condition->exists()){
   //              $delivery_condition->delete();
   //            }
   //            $checkMoveItemAvailable =True;
            
   //          }
   //          $move_items = ''; 
   //          // dd('here');
   //          if(!$checkMoveItemAvailable)
   //          {
   //            $move_items = new MoveItems();
   //            $move_items->move_id = $request->move_id;
   //            $move_items->item_id = null;
   //            $move_items->item = null;
   //            $move_items->screening_category_id = null;
   //            $move_items->packer_id = null;
   //            $move_items->item_number = $items[0]['item_number'];
   //            $move_items->is_delivered = $items[0]['is_delivered'];
   //            $move_items->is_unpacked = null;
   //            $move_items->move_type = 5;
   //            $move_items->description = null; 
   //            $move_items->save();
   //          }
   //            // if($move_items->save())
   //            // {
   //              // $delivery_condition = MoveItemCondition::where('move_item_id',$move_items['id'])->where('move_type',5);
   //              // $delivery_condition_id = $delivery_condition->pluck('id');
   //              // $existing_images =  MoveConditionImage::whereIn('move_condition_id',$delivery_condition_id)->delete();
   //              // MoveItemConditionSide::whereIn('item_condition_id',$delivery_condition_id)->delete();
   //              // if($delivery_condition->exists()){
   //              //   $delivery_condition->delete();
   //              // }
			// // if (isset($items[0]['conditions'])) {
			// 	// dump($move_items);
			// 	// dump(isset($items[0]['conditions']));
   //              foreach ($item['conditions'] as $conditionkey => $condition)
   //              {
   //               	$move_item_condition                 = new MoveItemCondition();
   //                  $move_item_condition->move_id        = $request->move_id;
   //                	$move_item_condition->move_item_id   = $move_item_number->id;
   //                	$move_item_condition->condition_id   = $condition['id'];
   //                	$move_item_condition->move_type      = 5;
   //                	$move_item_condition->save();

   //                	if (isset($condition['condition_images'])) 
   //                	{
   //                   	$add_condition_images_arr = [];
   //                    	foreach ($condition['condition_images'] as $imageKey => $conditionImage) {
   //                      // $add_condition_images = array(
   //                      //               "move_condition_id"   => $move_item_condition->id,
   //                      //               "image" => $conditionImage['image']
   //                      //             );
   //                      // array_push($add_condition_images_arr,$add_condition_images);
   //                        $condition_image                    = new MoveConditionImage();
   //                        $condition_image->move_condition_id = $move_item_condition->id;
   //                        $condition_image->image             = $conditionImage['image'];
   //                        $condition_image->save();
   //                    	}
   //                    // start add data of condition images
   //                    // $insert_condition_images_data = MoveConditionImage::insert($add_condition_images_arr);
   //                    //end add data of condition images
   //                    // $add_condisition_side_arr = [];
   //                    	foreach ($condition['condition_side'] as $condition_sidekey => $condition_side)
   //                      {
   //                        // $add_condition_side = array(
   //                        //   "item_condition_id"   => $move_item_condition->id,
   //                        //   "condition_side_id" => $condition_side['id']
   //                        // );
   //                        // array_push($add_condisition_side_arr,$add_condition_side);
   //                          $move_item_condition_side                     = new MoveItemConditionSide();
   //                          $move_item_condition_side->item_condition_id  = $move_item_condition->id;
   //                          $move_item_condition_side->condition_side_id  = $condition_side['id'];
   //                          $move_item_condition_side->save();
   //                      }
   //                      // start add data of condition side
   //                      // $insert_condition_side_data = MoveItemConditionSide::insert($add_condisition_side_arr);
   //                      //end add data of condition side
   //                	}
   //              // }
   //          }
   //        }
   //        return $this->apiSuccess("Delivery items added successfully!");
   //      }else{
   //        // dd($request->move_id);
   //        $items = $request->items;
   //        foreach($items as $item)
   //        { 
   //          $checkMoveItemAvailable = false;
   //          $data = MoveItems::where('move_id',$request->move_id)->where('item_number',$item['item_number'])->first();
   //          if(MoveItems::where('move_id',$request->move_id)->exists() && $data != null)
   //          {
   //            $changed_move_item_number = MoveItems::where('move_id',$request->move_id)->where('item_number',$item['item_number'])->update(['move_type' => 5, 'is_delivered' => $items[0]['is_delivered']]);
   //            $move_item_number = MoveItems::where('move_id',$request->move_id)->where('item_number',$item['item_number'])->first();
   //            $delivery_condition = MoveItemCondition::where('move_item_id',$move_item_number->id)->where('move_type',5);
   //            $delivery_condition_id = $delivery_condition->pluck('id');
   //            $existing_images =  MoveConditionImage::whereIn('move_condition_id',$delivery_condition_id)->delete();
   //            MoveItemConditionSide::whereIn('item_condition_id',$delivery_condition_id)->delete();
   //            if($delivery_condition->exists()){
   //              $delivery_condition->delete();
   //            }
   //            $checkMoveItemAvailable =True;
            
   //          }
           
   //          $move_items = '';
   //          if(!$checkMoveItemAvailable)
   //          {
   //            $move_items = new MoveItems();
   //            $move_items->move_id = $request->move_id;
   //            $move_items->item_id = null;
   //            $move_items->item = null;
   //            $move_items->screening_category_id = null;
   //            $move_items->packer_id = null;
   //            $move_items->item_number = $item['item_number'];
   //            $move_items->is_delivered = $item['is_delivered'];
   //            $move_items->is_unpacked = null;
   //            $move_items->move_type = 5;
   //            $move_items->description = null; 
   //            $move_items->save();
   //          }
   //          // dump($move_items);
   //            // if($move_items->save())
   //            // {
   //              // $delivery_condition = MoveItemCondition::where('move_item_id',$move_items['id'])->where('move_type',5);
   //              // $delivery_condition_id = $delivery_condition->pluck('id');
   //              // $existing_images =  MoveConditionImage::whereIn('move_condition_id',$delivery_condition_id)->delete();
   //              // MoveItemConditionSide::whereIn('item_condition_id',$delivery_condition_id)->delete();
   //              // if($delivery_condition->exists()){
   //              //   $delivery_condition->delete();
   //              // }
   //          // if (isset($items[0]['conditions'])) {
   //              foreach ($item['conditions'] as $conditionkey => $condition) {

   //                $move_item_condition                 = new MoveItemCondition();
   //                $move_item_condition->move_id        = $request->move_id;
   //                $move_item_condition->move_item_id   = $move_items->id;
   //                $move_item_condition->condition_id   = $condition['id'];
   //                $move_item_condition->move_type      = 5;
   //                $move_item_condition->save();

   //                if (isset($condition['condition_images'])) {
   //                    $add_condition_images_arr = [];
   //                    foreach ($condition['condition_images'] as $imageKey => $conditionImage) {
   //                      // $add_condition_images = array(
   //                      //               "move_condition_id"   => $move_item_condition->id,
   //                      //               "image" => $conditionImage['image']
   //                      //             );
   //                      // array_push($add_condition_images_arr,$add_condition_images);
   //                        $condition_image                    = new MoveConditionImage();
   //                        $condition_image->move_condition_id = $move_item_condition->id;
   //                        $condition_image->image             = $conditionImage['image'];
   //                        $condition_image->save();
   //                    }
   //                    // start add data of condition images
   //                    // $insert_condition_images_data = MoveConditionImage::insert($add_condition_images_arr);
   //                    //end add data of condition images
   //                    $add_condisition_side_arr = [];
   //                    foreach ($condition['condition_side'] as $condition_sidekey => $condition_side)
   //                      {
   //                        // $add_condition_side = array(
   //                        //   "item_condition_id"   => $move_item_condition->id,
   //                        //   "condition_side_id" => $condition_side['id']
   //                        // );
   //                        // array_push($add_condisition_side_arr,$add_condition_side);
   //                          $move_item_condition_side                     = new MoveItemConditionSide();
   //                          $move_item_condition_side->item_condition_id  = $move_item_condition->id;
   //                          $move_item_condition_side->condition_side_id  = $condition_side['id'];
   //                          $move_item_condition_side->save();
                                        
   //                      }
   //                      // start add data of condition side
   //                      // $insert_condition_side_data = MoveItemConditionSide::insert($add_condisition_side_arr);
   //                      //end add data of condition side
   //                }
   //            }
   //           // }
   //        }
   //        return $this->apiSuccess("Delivery items added successfully!");
   //      }
   //    }else{
   //      return $this->apiError("No move found for given move number.");
   //  }
   //  }
}
