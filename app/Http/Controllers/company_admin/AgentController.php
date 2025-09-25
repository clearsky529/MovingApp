<?php

namespace App\Http\Controllers\company_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Agents;
use App\CompanyUser;
use App\Companies;
use App\CompanyAgent;
use App\Countries;
use App\States;
use App\Cities;
use App\CompanyType;
use Illuminate\Validation\Rule;
use App\Helpers\CompanyAdmin;
use Illuminate\Validation\ValidationException;
use Session;

class AgentController extends Controller
{
    public function index()
    {
        $userId = '';
        if(Session::get('company-admin')){
          $userId = Session::get('company-admin');
        }
        elseif(Auth::user() != null){
          $userId = Auth::user()->id;
        }
        elseif(Session::get('company-admin') == null || Auth::user()->id == null)
        {
          return redirect()->route('/');
        }
       
    	$company_id   = Companies::where('tbl_users_id',$userId)->value('id');
        // dd($company_id);
    	$this->agents = CompanyAgent::with('companyType','countryName','stateName','cityName')->where('company_id',$company_id)->orderBy('id','desc')->get();

    	return view('theme.company-admin.agents.index',$this->data);
    }

    public function show($id)
    {
        $userId = '';
        if(Session::get('company-admin')){
          $userId = Session::get('company-admin');
        }
        elseif(Auth::user() != null){
          $userId = Auth::user()->id;
        }
        elseif(Session::get('company-admin') == null || Auth::user()->id == null)
        {
          return redirect()->route('/');
        }
        $id = \Crypt::decrypt($id);
    	$company_id  = Companies::where('tbl_users_id',$userId)->value('id');
    	$this->agent = CompanyAgent::with('companyType','countryName','stateName','cityName')->where('id',$id)->first();

    	return view('theme.company-admin.agents.view',$this->data);
    }

    public function create()
    {
        $userId = '';
        if(Session::get('company-admin')){
          $userId = Session::get('company-admin');
        }
        elseif(Auth::user() != null){
          $userId = Auth::user()->id;
        }
        elseif(Session::get('company-admin') == null || Auth::user()->id == null)
        {
          return redirect()->route('/');
        }
    	$this->companyTypes = CompanyType::all();
    	$countries          = Countries::all();
        $this->countries    = $countries;
        $this->kika_ids     = Companies::where('tbl_users_id','!=',$userId)->get();

    	return view('theme.company-admin.agents.create',$this->data);
    }

    public function store(Request $request)
    {
        $request->website = strpos($request->website, 'http') !== 0 ? "http://$request->website" : $request->website;


    	$company_id = CompanyAdmin::getCompanyId();

    	$this->validate($request, [
            'company_name'  => 'required|string|min:2',
            'email'         => 'required|email',
            'kika_id'       => [Rule::unique('company_agents')->where(function($query) use ($company_id) {
                                    $query->where('company_id', $company_id);
                                }), 'sometimes', 'nullable', 'string', 'min:2',],
            'phone'         => 'sometimes|nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
            'company_type'  => 'required',
            'status'        => 'required',
            'country'       => 'required',
            'state'         => 'required',
            'website'       => 'required',
    	],
        [
            'phone.required' => 'The contact number field is required.' ,
            'phone.regex'    => 'The contact number is not valid.' ,
            'phone.min'      => 'The contact number must be at least 6.' ,
        ]);

        if(!filter_var($request->website, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['website' => 'URL format is not correct.']);
        } 

        $userTypeId = Session::get('userTypeId');
    	$agent                    = new CompanyAgent();
    	$agent->company_id        = $company_id;
        $agent->kika_id           = $request->kika_id ? $request->kika_id : null;
    	$agent->email             = $request->email;
    	$agent->company_name      = $request->company_name;
    	$agent->company_type      = $request->company_type;
    	$agent->phone             = $request->phone;
    	$agent->status            = $request->status;
    	$agent->website           = $request->website ? $request->website : null;
    	$agent->city              = $request->city;
    	$agent->state             = $request->state;
    	$agent->country           = $request->country;
      $agent->is_kika_direct    = 0;
        $agent->created_by        = $userTypeId ? $userTypeId : null;

    	if ($agent->save()) {
    		return redirect('company-admin/agents')->with('flash_message_success', 'Agent successfully added!');
    	}
    }

