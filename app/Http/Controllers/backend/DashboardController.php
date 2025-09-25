<?php

namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;
use App\Companies;
use App\Move;
use App\DashboardCountSetting;


class DashboardController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @return View
     */
    public function index()
    {
        $default_count = DashboardCountSetting::value('duration');

        if($default_count != "all"){

            switch ($default_count) {
                case "last_week":
                    $from_date = date('Y/m/d H:i:s', strtotime('monday last week'));
                    $to_date = date('Y/m/d 23:i:s', strtotime('sunday last week'));
                    break;

                case "last_month":
                    $from_date = date("Y/m/d H:i:s", mktime(0, 0, 0, date("m")-1, 1));
                    $to_date = date("Y/m/d 23:59:59", mktime(0, 0, 0, date("m"), 0));
                    break;

                case "last_2_month":
                    $from_date = date("Y/m/d H:i:s", mktime(0, 0, 0, date("m")-2, 1));
                    $to_date = date("Y/m/d 23:59:59", mktime(0, 0, 0, date("m"), 0));
                    break;

                case "this_week":
                    $from_date = date("Y/m/d 00:00:00", strtotime('monday this week'));
                    $to_date = date("Y/m/d H:i:s");
                    break;

                case "this_month":
                    $from_date = date("Y/m/01 00:00:00");
                    $to_date = date("Y/m/d H:i:s");
                    break;

                case "next_week":
                    $from_date = date('Y/m/d H:i:s', strtotime('monday next week'));
                    $to_date = date('Y/m/d 23:i:s', strtotime('sunday next week'));
                    break;

                case "next_month":
                    $from_date = date('Y/m/01 00:00:00', strtotime('+1 month'));
                    $to_date = date("Y/m/t 23:59:59", strtotime($from_date));
                    break;
                    
                default:
                    $from_date = date("2018/m/d 00:00:00");
                    $to_date = date("Y/m/d H:i:s");
            }   


            $move_count['uplift'] = Move::whereHas('uplift')
                                        ->where('created_at','>=',$from_date)
                                        ->where('created_at','<=',$to_date)
                                        ->count();

            $move_count['transit'] = Move::whereHas('transit')
                                        ->where('created_at','>=',$from_date)
                                        ->where('created_at','<=',$to_date)
                                        ->count();

            $move_count['transload'] = Move::whereHas('transload')
                                        ->where('created_at','>=',$from_date)
                                        ->where('created_at','<=',$to_date)
                                        ->count();

            $move_count['delivered'] = Move::whereHas('delivery')
                                        ->where('created_at','>=',$from_date)
                                        ->where('created_at','<=',$to_date)
                                        ->count();

            $move_count['inprogress_uplift'] = Move::whereHas('uplift', function($q){
                                            $q->where('status',1);
                                        })
                                        ->where('created_at','>=',$from_date)
                                        ->where('created_at','<=',$to_date)
                                        ->count();

            $move_count['inprogress_transload'] = Move::whereHas('transload', function($q){
                                            $q->where('status',1);
                                        })
                                        ->where('created_at','>=',$from_date)
                                        ->where('created_at','<=',$to_date)
                                        ->count();

            $move_count['inprogress_delivery'] = Move::whereHas('delivery', function($q){
                                            $q->where('status',1);
                                        })
                                        ->where('created_at','>=',$from_date)
                                        ->where('created_at','<=',$to_date)
                                        ->count();

            $company['all'] = Companies::where('created_at','>=',$from_date)
                                        ->where('created_at','<=',$to_date)
                                        ->whereHas('user',function($q){
                                            $q->where('status',1);
                                        })
                                        ->count();
            $company['mobility'] = Companies::where('created_at','>=',$from_date)
                                        ->where('created_at','<=',$to_date)
                                        ->where('type',1)
                                        ->whereHas('user',function($q){
                                            $q->where('status',1);
                                        })
                                        ->count();

            $company['moving'] = Companies::where('created_at','>=',$from_date)
                                        ->where('created_at','<=',$to_date)
                                        ->where('type',2)
                                        ->whereHas('user',function($q){
                                            $q->where('status',1);
                                        })
                                        ->count();

            $company['contractor'] = Companies::where('created_at','>=',$from_date)
                                        ->where('created_at','<=',$to_date)
                                        ->where('type',3)
                                        ->whereHas('user',function($q){
                                            $q->where('status',1);
                                        })
                                        ->count();

        }else{
            $move_count['uplift'] = Move::whereHas('uplift')->count();
            $move_count['transit'] = Move::whereHas('transit')->count();
            $move_count['transload'] = Move::whereHas('transload')->count();
            $move_count['delivered'] = Move::whereHas('delivery')->count();
            $move_count['inprogress_uplift'] = Move::whereHas('uplift', function($q){
                                                $q->where('status',1);
                                            })->count();
            $move_count['inprogress_transload'] = Move::whereHas('transload', function($q){
                                                $q->where('status',1);
                                            })->count();
            $move_count['inprogress_delivery'] = Move::whereHas('delivery', function($q){
                                                $q->where('status',1);
                                            })->count();

            $company['all'] = Companies::whereHas('user',function($q){
                                            $q->where('status',1);
                                        })->count(); 

            $company['mobility'] = Companies::whereHas('user',function($q){
                                                $q->where('status',1);
                                            })
                                            ->where('type',1)
                                            ->count(); 

            $company['moving'] = Companies::whereHas('user',function($q){
                                                $q->where('status',1);
                                            })
                                            ->where('type',2)
                                            ->count(); 

            $company['contractor'] = Companies::whereHas('user',function($q){
                                                $q->where('status',1);
                                            })
                                            ->where('type',3)
                                            ->count(); 

        }
        $this->company = $company;
        $this->move_count = $move_count;
        $this->approved_companies = Companies::where('status',1)->count();
        $this->suspend_companies = Companies::where('status',0)->count();

        return view('theme.admin.index',$this->data);
    }
}