<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\API\v1\companyUser\AuthenticationController;
use App\Http\Controllers\API\v1\companyUser\DataListingController;
use App\Http\Controllers\API\v1\companyUser\MoveController;
use App\Http\Controllers\API\v1\companyUser\TransloadMoveController;
use App\Http\Controllers\API\v1\companyUser\ItemController;

use App\Http\Controllers\API\v1\companyUser\DeliveryMoveController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
///////////////////////////////////////////
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['namespace' => 'API'], function () {

	Route::prefix('v1')->namespace('App\Http\Controllers\API\v1')->group(function () {

		Route::group(['namespace' => 'companyUser'], function () {

			Route::post('login', [AuthenticationController::class, 'login']);


			Route::group(['middleware' => 'auth:api', 'checkapistatus'], function () {

				Route::get('logout', [AuthenticationController::class, 'logout']);

				// Static data APIs
				Route::get('carton/choice', [DataListingController::class, 'getCartonChoice']);
				Route::get('carton/condition-and-side', [DataListingController::class, 'getCartonConditionAndSide']);
				Route::get('carton/item-label', [DataListingController::class, 'getCartonItemLabel']);
				Route::get('carton/packer', [DataListingController::class, 'getCartonPacker']);
				Route::get('screening/category', [DataListingController::class, 'getScreeningCategory']);
				Route::post('move/trasnload/categories', [DataListingController::class, 'getTransloadCategories']);
				Route::get('room/choice', [DataListingController::class, 'getRoomChoice']);

				// Moves APIs
				Route::post('move/search', [MoveController::class, 'searchMove']);
				Route::post('move/getMoves', [MoveController::class, 'getMoves']);
				Route::post('move/change-status', [MoveController::class, 'changeMoveStatus']);
				Route::post('move/change-status/test', [MoveController::class, 'changeMoveStatusTest']);
				Route::post('move/changeoverflow-status', [MoveController::class, 'changeoverflowStatus']);
				Route::post('move/pre-check', [MoveController::class, 'preMoveCheck']);
				Route::post('move/pre-check/test', [MoveController::class, 'preMoveCheckTest']);
				Route::post('move/manage-comment', [MoveController::class, 'manageComment']);
				Route::post('move/create-move', [MoveController::class, 'createMove']);
				Route::post('move/create-transload-move', [MoveController::class, 'createTransloadMove']);
				Route::post('move/create-nonkika-move', [MoveController::class, 'createNonkikaMove']);
				Route::post('move/change-container-number', [MoveController::class, 'changeContainerNumber']);

				// Uplift/Transload Move APIs
				Route::post('move/transload/classify', [TransloadMoveController::class, 'classify']);
				Route::post('move/transload/add-container', [TransloadMoveController::class, 'addContainer']);
				Route::post('move/transload/add-item-container', [TransloadMoveController::class, 'addItemContainer']);
				Route::post('move/transload/edit-item-condition', [TransloadMoveController::class, 'editItemCondition']);

				// ICR items API
				Route::post('items/manage', [ItemController::class, 'manageUpliftICR']);
				Route::post('items/assign-category', [ItemController::class, 'assignCategory']);
				Route::post('items/get-all', [ItemController::class, 'getMoveItems']);
				Route::post('items/add-description', [ItemController::class, 'addDescription']);
				Route::post('items/delete-move-item', [ItemController::class, 'deleteItem']);

				// Delivery Move API
				Route::post('move/delivery/manage-delivery-items', [DeliveryMoveController::class, 'manageDeliveryItem']);
				Route::post('move/delivery/unpack-items', [DeliveryMoveController::class, 'unpackItems']);
				Route::post('move/delivery/nonKika-items', [DeliveryMoveController::class, 'nonKikaItems']);

				// Agent data API
				Route::post('agents/list', [DataListingController::class, 'listAllAgents']);
				Route::post('move/update-agent', [DataListingController::class, 'updateMoveAgent']);

				// Risk Assessment API
				Route::post('move/risk-assessment', [MoveController::class, 'moveRiskAssessment']);

				// Set Room
				Route::post('move/set-room', [MoveController::class, 'setRoom']);

			});


		});

	});

});