    public function edit($id)
    {
        $userId = '';
        if(Session::get('company-admin')){
          $userId = Session::get('company-admin');
        }
        elseif(Auth::user() != null){
          $userId = Auth::user()->id;
        }
        elseif(Session::get('company-admin') == null || Auth::user()->id == null)
        {
          return redirect()->route('/');
        }
        
        $id = \Crypt::decrypt($id);
    	$company_id         = Companies::where('tbl_users_id',$userId)->value('id');
    	$this->companyTypes = CompanyType::all();
    	$this->countries    = Countries::all();
    	$agent = CompanyAgent::with('companyType','countryName','stateName','cityName')->where('id',$id)->first();
        $this->agent    = $agent;
    	$this->states   = States::where('country_id',$agent->country)->get();
    	$this->cities   = Cities::where('state_id',$agent->state)->get();
        $this->kika_ids = Companies::where('tbl_users_id','!=',$userId)->get();

    	return view('theme.company-admin.agents.edit',$this->data);
    }

    public function update(Request $request, $id)
    {
        $request->website = strpos($request->website, 'http') !== 0 ? "http://$request->website" : $request->website;
    
        $company_id = CompanyAdmin::getCompanyId();

    	$this->validate($request, [
        'company_name'           => 'required|string|min:2',
        'email'                  => 'required|email',
        'kika_id'                => [Rule::unique('company_agents')->where(function($query) use ($company_id, $id) {
                                    $query->where('company_id', $company_id)
                                          ->where('id','!=',$id);
                                }), 'sometimes', 'nullable', 'string', 'min:2',],
        'phone'                  => 'sometimes|nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
        'company_type'           => 'required',
        'status'                 => 'required',
        'country'                => 'required',
        'state'                  => 'required',
        'website'                => 'required',
    	],
      [
        'phone.required' => 'The contact number field is required.' ,
        'phone.regex'    => 'The contact number is not valid.' ,
        'phone.min'      => 'The contact number must be at least 6.' ,
      ]);

        if(!filter_var($request->website, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['website' => 'URL format is not correct.']);
        } 
      
    	$agent = CompanyAgent::where('id',$id)->first();
        $agent->kika_id           = $request->kika_id ? $request->kika_id : null;
    	$agent->email             = $request->email;
    	$agent->company_name      = $request->company_name;
    	$agent->company_type      = $request->company_type;
    	$agent->phone             = $request->phone;
    	$agent->status            = $request->status;
    	$agent->website           = $request->website ? $request->website : null;
    	$agent->city              = $request->city;
    	$agent->state             = $request->state;
    	$agent->country           = $request->country;
    	
    	if ($agent->save()) {
    	  return redirect('company-admin/agents')->with('flash_message_success', 'Agent successfully updated!');
    	}
    }

    public function delete($id)
    {
        $id = \Crypt::decrypt($id);
    	CompanyAgent::where('id',$id)->delete();

    	return redirect(route('company-admin.agents'))->with('flash_message_success', 'Agent successfully deleted');
    }

    public function changeStatus(Request $request)
    {
      $agent = CompanyAgent::find($request->company_id);
      $agent->status = $request->status;
      $agent->save();
    }

    public function locationFetch(Request $request)
    {
      $select    = $request->get('select');
      $value     = $request->get('value');
      $dependent = $request->get('dependent');

      if ($dependent == 'state') {
        $data = States::where('country_id', $value)
                      ->get();

      }elseif($dependent == 'city'){
        $data = Cities::where('state_id', $value)
                      ->get();

      }

      $output = '<option disabled selected>Select '.ucfirst($dependent).'</option>';

      foreach($data as $row)
      {
        $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
      }

      echo $output;
    }

    public function fetchAgent(Request $request)
    {
        // $company = Companies::where('kika_id',$request->kika_id)->value('kika_direct');
        $company = Companies::where('kika_id',$request->kika_id)->first();
        $comoanyName = $company->name;
        if($company->kika_direct == 1){
          return $comoanyName;
        }
        $output['company'] = Companies::where('kika_id',$request->kika_id)->first();

        if ($output['company']) {
            $states = States::where('country_id', $output['company']['country'])->get();

            $output['state'] = '<option disabled selected>Select state</option>';

            foreach($states as $state)
            {
                $output['state'] .= '<option value="'.$state->id.'">'.$state->name.'</option>';
            }

            $cities = Cities::where('state_id', $output['company']['state'])->get();

            $output['city'] = '<option disabled selected>Select city</option>';

            foreach($cities as $city)
            {
                $output['city'] .= '<option value="'.$city->id.'">'.$city->name.'</option>';
            }
        }

        return $output;
    }
}
