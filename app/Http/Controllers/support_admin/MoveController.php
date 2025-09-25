<?php

namespace App\Http\Controllers\support_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Move;
use App\Companies;
use App\DeliveryMoves;
use App\UpliftMoves;

class MoveController extends Controller
{
    public function index(Request $request,$activeTab = null)
    {
        $activeTab = '#'.$activeTab;
        $this->activeTab = $activeTab;

    	if($request->isMethod('post')) 
    	{
    	    $moves = Move::with('type','company')->where('archive_status',0);
            if($request->from_date && $request->to_date){

                $request->from_date = date_create($request->from_date);
                $request->to_date = date_create($request->to_date);

                $request->from_date = date_format($request->from_date,"Y-m-d H:i:s");
                $request->to_date = date_format($request->to_date,"Y-m-d 23:i:s"); 

                $moves = $moves->where('created_at', '>=', $request->from_date)
                             ->where('created_at', '<=', $request->to_date);

            }
            if ($request->company_id != "all") {
                $moves = $moves->where('company_id',$request->company_id);
            }
            $moves = $moves->orderBy('id','desc')->get();
            $this->moves = $moves;
    	}else{
	    	$moves = Move::with('type','company')->where('archive_status',0)->orderBy('id','desc')->get();
            $this->moves = $moves;
    	}

    	$companies = Companies::get();
        $this->companies = $companies;
    	return view('theme.support-admin.move.index',$this->data);

    }

    public function show($id)
    {
        $id = \Crypt::decrypt($id);

        $this->uplift_move = UpliftMoves::where('id',$id)->first();
    	
        return view('theme.support-admin.move.view',$this->data);
    }

    public function showDelivery($id)
    {
        $id = \Crypt::decrypt($id);

        $this->delivery_move = DeliveryMoves::where('id',$id)->first();

        return view('theme.support-admin.move.delivery_view',$this->data);
    }
}
