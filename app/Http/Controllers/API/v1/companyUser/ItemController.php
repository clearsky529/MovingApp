<?php

namespace App\Http\Controllers\API\v1\companyUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Move;
use App\UpliftMoves;
use App\MoveItems;
use App\ContainerItem;
use App\MoveConditionImage;
use App\MoveSubItems;
use App\MoveItemCondition;
use App\MoveItemConditionSide;
use App\ScreeningItemCategory;
use App\ItemLabel;
use Storage;
use App\Helpers\CompanyAdmin;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemController extends BaseController
{
    public function manageUpliftICR(Request $request)
    {
        Log::debug('ItemController::manageUpliftICR, route - items/manage, debug 1', ['move_id' => $request->move_id]);
        $company_id = (int) CompanyAdmin::getCompanyUserCompany();
        // if(Move::where('company_id',$company_id)->where([['id','=',$request->move_id],['type_id','=',1]])->exists()
        if (!Move::where([['id', '=', $request->move_id], ['type_id', '=', 1]])->exists()) {
            Log::debug('~~~~~~> Move is not a type 1, Please contact your office. The status of this inventory must be changed to ‘In Progress’', ['move_id' => $request->move_id]);
            return $this->apiError("Please contact your office. The status of this inventory must be changed to ‘In Progress’++++++++++");

        }
        if (!UpliftMoves::where([['move_id', '=', $request->move_id], ['status', '=', 1]])->exists()) {
            Log::debug('~~~~~~> Up Lift Move status is not a in progress, Please contact your office. The status of this inventory must be changed to ‘In Progress’', ['move_id' => $request->move_id]);
            UpliftMoves::where('move_id', $request->move_id)->update(['status' => 1]);
            // return $this->apiError("Please contact your office. The status of this inventory must be changed to ‘In Progress’-----------------");
        }
        Log::debug('ItemController::manageUpliftICR, route - items/manage, debug 2');
        $move_item_array_data = [];
        $sub_item_array_data = [];
        $move_condition_image_array = [];
        $move_item_condition_side_array = [];
        // Added by JG VPN - 22-02-2024

        $item_count = count($request->items);
        if (!($item_count <= 1500)) {
            Log::debug('ItemController::manageUpliftICR, route - items/manage, Item limit exceeds. You cannot add items more than 1500.');
            return $this->apiError("Item limit exceeds. You cannot add items more than 1500.");
        }



        foreach ($request->items as $itemKey => $item) {
            if (!empty($item['id'])) {
                $moveItem = MoveItems::where('id', $item['id'])->first();

                if (!$moveItem) {
                    return $this->apiError("No item found for given item ID!");
                }

                try {
                    $moveItem->move_id = $request->move_id;
                    $moveItem->item_id = null;
                    $moveItem->item = null;
                    if (empty($item['cartoon_item_details'])) {
                        $moveItem->item_id = isset($item['item_details']['id']) ? $item['item_details']['id'] : null;
                        $moveItem->item = isset($item['item_details']['item']) ? $item['item_details']['item'] : null;
                    }
                    $moveItem->screening_category_id = isset($item['category_id']) ? $item['category_id'] : null;
                    $moveItem->packer_id = isset($item['packer_id']) ? $item['packer_id'] : null;
                    $moveItem->item_number = isset($item['item_number']) ? $item['item_number'] : null;
                    $moveItem->move_type = 1;
                    $moveItem->is_overflow = isset($item['is_overflow']) ? $item['is_overflow'] : 0;
                    $moveItem->room_id = isset($item['room_id']) ? $item['room_id'] : null;
                    $moveItem->save();
                    // Log::debug('ItemController::manageUpliftICR, route - items/manage, debug 3.1: move item updated',["move_item_id" => $moveItem->id]);
                } catch (Exception $e) {
                    Log::debug('ItemController::manageUpliftICR, route - items/manage, debug 3.1: move_item exception', ["Exception" => $e->getMessage()]);
                }

                MoveSubItems::where('move_item_id', $moveItem->id)->delete();

                if (!empty($item['sub_item'])) {
                    $sub_item_id = isset($item['sub_item']['id']) ? $item['sub_item']['id'] : null;
                    // Log::debug('ItemController::manageUpliftICR, route - items/manage, debug 101: before create move sub item');
                    // Added by JG VPN - 22-02-2024
                    $sub_item_array_data[] = [
                        'move_item_id' => $moveItem->id,
                        'sub_item_id' => $sub_item_id
                    ];
                    // $move_sub_item_last_insert_id = $this->createMoveSubItem($moveItem->id,$sub_item_id);
                    // Log::debug('ItemController::createMoveSubItem, route - items/manage, debug 4.1',["move_sub_item_last_insert_id"=>$move_sub_item_last_insert_id]);
                } else {
                    if (!empty($item['cartoon_item_details'])) {
                        // Log::debug('ItemController::manageUpliftICR, route - items/manage, debug 103.1: cartoon_item_details is not empty, print data, cartoon_item_details:',$item['cartoon_item_details']);
                        foreach ($item['cartoon_item_details'] as $cartoonItems) {

                            if (!empty($cartoonItems)) {
                                if ((int) $cartoonItems['id'] == 0) {
                                    // Added by JG VPN - 22-02-2024

                                    // $ItemLabel = new ItemLabel();
                                    // // $ItemLabel->item = isset($cartoonItems['sub_item']['item']) ? $cartoonItems['sub_item']['item'] : null;
                                    // $ItemLabel->item = isset($cartoonItems['item']) ? $cartoonItems['item'] : null;
                                    // $ItemLabel->parent_id = 0;
                                    // $ItemLabel->parent_item_id = 0;
                                    // // $ItemLabel->item_type = isset($cartoonItems['sub_item']['item_type']) ? $cartoonItems['sub_item']['item_type'] : null;
                                    // $ItemLabel->item_type =  isset($cartoonItems['item']) ? $cartoonItems['item'] : null;
                                    // $ItemLabel->is_master = 0;
                                    // $ItemLabel->save();

                                    $ItemLabelArray = [
                                        'item' => isset($cartoonItems['item']) ? $cartoonItems['item'] : null,
                                        'parent_id' => 0,
                                        'parent_item_id' => 0,
                                        'item_type' => isset($cartoonItems['item']) ? $cartoonItems['item'] : null,
                                        'is_master' => 0
                                    ];
                                    $ItemLabel = $this->insertItemLabel($ItemLabelArray);

                                    // Log::debug('ItemController::manageUpliftICR, route - items/manage, debug 102: before create move sub item, print data, $cartoonItems:',$cartoonItems);
                                    $sub_item_array_data[] = [
                                        'move_item_id' => $moveItem->id,
                                        'sub_item_id' => $ItemLabel->id
                                    ];
                                    // $move_sub_item_last_insert_id = $this->createMoveSubItem($moveItem->id,$ItemLabel->id);
                                    // Log::debug('ItemController::createMoveSubItem, route - items/manage, debug 4.2',["move_sub_item_last_insert_id"=>$move_sub_item_last_insert_id]);
                                }
                            }
                        }
                    }
                }
                $moveItemCondition = MoveItemCondition::where('move_item_id', $moveItem->id)->where('move_type', 1)->pluck('id');

                if (!empty($item['condition'])) {
                    $moveItemConditionArray = [];
                    foreach ($item['condition'] as $conditionKey => $condition) {
                        // Added by JG VPN - 22-02-2024

                        // $move_item_condition = new MoveItemCondition();
                        // $move_item_condition->move_id = $request->move_id;
                        // $move_item_condition->move_item_id = $moveItem->id;
                        // $move_item_condition->condition_id = $condition['id'];
                        // $move_item_condition->move_type = 1;
                        // $move_item_condition->save();

                        $moveItemConditionArray[] = [
                            'move_id' => $request->move_id,
                            'move_item_id' => $moveItem->id,
                            'condition_id' => isset($condition['id']) ? $condition['id'] : null,
                            'move_type' => 1,
                        ];
                    }
                    try {
                        MoveItemCondition::insert($moveItemConditionArray);
                    } catch (Exception $e) {
                        Log::debug('ItemController::manageUpliftICR, route - items/manage, debug : move_item_condition exception', ["Exception" => $e->getMessage()]);
                    }

                    $item_condition_first_insert_id = DB::getPdo()->lastInsertId();
                    $item_condition_last_insert_id = $item_condition_first_insert_id + (count($item['condition']) - 1);
                    $item_condition_inserted_ids = range($item_condition_first_insert_id, $item_condition_last_insert_id);

                    $existing_images = MoveConditionImage::whereIn('move_condition_id', $moveItemCondition)->delete();
                    foreach ($item['condition'] as $item_condition_key => $condition) {
                        $item_condition_id = $item_condition_inserted_ids[$item_condition_key];
                        if (!empty($condition['condition_images'])) {
                            foreach ($condition['condition_images'] as $imageKey => $conditionImage) {
                                // $condition_image = new MoveConditionImage();
                                // $condition_image->move_condition_id = $move_item_condition->id;
                                // $condition_image->image = $conditionImage['image'];
                                // $condition_image->save();

                                $move_condition_image_array[] = [
                                    'move_condition_id' => $item_condition_id,
                                    'image' => $conditionImage['image'],
                                ];
                            }
                        }

                        MoveItemConditionSide::whereIn('item_condition_id', $moveItemCondition)->delete();
                        foreach ($condition['condition_side'] as $side) {

                            // $move_item_condition_side = new MoveItemConditionSide();
                            // $move_item_condition_side->item_condition_id = $move_item_condition->id;
                            // $move_item_condition_side->condition_side_id = $side['id'];
                            // $move_item_condition_side->save();

                            $move_item_condition_side_array[] = [
                                'item_condition_id' => $item_condition_id,
                                'condition_side_id' => $side['id'],
                            ];
                        }
                    }
                }
                MoveItemCondition::whereIn('id', $moveItemCondition)->where('move_type', 1)->delete();
            } else {
                if (!isset($item['item_number'])) {
                    return $this->apiError("item_number parameter is required.");
                }
                $existingMoveItem = MoveItems::select('id')->where(['move_id' => $request->move_id, 'item_number' => $item['item_number']]);
                if ($existingMoveItem->exists()) {
                    if ($moveItemConditions = MoveItemCondition::where('move_item_id', $existingMoveItem->value('id'))->get()) {
                        foreach ($moveItemConditions as $moveItemCondition) {
                            MoveItemConditionSide::where('item_condition_id', $moveItemCondition->id)->delete();
                            MoveConditionImage::where('move_condition_id', $moveItemCondition->id)->delete();
                            MoveItemCondition::where('move_item_id', $existingMoveItem->value('id'))->delete();
                        }
                    }
                    $MoveSubItems = MoveSubItems::select('sub_item_id')->where('move_item_id', $existingMoveItem->value('id'))->get();

                    if (!$MoveSubItems->isEmpty()) {
                        foreach ($MoveSubItems as $val) {
                            ItemLabel::where('id', $val['sub_item_id'])->where('is_master', '0')->delete();
                        }
                    }
                    MoveSubItems::where('move_item_id', $existingMoveItem->value('id'))->delete();
                    ContainerItem::where('move_item_id', $existingMoveItem->value('id'))->delete();
                    $existingMoveItem->delete();
                }

                $move_item_item_id = null;
                $move_item_details = null;
                if (empty($item['cartoon_item_details'])) {
                    $move_item_item_id = isset($item['item_details']['id']) ? $item['item_details']['id'] : null;
                    $move_item_details = isset($item['item_details']['item']) ? $item['item_details']['item'] : null;
                }
                $move_item_array_data[] = [
                    'move_id' => $request->move_id,
                    'item_id' => $move_item_item_id,
                    'item' => $move_item_details,
                    'screening_category_id' => isset($item['category_id']) ? $item['category_id'] : null,
                    'packer_id' => isset($item['packer_id']) ? $item['packer_id'] : null,
                    'item_number' => isset($item['item_number']) ? $item['item_number'] : null,
                    'move_type' => 1,
                    'is_overflow' => isset($item['is_overflow']) ? $item['is_overflow'] : 0,
                    'room_id' => isset($item['room_id']) ? $item['room_id'] : null,
                ];
            }
        }
        try {
            MoveItems::insert($move_item_array_data);
        } catch (Exception $e) {
            Log::debug('ItemController::manageUpliftICR, route - items/manage, debug : move_item exception', ["Exception" => $e->getMessage()]);
        }
        $move_item_first_insert_id = DB::getPdo()->lastInsertId();

        foreach ($request->items as $itemKey => $item) {

            if (empty($item['id'])) {
                $move_item_last_insert_id = $move_item_first_insert_id + ($item_count - 1);
                $item_inserted_ids = range($move_item_first_insert_id, $move_item_last_insert_id);
                $responseId = $item_inserted_ids[$itemKey];
                if (!empty($item['sub_item'])) {
                    // Log::debug('ItemController::manageUpliftICR, route - items/manage, debug 104: before create move sub item');
                    $sub_item_id = isset($item['sub_item']['id']) ? $item['sub_item']['id'] : null;
                    // Added by JG VPN - 22-02-2024
                    $sub_item_array_data[] = [
                        'move_item_id' => $responseId,
                        'sub_item_id' => $sub_item_id
                    ];
                    // $move_sub_item_last_insert_id = $this->createMoveSubItem($responseId,$sub_item_id);
                    // Log::debug('ItemController::createMoveSubItem, route - items/manage, debug 4.4',["move_sub_item_last_insert_id"=>$move_sub_item_last_insert_id]);

                } else {
                    if (!empty($item['cartoon_item_details'])) {
                        foreach ($item['cartoon_item_details'] as $cartoonItems) {
                            $cartoon_item_id = isset($cartoonItems['id']) ? $cartoonItems['id'] : null;
                            // Added by JG VPN - 22-02-2024
                            if ((int) $cartoon_item_id == 0) {
                                // $ItemLabel = new ItemLabel();
                                // $ItemLabel->item = isset($cartoonItems['item']) ? $cartoonItems['item'] : null;
                                // $ItemLabel->parent_id = 0;
                                // $ItemLabel->parent_item_id = 0;
                                // $ItemLabel->item_type = isset($cartoonItems['item_type']) ? $cartoonItems['item_type'] : null;
                                // $ItemLabel->is_master = 0;
                                // $ItemLabel->save();

                                $ItemLabelArray = [
                                    'item' => isset($cartoonItems['item']) ? $cartoonItems['item'] : null,
                                    'parent_id' => 0,
                                    'parent_item_id' => 0,
                                    'item_type' => isset($cartoonItems['item_type']) ? $cartoonItems['item_type'] : null,
                                    'is_master' => 0
                                ];
                                $ItemLabel = $this->insertItemLabel($ItemLabelArray);

                                // Log::debug('ItemController::manageUpliftICR, route - items/manage, debug 105: before create move sub item');
                                $sub_item_id = isset($item['sub_item']['id']) ? $item['sub_item']['id'] : null;
                                $sub_item_array_data[] = [
                                    'move_item_id' => $responseId,
                                    'sub_item_id' => $ItemLabel->id
                                ];
                                // $move_sub_item_last_insert_id = $this->createMoveSubItem($responseId, $ItemLabel->id);
                                // Log::debug('ItemController::createMoveSubItem, route - items/manage, debug 4.4',["move_sub_item_last_insert_id"=>$move_sub_item_last_insert_id]);
                            } else {
                                // Log::debug('ItemController::manageUpliftICR, route - items/manage, debug 106: before create move sub item');
                                // Added by JG VPN - 22-02-2024
                                $sub_item_array_data[] = [
                                    'move_item_id' => $responseId,
                                    'sub_item_id' => $cartoon_item_id
                                ];
                                // $move_sub_item_last_insert_id = $this->createMoveSubItem($responseId,$cartoon_item_id);
                                // Log::debug('ItemController::createMoveSubItem, route - items/manage, debug 4.4',["move_sub_item_last_insert_id"=>$move_sub_item_last_insert_id]);

                            }
                        }
                    }
                }

                if (!empty($item['condition'])) {
                    $moveItemConditionArray = [];
                    foreach ($item['condition'] as $conditionKey => $condition) {
                        // Added by JG VPN - 22-02-2024

                        // $move_item_condition = new MoveItemCondition();
                        // $move_item_condition->move_id = $request->move_id;
                        // $move_item_condition->move_item_id = $responseId;
                        // $move_item_condition->condition_id = isset($condition['id']) ? $condition['id'] : null;
                        // $move_item_condition->move_type = 1;
                        // $move_item_condition->save();
                        $moveItemConditionArray[] = [
                            'move_id' => $request->move_id,
                            'move_item_id' => $responseId,
                            'condition_id' => isset($condition['id']) ? $condition['id'] : null,
                            'move_type' => 1,
                        ];
                    }
                    MoveItemCondition::insert($moveItemConditionArray);

                    $item_condition_first_insert_id = DB::getPdo()->lastInsertId();
                    $item_condition_last_insert_id = $item_condition_first_insert_id + (count($item['condition']) - 1);
                    $item_condition_inserted_ids = range($item_condition_first_insert_id, $item_condition_last_insert_id);

                    foreach ($item['condition'] as $item_condition_key => $condition) {
                        $item_condition_id = $item_condition_inserted_ids[$item_condition_key];
                        if (!empty($condition['condition_images'])) {
                            foreach ($condition['condition_images'] as $imageKey => $conditionImage) {
                                // $condition_image = new MoveConditionImage();
                                // $condition_image->move_condition_id = $move_item_condition->id;
                                // $condition_image->image = isset($conditionImage['image']) ? $conditionImage['image'] : null;
                                // $condition_image->save();
                                $move_condition_image_array[] = [
                                    'move_condition_id' => $item_condition_id,
                                    'image' => isset($conditionImage['image']) ? $conditionImage['image'] : null,
                                ];
                            }
                        }

                        if (!empty($condition['condition_side'])) {
                            foreach ($condition['condition_side'] as $sideKey => $side) {

                                // $move_item_condition_side = new MoveItemConditionSide();
                                // $move_item_condition_side->item_condition_id = $move_item_condition->id;
                                // $move_item_condition_side->condition_side_id = isset($side['id']) ? $side['id'] : null;
                                // $move_item_condition_side->save();
                                $move_item_condition_side_array[] = [
                                    'item_condition_id' => $item_condition_id,
                                    'condition_side_id' => isset($side['id']) ? $side['id'] : null,
                                ];
                            }
                        }

                    }
                }
            }
        }

        // Added by JG VPN - 22-02-2024
        if (!empty($sub_item_array_data)) {
            try {
                MoveSubItems::insert($sub_item_array_data);
                // Log::debug('ItemController::manageUpliftICR, route - items/manage, debug : move sub items inserted');
            } catch (Exception $e) {
                Log::debug('ItemController::manageUpliftICR, route - items/manage, debug : move_sub_items exception', ["Exception" => $e->getMessage()]);
            }
        }
        if (!empty($move_condition_image_array)) {
            try {
                MoveConditionImage::insert($move_condition_image_array);
                // Log::debug('ItemController::manageUpliftICR, route - items/manage, debug : move condition image inserted');
            } catch (Exception $e) {
                Log::debug('ItemController::manageUpliftICR, route - items/manage, debug : move_condition_image exception', ["Exception" => $e->getMessage()]);
            }

        }
        if (!empty($move_item_condition_side_array)) {
            try {
                MoveItemConditionSide::insert($move_item_condition_side_array);
                // Log::debug('ItemController::manageUpliftICR, route - items/manage, debug : move item condition side inserted');
            } catch (Exception $e) {
                Log::debug('ItemController::manageUpliftICR, route - items/manage, debug : move_item_condition_side exception', ["Exception" => $e->getMessage()]);
            }
        }

        $response['move_id'] = $request->move_id;
        if (!empty($responseId)) {
            $response['items'] = MoveItems::where('id', $responseId)
                ->with([
                    'itemUpliftCategory',
                    'itemScreeningCategory.Category',
                    'itemPacker',
                    'itemDetails',
                    'subItems.subItemDetails',
                    'cartoonItem.cartoonItemDetails',
                    'condition.conditionDetails',
                    'condition.conditionSides.sideDetails',
                ])
                ->where('move_id', $request->move_id)
                ->get();
        }

        if (!empty($response['items'])) {
            foreach ($response['items'] as $itemKey => $item) {

                // 	echo "<pre>";
                // echo($item['cartoonItem']->cartoonItemDetails);
                // die;
                if ($item['itemScreeningCategory']) {
                    $item['item_screening_category'] = $item['itemScreeningCategory']['Category'];
                    unset($item['itemScreeningCategory']);
                }

                if ($item['item_id'] == 0) {
                    $item['sub_item'] = null;
                } else {
                    if (isset($item['subItems'])) {
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
                    if (isset($item['cartoonItem'])) {
                        $foodArray = [];
                        foreach ($item['cartoonItem'] as $cartoonItems) {
                            array_push($foodArray, json_decode(json_encode($cartoonItems['cartoonItemDetails']), true));
                        }
                        $item['cartoon_item_details'] = $foodArray;
                    }

                }


                if (!empty($item['condition'])) {
                    foreach ($item['condition'] as $conditionKey => $condition) {
                        $condition_details = $condition['conditionDetails'];
                        unset($item['condition'][$conditionKey]);
                        $item['condition'][$conditionKey] = $condition_details;
                        $sideArray = array();
                        foreach ($condition['conditionSides'] as $sideKey => $side) {
                            array_push($sideArray, $side['sideDetails']);
                            unset($condition['conditionSides'][$sideKey]);
                        }
                        $item['condition'][$conditionKey]['condition_side'] = $sideArray;
                    }
                }
            }
        }
        $this->response = $response;
        Log::debug('ItemController::manageUpliftICR, route - items/manage, Request completed - Items added successfully');
        return $this->sendResponse($this->data, "Items added successfully!");
    }

    private function insertItemLabel($data)
    {
        try {
            $itemLabel = new ItemLabel($data);
            $itemLabel->save();
            // Log::debug('ItemController::manageUpliftICR, route - items/manage, debug : item label inserted', ["item_label_id" => $itemLabel->id]);
            return $itemLabel;
        } catch (Exception $e) {
            Log::debug('ItemController::manageUpliftICR, route - items/manage, debug : item_label exception', ["Exception" => $e->getMessage()]);
        }
    }

    private function insertMoveItemCondition($data)
    {
        try {
            $move_item_condition = new MoveItemCondition($data);
            $move_item_condition->save();
            // Log::debug('ItemController::manageUpliftICR, route - items/manage, debug : move item condition inserted', ["move_item_condition" => $move_item_condition->id]);
            return $move_item_condition;
        } catch (Exception $e) {
            Log::debug('ItemController::manageUpliftICR, route - items/manage, debug : move_item_condition exception', ["Exception" => $e->getMessage()]);
        }
    }

    private function createMoveSubItem($move_item_id = null, $sub_item_id = null)
    {
        try {
            $move_sub_item = new MoveSubItems();
            $move_sub_item->move_item_id = $move_item_id;
            $move_sub_item->sub_item_id = $sub_item_id;
            $move_sub_item->save();
            Log::debug('ItemController::createMoveSubItem, route - items/manage, debug 4', ["move_sub_item->id" => $move_sub_item->id]);

            return $move_sub_item->id;
        } catch (\Exception $e) {
            // do task when error
            echo $e->getMessage();   // insert query
            Log::debug('ItemController::createMoveSubItem, route - items/manage, debug 5, exception: ', ["move_sub_item->id" => $e->getMessage()]);
            exit;
        }
        return 0;
    }
    public function assignCategory(Request $request)
    {

        try {
            $move = Move::where('id', $request->move_id)->first();

            if (is_null($move->uplift->item_count)) {
                foreach ($request->item_category as $categoryId => $itemArray) {
                    foreach ($itemArray as $moveItem) {
                        if (ScreeningItemCategory::where('move_item_id', $moveItem)->where('move_id', $request->move_id)->exists()) {
                            $categoryItems = ScreeningItemCategory::where('move_item_id', operator: $moveItem)
                                ->where('move_id', $request->move_id)
                                ->first();

                            $move_item = MoveItems::where('id', $moveItem)->update(["screening_category_id" => $categoryId]);
                            $updateItem = ScreeningItemCategory::where('move_item_id', $moveItem)->where('move_id', $request->move_id)->update(["category_id" => $categoryId]);

                        } else {
                            $categoryItems = new ScreeningItemCategory();
                        }

                        $categoryItems->category_id = $categoryId;
                        $categoryItems->move_item_id = $moveItem;
                        $categoryItems->move_id = $request->move_id;
                        $categoryItems->save();
                    }
                }
            } else {
                foreach ($request->item_category as $categoryId => $itemArray) {
                    foreach ($itemArray as $moveItem) {

                        if (!$move_item = MoveItems::where('move_id', $request->move_id)->where('move_type', $move->type_id)->where('item_number', $moveItem)->first()) {

                            $move_item = new MoveItems();
                            $move_item->move_id = $request->move_id;
                            $move_item->screening_category_id = $categoryId;
                            $move_item->item_number = $moveItem;
                            $move_item->move_type = $move->type_id;
                            $move_item->save();
                        }

                        if (ScreeningItemCategory::where('move_item_id', $move_item->id)->where('move_id', $request->move_id)->exists()) {
                            $categoryItems = ScreeningItemCategory::where('move_item_id', $move_item->id)
                                ->where('move_id', $request->move_id)
                                ->first();

                            MoveItems::where('id', $move_item->id)->update(["screening_category_id" => $categoryId]);
                            $updateItem = ScreeningItemCategory::where('move_item_id', $move_item->id)->where('move_id', $request->move_id)->update(["category_id" => $categoryId]);


                        } else {
                            $categoryItems = new ScreeningItemCategory();
                            $categoryItems->category_id = $categoryId;
                            $categoryItems->move_item_id = $move_item->id;
                            $categoryItems->move_id = $request->move_id;
                            $categoryItems->save();
                        }

                    }
                }
            }

            return $this->apiSuccess("Screening categories assigned to items successfully!");
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

    }

    public function getMoveItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'move_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }

        $response['items'] = MoveItems::with([
            //'itemCategory',
            'itemPacker',
            'itemDetails',
            'screeningCategory:id,category_name,color_code',
            'subItems.subItemDetails',
            'condition.conditionDetails',
            'condition.conditionSides.sideDetails',
        ])
            ->where('move_id', $request->move_id)
            ->get();

        foreach ($response['items'] as $itemKey => $item) {

            if (isset($item['subItems'])) {
                $sub_items = $item['subItems']['subItemDetails'];
                unset($item['subItems']);
                $item['sub_item'] = $sub_items;
            }

            if (!empty($item['condition'])) {
                foreach ($item['condition'] as $conditionKey => $condition) {
                    $condition_details = $condition['conditionDetails'];
                    unset($item['condition'][$conditionKey]);
                    $item['condition'][$conditionKey] = $condition_details;
                    $sideArray = array();
                    foreach ($condition['conditionSides'] as $sideKey => $side) {
                        array_push($sideArray, $side['sideDetails']);
                        unset($condition['conditionSides'][$sideKey]);
                    }
                    $item['condition'][$conditionKey]['condition_side'] = $sideArray;
                }
            }
        }
        // $this->response = $response;
        return $this->sendResponse($response, "Move items fetched successfully!");
    }

    public function addDescription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|integer',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }

        $moveItem = MoveItems::where('id', $request->item_id)->first();

        if ($moveItem) {
            $moveItem->description = $request->description;
            $moveItem->save();

            return $this->apiSuccess("Description added to given move item!");
        } else {
            return $this->apiError("No item found for given move ID!");
        }
    }

    public function deleteItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'move_item_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        }

        $container_item = ContainerItem::where('move_item_id', $request->move_item_id);

        if ($container_item->exists()) {
            $container_item->delete();
            return $this->apiSuccess("Move item deleted successfully!");
        } else {
            return $this->apiError("No move item found for given credentials!");
        }
    }
}
