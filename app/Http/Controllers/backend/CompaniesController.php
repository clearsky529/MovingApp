<?php
namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Companies;
use App\Countries;
use App\States;
use App\Cities;
use Mockery\Exception;
use App\{Move,ScreeningMoves,MoveContact,TransloadActivity,UserPlanAddon,UserSubscription,ContainerItem};
use App\{CompanyUser,MoveItemCondition,MoveItemConditionSide,MoveConditionImage,MoveSubItems,UpliftMoves,DeliveryMoves,TransloadMoves};
use App\{User,StripePayment,CompanyAgent,TermsAndConditionsChecked,PackageSignature,MoveComments,CommentImages,MoveItems};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Crypt;
use Validator;
use Session;
use Carbon\Carbon;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->states = null;
        $this->cities = null;

        if($request->isMethod('post'))
        {
            $companies = Companies::with('countryName','stateName','cityName','companyType','subscription','getRefferdCompany');

            if($request->country != '0'){
                $companies = $companies->where('country',$request->country);
                $this->states = States::where('country_id',$request->country)->get();
            }

            if($request->state && $request->state != '0'){
                $companies = $companies->where('state',$request->state);
                $this->cities = Cities::where('state_id',$request->state)->get();
            }

            if($request->city && $request->city != '0'){
                $companies = $companies->where('city',$request->city);
            }

            $this->companies = $companies->get();

        }
        else{
            $this->companies = Companies::with('countryName','stateName','cityName','subscription')->get();
        }

        $this->countries = Countries::all();
        $this->moves     = Move::with('uplift','transit','screening','transload')->groupBy('company_id')->get();

        return view('theme.admin.companies.index',$this->data);
    }

    public function reset(Request $request)
    {
        return redirect('admin/companies');
    }

    public function changeStatus(Request $request)
    {
        $company = Companies::find($request->company_id);
        $company->user->status = $request->status;
        $company->push();
    }

    public function show(Request $request,$id)
    {
        $id = \Crypt::decrypt($id);

        $all_companies       = Companies::count();
        $this->all_companies = $all_companies;

        $company        = Companies::with('countryName','stateName','cityName','companyType','getRefferdCompany')
                            ->where('id',$id)
                            ->first();
        $this->company  = $company;

        $get_referby       = Companies::where('tbl_users_id',$company->referred_by)->first();
        $this->get_referby = $get_referby;

        $suspend_companies  = Companies::where('status',0)->count();
        $move_count         = Move::where('company_id',$id)->where('archive_status',0)->count();
        $this->move_count   = $move_count;
        $user_count         = CompanyUser::where('company_id',$id)->count();
        $this->user_count   = $user_count;


        $this->active_user = CompanyUser::whereHas('company', function($q) use($id){
                              $q->where('company_id','=',$id);
                            })
                            ->whereHas('userInfo', function($q){
                                $q->where('status',1);
                            })
                            ->count();

        $this->active_user_details = CompanyUser::with('userInfo')->where('company_id',$id)
                            ->whereHas('userInfo', function($q){
                                $q->where('status',1);
                            })
                            ->get();
                            // dd($id);

        $this->delete_user = CompanyUser::whereHas('company', function($q) use($id){
                        $q->where('company_id','=',$id);
                      })
                      ->where('deleted_at','!=','null')
                      ->whereMonth('deleted_at', Carbon::now()->month)
                      ->withTrashed()
                      ->count();

        $this->delete_user_details = CompanyUser::join('users', 'users.id', '=', 'company_users.user_id')
                                ->where('company_users.company_id',$id)
                                ->where('company_users.deleted_at','!=','null')
                                ->whereMonth('company_users.deleted_at', Carbon::now()->month)
                                ->withTrashed()
                                ->get();
        // dd($this->delete_user_details);

        return view('theme.admin.companies.view',$this->data);
    }

    public function archiveshow($id)
    {
        $id = \Crypt::decrypt($id);
        $this->get_delete_data = CompanyUser::join('users', 'users.id', '=', 'company_users.user_id')
                                ->where('company_users.company_id',$id)
                                ->where('company_users.deleted_at','!=','null')
                                ->withTrashed()
                                ->get();

        // dd($this->get_delete_data);

        return view('theme.admin.companies.archive',$this->data);
    }

    public function locationFetch(Request $request)
    {
        $select     = $request->get('select');
        $value      = $request->get('value');
        $dependent  = $request->get('dependent');

        if ($dependent == 'state') {
            $data = States::where('country_id', $value)
                   ->get();
        }
        elseif($dependent == 'city') {
            $data = Cities::where('state_id', $value)->get();
        }
        $output = '<option value="0">Select '.ucfirst($dependent).'</option>';

        foreach($data as $row)
        {
            $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
        }
        echo $output;
    }

    public function findAccount(Request $request)
    {

        if(Move::where('company_id',$request->id)->exists())
        {

            $moves = Move::with('uplift','transit','screening','transload')
                            ->where('company_id',$request->id)
                            ->get();
            $arr   = [];
            foreach($moves as $move){
                try {
                    if($move->uplift['status'] == 1 || $move->screening['status'] == 1 || $move->transload['status'] == 1 || $move->delivery['status'] == 1){
                        array_push($arr,0);
                    }
                    elseif(($move->uplift['status'] == 0 || $move->uplift['status'] == 2) || ($move->screening['status'] == 0 ||  $move->screening['status'] == 2) ||($move->transload['status'] == 0 ||  $move->transload['status'] == 1) || ($move->delivery['status'] == 0 || $move->delivery['status'] == 1)){
                        array_push($arr,1);
                    }
                }
                catch (\Exception $e)
                {

                }


            }
        }
        else{
            return 1;
        }

        if(!empty($arr)){
            if (in_array(0, $arr))
            {
                return 0;
            }
            else
            {
                return 1;
            }
        }else{
            return 1;
        }

    }

    public function deleteAccount(Request $request)
    {

        $password   = $request->password;
        $companyId  = $request->id;
        $validator  = Validator::make($request->all(),
            [ 'password'       => 'required' ]);

		if ($validator->fails()){
			return response()->json(['error'=>$validator->errors()->all()]);
		}
        $login_user = Auth::user();
        if(Hash::check($password, Auth::user()->password, []))
        {
            $moves = Move::where('company_id',$companyId)->get();
            foreach($moves as $move)
            {
                TermsAndConditionsChecked::where('move_id',$move->id)->delete();
                PackageSignature::where('move_id',$move->id)->delete();
                $existing_comment = MoveComments::where('move_id','=',$move->id)->first();

                if(!is_null($existing_comment)){
                    CommentImages::where('comment_id',$existing_comment->id)->delete();
                    MoveComments::where('id',$existing_comment->id)->delete();
                }

                $existingMoveItem = MoveItems::where('move_id',$move->id)->get();

                if($existingMoveItem){
                    foreach ($existingMoveItem as $key => $value) {
                        if($moveItemConditions = MoveItemCondition::where('move_item_id',$value->value('id'))->get()){
                            if(isset($moveItemConditions))
                            {
                                foreach ($moveItemConditions as $moveItemCondition) {
                                    MoveItemConditionSide::where('item_condition_id',$moveItemCondition->id)->delete();
                                    MoveConditionImage::where('move_condition_id',$moveItemCondition->id)->delete();
                                    MoveItemCondition::where('move_item_id',$value->id)->delete();
                                }
                            }
                        }
                        MoveSubItems::where('move_item_id',$value->id)->delete();
                        $container_id = ContainerItem::where('move_item_id',$value->id)->first();
                        if($container_id){
                            $container_id->delete();
                        }
                        MoveItems::where('id',$value->id)->delete();
                    }

                }
                UpliftMoves::where('move_id',$move->id)->delete();
                DeliveryMoves::where('move_id',$move->id)->delete();
                // TransitMoves::where('move_id',$move->id)->delete();
                TransloadMoves::where('move_id',$move->id)->delete();
                ScreeningMoves::where('move_id',$move->id)->delete();
                MoveContact::where('move_id',$move->id)->delete();
                TransloadActivity::where('move_id',$move->id)->delete();
                Move::where('id',$move->id)->delete();
            }

            $agent   = CompanyAgent::where('company_id',$companyId)->delete();

            $company = Companies::where('id',$companyId)->first();

           $companyUser = User::where('id',$company->tbl_users_id)->first();
            // dd($companyUser);
            StripePayment::where('user_id',$company->tbl_users_id)->delete();
            CompanyAgent::where('company_id',$companyId)->delete();
            UserPlanAddon::where('user_id',$companyUser->id)->delete();
            UserSubscription::where('user_id',$companyUser->id)->delete();
            Companies::where('id',$companyId)->delete();
            $companyUser1 = CompanyUser::where('company_id',$companyId)->get();
            foreach ($companyUser1 as $key => $user) {
                User::where('id',$user->user_id)->forceDelete();
            }
            User::where('id',$companyUser->id)->forceDelete();

            // $companyUser->delete();
            return 1;
        }
        else{
            return 0;
        }

    }

    // public function seceretLogin(Request $request)
    // {
    //     // dd('here');
    //     // dd($request->id);
    //     $company = Companies::where('id',$request->id)->first();
    //     $user = User::where('id',$company->tbl_users_id)->first();
    //     // dd($user);
    //     // Auth::logout();

    //     //     return redirect('admin/login');
    //     if($company){
    //         //  $params   = $_SERVER['QUERY_STRING'];
    //     // dd($params);
    //     // dd($user->id);
    //         Session::put('company-admin',$user->id);
    //         return redirect()->route('company-admin.home');
    //      }else
    //       {
    //            return redirect()->back()->withErrors(trans('keywords.Something Wents Wrong'));
    //       }
    // }
}
