<?php

namespace App\Http\Controllers\company_admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Helpers\GetIcrData;
use App\{Move, User};
use App\MoveType;
use App\MoveContact;
use App\UpliftMoves;
use App\DeliveryMoves;
use App\TransitMoves;
use App\TransloadMoves;
use App\ScreeningMoves;
use App\CompanyAgent;
use App\TransloadActivity;
use App\{ScreeningCategories, ScreeningItemCategory};
use App\Companies;
use App\MoveItems;
use App\CartonCondition;
use App\MoveContainer;
use App\MoveItemCondition;
use App\PackerCode;
use App\ConditionSide;
use App\TermsAndConditions;
use App\PackageSignature;
use App\TermsAndConditionsChecked;
use App\{
    MoveComments,
    CommentImages,
    MoveItemConditionSide,
    MoveConditionImage,
    MoveSubItems,
    ContainerItem,
    RiskAssessment,
    RiskAssessmentDetail,
    RiskTitles
};
use App\Helpers\CompanyAdmin;
use App\Helpers\CompanyUserDetails;
use App\Mail\SendICR;
use Crypt;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Mail;
use File;
use Mpdf\Mpdf;
use Session;
use DataTables;
use Kirschbaum\PowerJoins\PowerJoins;

class MovesController extends Controller
{
    public function index(Request $request, $activeTab = null)
    {
        $this->userId = '';
        if (Session::get('company-admin')) {
            $user = Session::get('company-admin');
            $this->userId = User::where('id', $user)->first();
        } elseif (Auth::user() != null) {
            $this->userId = Auth::user();
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $this->activeTab = '#' . $activeTab;
        $company_id = CompanyAdmin::getCompanyId();

        $company = Companies::where('id', $company_id)->first();
        // dd($company);
        $companyAgent = CompanyAgent::where('kika_id', $company->kika_id)->latest()->first();
        // dd($this->userId);
        // $this->moves = Move::with(['uplift','transit','screening','transload','delivery','contact'])


        // $update_uplift_move = Move::where('id',$company->id)->update(['is_completed_icr',1]);
        // $move = new Move();
        // $move->id   = $move->id;
        // $move->is_completed_icr   = 1;
        // $move->save();
//        dd($this->data);
        if ($request->ajax()) {

            $activeTabFilter = $request->activeTabFilter;
            $orderBy = [
                0 => "status",
                1 => "date",
                2 => "move",
                3 => "customer",
                4 => "agent",
                5 => "controlling_agent",
                6 => "volume",
            ];
            // Relations and sort configurations
            $sortColumns = [
                'uplift' => [
                    'date' => 'date',
                    'move' => 'move_number',
                    'agent' => 'origin_agent',
                    'status' => 'uplift_moves.status',
                    'customer' => 'contact_name',
                    'controlling_agent' => 'controlling_agent',
                    'volume' => 'volume',

                ],
                'delivery' => [
                    'date' => 'date',
                    'move' => 'move_number',
                    'agent' => 'delivery_agent',
                    'status' => 'delivery_moves.status',
                    'customer' => 'contact_name',
                    'controlling_agent' => 'controlling_agent',
                    'volume' => 'volume',
                ],
                'transload' => [
                    'date' => 'created_at',
                    'move' => 'move_number',
                    'agent' => 'controlling_agent',
                    'status' => 'transload_moves.status',
                    'customer' => 'contact_name',
                    'controlling_agent' => 'controlling_agent',
                    'volume' => 'volume',
                ],
                'screening' => [
                    'date' => 'created_at',
                    'move' => 'move_number',
                    'agent' => 'controlling_agent',
                    'status' => 'status',
                    'customer' => 'contact_name',
                    'controlling_agent' => 'controlling_agent',
                    'volume' => 'volume',
                ],
            ];
            $joinTables = [
                'uplift' => 'uplift_moves',
                'delivery' => 'delivery_moves',
                'transload' => 'transload_moves',
                'screening' => 'screening_moves'
            ];
            $with = [
                'uplift',
                'delivery',
                'transload',
                'screening',
                'contact',
                'upliftRiskAssessment',
                'deliveryRiskAssessment',
            ];
            // Initialize query with relations
            $data = Move::with($with)
                ->select([DB::raw('moves.*')])
                ->leftJoinRelationship($activeTabFilter)
                ->where('archive_status', '<=', 0)
                ->where(function ($query) use ($company_id) {
                    $query->where('company_id', $company_id)
                        ->orWhere('foreign_controlling_agent', $company_id)
                        ->orWhere('foreign_origin_agent', $company_id)
                        ->orWhere('foreign_destination_agent', $company_id);
                });
            // Sorting by Customer
            if (isset($request->order) && $orderBy[$request->order[0]['column']] == 'customer') {
                $data->leftJoinRelationship('contact');
            }
            // Filter by Status
            if (isset($request->statusFilter) && $request->statusFilter == 'completed') {
                $data->whereHas($activeTabFilter, function ($query) {
                    $query->whereIn('status', [0, 1]);
                });
            }


            $data->whereNotNull("{$joinTables[$activeTabFilter]}.move_id");
            // Search
            if (isset($request->search['value']) && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $searchTerms = explode(' ', $searchValue); // Split the search string into terms

                $data->where(function ($query) use ($searchTerms, $activeTabFilter) {
                    foreach ($searchTerms as $term) {
                        $query->where(function ($subQuery) use ($term, $activeTabFilter) {
                            $subQuery->where('move_number', 'like', "%$term%")
                                ->orWhereHas('contact', function ($contactQuery) use ($term) {
                                    $contactQuery->where('contact_name', 'like', "%$term%");
                                })->orWhere('controlling_agent', 'like', "%$term%");

                            if ($activeTabFilter == 'uplift' || $activeTabFilter == 'delivery') {
                                $subQuery->orWhereHas($activeTabFilter, function ($contactQuery) use ($term, $activeTabFilter) {
                                    if ($activeTabFilter == 'uplift') {
                                        $contactQuery->where('origin_agent', 'like', "%$term%");
                                    } elseif ($activeTabFilter == 'delivery') {
                                        $contactQuery->where('delivery_agent', 'like', "%$term%");
                                    }
                                });
                            }

                        });
                    }
                });
            }
            // Sorting by Status or other columns based on active tab
            if (isset($request->order)) {
                $sortField = $orderBy[$request->order[0]['column']];
                $data->orderBy($sortColumns[$activeTabFilter][$sortField], $request->order[0]['dir']);
            } else {
                $data->orderBy('id', 'desc');
            }


            $totalRecords = Move::query()->count();
            $filteredRecords = $data->count();

            /*$sql = $data->toSql();
           $bindings = $data->getBindings();
           $fullSql = \Str::replaceArray('?', $bindings, $sql);
           dd($fullSql);*/

            //            dd($data->get()->toArray());
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) use ($activeTabFilter) {
                    //                    dd($row);
                    $moveStatus = isset($row->{$activeTabFilter}->status) ? $row->{$activeTabFilter}->status : '';
                    $status = '';
                    if ($moveStatus == 0) {
                        $status = '<span class="label label-primary">Pending</span>';
                    } elseif ($moveStatus == 1) {
                        $status = '<span class="label label-warning">In Progress</span>';
                    } elseif ($moveStatus == 2) {
                        $status = '<span class="label label-success">Complete</span>';
                    }

                    return $status;
                })
                ->addColumn('move_date', function ($row) use ($activeTabFilter) {
                    $moveDate = isset($row->{$activeTabFilter}->date) ? $row->{$activeTabFilter}->date : "";
                    if ($activeTabFilter == 'transload' || $activeTabFilter == 'screening') {
                        $moveDate = isset($row->transload->created_at) ? $row->transload->created_at : "";
                    }
                    if ($moveDate == "") {
                        return "-";
                    }

                    $date = new \DateTime($moveDate);
                    return $date->format('d M Y');
                })
                ->addColumn('contact', function ($row) use ($request) {
                    return $row->contact ? $row->contact->contact_name : '-';
                })
                ->addColumn('origin_agent', function ($row) use ($activeTabFilter) {
                    $agent = '';
                    if ($activeTabFilter == 'uplift') {
                        $agent = $row->uplift->origin_agent;
                    } elseif ($activeTabFilter == 'delivery') {
                        $agent = $row->delivery->delivery_agent;
                    } elseif ($activeTabFilter == 'transload' || $activeTabFilter == 'screening') {
                        $agent = $row->controlling_agent;

                    }
                    return $agent;
                })
                ->addColumn('volume', function ($row) use ($activeTabFilter) {
                    return isset($row->{$activeTabFilter}->volume) ? $row->{$activeTabFilter}->volume : "-";
                })
                ->addColumn('move_number', function ($row) {
                    return isset($row->move_number) ? $row->move_number : '-';
                })
                ->addColumn('controlling_agent', function ($row) {
                    return isset($row->controlling_agent) ? $row->controlling_agent : '-';
                })
                ->addColumn('action', function ($move) use ($activeTabFilter) {
                    if ($activeTabFilter == 'uplift') {
                        $userId = $this->userId;
                        return view('theme.company-admin.moves.action-button.uplift', compact('move', 'userId'));
                    } elseif ($activeTabFilter == 'delivery') {
                        return view('theme.company-admin.moves.action-button.delivery', compact('move'));
                    } elseif ($activeTabFilter == 'transload') {
                        return view('theme.company-admin.moves.action-button.transload', compact('move'));
                    } elseif ($activeTabFilter == 'screening') {
                        return view('theme.company-admin.moves.action-button.screening', compact('move'));
                    }
                    return false;

                })
                ->rawColumns(['volume', 'origin_agent', 'contact', 'action', 'status', 'move_date'])
                ->setTotalRecords($totalRecords)
                ->setFilteredRecords($filteredRecords)
                ->make(true);
        }

