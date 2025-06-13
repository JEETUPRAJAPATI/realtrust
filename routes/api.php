<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\LoginController as OwnerLoginController;
use App\Http\Controllers\Owner\NotificationController;
use App\Http\Controllers\Owner\PropertyController as OwnerPropertyController;
use App\Http\Controllers\Owner\RegisterController as OwnerRegisterController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\OtpController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\RegisterController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\PropertyController;
use App\Http\Controllers\User\ScheduleVisitController;
use App\Http\Controllers\User\UserController;
use App\Notifications\PropertyVisitScheduled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     Route::get('posts', [UserController::class, 'posts'])->name('posts');
// });


// Route::post('/send-notification', [OwnerPropertyController::class, 'sendNotification']);
// Route::get('/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
// Route::get('/notification/{id}', [NotificationController::class, 'view'])->name('notification.view');

// Global api for Frontend
Route::get('properties', [PropertyController::class, 'index']);
Route::get('properties/for-sale', [PropertyController::class, 'getForSaleProperties'])->name('for-sale');
Route::get('properties/for-rent', [PropertyController::class, 'getForRentProperties'])->name('for-rent');
Route::get('properties/for-upcoming-project', [PropertyController::class, 'getForUpCommingProject'])->name('for-upcoming-project');
Route::get('properties/{id}/show', [PropertyController::class, 'show'])->name('show');
Route::get('/features', [PropertyController::class, 'getFeatures'])->name('features');
Route::get('/amenities', [PropertyController::class, 'getAmenities'])->name('amenities');

Route::get('/share-url', [PropertyController::class, 'shareUrl'])->name('share-url');


Route::get('additional-details', [UserController::class, 'additionalDetails'])->name('additional-details');
Route::post('/inquery', [ContactController::class, 'inquery']);
Route::post('/contacts', [ContactController::class, 'store']);
// Route::get('cities', [UserController::class, 'get_cities'])->name('cities');
Route::get('sliders', [UserController::class, 'sliders'])->name('sliders');
Route::get('posts', [UserController::class, 'posts'])->name('posts');
Route::get('locality', [UserController::class, 'locality'])->name('locality');
Route::get('socity', [UserController::class, 'socity'])->name('socity');
Route::get('galaryImage', [PropertyController::class, 'galaryImages']);
Route::get('/{search}', [PropertyController::class, 'filterProperties']);

// Route::post('/filter', [PropertyController::class, 'filterItem'])->name('filter');


// Route::post('razorpay-payment',[PaymentController::class,'store'])->name('razorpay.payment.store');


// User api
Route::group(['prefix' => 'user', 'namespace' => 'user'], function () {
    Route::post('otp/send', [OtpController::class, 'requestOtp']);
    Route::post('login', [LoginController::class, 'login'])->name('user.login');
    Route::post('register', [RegisterController::class, 'register'])->name('user.register');
    Route::post('phonepe/callback', [PaymentController::class, 'paymentCallback'])->name('phonepe.callback');
    Route::post('create-order', [PaymentController::class, 'createOrder'])->name('createOrder');

    Route::group(['middleware' => 'user'], function () {

        Route::post('upload-documents', [DashboardController::class, 'uploadDocuments'])->name('user.upload-documents');
        Route::post('upload-documents-update', [DashboardController::class, 'uploadDocumentsUpdate'])->name('user.update.documents');
        Route::get('get-documents-list', [DashboardController::class, 'getDocumentsList'])->name('get.documents.list');
        Route::get('/property/agreement', [DashboardController::class, 'getAgreement'])->name('property.agreement');
        Route::post('upload-agreement', [DashboardController::class, 'upload_agreement'])->name('user.upload-agreement');
        Route::delete('documents/delete', [DashboardController::class, 'deleteDocuments']);
        
        // Route for history schedule property
        Route::get('history_schedule_properties', [DashboardController::class, 'historyScheduleProperties'])->name('history_schedule_properties');
        // Route for get schedule property timing
        Route::get('get_schedule_property_timing', [DashboardController::class, 'getSchedulePropertyTiming'])->name('user.get_schedule_property_timing');
        Route::post('logout', [LoginController::class, 'logout'])->name('user.logout');
        // Route::post('create-order', [PaymentController::class, 'createOrder'])->name('createOrder');
        Route::post('/verify-payment', [PaymentController::class, 'paymentCallback']);
    });
});


