<?php

namespace App\Http\Controllers\support_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Companies;
use App\Countries;
use App\States;
use App\Cities;
use App\Move;
use App\CompanyUser;
use App\User;

class CompaniesController extends Controller
{
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

        }else{
            $this->companies = Companies::with('countryName','stateName','cityName','subscription')->get();
        }

        $this->countries = Countries::all();

        return view('theme.support-admin.companies.index',$this->data);
    }

    public function reset(Request $request)
    {
        return redirect('support-admin/companies');
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
        
        $all_companies = Companies::count();
        $this->all_companies = $all_companies;

        $company = Companies::with('countryName','stateName','cityName','companyType','getRefferdCompany')
                            ->where('id',$id)
                            ->first();
        $this->company = $company;

        $suspend_companies  = Companies::where('status',0)->count();
        $move_count         = Move::where('company_id',$id)->count();
        $this->move_count = $move_count;
        $user_count         = CompanyUser::where('company_id',$id)->count();
        $this->user_count = $user_count;

        return view('theme.support-admin.companies.view',$this->data);
    }

    public function locationFetch(Request $request)
    {
        $select     = $request->get('select');
        $value      = $request->get('value');
        $dependent  = $request->get('dependent');

        if ($dependent == 'state') {
            $data = States::where('country_id', $value)
                   ->get();
        }elseif($dependent == 'city'){
            $data = Cities::where('state_id', $value)
                   ->get();
        }

        $output = '<option value="0">Select '.ucfirst($dependent).'</option>';

        foreach($data as $row)
        {
            $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
        }

        echo $output;
    }
}
