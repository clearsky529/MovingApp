<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\support_admin\CmsController;
use App\Http\Controllers\backend\SeceretLoginController;
use App\Http\Controllers\Auth\FrontController;
use App\Http\Controllers\backend\StripeTestController;
use App\Http\Controllers\backend\DashboardController;
use App\Http\Controllers\backend\ProfileController;
use App\Http\Controllers\backend\ChangePasswordController;
use App\Http\Controllers\backend\UserController;
use App\Http\Controllers\backend\CompaniesController;
use App\Http\Controllers\backend\MoveController;
use App\Http\Controllers\backend\SubscriptionController;
use App\Http\Controllers\backend\SettingController;
use App\Http\Controllers\company_admin\RegisterController;
use App\Http\Controllers\company_admin\MovesController as CompanyMovesController;
use App\Http\Controllers\company_admin\StripePaymentController;
use App\Http\Controllers\company_admin\DashboardController as CompanyDashboardController;
use App\Http\Controllers\company_admin\CompanyUserController;
use App\Http\Controllers\company_admin\AgentController;
use App\Http\Controllers\support_admin\DashboardController as SupportDashboardController;
use App\Http\Controllers\support_admin\ProfileController as SupportProfileController;
use App\Http\Controllers\support_admin\ChangePasswordController as SupportChangePasswordController;
use App\Http\Controllers\support_admin\UserController as SupportUserController;
use App\Http\Controllers\support_admin\CompaniesController as SupportCompaniesController;
use App\Http\Controllers\support_admin\MoveController as SupportMoveController;
use App\Http\Controllers\support_admin\CmsController as SupportCmsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/vpn-php-info', [CmsController::class, 'vpnPhpInfo'])->name('support-admin.cms.phpinfo');

Route::get('/', function () {
  return view('auth.frontend.coming-soon');
})->name('/');

Route::group(['middleware' => 'auth'], function () {
  Route::get('secretlogin/{id}', [SeceretLoginController::class, 'seceretLogin'])->name('secret-login');
});

// //Frontend route
// Route::get('/uplift_tutorial_video','Auth\FrontController@uplift_tutorial_video')->name('auth.frontend.uplift_tutorial_video');
// Route::get('/{title}','Auth\FrontController@lending_slug')->name('auth.frontend.slug');
// Route::get('/{title1}','auth\FrontController@lending_slug1')->name('auth.frontend.slug1');
// Route::get('(:any)/{subtitle}','auth\FrontController@sub_title')->name('auth.frontend.titleslug');
// Route::get('url_pdf','auth\FrontController@url_pdf')->name('auth.frontend.pdf');


