<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use App\CompanyUser;
use App\MoveType;
use App\UpliftMoves;
use App\ScreeningMoves;
use App\TransloadMoves;
use App\Moves;
use Illuminate\Support\Facades\Auth;
use DB;
use Session;


class CompanyUserDetails
{
    /**
     * @param int $user_id User-id
     * 
     * @return string
     */
    public static function getCompanyId()
    {
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $company_id = CompanyUser::where('user_id', $userId)->value('company_id');

        return json_encode($company_id);
    }

    public static function changeConditionalStatus($move_id)
    {
        $statuses = [
            1 => "uplift_moves",
            3 => "screening_moves",
            4 => "transload_moves",
            5 => "delivery_moves",
        ];

        foreach ($statuses as $statusKey => $status) {

            if ((DB::table($status)->where('move_id', $move_id)->whereIn('status', [0, 1])->exists())) {

                $move = DB::table('moves')->where('id', $move_id)->update(['type_id' => $statusKey]);

                break;
            }
        }
    }
}