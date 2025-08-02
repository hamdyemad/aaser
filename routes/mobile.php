<?php

use App\Http\Mobile\Controllers\AsserHistoryController;
use App\Http\Mobile\Controllers\ContactController;
use App\Http\Mobile\Controllers\EntertainmentActivityController;
use App\Http\Mobile\Controllers\EpisodeController;
use App\Http\Mobile\Controllers\ExhibitionConferenceController;
use App\Http\Mobile\Controllers\GuideController;
use App\Http\Mobile\Controllers\HomeController;
use App\Http\Mobile\Controllers\ParticipantController;
use App\Http\Mobile\Controllers\ShepherdController;
use App\Http\Mobile\Controllers\TouristAttractionController;
use App\Http\Mobile\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => "auth"], function($router) {
    $router->post('register',[UserController::class,'register']);
    $router->post('login',[UserController::class,'login']);
    $router->post('reset_password',[UserController::class,'resetPassword']);
    $router->post('confirm_code',[UserController::class,'confirmCode']);

    // $router->middleware(['auth:sanctum'])->group(function ($router) {
    //     $router->get('profile',[UserController::class,'getProfile']);
    //     $router->post('profile',[UserController::class,'edit']);
    // });
});

$router->middleware(['auth:sanctum'])->group(function ($router) {

    $router->get('home',[HomeController::class,'index']);

    $router->group(['prefix' => 'shepherds'], function($router) {
        $router->get('/',[ShepherdController::class,'all']);
        $router->get('/{id}',[ShepherdController::class,'show']);
    });

    $router->group(['prefix' => 'participants'], function($router) {
        $router->get('/',[ParticipantController::class,'all']);
        $router->get('/{id}',[ParticipantController::class,'show']);
    });

    $router->group(['prefix' => 'histories'], function($router) {
        $router->get('/',[AsserHistoryController::class,'all']);
        $router->get('/{id}',[AsserHistoryController::class,'show']);
    });

    $router->group(['prefix' => 'exhibition-conferences'], function($router) {
        $router->get('/',[ExhibitionConferenceController::class,'all']);
        $router->get('/{id}',[ExhibitionConferenceController::class,'show']);
        $router->post('/visitors',[ExhibitionConferenceController::class,'addVisitor']);
        $router->post('/participants',[ExhibitionConferenceController::class,'addParticipant']);
    });


    $router->group(['prefix' => 'entertainment-activities'], function($router) {
        $router->get('/',[EntertainmentActivityController::class,'all']);
        $router->get('/{id}',[EntertainmentActivityController::class,'show']);
    });


    $router->post('contact-us',[ContactController::class,'addContact']);


    $router->group(['prefix' => 'episodes'], function($router) {
        $router->get('/',[EpisodeController::class,'all']);
        $router->get('/{id}',[EpisodeController::class,'show']);
    });


    $router->group(['prefix' => 'tourist_attractions'], function($router) {
        $router->get('/',[TouristAttractionController::class,'all']);
        $router->post('/rates',[TouristAttractionController::class,'add_rate']);
        $router->post('/services',[TouristAttractionController::class,'addTouristService']);
        $router->get('/{id}',[TouristAttractionController::class,'show']);
    });


    $router->group(['prefix' => 'guides'], function($router) {
        $router->get('/',[GuideController::class,'all']);
        $router->post('/offers',[GuideController::class,'addOfferRequest']);
        $router->get('/{id}',[GuideController::class,'show']);
    });


});