Route::group(['prefix' => 'user', 'namespace' => 'user', 'middleware' => 'user'], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('user.dashboard.index');
    Route::get('profile', [DashboardController::class, 'profile'])->name('user.profile');
    Route::post('profile', [DashboardController::class, 'profileUpdate'])->name('user.profile.update');

    Route::get('properties', [PropertyController::class, 'index'])->name('user.properties.index');
    Route::post('properties/properties_list', [PropertyController::class, 'properties_list'])->name('properties.properties_list');
    Route::get('properties/{unique_id}', [PropertyController::class, 'show'])->name('properties.show');

    Route::get('payment-history', [PaymentController::class, 'paymentHistory'])->name('user.payment-history');
})->name('user.dashboard');


// Route::group(['middleware' => 'user,check.token.expiration'], function () {
//     // Schedule properties
//     Route::post('schedule_properties', [ScheduleVisitController::class, 'scheduleVisit'])->name('schedule_properties');
// });

Route::middleware(['user', 'check.token.expiration'])->group(function () {
    Route::post('schedule_properties', [ScheduleVisitController::class, 'scheduleVisit'])->name('schedule_properties');
    Route::post('join_schedule_properties', [ScheduleVisitController::class, 'joinScheduleVisit'])->name('join_schedule_properties');
});


// Owner api
Route::group(['prefix' => 'owner', 'namespace' => 'owner'], function () {
    Route::post('/register', [OwnerRegisterController::class, 'register'])->name('owner.register');
    Route::post('otp/send', [OtpController::class, 'requestOtp']);
    Route::post('/login', [OwnerLoginController::class, 'login'])->name('owner.login');
    Route::group(['middleware' => 'owner'], function () {
        Route::post('upload-documents', [OwnerDashboardController::class, 'uploadDocuments'])->name('owner.upload-documents');
        Route::post('upload-agreement', [OwnerDashboardController::class, 'upload_agreement'])->name('owner.upload-agreement');
        Route::get('get-tenant-document', [OwnerDashboardController::class, 'getTenantDocument'])->name('get.tenant.document');
        // Route::get('get-documents', [OwnerDashboardController::class, 'getDocuments'])->name('owner.get-documents');
        // Route::get('get-agreement', [OwnerDashboardController::class, 'getAgreement'])->name('owner.get-agreement');
        Route::get('/property/agreement', [OwnerDashboardController::class, 'getAgreement'])->name('property.agreement');
        Route::post('agreement', [OwnerDashboardController::class, 'uploadAgreementDetail'])->name('agreement');
        Route::get('get-notification', [OwnerDashboardController::class, 'getNotification'])->name('owner.notification');
        Route::get('/notifications/read/{id}', [OwnerDashboardController::class, 'markAsRead'])->name('notifications.read');

        Route::post('logout', [OwnerLoginController::class, 'logout'])->name('owner.logout');
    });
});
Route::group(['prefix' => 'owner', 'namespace' => 'properties', 'middleware' => 'owner'], function () {

    Route::get('dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard.index');
    Route::get('profile', [OwnerDashboardController::class, 'profile'])->name('owner.profile');

    Route::post('profile', [OwnerDashboardController::class, 'profileUpdate'])->name('owner.profile.update');
   Route::get('property/listing', [PropertyController::class, 'property_list'])->name('owner.property.listing');
    Route::get('properties', [OwnerPropertyController::class, 'properties_list'])->name('properties.index');
    Route::post('/properties/properties_list', [OwnerPropertyController::class, 'properties_list'])->name('properties.properties_list');
    Route::post('properties/store', [OwnerPropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{unique_id}', [OwnerPropertyController::class, 'show'])->name('properties.show');
    Route::get('/properties/{unique_id}/edit', [OwnerPropertyController::class, 'edit'])->name('properties.edit');
    Route::POST('properties/{unique_id}', [OwnerPropertyController::class, 'update'])->name('properties.update');
    Route::delete('properties/{unique_id}', [OwnerPropertyController::class, 'destroy'])->name('properties.destroy');


    Route::get('intrested_user', [OwnerPropertyController::class, 'getPropertyIntesrtListUser'])->name('intrested_user');
})->name('owner.dashboard');




// routes/api.php
Route::match(['get', 'post'], '/webhook/whatsapp', [ContactController::class, 'handle']);

// Route::post('/create-order', [PaymentController::class, 'store']);