Route::group(['middleware' => 'prevent-back-history'], function () {
  //Frontend route
  Route::get('/uplift_tutorial_video', [FrontController::class, 'uplift_tutorial_video'])->name('auth.frontend.uplift_tutorial_video');
  Route::get('/{title}', [FrontController::class, 'lending_slug'])->name('auth.frontend.slug');

  Route::group(['prefix' => 'admin'], function () {
    Auth::routes();
    Route::get('/', function () {
      return redirect('/admin/login');
    })->name('/');

    Route::group(['middleware' => 'auth'], function () {

      Route::group(['middleware' => ['role:super-admin']], function () {
        Route::get('stripeTest', [StripeTestController::class, 'test'])->name('testStripe');

        Route::get('/home', [DashboardController::class, 'index'])->name('home');
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::get('/changepassword', [ChangePasswordController::class, 'index'])->name('changepassword');
        Route::post('/changepassword', [ChangePasswordController::class, 'store'])->name('changepassword.store');
        Route::match(['get', 'post'], '/edit-user-profile', [UserController::class, 'EditProfile'])->name('edit-user-profile');

        //Companies route
        Route::match(['get', 'post'], '/companies', [CompaniesController::class, 'index'])->name('admin.companies');
        Route::post('/companies/reset', [CompaniesController::class, 'reset'])->name('admin.companies.reset');
        Route::post('/companies/change-status', [CompaniesController::class, 'changeStatus'])->name('admin.companies.status');
        Route::get('/companies/view-company/{id}', [CompaniesController::class, 'show'])->name('admin.companies.view');
        Route::get('/companies/archive-company/{id}', [CompaniesController::class, 'archiveshow'])->name('admin.companies.archive');
        Route::post('/companies/location-fetch', [CompaniesController::class, 'locationFetch'])->name('admin.companies.locationFetch');
        Route::get('/companies/delete', [CompaniesController::class, 'findAccount'])->name('admin.companies.delete');
        Route::get('/companies/deleteAccount', [CompaniesController::class, 'deleteAccount'])->name('admin.companies.deleteAccount');

        //moves route
        Route::match(['get', 'post'], '/move/{activeTab?}', [MoveController::class, 'index'])->name('admin.move');
        Route::get('/move/view-move/{id}', [MoveController::class, 'show'])->name('admin.move.view');
        Route::get('/move/view-delivery/{id}', [MoveController::class, 'showDelivery'])->name('admin.move.delivery_view');
        Route::get('/archive-move/archive', [MoveController::class, 'archiveIndex'])->name('admin.archive');

        //Company-user route
        Route::match(['get', 'post'], '/company-user', [UserController::class, 'companyUser'])->name('admin.company-user');
        Route::get('/company-user/view-user/{id}', [UserController::class, 'showCompanyUser'])->name('admin.company-user.view');

        //Subscription route
        Route::match(['get', 'post'], '/subscription', [SubscriptionController::class, 'index'])->name('admin.subscription');
        Route::get('/subscription/view-subscription/{id}', [SubscriptionController::class, 'show'])->name('admin.subscription.view');
        Route::get('/subscription/create-subscription', [SubscriptionController::class, 'create'])->name('admin.subscription.create');
        Route::post('/subscription/store-subscription', [SubscriptionController::class, 'store'])->name('admin.subscription.store');
        Route::get('/subscription/edit-subscription/{id}', [SubscriptionController::class, 'edit'])->name('admin.subscription.edit');
        Route::match(['get', 'post'], '/subscription/update-subscription/{id?}', [SubscriptionController::class, 'update'])->name('admin.subscription.update');
        Route::get('/subscription/delete-subscription/{id}', [SubscriptionController::class, 'delete'])->name('admin.subscription.delete');

        //Setting route
        Route::match(['get', 'post'], '/setting', [SettingController::class, 'index'])->name('admin.setting');
        Route::post('/setting/change-free-trial', [SettingController::class, 'changeFreeTrial'])->name('admin.setting.change-free-trial');
        Route::post('/setting/change-device-price', [SettingController::class, 'changeDevicePrice'])->name('admin.setting.change-device-price');
        Route::post('/setting/change-icr-price', [SettingController::class, 'changeIcrPrice'])->name('admin.setting.change-icr-price');
      });

    });
  });

  Route::group(['prefix' => 'company-admin'], function () {

    //Register route
    Route::get('/terms-condition', [RegisterController::class, 'termscondition'])->name('terms-condition');
    Route::get('/contact_us', [RegisterController::class, 'contactus'])->name('company-admin.contact-us');
    Route::get('/register/step-1/{referral_code?}', [RegisterController::class, 'index'])->name('company-admin.register');
    Route::post('/register/step-2', [RegisterController::class, 'registerStep1'])->name('company-admin.submit-step-1');
    Route::get('/register/form/step-2', [RegisterController::class, 'formStep2'])->name('company-admin.register.step-2');
    Route::post('/location-fetch', [RegisterController::class, 'locationFetch'])->name('company-admin.locationFetch');
    Route::post('/register/step-3', [RegisterController::class, 'registerStep2'])->name('company-admin.submit-step-2');
    Route::get('/register/form/step-3', [RegisterController::class, 'formStep3'])->name('company-admin.register.step-3');
    Route::post('/register', [RegisterController::class, 'register'])->name('company-admin.store');
    Route::get('/stripe', [RegisterController::class, 'stripeForm'])->name('company-admin.stripe');
    Route::get('/register/success', [RegisterController::class, 'RegisterSuccess'])->name('company-admin.register.success');
    Route::get('/send-mail/{id}', [RegisterController::class, 'activeUser']);
    Route::get('/move/getdata/{move_id}/{move_type}/{comment_type}', [CompanyMovesController::class, 'getData'])->name('move.getdata');
    Route::get('/move/gettransload/{move_id}', [CompanyMovesController::class, 'gettransload'])->name('move.getdata');
    Route::get('/move/pre-move-comment/{move_id}/{move_type}', [CompanyMovesController::class, 'preMoveComment'])->name('move.getdata');
    Route::get('/move/pre-move-comment-image/{move_id}/{move_type}', [CompanyMovesController::class, 'preMoveCommentImage'])->name('move.getdata');
    Route::get('/move/post-move-comment-image/{move_id}/{move_type}', [CompanyMovesController::class, 'postMoveCommentImage'])->name('move.postcomment.image');

    Route::get('/', function () {
      return redirect('/admin/login');
    });

    //stripe payment route
    Route::post('/profile/stripe/payment', [StripePaymentController::class, 'stripePayment'])->name('company-admin.stripe.stripe');

    // if($_SERVER['QUERY_STRING'] == "" && $_SERVER['QUERY_STRING']== "isFromAdmin=true"){
    Route::group(['middleware' => 'auth', 'checkStatus'], function () {
      Route::group(['middleware' => ['role:super-admin|company-admin']], function () {

        // User profile routes
        Route::get('/home', [CompanyDashboardController::class, 'index'])->name('company-admin.home');
        Route::get('/change-password', [CompanyDashboardController::class, 'changePasswordForm'])->name('company-admin.change-password');
        Route::post('/change-password', [CompanyDashboardController::class, 'changePassword'])->name('company-admin.change-password.store');
        Route::get('/profile', [CompanyDashboardController::class, 'profile'])->name('company-admin.profile');
        Route::match(['get', 'post'], '/edit-profile', [CompanyDashboardController::class, 'EditProfile'])->name('company-admin.edit-profile');
        Route::post('/profile/add-on', [CompanyDashboardController::class, 'addOnUSer'])->name('company-admin.profile.add-on');
        Route::get('/profile/subscription/extend', [CompanyDashboardController::class, 'extendPlan'])->name('company-admin.extend-plan');
        Route::post('/profile/subscription/selectedPlan', [CompanyDashboardController::class, 'selectedPlan'])->name('company-admin.selected-plan');
        Route::post('/profile/subscription/payment', [CompanyDashboardController::class, 'planPaymentGateway'])->name('company-admin.subcription.payment');
        Route::post('/profile/refer-friend', [CompanyDashboardController::class, 'referFriend'])->name('company-admin.refer-friend');

        // Manage Users
        Route::match(['get', 'post'], '/device', [CompanyUserController::class, 'index'])->name('company-admin.device');
        Route::get('/device/create-device', [CompanyUserController::class, 'create'])->name('company-admin.user.create');
        Route::post('/device/store-device', [CompanyUserController::class, 'store'])->name('company-admin.user.store');
        Route::get('/device/view-device/{id}', [CompanyUserController::class, 'show'])->name('company-admin.user.view');
        Route::get('/device/delete-device/{id}', [CompanyUserController::class, 'delete'])->name('company-admin.user.delete');
        Route::get('/device/edit-device/{id}', [CompanyUserController::class, 'edit'])->name('company-admin.user.edit');
        Route::post('/device/update-device/{id}', [CompanyUserController::class, 'update'])->name('company-admin.user.update');
        Route::post('/device/change-status', [CompanyUserController::class, 'changeStatus'])->name('company-admin.user.status');
        Route::post('/device/location-fetch', [CompanyUserController::class, 'locationFetch'])->name('company-admin.user.locationFetch');
        Route::get('device/logout-device/{id}', [CompanyUserController::class, 'logoutUser'])->name('company-admin.user.logout');
        Route::get('/device/userDetails', [CompanyUserController::class, 'deleteUserDetails'])->name('company-admin.user.userDetails');

        // Agents routes
        Route::get('/agents', [AgentController::class, 'index'])->name('company-admin.agents');
        Route::get('/agents/view-agents/{id}', [AgentController::class, 'show'])->name('company-admin.agents.view');
        Route::get('/agents/create-agents', [AgentController::class, 'create'])->name('company-admin.subscription.create');
        Route::post('/agents/store-agents', [AgentController::class, 'store'])->name('company-admin.agents.store');
        Route::get('/agents/edit-agents/{id}', [AgentController::class, 'edit'])->name('company-admin.agents.edit');
        Route::post('/agents/update-agents/{id}', [AgentController::class, 'update'])->name('company-admin.agents.update');
        Route::get('/agents/delete-agents/{id}', [AgentController::class, 'delete'])->name('company-admin.agents.delete');
        Route::post('/agents/change-status', [AgentController::class, 'changeStatus'])->name('company-admin.agents.change-status');
        Route::post('/agents/location-fetch', [AgentController::class, 'locationFetch'])->name('company-admin.agents.locationFetch');
        Route::post('/agents/fetch-agent', [AgentController::class, 'fetchAgent'])->name('company-admin.agents.fetchAgent');

        // Manage moves route
        Route::get('/move/{activeTab?}', [CompanyMovesController::class, 'index'])->name('company-admin.move');
        Route::post('/move/change-status', [CompanyMovesController::class, 'changeStatus'])->name('company-admin.move.change-status');
        Route::get('/move/delete/{id}', [CompanyMovesController::class, 'deleteMove'])->name('company-admin.moves.delete');
        // Route::post('/move/send-email-delivery-icr', [CompanyMovesController::class, 'sendEmailIcr'])->name('company-admin.moves.send-email-delivery-icr');
        Route::post('/move/get-agent', [CompanyMovesController::class, 'getAgent'])->name('company-admin.moves.get-agent');
        Route::post('/move/archive-move', [CompanyMovesController::class, 'archiveMove'])->name('company-admin.moves.archive-move');
        Route::get('/archive-move/archive', [CompanyMovesController::class, 'archiveIndex'])->name('company-admin.archive');
        Route::post('/move/unarchive-move', [CompanyMovesController::class, 'unarchiveMove'])->name('company-admin.moves.unarchive-move');

        // Manage move route - uplift
        Route::get('/move/create/uplift', [CompanyMovesController::class, 'createUplift'])->name('company-admin.move.create-uplift');
        Route::post('/move/create/delivery', [CompanyMovesController::class, 'storeUplift'])->name('company-admin.moves.store-uplift');
        Route::get('/move/show/uplift/{id}', [CompanyMovesController::class, 'showUplift'])->name('company-admin.moves.show-uplift');
        Route::get('/move/edit/uplift/{id}', [CompanyMovesController::class, 'editUplift'])->name('company-admin.moves.edit-uplift');
        Route::post('/move/update/uplift/{id}', [CompanyMovesController::class, 'updateUplift'])->name('company-admin.moves.update-uplift');
        Route::get('/move/uplift/send-to-delivery/{id}', [CompanyMovesController::class, 'sendToDelivery'])->name('company-admin.moves.send-to-delivery');
        Route::get('/move/uplift/icr/{id}', [CompanyMovesController::class, 'upliftICR'])->name('company-admin.moves.uplift-icr');
        Route::get('/move/uplift/icr/pdf/{move_id}', [CompanyMovesController::class, 'icrPdf'])->name('company-admin.moves.uplift-icr-pdf');
        Route::get('/move/uplift/premovecomment/{id}/{company_id}', [CompanyMovesController::class, 'upliftPreComment'])->name('company-admin.moves.uplift-movecomment-pdf');
        Route::get('/move/uplift/premove/comment/{move_id}/{company_id}', [CompanyMovesController::class, 'commentPrePdf'])->name('company-admin.moves.uplift-pre-move-comment-pdf');
        Route::get('/move/uplift/postmovecomment/{id}/{company_id}', [CompanyMovesController::class, 'upliftPostComment'])->name('company-admin.moves.uplift-postmovecomment-pdf');
        Route::get('/move/uplift/postmove/comment/{move_id}', [CompanyMovesController::class, 'commentPostPdf'])->name('company-admin.moves.uplift-post-move-comment-pdf');
        Route::get('/move/uplift/icrimage/{id}', [CompanyMovesController::class, 'upliftIcrImage'])->name('company-admin.moves.uplift-icrimage');
        Route::get('/move/uplift/icrimage/pdf/{id}', [CompanyMovesController::class, 'icrImagePDF'])->name('company-admin.moves.uplift-icrimage-pdf');
        // overflow
        Route::get('/move/uplift/overflow-icr/{id}', [CompanyMovesController::class, 'upliftOverflowIcr'])->name('company-admin.moves.uplift-overflow-icr');
        // Risk Assessment
        Route::get('/move/uplift/riskassessment/{id}', [CompanyMovesController::class, 'upliftRiskAssessment'])->name('company-admin.moves.uplift-risk-assessment');
        Route::get('/move/uplift/riskassessment/pdf/{move_id}', [CompanyMovesController::class, 'riskAssessmentPdf'])->name('company-admin.moves.uplift-risk-assessment-pdf');

        // Manage move route - delivery
        Route::get('/move/create/delivery-form/{id}', [CompanyMovesController::class, 'createDelivery'])->name('company-admin.moves.create-delivery');
        Route::post('/move/store/delivery', [CompanyMovesController::class, 'storeDelivery'])->name('company-admin.moves.store-delivery');
        Route::get('/move/show/delivery/{id}', [CompanyMovesController::class, 'showDelivery'])->name('company-admin.moves.show-delivery');
        Route::get('/move/edit/delivery/{id}', [CompanyMovesController::class, 'editDelivery'])->name('company-admin.moves.edit-delivery');
        Route::post('/move/update/delivery/{id}', [CompanyMovesController::class, 'updateDelivery'])->name('company-admin.moves.update-delivery');
        Route::get('/move/delivery/icr/{id}', [CompanyMovesController::class, 'deliveryICR'])->name('company-admin.moves.delivery-icr');
        Route::get('/move/delivery/icr/pdf/{move_id}', [CompanyMovesController::class, 'icrPdf'])->name('company-admin.moves.delivery-icr-pdf');
        Route::get('/move/delivery/premovecomment/{id}', [CompanyMovesController::class, 'delivery_commentPrePdf'])->name('company-admin.moves.delivery-movecomment-pdf');
        Route::get('/move/delivery/premove/comment/{move_id}', [CompanyMovesController::class, 'deliverypre'])->name('company-admin.moves.delivery-pre-move-comment-pdf');
        Route::get('/move/delivery/postmovecomment/{id}', [CompanyMovesController::class, 'delivery_commentPostPdf'])->name('company-admin.moves.delivery-postmovecomment-pdf');
        Route::get('/move/delivery/postmove/comment/{move_id}', [CompanyMovesController::class, 'deliverypost'])->name('company-admin.moves.delivery-post-move-comment-pdf');
        Route::get('/move/delivery/icrimage/{id}', [CompanyMovesController::class, 'deliveryIcrImage'])->name('company-admin.moves.delivery-icrimage');
        Route::get('/move/delivery/icrimage/pdf/{id}', [CompanyMovesController::class, 'icrImagePDF'])->name('company-admin.moves.delivery-icrimage-pdf');
        // Risk Assessment
        Route::get('/move/delivery/riskassessment/{id}', [CompanyMovesController::class, 'deliveryRiskAssessment'])->name('company-admin.moves.delivery-risk-assessment');
        Route::get('/move/delivery/riskassessment/pdf/{id}', [CompanyMovesController::class, 'riskAssessmentPdf'])->name('company-admin.moves.delivery-risk-assessment-pdf');

        // Manage move route - transload
        Route::get('/move/transload/activity/{id}', [CompanyMovesController::class, 'transloadActivity'])->name('company-admin.moves.transload-activity');
        Route::get('/move/transload/icr/{id}', [CompanyMovesController::class, 'transloadICR'])->name('company-admin.moves.transload-icr');
        Route::get('/move/transload/icr/pdf/{move_id}', [CompanyMovesController::class, 'transloadICRPDF'])->name('company-admin.moves.transload-icr-pdf');

      });

      Route::post('/move/send-email-delivery-icr', [CompanyMovesController::class, 'sendEmailIcr'])->name('company-admin.moves.send-email-delivery-icr');
      //manage move route-screen
      Route::get('/move/screen/icr/{id}', [CompanyMovesController::class, 'screenICR'])->name('company-admin.moves.screen-icr');
      Route::get('/move/screen/icr/pdf/{move_id}', [CompanyMovesController::class, 'screenicrPdf'])->name('company-admin.moves.screen-icr-pdf');
    });
    // }else{
    // Route::get('/home', function(){
    // 	// return 'here';
    // })->name('company-admin.home');
    Route::get('/home', [CompanyDashboardController::class, 'index'])->name('company-admin.home');
    Route::get('/change-password', [CompanyDashboardController::class, 'changePasswordForm'])->name('company-admin.change-password');
    Route::post('/change-password', [CompanyDashboardController::class, 'changePassword'])->name('company-admin.change-password.store');
    Route::get('/profile', [CompanyDashboardController::class, 'profile'])->name('company-admin.profile');
    Route::match(['get', 'post'], '/edit-profile', [CompanyDashboardController::class, 'EditProfile'])->name('company-admin.edit-profile');
    Route::post('/profile/add-on', [CompanyDashboardController::class, 'addOnUSer'])->name('company-admin.profile.add-on');
    Route::get('/profile/subscription/extend', [CompanyDashboardController::class, 'extendPlan'])->name('company-admin.extend-plan');
    //fp
    Route::post('/profile/subscription/selectedPlan', [CompanyDashboardController::class, 'selectedPlan'])->name('company-admin.selected-plan');
    Route::post('/profile/subscription/payment', [CompanyDashboardController::class, 'planPaymentGateway'])->name('company-admin.subcription.payment');
    Route::post('/profile/refer-friend', [CompanyDashboardController::class, 'referFriend'])->name('company-admin.refer-friend');

    //Manage Users
    Route::match(['get', 'post'], '/device', [CompanyUserController::class, 'index'])->name('company-admin.user');
    Route::get('/device/create-device', [CompanyUserController::class, 'create'])->name('company-admin.user.create');
    Route::post('/device/store-device', [CompanyUserController::class, 'store'])->name('company-admin.user.store');
    Route::get('/device/view-device/{id}', [CompanyUserController::class, 'show'])->name('company-admin.user.view');
    Route::get('/device/delete-device/{id}', [CompanyUserController::class, 'delete'])->name('company-admin.user.delete');
    Route::get('/device/edit-device/{id}', [CompanyUserController::class, 'edit'])->name('company-admin.user.edit');
    Route::post('/device/update-device/{id}', [CompanyUserController::class, 'update'])->name('company-admin.user.update');
    Route::post('/device/change-status', [CompanyUserController::class, 'changeStatus'])->name('company-admin.user.status');
    Route::post('/device/location-fetch', [CompanyUserController::class, 'locationFetch'])->name('company-admin.user.locationFetch');
    Route::get('device/logout-device/{id}', [CompanyUserController::class, 'logoutUser'])->name('company-admin.user.logout');
    Route::get('/device/userDetails', [CompanyUserController::class, 'deleteUserDetails'])->name('company-admin.user.userDetails');

    //Agents routes
    Route::get('/agents', [AgentController::class, 'index'])->name('company-admin.agents');
    Route::get('/agents/view-agents/{id}', [AgentController::class, 'show'])->name('company-admin.agents.view');
    Route::get('/agents/create-agents', [AgentController::class, 'create'])->name('company-admin.subscription.create');
    Route::post('/agents/store-agents', [AgentController::class, 'store'])->name('company-admin.agents.store');
    Route::get('/agents/edit-agents/{id}', [AgentController::class, 'edit'])->name('company-admin.agents.edit');
    Route::post('/agents/update-agents/{id}', [AgentController::class, 'update'])->name('company-admin.agents.update');
    Route::get('/agents/delete-agents/{id}', [AgentController::class, 'delete'])->name('company-admin.agents.delete');
    Route::post('/agents/change-status', [AgentController::class, 'changeStatus'])->name('company-admin.agents.change-status');
    Route::post('/agents/location-fetch', [AgentController::class, 'locationFetch'])->name('company-admin.agents.locationFetch');
    Route::post('/agents/fetch-agent', [AgentController::class, 'fetchAgent'])->name('company-admin.agents.fetchAgent');

    // Manage moves route
    Route::get('/move/{activeTab?}', [CompanyMovesController::class, 'index'])->name('company-admin.move');
    Route::post('/move/change-status', [CompanyMovesController::class, 'changeStatus'])->name('company-admin.move.change-status');
    Route::get('/move/delete/{id}', [CompanyMovesController::class, 'deleteMove'])->name('company-admin.moves.delete');
    Route::post('/move/get-agent', [CompanyMovesController::class, 'getAgent'])->name('company-admin.moves.get-agent');
    Route::post('/move/archive-move', [CompanyMovesController::class, 'archiveMove'])->name('company-admin.moves.archive-move');
    Route::get('/archive-move/archive', [CompanyMovesController::class, 'archiveIndex'])->name('company-admin.archive');
    Route::post('/move/unarchive-move', [CompanyMovesController::class, 'unarchiveMove'])->name('company-admin.moves.unarchive-move');

    // Manage move route - uplift
    Route::get('/move/create/uplift', [CompanyMovesController::class, 'createUplift'])->name('company-admin.move.create-uplift');
    Route::post('/move/create/delivery', [CompanyMovesController::class, 'storeUplift'])->name('company-admin.moves.store-uplift');
    Route::get('/move/show/uplift/{id}', [CompanyMovesController::class, 'showUplift'])->name('company-admin.moves.show-uplift');
    Route::get('/move/edit/uplift/{id}', [CompanyMovesController::class, 'editUplift'])->name('company-admin.moves.edit-uplift');
    Route::post('/move/update/uplift/{id}', [CompanyMovesController::class, 'updateUplift'])->name('company-admin.moves.update-uplift');
    Route::get('/move/uplift/send-to-delivery/{id}', [CompanyMovesController::class, 'sendToDelivery'])->name('company-admin.moves.send-to-delivery');
    Route::get('/move/uplift/icr/{id}', [CompanyMovesController::class, 'upliftICR'])->name('company-admin.moves.uplift-icr');
    Route::get('/move/uplift/icr/pdf/{move_id}', [CompanyMovesController::class, 'icrPdf'])->name('company-admin.moves.uplift-icr-pdf');
    Route::get('/move/uplift/premovecomment/{id}/{company_id}', [CompanyMovesController::class, 'upliftPreComment'])->name('company-admin.moves.uplift-movecomment-pdf');
    Route::get('/move/uplift/premove/comment/{move_id}/{company_id}', [CompanyMovesController::class, 'commentPrePdf'])->name('company-admin.moves.uplift-pre-move-comment-pdf');
    Route::get('/move/uplift/postmovecomment/{id}/{company_id}', [CompanyMovesController::class, 'upliftPostComment'])->name('company-admin.moves.uplift-postmovecomment-pdf');
    Route::get('/move/uplift/postmove/comment/{move_id}/{company_id}', [CompanyMovesController::class, 'commentPostPdf'])->name('company-admin.moves.uplift-post-move-comment-pdf');
    Route::get('/move/uplift/icrimage/{id}', [CompanyMovesController::class, 'upliftIcrImage'])->name('company-admin.moves.uplift-icrimage');
    Route::get('/move/uplift/icrimage/pdf/{id}', [CompanyMovesController::class, 'icrImagePDF'])->name('company-admin.moves.uplift-icrimage-pdf');
    Route::get('/move/uplift/overflow-icr/{id}', [CompanyMovesController::class, 'upliftOverflowIcr'])->name('company-admin.moves.uplift-overflow-icr');
    Route::get('/move/uplift/overflow/pdf/{move_id}', [CompanyMovesController::class, 'overflowIcr'])->name('company-admin.moves.uplift-overflowIcr-pdf');
    // Risk Assessment
    Route::get('/move/uplift/riskassessment/{id}', [CompanyMovesController::class, 'upliftRiskAssessment'])->name('company-admin.moves.uplift-risk-assessment');
    Route::get('/move/uplift/riskassessment/pdf/{move_id}', [CompanyMovesController::class, 'riskAssessmentPdf'])->name('company-admin.moves.uplift-risk-assessment-pdf');

    // Manage move route - delivery
    Route::get('/move/create/delivery-form/{id}', [CompanyMovesController::class, 'createDelivery'])->name('company-admin.moves.create-delivery');
    Route::post('/move/store/delivery', [CompanyMovesController::class, 'storeDelivery'])->name('company-admin.moves.store-delivery');
    Route::get('/move/show/delivery/{id}', [CompanyMovesController::class, 'showDelivery'])->name('company-admin.moves.show-delivery');
    Route::get('/move/edit/delivery/{id}', [CompanyMovesController::class, 'editDelivery'])->name('company-admin.moves.edit-delivery');
    Route::post('/move/update/delivery/{id}', [CompanyMovesController::class, 'updateDelivery'])->name('company-admin.moves.update-delivery');
    Route::get('/move/delivery/icr/{id}', [CompanyMovesController::class, 'deliveryICR'])->name('company-admin.moves.delivery-icr');
    Route::get('/move/delivery/icr/pdf/{move_id}', [CompanyMovesController::class, 'icrPdf'])->name('company-admin.moves.delivery-icr-pdf');
    Route::get('/move/delivery/premovecomment/{id}', [CompanyMovesController::class, 'delivery_commentPrePdf'])->name('company-admin.moves.delivery-movecomment-pdf');
    Route::get('/move/delivery/premove/comment/{move_id}', [CompanyMovesController::class, 'deliverypre'])->name('company-admin.moves.delivery-pre-move-comment-pdf');
    Route::get('/move/delivery/postmovecomment/{id}', [CompanyMovesController::class, 'delivery_commentPostPdf'])->name('company-admin.moves.delivery-postmovecomment-pdf');
    Route::get('/move/delivery/postmove/comment/{move_id}', [CompanyMovesController::class, 'deliverypost'])->name('company-admin.moves.delivery-post-move-comment-pdf');
    Route::get('/move/delivery/icrimage/{id}', [CompanyMovesController::class, 'deliveryIcrImage'])->name('company-admin.moves.delivery-icrimage');
    Route::get('/move/delivery/icrimage/pdf/{id}', [CompanyMovesController::class, 'icrImagePDF'])->name('company-admin.moves.delivery-icrimage-pdf');
    // Risk Assessment
    Route::get('/move/delivery/riskassessment/{id}', [CompanyMovesController::class, 'deliveryRiskAssessment'])->name('company-admin.moves.delivery-risk-assessment');
    Route::get('/move/delivery/riskassessment/pdf/{id}', [CompanyMovesController::class, 'riskAssessmentPdf'])->name('company-admin.moves.delivery-risk-assessment-pdf');

    // Manage move route - transload
    Route::get('/move/transload/activity/{id}', [CompanyMovesController::class, 'transloadActivity'])->name('company-admin.moves.transload-activity');
    Route::get('/move/transload/icr/{id}', [CompanyMovesController::class, 'transloadICR'])->name('company-admin.moves.transload-icr');
    Route::get('/move/transload/icr/pdf/{move_id}', [CompanyMovesController::class, 'transloadICRPDF'])->name('company-admin.moves.transload-icr-pdf');

    // manage move route-screen
    Route::get('/move/screen/icr/{id}', [CompanyMovesController::class, 'screenICR'])->name('company-admin.moves.screen-icr');
    Route::get('/move/screen/icr/pdf/{move_id}', [CompanyMovesController::class, 'screenicrPdf'])->name('company-admin.moves.screen-icr-pdf');

    // }
  });

  // support-admin
  Route::group(['prefix' => 'support-admin'], function () {
    Route::get('/', function () {
      return redirect('/admin/login');
    })->name('/');

    Route::group(['middleware' => 'auth'], function () {
      Route::group(['middleware' => ['role:support-admin']], function () {

        Route::get('/home', [SupportDashboardController::class, 'index'])->name('support-admin.home');
        Route::get('/profile', [SupportProfileController::class, 'index'])->name('support-admin.profile');
        Route::get('/changepassword', [SupportChangePasswordController::class, 'index'])->name('support-admin.changepassword');
        Route::post('/changepassword', [SupportChangePasswordController::class, 'store'])->name('support-admin.changepassword.store');
        Route::match(['get', 'post'], '/edit-user-profile', [SupportUserController::class, 'EditProfile'])->name('support-admin.edit-user-profile');

        // companies
        Route::match(['get', 'post'], '/companies', [SupportCompaniesController::class, 'index'])->name('support-admin.companies');
        Route::post('/companies/reset', [SupportCompaniesController::class, 'reset'])->name('support-admin.companies.reset');
        Route::post('/companies/change-status', [SupportCompaniesController::class, 'changeStatus'])->name('support-admin.companies.status');
        Route::get('/companies/view-company/{id}', [SupportCompaniesController::class, 'show'])->name('support-admin.companies.view');
        Route::post('/companies/location-fetch', [SupportCompaniesController::class, 'locationFetch'])->name('support-admin.companies.locationFetch');

        // moves route
        Route::match(['get', 'post'], '/move/{activeTab?}', [SupportMoveController::class, 'index'])->name('support-admin.move');
        Route::get('/move/view-move/{id}', [SupportMoveController::class, 'show'])->name('support-admin.move.view');
        Route::get('/move/view-delivery/{id}', [SupportMoveController::class, 'showDelivery'])->name('support-admin.move.delivery_view');

        // cms
        Route::match(['get', 'post'], '/cms', [SupportCmsController::class, 'index'])->name('support-admin.cms');
        Route::get('/cms/create-cms', [SupportCmsController::class, 'create'])->name('support-admin.cms.create');
        Route::post('/cms/store-cms', [SupportCmsController::class, 'store'])->name('support-admin.cms.store');
        Route::get('/cms/view-cms/{id}', [SupportCmsController::class, 'show'])->name('support-admin.cms.view');
        Route::get('/cms/edit-cms/{id}', [SupportCmsController::class, 'edit'])->name('support-admin.cms.edit');
        Route::post('/cms/update-cms/{id}', [SupportCmsController::class, 'update'])->name('support-admin.cms.update');
      });
    });
  });
});

Route::get('/company-admin/profile/fetch-stripe-publishable', function () {
  $publishableKey = array_key_exists('STRIPE_KEY', $_SERVER) ? $_SERVER['STRIPE_KEY'] : env("STRIPE_KEY");
  echo json_encode(['publishableKey' => $publishableKey]);
})->name('fetch-stripe-publishable');