        return view('theme.company-admin.moves.index', $this->data);
    }

    public function createUplift()
    {
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $company_id = CompanyAdmin::getCompanyId();

        $this->kika_ids = CompanyAgent::where('company_id', $company_id)
            ->where('status', '=', 1)
            ->where('kika_id', '!=', null)
            //    ->where('is_kika_direct','!=',1)
            ->groupBy('kika_id')
            ->get();

        // dd($this->kika_ids);

        $this->self_company = Companies::where('tbl_users_id', $userId)->first();
        //start code by ss_24_aug
        $this->kikadirect_self_company = Companies::where('tbl_users_id', $userId)->where('kika_direct', 1)->first();
        //end code by ss_24_aug
        return view('theme.company-admin.moves.uplift.create', $this->data);
    }

    public function showUplift($id)
    {
        $id = \Crypt::decrypt($id);
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $this->uplift_move = UpliftMoves::where('id', $id)->first();
        // dd($this->uplift_move->move_id);
        // $move_id = $this->uplift_move->move_id;
        // $update_uplift_move = Move::where('id',$id)->update(['is_completed_icr'=>1]);

        return view('theme.company-admin.moves.uplift.view', $this->data);
    }

    public function showDelivery($id)
    {
        $id = \Crypt::decrypt($id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $this->delivery_move = DeliveryMoves::where('id', $id)->first();

        return view('theme.company-admin.moves.delivery.view', $this->data);
    }

    public function storeUplift(Request $request)
    {
        // dd($request->all());
        $login_companyId = CompanyAdmin::getCompanyId();
        if (Move::where('company_id', $login_companyId)->where('move_number', '=', $request->move_number)->exists()) {
            $validatedData = $request->validate(
                [
                    /*'contact_number'          => 'sometimes|nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',*/
                    'move_number' => 'required|unique:moves',
                    'name' => 'required|string|min:2',
                    'kika_id' => 'nullable|sometimes',
                    'origin_agent' => 'required|string|min:2',
                    'origin_agent_email' => 'required|email',
                    'controlling_agent' => 'required|string|min:2',
                    'controlling_agent_email' => 'required|email',
                    'volume' => 'required',
                    'uplift_address' => 'required|string|min:5',
                    'date' => 'required',
                    'contractor' => 'sometimes|nullable|string|min:2',
                    'note' => 'nullable|sometimes',
                    'item_count' => [Rule::requiredIf(fn() => $request->icr_created), 'numeric', 'gte:1']
                ],
                [
                    'contact_number.required' => 'The contact number field is required.',
                    'contact_number.regex' => 'The contact number is not valid.',
                    'contact_number.min' => 'The contact number must be at least 6.',
                ]
            );
        } else {
            $validatedData = $request->validate(
                [
                    //                'contact_number'          => 'sometimes|nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
                    'name' => 'required|string|min:2',
                    'move_number' => 'required',
                    'kika_id' => 'nullable|sometimes',
                    'origin_agent' => 'required|string|min:2',
                    'origin_agent_email' => 'required|email',
                    'controlling_agent' => 'required|string|min:2',
                    'controlling_agent_email' => 'required|email',
                    'volume' => 'required',
                    'uplift_address' => 'required|string|min:5',
                    'date' => 'required',
                    'contractor' => 'sometimes|nullable|string|min:2',
                    'note' => 'nullable|sometimes',
                    'item_count' => [Rule::requiredIf(fn() => $request->icr_created), 'numeric', 'gte:1']
                ],
                [
                    'contact_number.required' => 'The contact number field is required.',
                    'contact_number.regex' => 'The contact number is not valid.',
                    'contact_number.min' => 'The contact number must be at least 6.',
                ]
            );
        }

        // echo "<pre>";
        // print_r($request->all()); exit;

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $move = new move();
        $move->company_id = CompanyAdmin::getCompanyId();

        // dd($request->controlling_agent_kika_id);

        if ($request->controlling_agent_kika_id != "Self" && $request->controlling_agent_kika_id != "none" && $request->controlling_agent_kika_id != "") {
            // dd("if");
            $controlling_agent_kika_id = CompanyAgent::where('id', $request->controlling_agent_kika_id)->value('kika_id');
            // dd($controlling_agent_kika_id);
            $agent_company = Companies::where('kika_id', $controlling_agent_kika_id)->value('id');

            $move->foreign_controlling_agent = $agent_company;
        }


        if ($request->contractor_agent_kika_id != "0") {
            $sub_contractor_kika_id = CompanyAgent::where('id', $request->contractor_agent_kika_id)->value('kika_id');
            $contractor_company = Companies::where('kika_id', $sub_contractor_kika_id)->value('id');

            $move->foreign_origin_contractor = $contractor_company;
        }

        if ($request->kika_id > "0") {
            $kika_id = CompanyAgent::where('id', $request->kika_id)->value('kika_id');
            $origin_company = Companies::where('kika_id', $kika_id)->value('id');

            // echo "<pre>"; print_r($origin_company); exit;

            $move->foreign_origin_agent = $origin_company;
            $move->origin_agent = $request->kika_id != "0" ? $request->kika_id : $request->origin_agent;
        }
        if ($request->hidden_kika_id == "") {
            $move->foreign_origin_agent = $move->company_id;
            $move->origin_agent = $move->company_id;
        }


        if ($request->controlling_agent_kika_id == "self") {
            $company_kikaId = Companies::where('id', $login_companyId)->value('kika_id');
            $controllingAgent = CompanyAgent::where('kika_id', $company_kikaId)->value('kika_id');
        } elseif ($request->controlling_agent_kika_id == null) {
            $company_kikaId = Companies::where('id', $login_companyId)->value('kika_id');
            $controllingAgent = CompanyAgent::where('kika_id', $company_kikaId)->value('kika_id');

        } elseif ($request->controlling_agent_kika_id == "none") {
            $controllingAgent = null;
        } else {
            $controllingAgent = $controlling_agent_kika_id;
        }
        // dd($controllingAgent);
        $userTypeId = Session::get('userTypeId');

        $move->move_number = $request->move_number;
        $refNum = Companies::where('tbl_users_id', $userId)->first();
        $move->reference_number = $refNum->kika_id . ' - ' . $request->move_number;
        $move->controlling_agent_kika_id = $controllingAgent;
        $move->controlling_agent = $request->controlling_agent;
        $move->controlling_agent_email = $request->controlling_agent_email;
        // $move->origin_agent              = $request->kika_id != "0" ? $request->kika_id : $request->origin_agent;
        $move->is_origin_agent_kika = $request->kika_id != "0" ? 1 : 0;
        $move->status = 0;
        $move->type_id = 1;
        if ($request->email) {
            $move->is_email_optional = 1;
        } else {
            $move->is_email_optional = 0;
        }
        $move->created_by = $userTypeId ? $userTypeId : null;

        // echo "<pre>"; print_r($origin_company); exit;

        $move->save();

        $contact_user = new MoveContact();
        $contact_user->move_id = $move->id;
        $contact_user->contact_name = $request->name;
        $contact_user->email = $request->email ? $request->email : $refNum->email;
        //$contact_user->contact_number = $request->contact_number;
        $contact_user->contact_number = '';

        $uplift_user = new UpliftMoves();
        $uplift_user->move_id = $move->id;
        $uplift_user->volume = $request->volume;
        $uplift_user->uplift_address = $request->uplift_address;
        $uplift_user->origin_agent_kika_id = $request->kika_id != "0" ? CompanyAgent::where('id', $request->kika_id)->value('kika_id') : null;
        $uplift_user->origin_agent = $request->origin_agent;
        $uplift_user->origin_agent_email = $request->origin_agent_email;
        $date = Carbon::parse($request->date);
        $uplift_user->date = $date->format('Y-m-d');
        $uplift_user->vehicle_registration = $request->vehicle_registration ? $request->vehicle_registration : null;
        $uplift_user->container_number = $request->container_number ? $request->container_number : null;
        $uplift_user->sub_contactor_kika_id = $request->contractor_agent_kika_id != "0" ? CompanyAgent::where('id', $request->contractor_agent_kika_id)->value('kika_id') : null;
        $uplift_user->sub_contactor = $request->contractor;
        $uplift_user->sub_contactor_email = $request->contractor_email;
        $uplift_user->note = $request->note ? $request->note : null;
        $uplift_user->is_icr_created = $request->icr_created ? 1 : 0;
        $uplift_user->item_count = $request->item_count ? $request->item_count : null;

        if ($uplift_user->item_count == null) {
            $uplift_user->status = 0;
        } else {
            $uplift_user->status = 2;
        }

        $this->kikadirect_self_company = Companies::where('tbl_users_id', $userId)->where('kika_direct', 1)->first();

        if ($contact_user->save() && $uplift_user->save()) {
            if ($request->create_delivery) {
                $company_id = CompanyAdmin::getCompanyId();

                $kika_ids = CompanyAgent::where('company_id', $company_id)
                    ->where('company_type', '!=', 1)
                    ->where('kika_id', '!=', null)
                    ->get();

                return redirect()->route('company-admin.moves.create-delivery', ['id' => Crypt::encrypt($move->id)])->with('flash_message_success', 'Uplift successfully created!');
            } else {
                return redirect('company-admin/move')->with('flash_message_success', 'Uplift successfully created!');
            }
        } else {
            return redirect('company-admin/move')->with('flash_message_error', 'Something went wrong!');
        }
    }

    public function createDelivery($id)
    {
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $id = \Crypt::decrypt($id);

        $company_id = CompanyAdmin::getCompanyId();

        $this->kika_ids = CompanyAgent::where('company_id', $company_id)
            ->where('status', '=', 1)
            ->where('kika_id', '!=', null)
            // ->where('is_kika_direct','!=',1)
            ->groupBy('kika_id')
            ->get();

        $this->self_company = Companies::where('tbl_users_id', $userId)->first();

        //start code by ss_24_aug
        $this->kikadirect_self_company = Companies::where('tbl_users_id', $userId)->where('kika_direct', 1)->first();
        //end code by ss_24_aug

        $move = Move::with('uplift')->where('company_id', $company_id)->where('id', $id)->first();

        $this->move = $move;
        $this->uplift_user = $move->uplift;
        $this->contact = $move->contact;

        return view('theme.company-admin.moves.delivery.create', $this->data);
    }

    public function storeDelivery(Request $request)
    {
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $validatedData = $request->validate([
            'volume' => 'required',
            'name' => 'required|string|min:2',
            //'contact_number'           => 'sometimes|nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
            'destination_address' => 'required|string|min:5',
            'kika_id' => 'nullable|sometimes',
            'destination_agent' => 'required|string|min:2',
            'destination_agent_email' => 'required|email',
            'date' => 'required',
            'contractor' => 'sometimes|nullable|string|min:2',
            'vehicle_registration' => 'sometimes|nullable|string|min:3',
            'container_number' => 'sometimes|nullable|string|min:2',
            'note' => 'nullable|sometimes'
        ]);

        $company_id = CompanyAdmin::getCompanyId();
        $move = Move::where('company_id', $company_id)->where('id', $request->move_id)->first();

        if ($request->contractor_agent_kika_id != "0") {

            $sub_contractor_kika_id = CompanyAgent::where('id', $request->contractor_agent_kika_id)->value('kika_id');
            $contractor_company = Companies::where('kika_id', $sub_contractor_kika_id)->value('id');

            $move->foreign_destination_contractor = $contractor_company;
            $move->save();
        }
        if ($request->kika_id != "0") {

            $kika_id = CompanyAgent::where('id', $request->kika_id)->value('kika_id');
            $destination_company = Companies::where('kika_id', $kika_id)->value('id');
            // dd($destination_company);
            $move->foreign_destination_agent = $destination_company;
            $move->save();
        }

        $delivery_move = new DeliveryMoves();
        $delivery_move->move_id = $request->move_id;
        $delivery_move->volume = $request->volume;
        $delivery_move->delivery_address = $request->destination_address;
        $delivery_move->delivery_agent_kika_id = $request->kika_id != "0" ? CompanyAgent::where('id', $request->kika_id)->value('kika_id') : null;
        $delivery_move->delivery_agent = $request->destination_agent;
        $delivery_move->delivery_agent_email = $request->destination_agent_email;
        $date = Carbon::parse($request->date);
        $delivery_move->date = $date->format('Y-m-d');
        $delivery_move->sub_contactor_kika_id = $request->contractor_agent_kika_id != "0" ? CompanyAgent::where('id', $request->contractor_agent_kika_id)->value('kika_id') : null;
        $delivery_move->sub_contactor = $request->contractor;
        $delivery_move->sub_contactor_email = $request->contractor_email;
        $delivery_move->vehicle_registration = $request->vehicle_registration ? $request->vehicle_registration : null;
        $delivery_move->container_number = $request->container_number ? $request->container_number : null;
        $delivery_move->status = 0;
        $delivery_move->note = $request->note ? $request->note : null;

        $request->kika_id != "0" ? Move::where('id', $request->move_id)->update(['destination_agent' => $request->kika_id, 'is_destination_agent_kika' => 1]) : Move::where('id', $request->move_id)->update(['destination_agent' => $request->destination_agent, 'is_destination_agent_kika' => 0]);

        //code by fp for assign move at 29_07_2021
        // dd($delivery_move->delivery_agent_kika_id);
        $refNum = Companies::where('tbl_users_id', $userId)->first();
        if ($refNum->kika_id != $delivery_move->delivery_agent_kika_id) {
            $assignCompanyId = Companies::where('kika_id', $delivery_move->delivery_agent_kika_id)->value('id');
            // dd($assignCompanyId);
            Move::where('id', $request->move_id)->update(['is_assign' => 1, 'assign_destination_company_id' => $assignCompanyId]);
        } else {
            Move::where('id', $request->move_id)->update(['assign_destination_company_id' => $company_id]);
        }

        $uplift_move = UpliftMoves::where('move_id', $move->id)->latest()->first();
        // dd($uplift_move);
        if ($request->screening) {
            $screening_move = new ScreeningMoves();
            $screening_move->move_id = $request->move_id;
            $screening_move->volume = $request->volume;
            if ($uplift_move->item_count != null && $request->screening == 'on') {
                $screening_move->status = 1;
                $update_type_id = Move::where('id', $move->id)->update(['type_id' => 3]);
            } else {
                $screening_move->status = 0;
                $update_type_id = Move::where('id', $move->id)->update(['type_id' => 1]);
            }
            $screening_move->save();

            $move->required_screening = $request->screening ? 1 : 0;
            $move->save();
        } else {
            if ($uplift_move->item_count != null) {
                // dd('here');
                $update_type_id = Move::where('id', $move->id)->update(['type_id' => 5]);
            }
            // else{
            //     if($move->item_count == null && $request->screening == 'on'){
            //         $update_type_id = Move::where('id',$move->id)->update(['type_id' => 3]);
            //     }else{
            //         $update_type_id = Move::where('id',$move->id)->update(['type_id' => 1]);
            //     }

            // }
        }

        if ($request->storage) {
            $move->required_storage = $request->storage ? 1 : 0;
            ;
            $move->save();
        }

        $move_contact = MoveContact::where('move_id', $delivery_move->move_id)->first();
        $move_contact->contact_name = $request->name;
        //$move_contact->contact_number = $request->contact_number;
        $move_contact->contact_number = '';

        if ($delivery_move->save() && $move_contact->save()) {
            return redirect('company-admin/move')->with('flash_message_success', 'Uplift and Delivery successfully created!');
        } else {
            return redirect('company-admin/move')->with('flash_message_error', 'Something went wrong!');
        }
    }

    public function editUplift($id)
    {
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $id = \Crypt::decrypt($id);
        $company_id = CompanyAdmin::getCompanyId();

        $this->uplift = $this->move = Move::select('moves.*', 'uplift_moves.*')
            ->join('uplift_moves', 'uplift_moves.move_id', '=', 'moves.id')
            ->where('uplift_moves.id', $id)
            ->first();

        $this->uplift_move = UpliftMoves::where('id', $id)->first();
        $this->self_company = Companies::where('tbl_users_id', $userId)->first();

        //start code by ss_24_aug
        $this->kikadirect_self_company = Companies::where('tbl_users_id', $userId)->where('kika_direct', 1)->first();
        //end code by ss_24_aug

        $this->kika_ids = CompanyAgent::where('company_id', $company_id)
            // ->where('company_type','!=',1)
            ->where('status', '=', 1)
            ->where('kika_id', '!=', null)
            //    ->where('is_kika_direct','!=',1)
            ->groupBy('kika_id')
            ->get();

        return view('theme.company-admin.moves.uplift.edit', $this->data);
    }

    public function updateuplift(Request $request, $id)
    {
        // dd($request->all());
        $login_companyId = CompanyAdmin::getCompanyId();
        $id = \Crypt::decrypt($id);
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $validatedData = $request->validate([
            'controlling_agent' => 'required|string|min:2',
            'controlling_agent_email' => 'required|email',
            'name' => 'required|string|min:2',
            // 'contact_number'          => 'sometimes|nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
            'volume' => 'required',
            'uplift_address' => 'required|string|min:5',
            'origin_agent' => 'required|string|min:2',
            'origin_agent_email' => 'required|email',
            'contractor' => 'sometimes|nullable|string|min:2',
            'date' => 'required',
            'status' => 'required',
            'item_count' => [Rule::requiredIf($request->icr_created), 'numeric', 'gte:1']
        ]);

        $uplift = Move::select('moves.*', 'uplift_moves.*')
            ->join('uplift_moves', 'uplift_moves.move_id', '=', 'moves.id')
            ->where('uplift_moves.id', $id)
            ->first();

        $uplift_move = UpliftMoves::where('id', $id)->first();
        $uplift_move->volume = $request->volume;
        $uplift_move->uplift_address = $request->uplift_address;
        $uplift_move->origin_agent_kika_id = $request->kika_id != "0" ? CompanyAgent::where('id', $request->kika_id)->value('kika_id') : null;
        $uplift_move->origin_agent = $request->origin_agent;
        $uplift_move->origin_agent_email = $request->origin_agent_email;
        $uplift_move->sub_contactor_kika_id = $request->contractor_agent_kika_id != "0" ? CompanyAgent::where('id', $request->contractor_agent_kika_id)->value('kika_id') : null;
        $uplift_move->sub_contactor = $request->contractor;
        $uplift_move->sub_contactor_email = $request->contractor_email;
        $uplift_move->status = $request->status;
        $date = Carbon::parse($request->date);
        $uplift_move->date = $date->format('Y-m-d');
        $uplift_move->is_icr_created = $request->icr_created ? 1 : 0;
        $uplift_move->item_count = $request->item_count ? $request->item_count : null;
        // $uplift_move->date                      = date('Y-m-d H:i:s',strtotime($request->date));

        $clientEmail = Companies::where('tbl_users_id', $userId)->first();
        $move_contact = MoveContact::where('move_id', $uplift_move->move_id)->first();
        $move_contact->contact_name = $request->name;
        $move_contact->email = $request->email ? $request->email : $clientEmail->email;
        //$move_contact->contact_number = $request->contact_number;


        if ($request->controlling_agent_kika_id == "self") {
            $company_kikaId = Companies::where('id', $login_companyId)->value('kika_id');
            // dd($company_kikaId);
            $controllingAgent = CompanyAgent::where('kika_id', $company_kikaId)->where('company_id', $login_companyId)->value('kika_id');
            // dd($controllingAgent);
            // $controllingAgent = "self";
        } elseif ($request->controlling_agent_kika_id == "") {
            $company_kikaId = Companies::where('id', $login_companyId)->value('kika_id');
            $controllingAgent = CompanyAgent::where('id', $company_kikaId)->value('kika_id');
            // $controllingAgent = "self";
        } elseif ($request->controlling_agent_kika_id == "none") {
            $controllingAgent = null;
        } else {
            $controllingAgent = CompanyAgent::where('id', $request->controlling_agent_kika_id)
                ->value('kika_id');
        }


        $move = Move::where('id', $uplift_move->move_id)->first();
        $move_number_exist = Move::where('company_id', $login_companyId)->where('id', '!=', $uplift->id)->where('move_number', $request->get('move_number'))->get();

        if (count($move_number_exist) > 0) {
            $validatedData = $request->validate([
                'move_number' => 'required|unique:moves',
            ]);
        }
        $move->move_number = $request->move_number;
        $move->reference_number = $clientEmail->kika_id . ' - ' . $move->move_number;
        $move->controlling_agent_kika_id = $controllingAgent;
        $move->controlling_agent = $request->controlling_agent;
        $move->controlling_agent_email = $request->controlling_agent_email;

        if ($request->controlling_agent_kika_id != "self" && $request->controlling_agent_kika_id != "") {

            $controlling_agent_kika_id = CompanyAgent::where('id', $request->controlling_agent_kika_id)->value('kika_id');
            $agent_company = Companies::where('kika_id', $controlling_agent_kika_id)->value('id');

            $move->foreign_controlling_agent = $agent_company;
        }

        if ($request->contractor_agent_kika_id != "0") {

            $sub_contractor_kika_id = CompanyAgent::where('id', $request->contractor_agent_kika_id)->value('kika_id');
            $contractor_company = Companies::where('kika_id', $sub_contractor_kika_id)->value('id');

            $move->foreign_origin_contractor = $contractor_company;
        }

        // code by ss_25_aug
        // if ($request->kika_id != "0") {

        //     $kika_id        = CompanyAgent::where('id',$request->kika_id)->value('kika_id');
        //     $origin_company = Companies::where('kika_id',$kika_id)->value('id');

        //     $move->foreign_origin_agent   = $origin_company;
        // }

        if ($request->kika_id > "0") {
            $kika_id = CompanyAgent::where('id', $request->kika_id)->value('kika_id');
            $origin_company = Companies::where('kika_id', $kika_id)->value('id');
            $move->foreign_origin_agent = $origin_company;
            $move->origin_agent = $request->kika_id != "0" ? $request->kika_id : $request->origin_agent;
        }
        if ($request->hidden_kika_id == "") {
            $move->foreign_origin_agent = $move->company_id;
            $move->origin_agent = $move->company_id;
        }


        // $move->origin_agent              = $request->kika_id != "0" ? $request->kika_id : $request->origin_agent;
        // end code by ss_25_aug
        $move->is_origin_agent_kika = $request->kika_id != "0" ? 1 : 0;

        if ($uplift_move->save() && $move_contact->save() && $move->save()) {
            return redirect('company-admin/move')->with('flash_message_success', 'Uplift successfully updated');
        } else {
            return redirect('company-admin/move')->with('flash_message_error', 'Something went wrong!');
        }
    }

    public function editDelivery($id)
    {
        $id = \Crypt::decrypt($id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $this->move = Move::select('moves.*', 'delivery_moves.*')
            ->join('delivery_moves', 'delivery_moves.move_id', '=', 'moves.id')
            ->where('delivery_moves.id', $id)
            ->first();
        $this->delivery_move = DeliveryMoves::where('id', $id)->first();
        // dd($this->delivery_move);
        $company_id = CompanyAdmin::getCompanyId();
        $this->self_company = Companies::where('tbl_users_id', $userId)->first();

        //start code by ss_24_aug
        $this->kikadirect_self_company = Companies::where('tbl_users_id', $userId)->where('kika_direct', 1)->first();
        //end code by ss_24_aug

        $this->kika_ids = CompanyAgent::where('company_id', $company_id)
            ->where('company_type', '!=', 1)
            ->where('status', '=', 1)
            ->where('kika_id', '!=', null)
            // ->where('is_kika_direct','!=',1)
            ->groupBy('kika_id')
            ->get();
        // dd($this->kika_ids);
        return view('theme.company-admin.moves.delivery.edit', $this->data);
    }

    public function updateDelivery(Request $request, $id)
    {
        // dd($request->all());
        $id = \Crypt::decrypt($id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|min:2',
            //'contact_number'           => 'sometimes|nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:6',
            'email' => 'required|email',
            'volume' => 'required',
            'destination_address' => 'required|string|min:5',
            'kika_id' => 'nullable|sometimes',
            'destination_agent' => 'required|string|min:2',
            'destination_agent_email' => 'required|email',
            'contractor' => 'sometimes|nullable|string|min:2',
            'date' => 'required',
            'status' => 'required',
        ]);

        $delivery_move = DeliveryMoves::where('id', $id)->first();
        $delivery_move->volume = $request->volume;
        $delivery_move->delivery_address = $request->destination_address;
        $delivery_move->delivery_agent_kika_id = $request->kika_id != "0" ? CompanyAgent::where('id', $request->kika_id)->value('kika_id') : null;
        $delivery_move->delivery_agent = $request->destination_agent;
        $delivery_move->delivery_agent_email = $request->destination_agent_email;
        $delivery_move->sub_contactor_kika_id = $request->contractor_agent_kika_id != "0" ? CompanyAgent::where('id', $request->contractor_agent_kika_id)->value('kika_id') : null;
        $delivery_move->sub_contactor = $request->contractor;
        $delivery_move->sub_contactor_email = $request->contractor_email;
        $delivery_move->status = $request->status;
        $delivery_move->date = date('Y-m-d H:i:s', strtotime($request->date));

        $move_contact = MoveContact::where('move_id', $delivery_move->move_id)->first();
        $move_contact->contact_name = $request->name;
        $move_contact->email = $request->email;
        //$move_contact->contact_number = $request->contact_number;

        $move = Move::where('id', $delivery_move->move_id)->first();

        if ($request->kika_id != "0") {

            $kika_id = CompanyAgent::where('id', $request->kika_id)->value('kika_id');
            $origin_company = Companies::where('kika_id', $kika_id)->value('id');

            // $move->foreign_origin_agent   = $origin_company;
            $move->foreign_destination_agent = $origin_company;

        }

        if ($request->contractor_agent_kika_id != "0") {

            $sub_contractor_kika_id = CompanyAgent::where('id', $request->contractor_agent_kika_id)->value('kika_id');
            $contractor_company = Companies::where('kika_id', $sub_contractor_kika_id)->value('id');

            $move->foreign_destination_contractor = $contractor_company;
            $move->save();
        }

        if ($request->screening && !ScreeningMoves::where('move_id', $delivery_move->move_id)->exists()) {
            $screening_move = new ScreeningMoves();
            $screening_move->move_id = $delivery_move->move_id;
            $screening_move->volume = $delivery_move->volume;
            $screening_move->status = 0;
            $screening_move->save();

        } elseif (!$request->screening && ScreeningMoves::where('move_id', $delivery_move->move_id)->exists()) {
            ScreeningMoves::where('move_id', $delivery_move->move_id)->delete();
        }

        $move->destination_agent = $request->kika_id != "0" ? $request->kika_id : $request->destination_agent;
        $move->required_screening = $request->screening ? 1 : 0;
        $move->required_storage = $request->storage ? 1 : 0;
        $move->is_destination_agent_kika = $request->kika_id != "0" ? 1 : 0;
        $move->save();

        //CHANGE TYPE ID by fp at 09_08_2021
        $uplift_move = UpliftMoves::where('move_id', $delivery_move->move_id)->first();
        // dd($uplift_move->status == 2 && $request->screening && $request->storage);
        if (($uplift_move->status == 0 || $uplift_move->status == 1) && $request->screening && $request->storage) {
            $move->type_id = 1;
            $move->save();
        } elseif ($uplift_move->status == 2 && $request->screening && $request->storage) {
            $move->type_id = 3;
            $move->save();
            $screen = ScreeningMoves::where('move_id', $uplift_move->move_id)->update(['status' => 1]);

        } elseif (($uplift_move->status == 0 || $uplift_move->status == 1) && !$move->screening && !$move->storage) {
            $move->type_id = 1;
            $move->save();
        } else {
            $move->type_id = 5;
            $move->save();
            $screen = ScreeningMoves::where('move_id', $uplift_move->move_id)->update(['status' => 2]);
        }

        //code by fp for assign move at 29_07_2021
        $refNum = Companies::where('tbl_users_id', $userId)->first();
        $company_id = CompanyAdmin::getCompanyId();
        if ($request->kika_id != "0") {
            if ($refNum->kika_id != $delivery_move->delivery_agent_kika_id) {
                $assignCompanyId = Companies::where('kika_id', $delivery_move->delivery_agent_kika_id)->value('id');
                Move::where('id', $delivery_move->move_id)->update(['is_assign' => 1, 'assign_destination_company_id' => $assignCompanyId]);
            } else {
                Move::where('id', $delivery_move->move_id)->update(['is_assign' => 0, 'assign_destination_company_id' => $company_id]);
            }
        }
        // stop code of assign move

        if ($delivery_move->save() && $move_contact->save()) {
            return redirect('company-admin/move/tab_3')->with('flash_message_success', 'Delivery successfully updated!');
        } else {
            return redirect('company-admin/move')->with('flash_message_error', 'Something went wrong!');
        }
    }

    public function transloadActivity($id)
    {
        $id = \Crypt::decrypt($id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $this->transload = TransloadMoves::where('id', $id)->first();

        return view('theme.company-admin.moves.transload.activity', $this->data);
    }

    public function transloadICR($id)
    {
        if (GetIcrData::detectScreenView() == 'mobile') {
            return redirect()->route('company-admin.moves.transload-icr-pdf', $id);
        }

        $id = \Crypt::decrypt($id);
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        return view('theme.company-admin.moves.transload.icr', compact('id'));
    }

    public function deliveryICR($id)
    {
        $deliveryID = \Crypt::decrypt($id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $id = DeliveryMoves::where('id', $deliveryID)->value('move_id');

        if (GetIcrData::detectScreenView() == 'mobile') {

            return redirect()->route('company-admin.moves.delivery-icr-pdf', \Crypt::encrypt($id));

        }
        return view('theme.company-admin.moves.delivery.icr', compact('id'));
    }

    public function upliftICR($id)
    {
        $upliftID = \Crypt::decrypt($id);

        if (GetIcrData::detectScreenView() == 'mobile') {
            return redirect()->route('company-admin.moves.uplift-icr-pdf', \Crypt::encrypt($upliftID));
        }
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $id = UpliftMoves::where('id', $upliftID)->value('move_id');

        return view('theme.company-admin.moves.uplift.icr', compact('id'));
    }

    public function upliftOverflowIcr($id)
    {
        if (GetIcrData::detectScreenView() == 'mobile') {
            return redirect()->route('company-admin.moves.uplift-overflowIcr-pdf', $id);
        }

        $moveID = \Crypt::decrypt($id);
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }
        $id = UpliftMoves::where('id', $moveID)->value('move_id');

        return view('theme.company-admin.moves.uplift.overflow-icr', compact('id'));
    }

    public function sendToDelivery($id)
    {
        $id = \Crypt::decrypt($id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $uplift_move = UpliftMoves::with('move.delivery')->where('id', $id)->first();
        $login_companyId = CompanyAdmin::getCompanyId();
        $move = Move::where('company_id', $login_companyId)->where('id', $uplift_move->move_id)->first();

        $icrpdfData = GetIcrData::getIcrData($move->id, 5);
        $data['move'] = $move;
        $data['move_mode'] = "Delivery Post";

        // $icr_pdf = PDF::loadView('theme.company-admin.pdf.icr',$icrpdfData)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);
        $customer = $uplift_move->move->delivery->delivery_agent_email;
        ini_set("pcre.backtrack_limit", "5000000");
        $company_admin = Companies::select('id', 'kika_id', 'icr_title_toggle', 'icr_title_image', 'title_bar_color_code')->where('kika_id', $uplift_move->origin_agent_kika_id)->first();
        $bg_color = '#d5d5d5';
        $div_html = '<div class="company-name text-black mb-10">' . $move->uplift->origin_agent . '</div>';
        $pdf_margin_top = 40;
        if ($company_admin && $company_admin->icr_title_toggle == 0) {
            if ($company_admin->title_bar_color_code != null) {
                $bg_color = $company_admin->title_bar_color_code;
            }

            if ($company_admin->icr_title_image != null) {
                $pdf_margin_top = 46;
                $s3_base_url = config('filesystems.disks.s3.url');
                $s3_image_path = $s3_base_url . 'icrtitle/';
                $title_image_path = '';
                if ($company_admin->icr_title_image != '') {
                    $title_image_path = $s3_image_path . $company_admin->icr_title_image;
                }

                $div_html = '<div class="mb-10">
                    <img style="max-width: 720px; max-height: 80px;" src="' . $title_image_path . '" alt="">
                </div>';
            }
        }

        $mpdf = new Mpdf([
            'fontDir' => str_replace('\\', '/', base_path('public/fonts/arial')),
            'fontdata' => [
                'customarialfont' => [
                    'R' => 'Arial.ttf',
                    'B' => 'Arial-bold.ttf',
                ]
            ],
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => $pdf_margin_top,
            'margin_bottom' => 16,
            'margin_header' => 12,
            'margin_footer' => 12,
        ]);

        $mpdf->SetHTMLHeader(
            '<div class="main-wrapper">
                ' . $div_html . '
                    <div class="mb-10 report-delivery" style="background-color: ' . $bg_color . '">
                        <table class="border-0">
                            <tr>
                                <td class="text-black f-14">Inventory And Condition Report - ' . $move->contact->contact_name . ' : ' . $move->move_number . ' - ' . ucfirst($move->type_id == 1 ? "Uplift" : "Delivery") . '</td>
                                <td class="text-black f-14" style="text-align: right">Page {PAGENO} of {nbpg}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>'
        );
        $mpdf->AddPage('P');
        $html = view('theme.company-admin.pdf.icr', $icrpdfData)->render();
        $mpdf->WriteHTML($html);

        Mail::send('mails.customer-icr-pdf', $data, function ($message) use ($mpdf, $move, $customer) {
            $message->to($customer)
                ->subject($move->contact->contact_name . " : " . $move->move_number . " - Delivery")
                // ->attachData($icr_pdf->output(), "delivery-icr.pdf");
                ->attachData($mpdf->Output(null, 'S'), "delivery-icr.pdf");

        });
        return redirect()->back()->with('flash_message_success', 'ICR sent successfully.');
    }

    public function deleteMove($id)
    {
        // $uplift_move = UpliftMoves::where('move_id',$id)->first();
        TermsAndConditionsChecked::where('move_id', $id)->delete();
        PackageSignature::where('move_id', $id)->delete();
        $existing_comment = MoveComments::where('move_id', '=', $id)->pluck('id')->toArray();
        if ($existing_comment) {
            CommentImages::where('comment_id', $existing_comment)->delete();
            MoveComments::where('move_id', '=', $id)->delete();
        }
        $existingMoveItem = MoveItems::where('move_id', $id)->pluck('id')->toArray();
        $moveItemConditions = MoveItemCondition::where('move_id', $id)->pluck('id')->toArray();
        $riskAssessment = RiskAssessment::where('move_id', $id)->pluck('id')->toArray();

        if ($riskAssessment) {
            RiskAssessmentDetail::where('risk_assessment_id', $riskAssessment)->delete();
            RiskAssessment::where('move_id', $id)->delete();
        }
        if ($moveItemConditions) {
            MoveItemConditionSide::where('item_condition_id', $moveItemConditions)->delete();
            MoveConditionImage::where('move_condition_id', $moveItemConditions)->delete();
            MoveItemCondition::where('move_id', $id)->delete();
        }
        if ($existingMoveItem) {
            MoveSubItems::where('move_item_id', $existingMoveItem)->delete();
            ContainerItem::where('move_item_id', $existingMoveItem)->delete();
            MoveItems::where('move_id', $id)->delete();
        }
        UpliftMoves::where('move_id', $id)->delete();
        DeliveryMoves::where('move_id', $id)->delete();
        // TransitMoves::where('move_id',$id)->delete();
        TransloadMoves::where('move_id', $id)->delete();
        ScreeningMoves::where('move_id', $id)->delete();
        MoveContact::where('move_id', $id)->delete();
        TransloadActivity::where('move_id', $id)->delete();
        Move::where('id', $id)->delete();
        // return redirect()->back()->with('flash_message_success', 'Move deleted successfully!');
    }

    public function sendEmailIcr(Request $request)
    {
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return json_encode(array('status' => 0, 'message' => 'User not found, please login again.'));
        }

        $user_role = User::join('roles', 'roles.id', '=', 'users.role_id')->where('users.id', $userId)->value('roles.name');
        if (!in_array($user_role, array('super-admin', 'company-admin'))) {
            return json_encode(array('status' => 0, 'message' => 'User does not have the right roles.'));
        }

        $uplift_move = UpliftMoves::with('move.delivery')->where('id', $request->delivery_icr_move_id)->first();

        $login_companyId = CompanyAdmin::getCompanyId();
        $move = Move::where('company_id', $login_companyId)->where('id', $uplift_move->move_id)->first();

        $icrpdfData = GetIcrData::getIcrData($move->id, 5);
        $data['move'] = $move;
        $data['move_mode'] = "Delivery Post";
        // $icr_pdf = PDF::loadView('theme.company-admin.pdf.icr',$icrpdfData)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);
        $mail_subject = $move->contact->contact_name . " - " . $move->move_number . " - Delivery ICR";
        // return redirect()->back()->with('flash_message_success', 'Delivery ICR has been sent successfully.');

        ini_set("pcre.backtrack_limit", "5000000");
        $company_admin = Companies::select('id', 'kika_id', 'icr_title_toggle', 'icr_title_image', 'title_bar_color_code')->where('kika_id', $uplift_move->origin_agent_kika_id)->first();
        $bg_color = '#d5d5d5';
        $div_html = '<div class="company-name text-black mb-10">' . $move->uplift->origin_agent . '</div>';
        $pdf_margin_top = 40;
        if ($company_admin && $company_admin->icr_title_toggle == 0) {
            if ($company_admin->title_bar_color_code != null) {
                $bg_color = $company_admin->title_bar_color_code;
            }

            if ($company_admin->icr_title_image != null) {
                $pdf_margin_top = 46;
                $s3_base_url = config('filesystems.disks.s3.url');
                $s3_image_path = $s3_base_url . 'icrtitle/';
                $title_image_path = '';
                if ($company_admin->icr_title_image != '') {
                    $title_image_path = $s3_image_path . $company_admin->icr_title_image;
                }

                $div_html = '<div class="mb-10">
                    <img style="max-width: 720px; max-height: 80px;" src="' . $title_image_path . '" alt="">
                </div>';
            }
        }

        $mpdf = new Mpdf([
            'fontDir' => str_replace('\\', '/', base_path('public/fonts/arial')),
            'fontdata' => [
                'customarialfont' => [
                    'R' => 'Arial.ttf',
                    'B' => 'Arial-bold.ttf',
                ]
            ],
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => $pdf_margin_top,
            'margin_bottom' => 16,
            'margin_header' => 12,
            'margin_footer' => 12,
        ]);

        $mpdf->SetHTMLHeader(
            '<div class="main-wrapper">
                ' . $div_html . '
                    <div class="mb-10 report-delivery" style="background-color: ' . $bg_color . '">
                        <table class="border-0">
                            <tr>
                                <td class="text-black f-14">Inventory And Condition Report - ' . $move->contact->contact_name . ' : ' . $move->move_number . ' - ' . ucfirst($move->type_id == 1 ? "Uplift" : "Delivery") . '</td>
                                <td class="text-black f-14" style="text-align: right">Page {PAGENO} of {nbpg}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>'
        );
        $mpdf->AddPage('P');
        $html = view('theme.company-admin.pdf.icr', $icrpdfData)->render();
        $mpdf->WriteHTML($html);

        Mail::send('mails.delivery-icr-pdf', $data, function ($message) use ($mpdf, $move, $request, $mail_subject) {
            $message->to($request->delivery_icr_mail)
                ->subject($mail_subject)
                ->attachData($mpdf->Output(null, 'S'), $mail_subject . ".pdf");
        });
        return json_encode(array('status' => 1, 'message' => 'Delivery ICR has been sent successfully.'));
        // return redirect()->back()->with('flash_message_success', 'Delivery ICR has been sent successfully.');

    }

    public function getAgent(Request $request)
    {
        $company_id = CompanyAdmin::getCompanyId();
        $agent = CompanyAgent::where('id', $request->agent_id)->where('company_id', $company_id)->first();
        if ($agent->is_kika_direct == 1 && $request->controllingsubscription !== 'Kika Direct' && $agent->company_id == $company_id) {
            return array(1, $agent);
        }
        // elseif($agent->is_kika_direct == 0 && $request->controllingsubscription !== 'Kika Direct'){
        // 	return array(2, $agent);
        // }
        return $agent;
    }

    public function changeStatus(Request $request)
    {
        // dd($move->id);
        switch ($request->type) {
            case "uplift":

                $move = Move::whereHas('uplift', function ($q) use ($request) {
                    $q->where('id', $request->move_id);
                })->first();


                if ($request->status != 2 && $move->transit) {
                    TransitMoves::where('id', $move->transit->id)->delete();
                } elseif ($request->status == 2 && !($move->transit)) {
                    $transit_move = new TransitMoves();
                    $transit_move->move_id = $move->id;
                    $transit_move->volume = $move->uplift->volume;
                    $transit_move->status = 1;
                    $transit_move->save();


                }

                if ($move->required_screening == 1) {
                    if ($move->screening) {
                        ScreeningMoves::where('id', $move->screening->id)->delete();
                    }
                    $screening_move = new ScreeningMoves();
                    $screening_move->move_id = $move->id;
                    $screening_move->volume = $move->uplift->volume;
                    $screening_move->status = 0;
                    $screening_move->save();
                }

                if ($move->required_storage == 1) {
                    if ($move->transload) {
                        TransloadMoves::where('id', $move->transload->id)->delete();
                    }
                    $transload_move = new TransloadMoves();
                    $transload_move->move_id = $move->id;
                    $transload_move->volume = $move->uplift->volume;
                    $transload_move->status = 0;
                    $transload_move->save();
                }

                $move->uplift->status = $request->status;
                $move->push();

                CompanyUserDetails::changeConditionalStatus($move->id);

                $redirect_tab = "tab_1";

                break;

            case "delivery":

                $move = Move::whereHas('delivery', function ($q) use ($request) {
                    $q->where('id', $request->move_id);
                })->first();

                DeliveryMoves::where('id', $request->move_id)
                    ->update(['status' => $request->status]);

                CompanyUserDetails::changeConditionalStatus($move->id);

                $redirect_tab = "tab_3";

                break;

            case "transload":

                $move = Move::whereHas('transload', function ($q) use ($request) {
                    $q->where('id', $request->move_id);
                })->first();

                TransloadMoves::where('id', $request->move_id)
                    ->update(['status' => $request->status]);

                CompanyUserDetails::changeConditionalStatus($move->id);

                $redirect_tab = "tab_4";

                break;

            case "transit":

                $move = Move::whereHas('transit', function ($q) use ($request) {
                    $q->where('id', $request->move_id);
                })->first();

                if ($move->required_screening == 1) {
                    if ($request->status != 2 && $move->screening) {
                        ScreeningMoves::where('id', $move->screening->id)->delete();
                    } elseif ($request->status == 2 && !($move->screening)) {
                        $screening_move = new ScreeningMoves();
                        $screening_move->move_id = $move->id;
                        $screening_move->volume = $move->uplift->volume;
                        $screening_move->status = 0;
                        $screening_move->save();
                    }
                }

                $move->transit->status = $request->status;
                $move->push();

                CompanyUserDetails::changeConditionalStatus($move->id);

                $redirect_tab = "tab_2";

                break;

            case "screening":

                $move = Move::whereHas('screening', function ($q) use ($request) {
                    $q->where('id', $request->move_id);
                })->first();

                if ($move->required_storage == 1) {
                    if ($request->status != 2 && $move->transload) {
                        TransloadMoves::where('id', $move->transload->id)->delete();

                    } elseif ($request->status == 2 && !($move->transload)) {
                        $transload_move = new TransloadMoves();
                        $transload_move->move_id = $move->id;
                        $transload_move->volume = $move->uplift->volume;
                        $transload_move->status = 0;
                        $transload_move->save();
                    }
                }

                $move->screening->status = $request->status;
                $move->push();

                CompanyUserDetails::changeConditionalStatus($move->id);

                $redirect_tab = "tab_5";

                break;
        }
        return $redirect_tab;
    }

    public function icrPdf($move_id)
    {
        $move_id = \Crypt::decrypt($move_id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        if (request()->segment(3) == "delivery") {
            $this->data = GetIcrData::getIcrData($move_id, 5);
        } else {
            $this->data = GetIcrData::getIcrData($move_id, 1);
        }

        ini_set("pcre.backtrack_limit", "5000000");
        $move_data = $this->data['move'];
        $company_admin = Companies::select('id', 'kika_id', 'icr_title_toggle', 'icr_title_image', 'title_bar_color_code')->where('kika_id', $move_data['origin_agent_kika_id'])->first();
        $bg_color = '#d5d5d5';
        $div_html = '<div class="company-name text-black mb-10">' . $move_data['origin_agent'] . '</div>';
        $pdf_margin_top = 40;
        if ($company_admin && $company_admin->icr_title_toggle == 0) {
            if ($company_admin->title_bar_color_code != null) {
                $bg_color = $company_admin->title_bar_color_code;
            }

            if ($company_admin->icr_title_image != null) {
                $pdf_margin_top = 46;
                $s3_base_url = config('filesystems.disks.s3.url');
                $s3_image_path = $s3_base_url . 'icrtitle/';
                $title_image_path = '';
                if ($company_admin->icr_title_image != '') {
                    $title_image_path = $s3_image_path . $company_admin->icr_title_image;
                }

                $div_html = '<div class="mb-10">
                    <img style="max-width: 720px; max-height: 80px;" src="' . $title_image_path . '" alt="">
                </div>';
            }
        }

        if ($this->data['move']['item_count'] != null && $this->data['move_type'] == 5) {
            //return view('theme.company-admin.pdf.nonkika-delivery-icr',$this->data);
            // $pdf = PDF::loadView('theme.company-admin.pdf.nonkika-delivery-icr',$this->data)->save(''.$path.'/'.$filename.'.pdf');

            // return $pdf = PDF::loadView('theme.company-admin.pdf.nonkika-delivery-icr',$this->data)
            //             ->setOptions([
            //                 'isPhpEnabled'      => true,
            //                 'isRemoteEnabled'   => true])
            //             ->stream();

            $mpdf = new Mpdf([
                'fontDir' => str_replace('\\', '/', base_path('public/fonts/arial')),
                'fontdata' => [
                    'customarialfont' => [
                        'R' => 'Arial.ttf',
                        'B' => 'Arial-bold.ttf',
                    ]
                ],
                'mode' => 'utf-8',
                'format' => 'A4', // Paper size (A4, Letter, etc.)
                'margin_left' => 12, // Left margin in millimeters
                'margin_right' => 12, // Right margin in millimeters
                'margin_top' => $pdf_margin_top, // Top margin in millimeters
                'margin_bottom' => 16, // Bottom margin in millimeters
                'margin_header' => 12, // Header margin
                'margin_footer' => 12, // Footer margin
            ]);

            $mpdf->SetHTMLHeader(
                '<div class="main-wrapper">
                    ' . $div_html . '
                        <div class="mb-10 report-delivery" style="background-color: ' . $bg_color . '">
                            <table class="border-0">
                                <tr>
                                    <td class="text-black f-14">' . $move_data->contact->contact_name . ' : ' . $move_data->move->move_number . ' - Delivery Outturn Report</td>
                                    <td class="text-black f-14" style="text-align: right">Page {PAGENO} of {nbpg}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>'
            );
            $mpdf->AddPage('P'); // You can specify 'A4', 'Letter', or custom dimensions
            $mpdf->WriteHTML(view('theme.company-admin.pdf.nonkika-delivery-icr', $this->data)->render());
            if (GetIcrData::detectScreenView() == 'mobile') {
                $filename = $move_data->contact->contact_name . ":" . $move_data->move->move_number . "- Delivery Outturn Report";
                $pdfContent = $mpdf->output($filename . '.pdf', 'D');
            }
            $pdfContent = $mpdf->Output(null, 'S');
            return response($pdfContent)->header('Content-Type', 'application/pdf');
        } else {
            // return view('theme.company-admin.pdf.icr',$this->data);
            // return $pdf = PDF::loadView('theme.company-admin.pdf.icr',$this->data)
            //                 ->setOptions([
            //                     'isPhpEnabled'      => true,
            //                     'isRemoteEnabled'   => true])
            //                 ->stream();

            $mpdf = new Mpdf([
                'fontDir' => str_replace('\\', '/', base_path('public/fonts/arial')),
                'fontdata' => [
                    'customarialfont' => [
                        'R' => 'Arial.ttf',
                        'B' => 'Arial-bold.ttf',
                    ]
                ],
                'mode' => 'utf-8',
                'format' => 'A4', // Paper size (A4, Letter, etc.)
                'margin_left' => 12, // Left margin in millimeters
                'margin_right' => 12, // Right margin in millimeters
                'margin_top' => $pdf_margin_top, // Top margin in millimeters
                'margin_bottom' => 16, // Bottom margin in millimeters
                'margin_header' => 12, // Header margin
                'margin_footer' => 12, // Footer margin
            ]);

            $mpdf->SetHTMLHeader(
                '<div class="main-wrapper">
                ' . $div_html . '
                    <div class="mb-10 report-delivery" style="background-color: ' . $bg_color . '">
                        <table class="border-0">
                            <tr>
                                <td class="text-black f-14">Inventory And Condition Report - ' . $move_data->contact->contact_name . ' : ' . $move_data->move->move_number . ' - ' . ucfirst($this->data['move_type'] == 1 ? "Uplift" : "Delivery") . '</td>
                                <td class="text-black f-14" style="text-align: right">Page {PAGENO} of {nbpg}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>'
            );
            $mpdf->AddPage('P'); // You can specify 'A4', 'Letter', or custom dimensions
            $mpdf->WriteHTML(view('theme.company-admin.pdf.icr', $this->data)->render());
            if (GetIcrData::detectScreenView() == 'mobile') {
                $filename = $move_data->contact->contact_name . ":" . $move_data->move->move_number . ($this->data['move_type'] == 1 ? " - Uplift" : " - Delivery");
                $pdfContent = $mpdf->output($filename . '.pdf', 'D');
            }

            $pdfContent = $mpdf->Output(null, 'S');
            return response($pdfContent)->header('Content-Type', 'application/pdf');
        }
    }

    public function overflowIcr($move_id)
    {
        $move_id = \Crypt::decrypt($move_id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $this->data = GetIcrData::getOverflowIcrData($move_id, 1);
        // return $pdf = PDF::loadView('theme.company-admin.pdf.uplift-overflowIcr',$this->data)
        //                 ->setOptions([
        //                     'isPhpEnabled'      => true,
        //                     'isRemoteEnabled'   => true])
        //                 ->stream();

        ini_set("pcre.backtrack_limit", "5000000");
        $move_data = $this->data['move'];
        $company_admin = Companies::select('id', 'kika_id', 'icr_title_toggle', 'icr_title_image', 'title_bar_color_code')->where('kika_id', $move_data['origin_agent_kika_id'])->first();
        $bg_color = '#d5d5d5';
        $div_html = '<div class="company-name text-black mb-10">' . $move_data['origin_agent'] . '</div>';
        $pdf_margin_top = 40;
        if ($company_admin && $company_admin->icr_title_toggle == 0) {
            if ($company_admin->title_bar_color_code != null) {
                $bg_color = $company_admin->title_bar_color_code;
            }

            if ($company_admin->icr_title_image != null) {
                $pdf_margin_top = 46;
                $s3_base_url = config('filesystems.disks.s3.url');
                $s3_image_path = $s3_base_url . 'icrtitle/';
                $title_image_path = '';
                if ($company_admin->icr_title_image != '') {
                    $title_image_path = $s3_image_path . $company_admin->icr_title_image;
                }

                $div_html = '<div class="mb-10">
                    <img style="max-width: 720px; max-height: 80px;" src="' . $title_image_path . '" alt="">
                </div>';
            }
        }

        $overflow_mpdf = new Mpdf([
            'fontDir' => str_replace('\\', '/', base_path('public/fonts/arial')),
            'fontdata' => [
                'customarialfont' => [
                    'R' => 'Arial.ttf',
                    'B' => 'Arial-bold.ttf',
                ]
            ],
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => $pdf_margin_top,
            'margin_bottom' => 16,
            'margin_header' => 12,
            'margin_footer' => 12,
        ]);

        $overflow_mpdf->SetHTMLHeader(
            '<div class="main-wrapper">
            ' . $div_html . '
                    <div class="mb-10 report-delivery" style="background-color: ' . $bg_color . '">
                        <table class="border-0">
                            <tr>
                                <td class="text-black f-14">Inventory And Condition Report - ' . $move_data->contact->contact_name . ' - ' . $move_data->move->move_number . ' - ' . ucfirst("Overflow") . '</td>
                                <td class="text-black f-14" style="text-align: right">Page {PAGENO} of {nbpg}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>'
        );
        $overflow_mpdf->AddPage('P');
        $overflow_html = view('theme.company-admin.pdf.uplift-overflowIcr', $this->data)->render();
        $overflow_mpdf->WriteHTML($overflow_html);

        if (GetIcrData::detectScreenView() == 'mobile') {
            $filename = $move_data->contact->contact_name . ":" . $move_data->move->move_number . "- Overflow";
            $pdfContent = $overflow_mpdf->Output($filename . '.pdf', 'D');
        }

        $pdfContent = $overflow_mpdf->Output(null, 'S');
        return response($pdfContent)->header('Content-Type', 'application/pdf');
    }

    public function transloadICRPDF($move_id)
    {
        $move_id = \Crypt::decrypt($move_id);
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $this->move = TransloadMoves::where('move_id', $move_id)->first();

        $move_containers_query = MoveContainer::where('move_id', $move_id);

        $this->move_containers = $move_containers_query->get();

        $move_containers_id = $move_containers_query->pluck('id');

        $this->move_items = MoveItems::where(['move_id' => $move_id])
            ->with(array(
                'container' => function ($query) use ($move_containers_id) {
                    $query->whereIn('id', $move_containers_id);
                }
            ))
            ->with('container.containerDetails', 'subItems', 'cartoonItem')
            ->get()
            ->sortBy('item_number');
        $move_conditions = array();
        $subItemArr = [];
        $blankArray = [];
        $subBlackArr = [];
        $elseSubBlackArr = [];
        $idArr = [];
        foreach ($this->move_items as $key => $item) {
            $sitem = '';
            $blankArray = [];
            $parant_item = $item->item;
            $elseSubBlackArr = [];
            $subitem_new = [];
            if ($parant_item != null) {
                $type = "kika";
                $idArr[] = $item->item_number;
                $subItem = isset($item->subItems->subItemDetails->item) ? $item->subItems->subItemDetails->item : NULL;
                if ($subItem != null) {
                    $blankArray[$subItem] = [];
                    // dd($item->transloadCondition);
                    foreach ($item->transloadCondition as $condition) {
                        // dd($condition);
                        if (!empty($condition->conditionSides)) {
                            $subArray = [];
                            foreach ($condition->conditionSides as $conditionSide) {
                                array_push($subArray, $conditionSide->sideDetails->side);
                            }

                            $newArr = [];
                            $newArr[$condition->conditionDetails->condition] = $subArray;
                            array_push($blankArray[$subItem], $newArr);
                            $subArray = [];
                        }
                        array_push($subBlackArr, $blankArray[$subItem]);
                        // dd($blankArray[$subItem]);
                    }
                } else {
                    $blankArray[$parant_item] = [];
                    foreach ($item->transloadCondition as $condition) {
                        if (count($condition->conditionSides) > 0) {
                            $subArray = [];
                            foreach ($condition->conditionSides as $conditionSide) {
                                array_push($subArray, $conditionSide->sideDetails->side);
                            }
                            $newArr = [];
                            $newArr[$condition->conditionDetails->condition] = $subArray;
                            array_push($blankArray[$parant_item], $newArr);
                            $subArray = [];
                        }
                    }

                }
                $array1 = $blankArray;
                array_push($move_conditions, $array1);
            } else {
                $elseSubBlackArr = [];
                $subitem_new = [];
                if ($item->item_id == null) {
                    if ($item->cartoonItem) {
                        foreach ($item->cartoonItem as $itm) {
                            $sitem .= $itm->cartoonItemDetails->item . ', ';
                            array_push($subitem_new, $itm->cartoonItemDetails->item);
                        }
                    }
                } else {
                    $sitem .= '';
                }

                $subitem = rtrim($sitem, ', ');
                $extraArr = [];
                $ik = 0;
                foreach ($item->transloadCondition as $condition) {
                    $idArr[] = $item->item_number;
                    $subArray = [];
                    if (count($condition->conditionSides) > 0) {
                        // array_push($subArray, $subitem_new);
                        foreach ($condition->conditionSides as $conditionSide) {
                            array_push($subArray, $conditionSide->sideDetails->side);
                        }

                        $newArr = [];
                        $newArr[$condition->conditionDetails->condition] = $subArray;
                        // print_r($condition->conditionDetails->condition);
                        array_push($elseSubBlackArr, $newArr);
                        $subArray = [];
                    } else {
                        $newArr = [];

                        $newArr[$condition->conditionDetails->condition] = $subArray;
                        // print_r($condition->conditionDetails->condition);
                        array_push($elseSubBlackArr, $newArr);
                        $subArray = [];
                    }
                }
                if (!empty($elseSubBlackArr)) {
                    if ($subitem != '') {
                        $extraArr[$subitem] = $elseSubBlackArr;
                    } else {
                        $extraArr[] = $elseSubBlackArr;
                    }
                    array_push($move_conditions, $extraArr);
                }

            }
        }
        // die;
        $this->move_conditions = $move_conditions;
        $this->idArr = $idArr;
        // $this->type = $type;
        // // die;
        // echo "<pre>";
        //  // print_r($idArr);
        // print_r($move_conditions);
        // exit;

        $this->condition_images = MoveItemCondition::where(['move_id' => $move_id, 'move_type' => 4])
            ->with('conditionImage')
            ->get();

        $this->comments = MoveComments::where(['move_id' => $move_id, 'move_type' => 4])
            ->with('image')
            ->get();

        if (GetIcrData::detectScreenView() == 'mobile') {
            $fileName = $this->move->move->contact->contact_name . ' : ' . $this->move->move->move_number . ' - Tranship Sheet';
            $pdf = PDF::loadView('theme.company-admin.pdf.transload-icr', $this->data)
                ->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);
            return $pdf->download($fileName . '.pdf');

        }
        // return view('theme.company-admin.pdf.transload-icr',$this->data);
        $pdf = PDF::loadView('theme.company-admin.pdf.transload-icr', $this->data)
            ->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true])
            ->stream();
        return $pdf;
    }

    public function getData($move_id, $move_type, $comment_type)
    {
        $move_id = \Crypt::decrypt($move_id);

        if ($move_type == 1 && $comment_type == 0) {
            $move_name = "Uplift Pre Move Comments";
            $type = "uplift";
        } elseif ($move_type == 5 && $comment_type == 0) {
            $move_name = "Delivery Pre Move Comments";
            $type = "delivery";
        } elseif ($move_type == 1 && $comment_type == 1) {
            $move_name = "Uplift Post Move Comments";
            $type = "uplift";
        } else {
            $move_name = "Delivery Post Move Comments";
            $type = "delivery";
        }
        $login_companyId = CompanyAdmin::getCompanyId();
        $move = Move::where('company_id', $login_companyId)->where('id', $move_id)->first();
        $pdfData['move_name'] = $move_name;
        $pdfData['title'] = $move->contact->contact_name . " - $move->move_number : $move_name";
        $pdfData['move_type'] = $type;
        $pdfData['termsAndConditions'] = TermsAndConditions::where([
            'move_type' => $move_type,
            'move_status' => $comment_type
        ])->get();
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
        $pdfData['images'] = MoveComments::where([
            'move_id' => $move_id,
            'move_type' => $move_type,
            'move_status' => $comment_type
        ])
            ->with('image')
            ->get();
        $pdfData['packageSignature'] = PackageSignature::where([
            'move_id' => $move_id,
            'move_type' => $move_type,
            'status' => $comment_type
        ])
            ->first();
        // dd($pdfData['packageSignature']);

        $commentpdf = PDF::loadView('api-pdf.move-notification', $pdfData)
            ->setOptions([
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true
            ]);
        return $commentpdf->stream();

    }

    public function upliftPreComment($move_id, $companyId)
    {
        $move_id = \Crypt::decrypt($move_id);
        $company_id = \Crypt::decrypt($companyId);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }
        // dd($company_id);
        $login_companyId = CompanyAdmin::getCompanyId();
        $id = Move::where('company_id', $company_id)->where('id', $move_id)->first('id');
        // dd($id);
        if (GetIcrData::detectScreenView() == 'mobile') {
            return redirect()->route('company-admin.moves.uplift-pre-move-comment-pdf', [Crypt::encrypt($id), Crypt::encrypt($company_id)]);
        }

        return view('theme.company-admin.moves.uplift.preComment', compact('id', 'company_id'));
    }

    public function commentPrePdf($move_id, $company_id)
    {
        $move_id = \Crypt::decrypt($move_id);
        $company_id = \Crypt::decrypt($company_id);
        // dd($company_id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }
        $login_companyId = CompanyAdmin::getCompanyId();
        $move = Move::where('company_id', $company_id)->where('id', $move_id->id)->first();


        $move_name = "Uplift Pre Move Comments";
        $pdfData['move_name'] = $move_name;
        $pdfData['title'] = $move->contact->contact_name . " - $move->move_number : $move_name";
        $pdfData['move_type'] = "uplift";
        $pdfData['termsAndConditions'] = TermsAndConditions::where([
            'move_type' => 1,
            'move_status' => 0
        ])->get();
        $pdfData['conditionCheck'] = TermsAndConditionsChecked::where([
            'move_id' => $move->id,
            'move_type' => 1,
            'move_status' => 0,
            'is_checked' => 1
        ])
            ->pluck('tnc_id')
            ->toArray();
        $pdfData['comment'] = MoveComments::where([
            'move_id' => $move->id,
            'move_type' => 1,
            'move_status' => 0
        ])
            ->first();
        $pdfData['images'] = MoveComments::where([
            'move_id' => $move->id,
            'move_type' => 1,
            'move_status' => 0
        ])
            ->with('image')->get();
        $pdfData['packageSignature'] = PackageSignature::where([
            'move_id' => $move->id,
            'move_type' => 1,
            'status' => 0
        ])->first();

        if (GetIcrData::detectScreenView() == 'mobile') {
            $pdf = PDF::loadView('api-pdf.move-notification', $pdfData)
                ->setOptions([
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true
                ]);
            return $pdf->download($pdfData['title'] . '.pdf');
        }
        // return view('api-pdf.move-notification',$this->pdfData);
        return $pdf = PDF::loadView('api-pdf.move-notification', $pdfData)
            ->setOptions([
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true
            ])
            ->stream();

    }

    public function upliftPostComment($move_id, $companyId)
    {
        $move_id = \Crypt::decrypt($move_id);
        $company_id = \Crypt::decrypt($companyId);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $login_companyId = CompanyAdmin::getCompanyId();
        $id = Move::where('company_id', $company_id)->where('id', $move_id)->first('id');

        if (GetIcrData::detectScreenView() == 'mobile') {
            return redirect()->route('company-admin.moves.uplift-post-move-comment-pdf', [Crypt::encrypt($id), Crypt::encrypt($company_id)]);
        }

        return view('theme.company-admin.moves.uplift.postComment', compact('id', 'company_id'));
    }


    public function commentPostPdf($move_id, $company_id)
    {
        $move_id = \Crypt::decrypt($move_id);
        $company_id = \Crypt::decrypt($company_id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $login_companyId = CompanyAdmin::getCompanyId();
        $move = Move::where('company_id', $company_id)->where('id', $move_id->id)->first();
        $move_name = "Uplift Post Move Comments";
        $pdfData['move_name'] = $move_name;
        $pdfData['title'] = $move->contact->contact_name . " - $move->move_number : $move_name";
        $pdfData['move_type'] = "uplift";
        $pdfData['termsAndConditions'] = TermsAndConditions::where([
            'move_type' => 1,
            'move_status' => 1
        ])
            ->get();
        $pdfData['conditionCheck'] = TermsAndConditionsChecked::where([
            'move_id' => $move->id,
            'move_type' => 1,
            'move_status' => 1,
            'is_checked' => 1
        ])
            ->pluck('tnc_id')
            ->toArray();
        $pdfData['comment'] = MoveComments::where([
            'move_id' => $move->id,
            'move_type' => 1,
            'move_status' => 1
        ])
            ->first();
        $pdfData['images'] = MoveComments::where([
            'move_id' => $move->id,
            'move_type' => 1,
            'move_status' => 1
        ])
            ->with('image')
            ->get();
        $pdfData['packageSignature'] = PackageSignature::where([
            'move_id' => $move->id,
            'move_type' => 1,
            'status' => 1
        ])
            ->first();

        if (GetIcrData::detectScreenView() == 'mobile') {
            $pdf = PDF::loadView('api-pdf.move-notification', $pdfData)
                ->setOptions([
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true
                ]);
            return $pdf->download($pdfData['title'] . '.pdf');
        }

        return $pdf = PDF::loadView('api-pdf.move-notification', $pdfData)
            ->setOptions([
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true
            ])
            ->stream();

    }

    public function delivery_commentPrePdf($move_id)
    {
        $move_id = \Crypt::decrypt($move_id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $id = DeliveryMoves::where('id', $move_id)->first();
        if (GetIcrData::detectScreenView() == 'mobile') {
            return redirect()->route('company-admin.moves.delivery-pre-move-comment-pdf', [Crypt::encrypt($move_id)]);
        }
        return view('theme.company-admin.moves.delivery.preComment', compact('move_id'));
    }

    public function deliverypre($move_id)
    {
        $move_id = \Crypt::decrypt($move_id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $delivery_move = DeliveryMoves::where('id', $move_id)->first();


        $move = Move::where('id', $delivery_move->move_id)->first();

        $move_name = "Delivery Pre Move Comments";
        $pdfData['move_name'] = $move_name;
        $pdfData['title'] = $move->contact->contact_name . " - $move->move_number : $move_name";
        $pdfData['move_type'] = "delivery";
        $pdfData['termsAndConditions'] = TermsAndConditions::where([
            'move_type' => 5,
            'move_status' => 0
        ])
            ->get();
        $pdfData['conditionCheck'] = TermsAndConditionsChecked::where([
            'move_id' => $move->id,
            'move_type' => 5,
            'move_status' => 0,
            'is_checked' => 1
        ])
            ->pluck('tnc_id')
            ->toArray();
        $pdfData['comment'] = MoveComments::where([
            'move_id' => $move->id,
            'move_type' => 5,
            'move_status' => 0
        ])
            ->first();
        $pdfData['images'] = MoveComments::where([
            'move_id' => $move->id,
            'move_type' => 5,
            'move_status' => 0
        ])
            ->with('image')
            ->get();
        $pdfData['packageSignature'] = PackageSignature::where([
            'move_id' => $move->id,
            'move_type' => 5,
            'status' => 0
        ])
            ->first();

        if (GetIcrData::detectScreenView() == 'mobile') {
            $pdf = PDF::loadView('api-pdf.move-notification', $pdfData)
                ->setOptions([
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true
                ]);
            return $pdf->download($pdfData['title'] . '.pdf');

        }
        return $pdf = PDF::loadView('api-pdf.move-notification', $pdfData)
            ->setOptions([
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true
            ])
            ->stream();
    }

    public function delivery_commentPostPdf($move_id)
    {
        $move_id = \Crypt::decrypt($move_id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $id = DeliveryMoves::where('id', $move_id)->first();
        if (GetIcrData::detectScreenView() == 'mobile') {
            return redirect()->route('company-admin.moves.delivery-post-move-comment-pdf', [Crypt::encrypt($move_id)]);
        }

        return view('theme.company-admin.moves.delivery.postComment', compact('move_id'));
    }

    public function deliverypost($move_id)
    {
        $move_id = \Crypt::decrypt($move_id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $delivery_move = DeliveryMoves::where('id', $move_id)->first();

        $move = Move::where('id', $delivery_move->move_id)->first();

        $move_name = "Delivery Post Move Comments";
        $pdfData['move_name'] = $move_name;
        $pdfData['title'] = $move->contact->contact_name . " - $move->move_number : $move_name";
        $pdfData['move_type'] = "delivery";
        $pdfData['termsAndConditions'] = TermsAndConditions::where([
            'move_type' => 5,
            'move_status' => 1
        ])
            ->get();
        $pdfData['conditionCheck'] = TermsAndConditionsChecked::where([
            'move_id' => $move->id,
            'move_type' => 5,
            'move_status' => 1,
            'is_checked' => 1
        ])
            ->pluck('tnc_id')
            ->toArray();
        $pdfData['comment'] = MoveComments::where([
            'move_id' => $move->id,
            'move_type' => 5,
            'move_status' => 1
        ])
            ->first();
        $pdfData['images'] = MoveComments::where([
            'move_id' => $move->id,
            'move_type' => 5,
            'move_status' => 1
        ])
            ->with('image')
            ->get();
        $pdfData['packageSignature'] = PackageSignature::where([
            'move_id' => $move->id,
            'move_type' => 5,
            'status' => 1
        ])
            ->first();

        if (GetIcrData::detectScreenView() == 'mobile') {
            $pdf = PDF::loadView('api-pdf.move-notification', $pdfData)
                ->setOptions([
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true
                ]);
            return $pdf->download($pdfData['title'] . '.pdf');
        }

        return $pdf = PDF::loadView('api-pdf.move-notification', $pdfData)
            ->setOptions([
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true
            ])
            ->stream();

    }

    public function archiveMove(Request $request)
    {
        $status = Move::where('id', $request->move_id)->update(['archive_status' => 1]);
        return 1;

    }

    public function archiveIndex($activeTab = null)
    {
        $this->userId = '';
        if (Session::get('company-admin')) {
            $user = Session::get('company-admin');
            $this->userId = User::where('id', $user)->first();
        } elseif (Auth::user() != null) {
            $this->userId = Auth::user();
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $this->activeTab = '#' . $activeTab;
        $company_id = CompanyAdmin::getCompanyId();
        $this->moves = Move::with(['uplift', 'transit', 'screening', 'transload', 'delivery', 'contact'])
            ->where(function ($query) {
                $query->where('archive_status', '=', 1);
            })
            ->where(function ($query) use ($company_id) {
                $query->where('company_id', '=', $company_id);
                $query->orWhere('foreign_controlling_agent', 'LIKE', '%' . $company_id . '%');
                $query->orWhere('foreign_origin_contractor', '%' . $company_id . '%');
                $query->orWhere('foreign_destination_contractor', '%' . $company_id . '%');
                $query->orWhere('foreign_origin_agent', '%' . $company_id . '%');
                $query->orWhere('foreign_destination_agent', '%' . $company_id . '%');
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('theme.company-admin.moves.archive_index', $this->data);
    }

    public function unarchiveMove(Request $request)
    {
        $status = Move::where('id', $request->move_id)->update(['archive_status' => 0]);
        return 1;
    }


    public function gettransload($move_id)
    {
        $move_id = \Crypt::decrypt($move_id);

        // $userId = '';
        // if(Session::get('company-admin')){
        //     $userId = Session::get('company-admin');
        // }
        // elseif(Auth::user() != null){
        //     $userId = Auth::user()->id;
        // }
        // elseif(Session::get('company-admin') == null || Auth::user()->id == null)
        // {
        //   return redirect()->route('/');
        // }

        $this->move = TransloadMoves::where('move_id', $move_id)->first();

        $move_containers_query = MoveContainer::where('move_id', $move_id);

        $this->move_containers = $move_containers_query->get();

        $move_containers_id = $move_containers_query->pluck('id');

        $this->move_items = MoveItems::where(['move_id' => $move_id])
            ->with(array(
                'container' => function ($query) use ($move_containers_id) {
                    $query->whereIn('id', $move_containers_id);
                }
            ))
            ->with('container.containerDetails', 'subItems', 'cartoonItem')
            ->get()
            ->sortBy('item_number');
        $move_conditions = array();
        $subItemArr = [];
        $blankArray = [];
        $subBlackArr = [];
        $elseSubBlackArr = [];
        $idArr = [];
        foreach ($this->move_items as $key => $item) {
            $sitem = '';
            $blankArray = [];
            $parant_item = $item->item;
            $elseSubBlackArr = [];
            $subitem_new = [];

            if ($parant_item != null) {
                $type = "kika";
                $idArr[] = $item->item_number;
                $subItem = isset($item->subItems->subItemDetails->item) ? $item->subItems->subItemDetails->item : NULL;
                if ($subItem != null) {
                    $blankArray[$subItem] = [];
                    foreach ($item->transloadCondition as $condition) {
                        if (!empty($condition->conditionSides)) {
                            $subArray = [];
                            foreach ($condition->conditionSides as $conditionSide) {
                                array_push($subArray, $conditionSide->sideDetails->side);
                            }

                            $newArr = [];
                            $newArr[$condition->conditionDetails->condition] = $subArray;

                            array_push($blankArray[$subItem], $newArr);
                            $subArray = [];
                        }
                        array_push($subBlackArr, $blankArray[$subItem]);
                    }
                } else {
                    $blankArray[$parant_item] = [];
                    foreach ($item->transloadCondition as $condition) {
                        if (count($condition->conditionSides) > 0) {
                            $subArray = [];
                            foreach ($condition->conditionSides as $conditionSide) {
                                array_push($subArray, $conditionSide->sideDetails->side);
                            }
                            $newArr = [];
                            $newArr[$condition->conditionDetails->condition] = $subArray;
                            array_push($blankArray[$parant_item], $newArr);
                            $subArray = [];
                        }
                    }

                }
                $array1 = $blankArray;
                array_push($move_conditions, $array1);
            } else {
                $elseSubBlackArr = [];
                $subitem_new = [];
                if ($item->item_id == null) {
                    if ($item->cartoonItem) {
                        foreach ($item->cartoonItem as $itm) {
                            $sitem .= $itm->cartoonItemDetails->item . ', ';
                            array_push($subitem_new, $itm->cartoonItemDetails->item);
                        }
                    }
                } else {
                    $sitem .= '';
                }

                $subitem = rtrim($sitem, ', ');
                $extraArr = [];
                $ik = 0;
                foreach ($item->transloadCondition as $condition) {
                    $idArr[] = $item->item_number;
                    $subArray = [];
                    if (count($condition->conditionSides) > 0) {
                        // array_push($subArray, $subitem_new);
                        foreach ($condition->conditionSides as $conditionSide) {
                            array_push($subArray, $conditionSide->sideDetails->side);
                        }

                        $newArr = [];
                        $newArr[$condition->conditionDetails->condition] = $subArray;
                        // print_r($condition->conditionDetails->condition);
                        array_push($elseSubBlackArr, $newArr);
                        $subArray = [];
                    } else {
                        $newArr = [];

                        $newArr[$condition->conditionDetails->condition] = $subArray;
                        // print_r($condition->conditionDetails->condition);
                        array_push($elseSubBlackArr, $newArr);
                        $subArray = [];
                    }
                }
                if (!empty($elseSubBlackArr)) {
                    if ($subitem != '') {
                        $extraArr[$subitem] = $elseSubBlackArr;
                    } else {
                        $extraArr[] = $elseSubBlackArr;
                    }

                    array_push($move_conditions, $extraArr);
                }

            }
        }
        // die;
        $this->move_conditions = $move_conditions;
        $this->idArr = $idArr;
        // $this->type = $type;
        // // die;
        // echo "<pre>";
        // //  print_r($idArr);
        // print_r($move_conditions);
        // exit;

        $this->condition_images = MoveItemCondition::where([
            'move_id' => $move_id,
            'move_type' => 4
        ])
            ->with('conditionImage')
            ->get();

        $this->comments = MoveComments::where([
            'move_id' => $move_id,
            'move_type' => 4
        ])
            ->with('image')
            ->get();
        // return view('theme.company-admin.pdf.transload-icr',$this->data);
        // $pdf = PDF::loadView('theme.company-admin.pdf.transload-icr',$this->data)->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true])->stream();
        // return $pdf;

        $commentpdf = PDF::loadView('theme.company-admin.pdf.transload-icr', $this->data)
            ->setOptions([
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true
            ]);
        if (GetIcrData::detectScreenView() == 'mobile') {
            $fileName = $this->move->move->contact->contact_name . ' : ' . $this->move->move->move_number . ' - Tranship Sheet';
            return $commentpdf->download($fileName . '.pdf');
        }
        return $commentpdf->stream();
    }

    public function getkikaAgent(Request $request)
    {
        $company_id = CompanyAdmin::getCompanyId();
        $agent = CompanyAgent::where('id', $request->controlling_kika_id)->first();

        return $agent;
    }

    public function screenICR($id)
    {
        if (GetIcrData::detectScreenView() == 'mobile') {
            return redirect()->route('company-admin.moves.screen-icr-pdf', $id);
        }

        $move_id = \Crypt::decrypt($id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $id = Move::where('id', $move_id)->value('id');

        return view('theme.company-admin.moves.screen.icrScreen', compact('id'));
    }

    public function screenicrPdf($move_id)
    {
        $move_id = \Crypt::decrypt($move_id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $data = [];
        $this->data['move'] = Move::with('contact', 'delivery')->where('id', $move_id)->first();
        $this->data['upliftMove'] = UpliftMoves::where('move_id', $move_id)->first();
        // dd($this->data['move']);

        $this->data['exceptions'] = CartonCondition::all();

        $this->data['descriptions'] = PackerCode::all();

        $this->data['conditionLocations'] = Conditionside::all();

        $this->data['termsAndConditions'] = TermsAndConditions::all();

        // $this->data['move_items'] = MoveItems::with('subItems','screeningCategory')->where([
        //     ['move_id',$move_id]
        // ])
        // ->orderBy('item_number','asc')
        // ->get();
        // $this->data['move_items'] = ScreeningItemCategory::with('Category')->where([
        //     ['move_id',$move_id]
        // ])
        // $this->data['move_items'] = MoveItems::with('category','')->where([
        //     ['move_id',$move_id]
        // ])
        // ->orderBy('item_number','asc')
        // ->get();
        $this->data['move_items'] = Move::with('items.itemScreeningCategory.Category')->where([
            ['id', $move_id]
        ])
            ->get();
        $this->data['comments'] = MoveComments::where(['move_id' => $move_id])->get();
        // dd($this->data['move_items']);
        // return view('theme.company-admin.pdf.screenIcr',$this->data);

        if (GetIcrData::detectScreenView() == 'mobile') {
            return $pdf = PDF::loadView('theme.company-admin.pdf.screenIcr', $this->data)
                ->setOptions([
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true
                ])
                ->download($this->data['move']->contact->contact_name . ":" . $this->data['move']->move_number . ' - Screen.pdf');
        }
        return $pdf = PDF::loadView('theme.company-admin.pdf.screenIcr', $this->data)
            ->setOptions([
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true
            ])
            ->stream();
    }

    public function preMoveComment($move_id, $move_type)
    {
        $move_id = \Crypt::decrypt($move_id);
        $comment_type = 0;
        if ($move_type == 1) {
            $move_name = "Uplift Pre Move Comments";
            $type = "uplift";
        } elseif ($move_type == 5) {
            $move_name = "Delivery Pre Move Comments";
            $type = "delivery";
        }

        $login_companyId = CompanyAdmin::getCompanyId();
        // $move = Move::where('company_id',$login_companyId)->where('id',$move_id)->first();
        $move = Move::where('id', $move_id)->first();
        $pdfData['move_name'] = $move_name;
        $pdfData['title'] = $move->contact->contact_name . " - $move->move_number : $move_name";
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
        $pdfData['company_name'] = $move->uplift->origin_agent;
        $pdfData['origin_agent'] = $move->uplift->uplift_address;
        $pdfData['delivery_agent'] = $move->delivery->delivery_address;
        $pdfData['container_number'] = $move->container_number;
        $commentpdf = PDF::loadView('api-pdf.pre-move-comment', $pdfData)
            ->setOptions([
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true
            ]);
        return $commentpdf->stream();
    }

    public function preMoveCommentImage($move_id, $move_type)
    {
        $move_id = \Crypt::decrypt($move_id);
        $move_type = (int) $move_type;
        $comment_type = 0;
        $move = Move::where('id', $move_id)->first();

        $move_comment_image = MoveComments::where([
            'move_id' => $move_id,
            'move_type' => $move_type,
            'move_status' => $comment_type
        ])
            ->with('image')
            ->first();

        if ($move_type == 1) {
            $type = "uplift";
        } elseif ($move_type == 5) {
            $type = "delivery";
        }
        $pdfData['move_name'] = ucfirst($type);
        $pdfData['title'] = $move->contact->contact_name . " : " . $move->move_number . " - " . ucfirst($type);
        $pdfData['move_type'] = $type;
        $pdfData['images'] = $move_comment_image->image;
        $pdfData['company_name'] = $move->uplift->origin_agent;
        $pdfData['origin_agent'] = $move->uplift->uplift_address;
        $pdfData['delivery_agent'] = $move->delivery->delivery_address;
        $pdfData['container_number'] = $move->container_number;
        $pdfData['comment_type'] = $comment_type;

        $commentpdf = PDF::loadView('api-pdf.move-comment-image', $pdfData)
            ->setOptions([
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
            ]);
        if (GetIcrData::detectScreenView() == 'mobile') {
            return $commentpdf->download($move->contact->contact_name . " : " . $move->move_number . " - " . ucfirst($type) . ' Pre Move Images.pdf');
        }
        return $commentpdf->stream();
    }

    public function postMoveCommentImage($move_id, $move_type)
    {
        $move_id = \Crypt::decrypt($move_id);
        $move_type = (int) $move_type;
        $comment_type = 1;
        $move = Move::where('id', $move_id)->first();

        $move_comment_image = MoveComments::where(['move_id' => $move_id, 'move_type' => $move_type, 'move_status' => $comment_type])->with('image')->first();

        if ($move_type == 1) {
            $type = "uplift";
        } elseif ($move_type == 5) {
            $type = "delivery";
        }
        $pdfData['move_name'] = ucfirst($type);
        $pdfData['title'] = $move->contact->contact_name . " : " . $move->move_number . " - " . ucfirst($type);
        $pdfData['move_type'] = $type;
        $pdfData['images'] = $move_comment_image->image;
        $pdfData['company_name'] = $move->uplift->origin_agent;
        $pdfData['origin_agent'] = $move->uplift->uplift_address;
        $pdfData['delivery_agent'] = $move->delivery->delivery_address;
        $pdfData['container_number'] = $move->container_number;
        $pdfData['comment_type'] = $comment_type;

        $commentpdf = PDF::loadView('api-pdf.move-comment-image', $pdfData)
            ->setOptions([
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
            ]);

        if (GetIcrData::detectScreenView() == 'mobile') {
            return $commentpdf->download($move->contact->contact_name . " : " . $move->move_number . "- " . $pdfData['move_type'] . "- Post Move Image");
        }

        return $commentpdf->stream();
    }

    public function upliftRiskAssessment($id)
    {
        $move_id = \Crypt::decrypt($id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $id = Move::where('id', $move_id)->value('id');
        if (GetIcrData::detectScreenView() == 'mobile') {
            return redirect()->route('company-admin.moves.uplift-risk-assessment-pdf', \Crypt::encrypt($id));
        }
        return view('theme.company-admin.moves.uplift.risk-assessment', compact('id'));
    }

    public function deliveryRiskAssessment($id)
    {
        $move_id = \Crypt::decrypt($id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $id = Move::where('id', $move_id)->value('id');
        if (GetIcrData::detectScreenView() == 'mobile') {
            return redirect()->route('company-admin.moves.delivery-risk-assessment-pdf', \Crypt::encrypt($id));
        }
        return view('theme.company-admin.moves.delivery.risk-assessment', compact('id'));
    }

    public function riskAssessmentPdf($move_id)
    {
        $move_id = \Crypt::decrypt($move_id);

        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $move = Move::where('id', $move_id)->first();
        if (request()->segment(3) == "delivery") {
            $move_type = 5;
        } else {
            $move_type = 1;
        }
        $pdfData['move_agent'] = $move->uplift->origin_agent;
        $pdfData['risk_title'] = RiskTitles::get()->toArray();
        $pdfData['risk_assessment'] = RiskAssessment::with('riskAssessmentDetail')->where([
            'move_id' => $move_id,
            'move_type' => $move_type
        ])->first();

        if ($move_type == 5) {
            $pdfData['move_name'] = "Delivery";
            $pdfData['title'] = $move->contact->contact_name . " - $move->move_number : Delivery";

            if (GetIcrData::detectScreenView() == 'mobile') {
                $pdf = PDF::loadView('theme.company-admin.pdf.risk-assessment', $pdfData)
                    ->setOptions([
                        'isPhpEnabled' => true,
                        'isRemoteEnabled' => true
                    ]);
                return $pdf->download($pdfData['title'] . ' Risk Assessment.pdf');
            }

            return PDF::loadView('theme.company-admin.pdf.risk-assessment', $pdfData)
                ->setOptions([
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true
                ])
                ->stream();
        } else {
            $pdfData['move_name'] = "Uplift";
            $pdfData['title'] = $move->contact->contact_name . " - $move->move_number : Uplift";

            if (GetIcrData::detectScreenView() == 'mobile') {
                $pdf = PDF::loadView('theme.company-admin.pdf.risk-assessment', $pdfData)
                    ->setOptions([
                        'isPhpEnabled' => true,
                        'isRemoteEnabled' => true
                    ]);
                return $pdf->download($pdfData['title'] . ' Risk Assessment.pdf');
            }
            return PDF::loadView('theme.company-admin.pdf.risk-assessment', $pdfData)
                ->setOptions([
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true
                ])
                ->stream();
        }
    }

    public function upliftIcrImage($id)
    {
        $upliftID = \Crypt::decrypt($id);

        if (GetIcrData::detectScreenView() == 'mobile') {
            return redirect()->route('company-admin.moves.uplift-icr-pdf', \Crypt::encrypt($upliftID));
        }
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }

        $id = UpliftMoves::where('id', $upliftID)->value('move_id');

        return view('theme.company-admin.moves.uplift.image-icr', compact('id'));
    }

    public function deliveryIcrImage($id)
    {
        $deliveryID = \Crypt::decrypt($id);

        if (GetIcrData::detectScreenView() == 'mobile') {
            return redirect()->route('company-admin.moves.delivery-icrimage-pdf', \Crypt::encrypt($deliveryID));
        }
        $userId = '';
        if (Session::get('company-admin')) {
            $userId = Session::get('company-admin');
        } elseif (Auth::user() != null) {
            $userId = Auth::user()->id;
        } elseif (Session::get('company-admin') == null || Auth::user()->id == null) {
            return redirect()->route('/');
        }
        $id = DeliveryMoves::where('move_id', $deliveryID)->value('move_id');

        return view('theme.company-admin.moves.delivery.image-icr', compact('id'));
    }

    public function icrImagePDF($move_id)
    {
        set_time_limit(300);
        ini_set('max_execution_time', 300);

        $move_id = \Crypt::decrypt($move_id);

        if (request()->segment(3) == "delivery") {
            $this->data = GetIcrData::getIcrData($move_id, 5);
        } else {
            $this->data = GetIcrData::getIcrData($move_id, 1);
        }

        ini_set("pcre.backtrack_limit", "5000000");
        $move_data = $this->data['move'];
        $check_condition_image = \Illuminate\Support\Facades\DB::table('move_item_conditions')
            ->join('move_condition_images', 'move_condition_images.move_condition_id', 'move_item_conditions.id')
            ->select('move_condition_images.image')
            ->where([
                ['move_id', $this->data['nonkika_move']->id],
                ['move_type', $this->data['move_type']],
            ])->groupBy('move_item_conditions.id')->get();
        $this->data['check_condition_image'] = count($check_condition_image);
        $company_admin = Companies::select('id', 'kika_id', 'icr_title_toggle', 'icr_title_image', 'title_bar_color_code')->where('kika_id', $move_data['origin_agent_kika_id'])->first();
        $bg_color = '#d5d5d5';
        $div_html = '<div class="company-name text-black mb-10">' . $move_data['origin_agent'] . '</div>';
        $pdf_margin_top = 40;
        if ($company_admin && $company_admin->icr_title_toggle == 0) {
            if ($company_admin->title_bar_color_code != null) {
                $bg_color = $company_admin->title_bar_color_code;
            }

            if ($company_admin->icr_title_image != null) {
                $pdf_margin_top = 46;
                $s3_base_url = config('filesystems.disks.s3.url');
                $s3_image_path = $s3_base_url . 'icrtitle/';
                $title_image_path = '';
                if ($company_admin->icr_title_image != '') {
                    $title_image_path = $s3_image_path . $company_admin->icr_title_image;
                }

                $div_html = '<div class="mb-10">
                    <img style="max-width: 720px; max-height: 80px;" src="' . $title_image_path . '" alt="">
                </div>';
            }
        }

        $mpdf = new Mpdf([
            'fontDir' => str_replace('\\', '/', base_path('public/fonts/arial')),
            'fontdata' => [
                'customarialfont' => [
                    'R' => 'Arial.ttf',
                    'B' => 'Arial-bold.ttf',
                ]
            ],
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => $pdf_margin_top,
            'margin_bottom' => 16,
            'margin_header' => 12,
            'margin_footer' => 12,
        ]);

        $mpdf->SetHTMLHeader(
            '<div class="main-wrapper">
                ' . $div_html . '
                    <div class="mb-10 report-delivery" style="background-color: ' . $bg_color . '">
                        <table class="border-0">
                            <tr>
                                <td class="text-black f-14">Inventory And Condition Report - ' . $move_data->contact->contact_name . ' : ' . $move_data->move->move_number . ' - ' . ucfirst($this->data['move_type'] == 1 ? "Uplift" : "Delivery") . ' Images</td>
                                <td class="text-black f-14" style="text-align: right">Page {PAGENO} of {nbpg}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>'
        );
        $mpdf->AddPage('P');
        $mpdf->WriteHTML(view('theme.company-admin.pdf.image-icr', $this->data)->render());
        $filename = $move_data->contact->contact_name . ":" . $move_data->move->move_number . ($this->data['move_type'] == 1 ? " - Uplift Images" : " - Delivery Images");
        if (GetIcrData::detectScreenView() == 'mobile') {
            $pdfContent = $mpdf->output($filename . '.pdf', 'D');
        }

        $pdfContent = $mpdf->Output($filename . '.pdf', 'S');
        return response($pdfContent)->header('Content-Type', 'application/pdf');
    }
}
