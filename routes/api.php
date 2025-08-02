<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShepherdController;
use App\Http\Controllers\StockPointController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\AsserHistoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReplacePointController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\TouristAttractionController;
use App\Http\Controllers\ExhibitionConferenceController;
use App\Http\Controllers\EntertainmentActivityController;


// the website
Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);
Route::post('reset_password',[UserController::class,'resetPassword']);
Route::post('confirm_code',[UserController::class,'confirmCode']);
//
Route::post('add_view_epsiode/{id}',[UserController::class,'addViewEpsiode']);
Route::post('add_view_touriste_attraction/{id}',[UserController::class,'addViewTouristeAttraction']);
//
Route::post('add_rate_touriste_attraction/{id}',[UserController::class,'addRateTouristeAttraction']);
Route::post('add_rate_guide/{id}',[UserController::class,'addRateGuide']);
    Route::post('add_point_to_user',[UserController::class,'addPointToUser']);
//
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('profile',[UserController::class,'getProfile']);
    Route::get('all_notifications',[NotificationController::class,'all']);
    Route::get('count_notifications',[NotificationController::class,'countNotification']);
    Route::post('send_notifications',[NotificationController::class,'sendNotification']);
    Route::post('logout',[UserController::class,'logout']);
    Route::post('edit_profile',[UserController::class,'edit']);
    Route::post('edit_user/{id}',[UserController::class,'editUser']);
    Route::get('all_users',[UserController::class,'allUsers']);
    Route::get('view_user/{id}',[UserController::class,'viewUser']);
    Route::get('view_points',[UserController::class,'viewPoints']);
    Route::get('view_user_points/{id}',[UserController::class,'viewUserPoints']);
    ////////////////
    Route::post('add_point_for_user',[UserController::class,'addPointForUser']);
    // Route::post('add_point_to_user',[UserController::class,'addPointToUser']);
    ////////////////
    Route::post('add_visitor',[ExhibitionConferenceController::class,'addVisitor']);
    Route::post('add_participant',[ExhibitionConferenceController::class,'addParticipant']);
    /////////////////
    Route::post('add_tourist_service',[TouristAttractionController::class,'touristeService']);
    //
    Route::post('add_activity_service',[EntertainmentActivityController::class,'activityService']);
});

Route::get('test', function() {
    return response()->json(['message' => 'API is working']);
});

