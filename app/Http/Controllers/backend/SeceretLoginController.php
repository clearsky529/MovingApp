<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Companies;
use App\Countries;
use App\States;
use App\Cities;
use App\{Move,ScreeningMoves,MoveContact,TransloadActivity,UserPlanAddon,UserSubscription,ContainerItem};
use App\{CompanyUser,MoveItemCondition,MoveItemConditionSide,MoveConditionImage,MoveSubItems,UpliftMoves,DeliveryMoves,TransloadMoves};
use App\{User,StripePayment,CompanyAgent,TermsAndConditionsChecked,PackageSignature,MoveComments,CommentImages,MoveItems};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Crypt;
use Validator;
use Session;

class SeceretLoginController extends Controller
{
    public function seceretLogin(Request $request)
    {
        $company = Companies::where('id',$request->id)->first();
        $user = User::where('id',$company->tbl_users_id)->first();
        $loginUserId = Auth::user()->id;
       
        if($company){
            //  $params   = $_SERVER['QUERY_STRING'];
            Session::put('company-admin',$user->id);
            Session::put('userTypeId',$loginUserId);
            return redirect()->route('company-admin.home');
         }else
          {
               return redirect()->back()->withErrors(trans('keywords.Something Wents Wrong'));
          }
    }
}
