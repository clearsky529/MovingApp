<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Move;
use App\User;
use App\UpliftMoves;
use App\MoveItems;
use App\PackageSignature;
use App\MoveType;
use App\CartonCondition;
use App\Companies;
use App\PackerCode;
use App\ConditionSide;
use App\TermsAndConditions;
use App\MoveComments;
use App\TermsAndConditionsChecked;
use Illuminate\Http\Request;

class GetIcrData
{
    /**
     * @param int $user_id User-id
     * 
     * @return string
     */

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function getIcrData($move_id, $move_type)
    {
        $data = [];
        $data['nonkika_move'] = Move::with(['roomChoice'])->where('id', $move_id)->first();
        $data['move'] = UpliftMoves::where('move_id', $move_id)->first();
        $move_conditions = array();

        if ($data['move']['item_count'] != null) {
            $data['move_type'] = $move_type;

            $data['exceptions'] = CartonCondition::all();

            $data['descriptions'] = PackerCode::all();

            $data['conditionLocations'] = Conditionside::all();

            $data['move_items'] = MoveItems::with('subItems')->where([
                ['move_id', $move_id],
                ['move_type', 5]
            ])
                ->orderBy('item_number', 'asc')
                ->get();

            foreach ($data['move_items'] as $key => $item) {
                $array1 = array();
                $blankArray = [];
                foreach ($item->deliveryCondition as $condition) {
                    // $blankArray[$condition->conditionDetails->condition] = $condition->conditionDetails->condition;
                    // array_push($blankArray, $condition->conditionDetails->condition);
                    $subArray = [];
                    foreach ($condition->conditionSides as $condition_side) {
                        array_push($subArray, $condition_side->sideDetails->side);
                    }
                    $newArr = [];
                    $newArr[$condition->conditionDetails->condition] = $subArray;
                    array_push($blankArray, $newArr);
                    $subArray = [];
                }
                $array1 = $blankArray;
                array_push($move_conditions, $array1);
            }

            // echo "<pre>";
            // print_r($move_conditions);
            // exit;

            $data['move_conditions'] = $move_conditions;

            $data['termsAndConditions'] = TermsAndConditions::all();

            $data['comments'] = MoveComments::where(['move_id' => $move_id])->get();

            if ($move_type == 5) {
                $packageSignature['pre_delivery'] = PackageSignature::where(['move_id' => $move_id, 'move_type' => 5, 'status' => 0])->first();
                $packageSignature['post_delivery'] = PackageSignature::where(['move_id' => $move_id, 'move_type' => 5, 'status' => 1])->first();
                $checked['postDeliveryChecked'] = TermsAndConditionsChecked::where(['move_id' => $move_id, 'move_type' => 5, 'move_status' => 1, 'is_checked' => 1])->pluck('tnc_id')->toArray();
                $checked['preDeliveryChecked'] = TermsAndConditionsChecked::where(['move_id' => $move_id, 'move_type' => 5, 'move_status' => 0, 'is_checked' => 1])->pluck('tnc_id')->toArray();
            } else {
                $packageSignature['uplift'] = PackageSignature::where(['move_id' => $move_id, 'move_type' => 1, 'status' => 1])->first();
                $checked['postUpliftChecked'] = TermsAndConditionsChecked::where(['move_id' => $move_id, 'move_type' => 1, 'move_status' => 1, 'is_checked' => 1])->pluck('tnc_id')->toArray();
            }

            $data['packageSignature'] = $packageSignature;
            $data['checked'] = $checked;
            return $data;
        } else {
            $data['move_type'] = $move_type;

            $data['exceptions'] = CartonCondition::all();

            $data['descriptions'] = PackerCode::all();

            $data['conditionLocations'] = Conditionside::all();

            $data['move_items'] = MoveItems::with([
                'subItems.subItemDetails',
                'subItems.cartoonItemDetails',
                'cartoonItem',
                'itemUpliftCategory',
                'deliveryCondition',
                'itemPacker',
                'roomChoice',
                'condition.conditionDetails',
                'condition.conditionSides.sideDetails',
                'condition.conditionImage',
                'deliveryCondition.conditionImage'
            ])
                ->where([
                    ['move_id', $move_id],
                    ['move_type', 1]
                ])
                ->orderBy('item_number', 'asc')
                ->get();

            $data['termsAndConditions'] = TermsAndConditions::all();

            $data['comments'] = MoveComments::where(['move_id' => $move_id])->get();

            if ($move_type == 5) {
                $packageSignature['pre_delivery'] = PackageSignature::where(['move_id' => $move_id, 'move_type' => 5, 'status' => 0])->first();
                $packageSignature['post_delivery'] = PackageSignature::where(['move_id' => $move_id, 'move_type' => 5, 'status' => 1])->first();
                $checked['postDeliveryChecked'] = TermsAndConditionsChecked::where(['move_id' => $move_id, 'move_type' => 5, 'move_status' => 1, 'is_checked' => 1])->pluck('tnc_id')->toArray();
                $checked['preDeliveryChecked'] = TermsAndConditionsChecked::where(['move_id' => $move_id, 'move_type' => 5, 'move_status' => 0, 'is_checked' => 1])->pluck('tnc_id')->toArray();
            } else {
                $packageSignature['uplift'] = PackageSignature::where(['move_id' => $move_id, 'move_type' => 1, 'status' => 1])->first();
                $checked['postUpliftChecked'] = TermsAndConditionsChecked::where(['move_id' => $move_id, 'move_type' => 1, 'move_status' => 1, 'is_checked' => 1])->pluck('tnc_id')->toArray();
            }

            $data['packageSignature'] = $packageSignature;
            $data['checked'] = $checked;

            return $data;
        }


    }