// the admin
Route::prefix('admin')->group(function () {
    Route::post('login',[AdminController::class,'login']);
    Route::get('show_setting',[SettingController::class,'show']);
    Route::post('add_answer',[ContactController::class,'addAnswer']);
    Route::post('login_service_provider',[ServiceProviderController::class,'Login']);
    Route::get('show_tourist_attraction/{id}',[TouristAttractionController::class,'show']);
    Route::get('all_tourist_attraction',[TouristAttractionController::class,'all']);
    Route::get('show_asser_history/{id}',[AsserHistoryController::class,'show']);
    Route::get('all_asser_history',[AsserHistoryController::class,'all']);
    Route::get('show_episode/{id}',[EpisodeController::class,'show']);
    Route::get('all_episode',[EpisodeController::class,'all']);
    Route::post('done_request/{id}',[RewardController::class,'doneRequest']);
    Route::post('confirm_request/{id}',[RewardController::class,'confirmRequest']);
    Route::get('show_participant/{id}',[ParticipantController::class,'show']);
    Route::get('all_participant',[ParticipantController::class,'all']);
    Route::get('show_shepherd/{id}',[ShepherdController::class,'show']);
    Route::get('all_shepherd',[ShepherdController::class,'all']);
    Route::get('show_entertainment_activity/{id}',[EntertainmentActivityController::class,'show']);
    Route::get('all_entertainment_activity',[EntertainmentActivityController::class,'all']);
    Route::get('show_exhibition_conference/{id}',[ExhibitionConferenceController::class,'show']);
    Route::get('all_exhibition_conference',[ExhibitionConferenceController::class,'all']);
    Route::get('all_exhibition_conference_site',[ExhibitionConferenceController::class,'allSite']);
    Route::get('show_guide/{id}',[GuideController::class,'show']);
    Route::get('all_guide',[GuideController::class,'all']);
    Route::get('all_types',[GuideController::class,'allTypes']);
    Route::post('add_type',[GuideController::class,'addType']);
    Route::post('delete_type/{id}',[GuideController::class,'deleteType']);
    Route::get('show_ad/{id}',[AdController::class,'show']);
    Route::get('all_ad',[AdController::class,'all']);
    Route::get('show_reward/{id}',[RewardController::class,'showReward']);
    Route::get('all_reward',[RewardController::class,'allReward']);
    Route::get('all_image',[ImageController::class,'all']);
    Route::get('show_replace_point/{id}',[ReplacePointController::class,'show']);
    Route::get('all_replace_point',[ReplacePointController::class,'all']);
    Route::get('show_stock_point/{id}',[StockPointController::class,'show']);
    Route::get('all_stock_point',[StockPointController::class,'all']);
    Route::post('add_edkjfnbkjdfgnbkjnkgmail',[EmailController::class,'addencwioath']);
    Route::post('edit_ekjdfngbjklsfngjhklbnmail/{id}',[EmailController::class,'edit']);
    Route::post('delete_efbgjhkfgngjkfbnjhgbmail/{id}',[EmailController::class,'delete']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('add_contact',[ContactController::class,'addContact']);
        Route::get('profile',[AdminController::class,'getProfile']);
        Route::post('logout',[AdminController::class,'logout']);
        Route::post('edit_profile',[AdminController::class,'edit']);
        Route::post('add_admin',[AdminController::class,'addAdmin']);
        Route::post('edit_admin/{id}',[AdminController::class,'editAdmin']);
        Route::post('delete_admin/{id}',[AdminController::class,'deleteAdmin']);
        Route::get('show_admin/{id}',[AdminController::class,'showAdmin']);
        Route::get('all_admins',[AdminController::class,'allAdmins']);
        /////
        Route::post('edit_setting',[SettingController::class,'edit']);
        /////
        Route::get('all_contacts',[ContactController::class,'allContacts']);
        Route::get('view_contact/{id}',[ContactController::class,'viewContact']);
        Route::post('delete_contact/{id}',[ContactController::class,'deleteContact']);
        /////
        Route::post('add_tourist_attraction',[TouristAttractionController::class,'add']);
        Route::post('edit_tourist_attraction/{id}',[TouristAttractionController::class,'edit']);
        Route::post('delete_tourist_attraction/{id}',[TouristAttractionController::class,'delete']);
        /////
        Route::post('add_asser_history',[AsserHistoryController::class,'add']);
        Route::post('edit_asser_history/{id}',[AsserHistoryController::class,'edit']);
        Route::post('delete_asser_history/{id}',[AsserHistoryController::class,'delete']);
        /////
        Route::post('add_episode',[EpisodeController::class,'add']);
        Route::post('edit_episode/{id}',[EpisodeController::class,'edit']);
        Route::post('delete_episode/{id}',[EpisodeController::class,'delete']);
        /////
        Route::post('add_participant',[ParticipantController::class,'add']);
        Route::post('edit_participant/{id}',[ParticipantController::class,'edit']);
        Route::post('delete_participant/{id}',[ParticipantController::class,'delete']);
        /////
        Route::post('add_shepherd',[ShepherdController::class,'add']);
        Route::post('edit_shepherd/{id}',[ShepherdController::class,'edit']);
        Route::post('delete_shepherd/{id}',[ShepherdController::class,'delete']);
        /////
        Route::post('add_entertainment_activity',[EntertainmentActivityController::class,'add']);
        Route::post('edit_entertainment_activity/{id}',[EntertainmentActivityController::class,'edit']);
        Route::post('delete_entertainment_activity/{id}',[EntertainmentActivityController::class,'delete']);
        /////
        Route::post('add_exhibition_conference',[ExhibitionConferenceController::class,'add']);
        Route::post('edit_exhibition_conference/{id}',[ExhibitionConferenceController::class,'edit']);
        Route::post('delete_exhibition_conference/{id}',[ExhibitionConferenceController::class,'delete']);
        /////
        Route::post('add_guide',[GuideController::class,'add']);
        Route::post('edit_guide/{id}',[GuideController::class,'edit']);
        Route::post('delete_guide/{id}',[GuideController::class,'delete']);
        /////
        Route::post('add_reward',[RewardController::class,'addReward']);
        Route::post('edit_reward/{id}',[RewardController::class,'editReward']);
        Route::post('delete_reward/{id}',[RewardController::class,'deleteReward']);
        Route::post('add_reward_request',[RewardController::class,'addRewardRequest']);
        Route::post('add_offer_request',[RewardController::class,'addOfferRequest']);
        Route::get('all_reward_request',[RewardController::class,'allRewardRequest']);


        /////
        Route::post('add_ad',[AdController::class,'add']);
        Route::post('edit_ad/{id}',[AdController::class,'edit']);
        Route::post('delete_ad/{id}',[AdController::class,'delete']);
        /////
        Route::post('add_service_provider',[ServiceProviderController::class,'add']);
        Route::post('last_active',[ServiceProviderController::class,'lastActive']);
        Route::post('edit_service_provider/{id}',[ServiceProviderController::class,'edit']);
        Route::post('delete_service_provider/{id}',[ServiceProviderController::class,'delete']);
        Route::get('show_service_provider/{id}',[ServiceProviderController::class,'show']);
        Route::get('all_service_provider',[ServiceProviderController::class,'all']);
        Route::get('service_provider_people',[ServiceProviderController::class,'serviceProviderPeople']);
        /////
        Route::post('edit_image',[ImageController::class,'edit']);
        Route::post('delete_image/{id}',[ImageController::class,'delete']);
        /////
        Route::post('add_permission',[RolePermissionController::class,'addPermission']);
        Route::post('delete_permission/{id}',[RolePermissionController::class,'deletePermission']);
        Route::get('all_permission',[RolePermissionController::class,'allPermission']);
        Route::post('add_role',[RolePermissionController::class,'addRole']);
        Route::post('edit_role/{id}',[RolePermissionController::class,'editRole']);
        Route::post('delete_role/{id}',[RolePermissionController::class,'deleteRole']);
        Route::get('view_role/{id}',[RolePermissionController::class,'viewRole']);
        Route::get('all_role',[RolePermissionController::class,'allRole']);
        Route::post('assign_admin_role',[RolePermissionController::class,'assignAdminRole']);
        Route::get('view_admin_role/{id}',[RolePermissionController::class,'viewAdminRole']);
        /////
        Route::post('add_stock_point',[StockPointController::class,'add']);
        Route::post('edit_stock_point/{id}',[StockPointController::class,'edit']);
        Route::post('delete_stock_point/{id}',[StockPointController::class,'delete']);
        Route::post('add_service_request',[StockPointController::class,'addServiceRequest']);
        /////
        Route::post('add_replace_point',[ReplacePointController::class,'add']);
        Route::post('edit_replace_point/{id}',[ReplacePointController::class,'edit']);
        Route::post('delete_replace_point/{id}',[ReplacePointController::class,'delete']);
        Route::post('add_replace_reward_request',[ReplacePointController::class,'addReplaceRewardRequest']);
        /////
        Route::post('add_email',[EmailController::class,'add']);
        Route::post('edit_email/{id}',[EmailController::class,'edit']);
        Route::post('delete_email/{id}',[EmailController::class,'delete']);
        Route::get('all_email',[EmailController::class,'all']);
    });
});
