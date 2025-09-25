<?php

namespace App\Http\Controllers\API\v1\companyUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\User;
use App\CompanyUser;
use App\Move;
use App\DeliveryMoves;
use App\ScreeningMoves;
use App\UpliftMoves;
use App\TransloadMoves;
use App\TransitMoves;
use App\MoveComments;
use App\UpliftCommentImages;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Helpers\CompanyAdmin;
use App\Helpers\CompanyUserDetails;

class UpliftMoveController extends BaseController
{
    //
}