    public static function getOverflowIcrData($move_id, $move_type)
    {
        $data = [];
        $data['nonkika_move'] = Move::where('id', $move_id)->first();
        $data['move'] = UpliftMoves::where('move_id', $move_id)->first();
        $move_conditions = array();

        $data['move_type'] = $move_type;

        $data['exceptions'] = CartonCondition::all();

        $data['descriptions'] = PackerCode::all();

        $data['conditionLocations'] = Conditionside::all();

        $data['move_items'] = MoveItems::with('subItems', 'cartoonItem')->where([
            ['move_id', $move_id],
            ['move_type', 1],
            ['is_overflow', 1]
        ])
            ->orderBy('item_number', 'asc')
            ->get();

        $data['termsAndConditions'] = TermsAndConditions::all();

        $data['comments'] = MoveComments::where(['move_id' => $move_id])->get();


        $packageSignature['uplift'] = PackageSignature::where(['move_id' => $move_id, 'move_type' => 1, 'status' => 1])->first();
        $checked['postUpliftChecked'] = TermsAndConditionsChecked::where(['move_id' => $move_id, 'move_type' => 1, 'move_status' => 1, 'is_checked' => 1])->pluck('tnc_id')->toArray();

        $data['packageSignature'] = $packageSignature;
        $data['checked'] = $checked;

        return $data;
    }

    public static function getPreMoveComment($move_id, $move_type)
    {
        $comment_type = 0;
        if ($move_type == 1) {
            $move_name = "Uplift Pre Move Comments";
            $type = "uplift";
        } elseif ($move_type == 5) {
            $move_name = "Delivery Pre Move Comments";
            $type = "delivery";
        }

        $move = Move::where('id', $move_id)->first();

        $pdfData['company_name'] = $move->uplift->origin_agent;
        $pdfData['move_name'] = $move_name;
        $pdfData['origin_agent'] = $move->uplift->uplift_address;
        $pdfData['delivery_agent'] = $move->delivery->delivery_address;
        $pdfData['container_number'] = $move->container_number;
        $pdfData['title'] = $move->contact->contact_name . " : " . $move->move_number . " - " . ucfirst($type);
        $pdfData['move_type'] = $type;
        $pdfData['termsAndConditions'] = TermsAndConditions::where([
            'move_type' => $move_type,
            'move_status' => $comment_type
        ])->get()->toArray();

        if ($move_type == 1) {
            $customOrder = [1, 2, 3, 19, 4];
            // Sort the array using the custom comparison function
            usort($pdfData['termsAndConditions'], function ($a, $b) use ($customOrder) {
                $aIndex = array_search($a['id'], $customOrder);
                $bIndex = array_search($b['id'], $customOrder);
                return $aIndex - $bIndex;
            });
        }

        $pdfData['conditionCheck'] = TermsAndConditionsChecked::where([
            'move_id' => $move_id,
            'move_type' => $move_type,
            'move_status' => $comment_type,
            'is_checked' => 1
        ])
            ->pluck('tnc_id')
            ->toArray();
        $pdfData['comment'] = MoveComments::where([
            'move_id' => $move_id,
            'move_type' => $move_type,
            'move_status' => $comment_type
        ])
            ->first();
        $pdfData['packageSignature'] = PackageSignature::where([
            'move_id' => $move_id,
            'move_type' => $move_type,
            'status' => $comment_type
        ])
            ->first();
        return $pdfData;
    }

    public static function detectScreenView()
    {
        $header = app(GetIcrData::class)->request->header('User-Agent');
        $viewType = 'web';
        if (preg_match('/android|iphone|ipad|ipod|blackberry|iemobile|opera mini/i', strtolower($header))) {
            // Request is from a mobile device
            $viewType = 'mobile';
        }
        return $viewType;
    }
}

































