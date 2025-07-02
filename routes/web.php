<?php

use App\Events\LocationUpdated;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AmenitiesController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FieldManagerController as AdminFieldManagerController;
use App\Http\Controllers\Admin\InqueryController;
use App\Http\Controllers\Admin\LocalityController as AdminLocalityController;
use App\Http\Controllers\Admin\OwnerController as AdminOwnerController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\SchedulePropertyController as AdminSchedulePropertyController;
use App\Http\Controllers\Admin\ScheduleVisitController as AdminScheduleVisitController;
use App\Http\Controllers\Admin\SocietyController as AdminSocietyController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FieldManager\DashboardController as FieldManagerDashboardController;
use App\Http\Controllers\FieldManager\LocationController;
use App\Http\Controllers\FieldManager\LoginController as FieldManagerLoginController;
use App\Http\Controllers\FieldManager\VisiterController;
use App\Http\Controllers\SignaturePDFController;
use App\Http\Controllers\Staff\ContactController as StaffContactController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\FieldManagerController;
use App\Http\Controllers\Staff\InqueryController as StaffInqueryController;
use App\Http\Controllers\Staff\LocalityController;
use App\Http\Controllers\Staff\LoginController as StaffLoginController;
use App\Http\Controllers\Staff\OwnerController;
use App\Http\Controllers\Staff\PostController as StaffPostController;
use App\Http\Controllers\Staff\PropertyController as StaffPropertyController;
use App\Http\Controllers\Staff\RecordingController;
use App\Http\Controllers\Staff\SchedulePropertyController;
use App\Http\Controllers\Staff\ScheduleVisitController;
use App\Http\Controllers\Staff\SocietyController;
use App\Http\Controllers\Staff\UserController as StaffUserController;
use App\Http\Controllers\Staff\WhatsAppController;
use App\Http\Controllers\User\PaymentController;
use App\Notifications\PropertyVisitScheduled;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\AdditionalDetailController;
use App\Http\Controllers\InvoiceController;

use Illuminate\Support\Facades\Log;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// fillup form and conform timing
Route::get('/conform-timing/{propertyId}',  [WhatsAppController::class, 'confirmTiming'])->name('confirm.timing');
Route::get('/conform-timing/field_manager/{propertyId}', [WhatsAppController::class, 'confirmTimingFieldManager'])->name('confirm.timing.field_manager');

Route::post('/confirm-timing', [WhatsAppController::class, 'confirmTimingSubmit'])->name('confirm.timing.submit');
// Auth::routes();
Route::post('phonepe/callback', [PaymentController::class, 'paymentCallback'])->name('phonepe.callback');
Route::get('staff/place-call/{uid}/{fid}', [StaffContactController::class, 'placeCall'])->name('place.call');
Route::get('staff/conference-call/{uid}/{sid}', [StaffContactController::class, 'conferenceCall'])->name('conference.call');
Auth::routes(['login' => false]);
Route::get('/', function () {
    return redirect()->route('login');
})->name('login');

// Admin login routes
Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('auth.login');

// Staff login routes
// Route::get('/staff', [StaffLoginController::class, 'index'])->name('staff');
// Route::post('staff/login', [StaffLoginController::class, 'login'])->name('staff.login');

// FieldManager login routes
// Route::get('/field_manager', [FieldManagerLoginController::class, 'index'])->name('field_manager');
// Route::post('field_manager/login', [FieldManagerLoginController::class, 'login'])->name('field_manager.login');


Route::post('/get-societies', [AdminLocalityController::class, 'getSocieties'])->name('get.societies');

Route::group(
    ['prefix' => 'field_manager', 'namespace' => 'field_manager', 'middleware' => ['field_manager'], 'as' => 'field_manager.'],
    function () {
        Route::post('logout', [FieldManagerLoginController::class, 'logout'])->name('logout');
        Route::get('dashboard', [FieldManagerDashboardController::class, 'index'])->name('dashboard');
        Route::get('profile', [FieldManagerDashboardController::class, 'profile'])->name('profile');
        Route::post('profile', [FieldManagerDashboardController::class, 'profileUpdate'])->name('profile.update');
        Route::get('changepassword', [FieldManagerDashboardController::class, 'changePassword'])->name('changepassword');
        Route::post('changepassword', [FieldManagerDashboardController::class, 'changePasswordUpdate'])->name('changepassword.update');


        Route::get('settings', [FieldManagerDashboardController::class, 'settings'])->name('settings');
        Route::post('settings', [FieldManagerDashboardController::class, 'settingStore'])->name('settings.store');


        Route::get('visiter', [VisiterController::class, 'index'])->name('visiter.index');
        Route::get('visiter/{id}/edit', [VisiterController::class, 'edit'])->name('visiter.edit');
        Route::put('visiter/{id}', [VisiterController::class, 'update'])->name('visiter.update');
        Route::POST('visiter/send-otp', [VisiterController::class, 'sendOtpMessage'])->name('visiter.send-otp');

        route::get('visiter/{id}', [VisiterController::class, 'view'])->name('visiter.view');

        Route::post('location/update/{id}', [LocationController::class, 'updateLocation'])->name('field_manager.update');
        Route::get('visiter/{id}/user', [VisiterController::class, 'userList'])->name('visiter.users');
    }
);
// Admin Controller
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['admin'], 'as' => 'admin.'], function () {

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('profile', [DashboardController::class, 'profileUpdate'])->name('profile.update');
    Route::get('changepassword', [DashboardController::class, 'changePassword'])->name('changepassword');
    Route::post('changepassword', [DashboardController::class, 'changePasswordUpdate'])->name('changepassword.update');

    // invoice routes
    Route::get('invoice/list', [InvoiceController::class, 'invoicesList'])->name('invoice.list');
    Route::get('invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('invoice/store', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('invoice/download/{id}', [InvoiceController::class, 'downloadInvoice'])->name('payments.invoice.download');
    Route::get('invoice/edit/{id}', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::post('invoice/update/{id}', [InvoiceController::class, 'update'])->name('invoice.update');


    Route::get('settings', [DashboardController::class, 'settings'])->name('settings');
    Route::post('settings', [DashboardController::class, 'settingStore'])->name('settings.store');

    // Route::resource('sliders', SliderController::class);
    Route::get('sliders', [SliderController::class, 'index'])->name('sliders.index');
    Route::get('sliders/create', [SliderController::class, 'create'])->name('sliders.create');
    Route::post('sliders', [SliderController::class, 'store'])->name('sliders.store');
    Route::get('sliders/{id}/edit', [SliderController::class, 'edit'])->name('sliders.edit');
    Route::put('sliders/{id}', [SliderController::class, 'update'])->name('sliders.update');
    route::delete('sliders/{id}', [SliderController::class, 'destroy'])->name('sliders.destroy');

    Route::get('user', [UserController::class, 'index'])->name('user.index');
    Route::get('user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('user', [UserController::class, 'store'])->name('user.store');
    Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('user/{id}', [UserController::class, 'update'])->name('user.update');
    route::delete('user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::get('user/verify/{id}', [UserController::class, 'getUserDetails'])->name('user.verify');
    Route::put('/user/{id}/update-status', [UserController::class, 'updateStatus'])->name('user.updateStatus');

    Route::get('owner', [AdminOwnerController::class, 'index'])->name('owner.index');
    Route::get('owner/create', [AdminOwnerController::class, 'create'])->name('owner.create');
    Route::post('owner', [AdminOwnerController::class, 'store'])->name('owner.store');
    Route::get('owner/{id}/edit', [AdminOwnerController::class, 'edit'])->name('owner.edit');
    Route::put('owner/{id}', [AdminOwnerController::class, 'update'])->name('owner.update');
    route::delete('owner/{id}', [AdminOwnerController::class, 'destroy'])->name('owner.destroy');


    Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('staff/permission/{id}', [StaffController::class, 'permission'])->name('staff.permission');
    Route::post('staff/update-permission', [StaffController::class, 'updatePermission'])->name('update-permission');
    Route::get('staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('staff/{id}', [StaffController::class, 'update'])->name('staff.update');
    route::delete('staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
    Route::put('/update-password/{id}', [StaffController::class, 'updatePassword'])->name('password.update');


    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    route::delete('categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('tags', [TagController::class, 'index'])->name('tags.index');
    Route::get('tags/create', [TagController::class, 'create'])->name('tags.create');
    Route::post('tags', [TagController::class, 'store'])->name('tags.store');
    Route::get('tags/{id}/edit', [TagController::class, 'edit'])->name('tags.edit');
    Route::put('tags/{id}', [TagController::class, 'update'])->name('tags.update');
    route::delete('tags/{id}', [TagController::class, 'destroy'])->name('tags.destroy');


    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('posts/{slug}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('posts/{slug}', [PostController::class, 'update'])->name('posts.update');
    route::delete('posts/{slug}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('posts/{slug}', [PostController::class, 'show'])->name('posts.show');


    Route::get('society', [AdminSocietyController::class, 'index'])->name('society.index');
    Route::get('society/create', [AdminSocietyController::class, 'create'])->name('society.create');
    Route::post('society', [AdminSocietyController::class, 'store'])->name('society.store');
    Route::get('society/{slug}/edit', [AdminSocietyController::class, 'edit'])->name('society.edit');
    Route::put('society/{slug}', [AdminSocietyController::class, 'update'])->name('society.update');
    route::delete('society/{slug}', [AdminSocietyController::class, 'destroy'])->name('society.destroy');
    Route::get('society/{slug}', [AdminSocietyController::class, 'show'])->name('society.show');

    Route::get('locality', [AdminLocalityController::class, 'index'])->name('locality.index');
    Route::get('locality/create', [AdminLocalityController::class, 'create'])->name('locality.create');
    Route::post('locality', [AdminLocalityController::class, 'store'])->name('locality.store');
    Route::get('locality/{slug}/edit', [AdminLocalityController::class, 'edit'])->name('locality.edit');
    Route::put('locality/{slug}', [AdminLocalityController::class, 'update'])->name('locality.update');
    route::delete('locality/{slug}', [AdminLocalityController::class, 'destroy'])->name('locality.destroy');
    Route::get('locality/{slug}', [AdminLocalityController::class, 'show'])->name('locality.show');


    Route::get('features', [FeatureController::class, 'index'])->name('features.index');
    Route::get('features/create', [FeatureController::class, 'create'])->name('features.create');
    Route::post('features', [FeatureController::class, 'store'])->name('features.store');
    Route::get('features/{slug}/edit', [FeatureController::class, 'edit'])->name('features.edit');
    Route::put('features/{slug}', [FeatureController::class, 'update'])->name('features.update');
    Route::delete('features/{slug}', [FeatureController::class, 'destroy'])->name('features.destroy');
    Route::get('features/{slug}', [FeatureController::class, 'show'])->name('features.show');


    Route::get('amenities', [AmenitiesController::class, 'index'])->name('amenities.index');
    Route::get('amenities/create', [AmenitiesController::class, 'create'])->name('amenities.create');
    Route::post('amenities', [AmenitiesController::class, 'store'])->name('amenities.store');
    Route::get('amenities/{slug}/edit', [AmenitiesController::class, 'edit'])->name('amenities.edit');
    Route::put('amenities/{slug}', [AmenitiesController::class, 'update'])->name('amenities.update');
    Route::delete('amenities/{slug}', [AmenitiesController::class, 'destroy'])->name('amenities.destroy');
    Route::get('amenities/{slug}', [AmenitiesController::class, 'show'])->name('amenities.show');


    Route::get('admin-list', [AdminController::class, 'index'])->name('admin-list.index');
    Route::get('admin-list/create', [AdminController::class, 'create'])->name('admin-list.create');
    Route::post('admin-list', [AdminController::class, 'store'])->name('admin-list.store');
    Route::get('admin-list/{id}/edit', [AdminController::class, 'edit'])->name('admin-list.edit');
    Route::put('admin-list/{id}', [AdminController::class, 'update'])->name('admin-list.update');
    route::delete('admin-list/{id}', [AdminController::class, 'destroy'])->name('admin-list.destroy');



    Route::get('properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('properties/filter/{status}', [PropertyController::class, 'filterStatus'])->name('properties.filter');
    Route::get('properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('properties/{slug}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('properties/{slug}', [PropertyController::class, 'update'])->name('properties.update');
    Route::delete('properties/{slug}', [PropertyController::class, 'destroy'])->name('properties.destroy');
    Route::get('properties/{slug}', [PropertyController::class, 'show'])->name('properties.show');

    Route::get('properties/user/{id}', [PropertyController::class, 'userList'])->name('properties.user');

    Route::put('/properties/{id}/status', [PropertyController::class, 'updateStatus'])->name('properties.updateStatus');
    Route::post('properties/gallery/delete', [PropertyController::class, 'galleryImageDelete'])->name('gallery-delete');

    // name,email.socirty name,phone number
    Route::get('field_manager', [AdminFieldManagerController::class, 'index'])->name('field_manager.index');
    Route::get('field_manager/create', [AdminFieldManagerController::class, 'create'])->name('field_manager.create');
    Route::post('field_manager', [AdminFieldManagerController::class, 'store'])->name('field_manager.store');
    Route::get('field_manager/{id}/edit', [AdminFieldManagerController::class, 'edit'])->name('field_manager.edit');
    Route::put('field_manager/{id}', [AdminFieldManagerController::class, 'update'])->name('field_manager.update');
    route::delete('field_manager/{id}', [AdminFieldManagerController::class, 'destroy'])->name('field_manager.destroy');
    Route::post('/fieldManager-password-update/{id}', [AdminFieldManagerController::class, 'fieldManagerPasswordUpdate'])->name('fieldManagerPassword.update');

    Route::get('contact', [ContactController::class, 'index'])->name('contact.index');
    Route::get('contact/create', [ContactController::class, 'create'])->name('contact.create');
    Route::post('contact', [ContactController::class, 'store'])->name('contact.store');
    Route::get('contact/{slug}', [ContactController::class, 'show'])->name('contact.show');
    Route::get('contact/{slug}/edit', [ContactController::class, 'edit'])->name('contact.edit');
    Route::put('contact/{slug}', [ContactController::class, 'update'])->name('contact.update');
    route::delete('contact/{slug}', [ContactController::class, 'destroy'])->name('contact.destroy');
    Route::put('/contact/{id}/status', [ContactController::class, 'updateStatus'])->name('contact.updateStatus');

    Route::get('inquery', [InqueryController::class, 'index'])->name('inquery.index');
    Route::get('inquery/create', [InqueryController::class, 'create'])->name('inquery.create');
    Route::post('inquery', [InqueryController::class, 'store'])->name('inquery.store');
    Route::get('inquery/{slug}', [InqueryController::class, 'show'])->name('inquery.show');
    Route::get('inquery/{slug}/edit', [InqueryController::class, 'edit'])->name('inquery.edit');
    Route::put('inquery/{slug}', [InqueryController::class, 'update'])->name('inquery.update');
    route::delete('inquery/{slug}', [InqueryController::class, 'destroy'])->name('inquery.destroy');
    Route::put('/inquery/{id}/status', [InqueryController::class, 'updateStatus'])->name('inquery.updateStatus');

    Route::get('schedule_properties', [AdminSchedulePropertyController::class, 'index'])->name('schedule_properties.index');
    Route::get('schedule_properties/get-field-manager', [AdminSchedulePropertyController::class, 'getFieldManagerDetails'])->name('schedule_properties.get-field-manager');
    Route::get('schedule_properties/{property_id}', [AdminSchedulePropertyController::class, 'visit'])->name('schedule_properties.visit');
    Route::get('schedule_properties/{slug}/view', [AdminSchedulePropertyController::class, 'view'])->name('schedule_properties.view');
    Route::get('schedule_properties/create', [AdminSchedulePropertyController::class, 'create'])->name('schedule_properties.create');
    Route::post('schedule_properties/update_field_manager', [AdminSchedulePropertyController::class, 'update_field_manager'])->name('schedule_properties.update_field_manager');
    Route::post('schedule_properties', [AdminSchedulePropertyController::class, 'store'])->name('schedule_properties.store');
    Route::get('schedule_properties/{id}/edit', [AdminSchedulePropertyController::class, 'edit'])->name('schedule_properties.edit');
    Route::put('schedule_properties/{id}', [AdminSchedulePropertyController::class, 'update'])->name('schedule_properties.update');
    route::delete('schedule_properties/{id}', [AdminSchedulePropertyController::class, 'destroy'])->name('schedule_properties.destroy');


    Route::get('send-time-confirmation/{id}', [WhatsAppController::class, 'sendTimeConfirmation'])->name('owner.send-time-confirmation');
    Route::get('send-time-confirmation/{field_manager_id}/{property_id}', [WhatsAppController::class, 'sendTimeConfirmationFieldManager'])->name('field_manager.send-time-confirmation');


    Route::post('schedule_visit/add', [AdminScheduleVisitController::class, 'add'])->name('schedule_visit.add');

    Route::get('schedule_visit', [AdminScheduleVisitController::class, 'index'])->name('schedule_visit.index');
    Route::get('schedule_visit/create', [AdminScheduleVisitController::class, 'create'])->name('schedule_visit.create');
    Route::post('schedule_visit', [AdminScheduleVisitController::class, 'store'])->name('schedule_visit.store');
    Route::get('schedule_visit/property', [AdminScheduleVisitController::class, 'getOwnersByProperty'])->name('schedule_visit.property');

    Route::get('schedule_visit/{id}/view', [AdminScheduleVisitController::class, 'view'])->name('schedule_visit.view');
    Route::get('schedule_visit/{id}/sendTemplateUser', [AdminScheduleVisitController::class, 'sendTemplateUser'])->name('schedule_visit.sendTemplateUser');

    Route::get('schedule_visit/{id}/user', [AdminScheduleVisitController::class, 'userList'])->name('schedule_visit.user');

    Route::get('schedule_visit/{id}/edit', [AdminScheduleVisitController::class, 'edit'])->name('schedule_visit.edit');
    Route::put('schedule_visit/{id}', [AdminScheduleVisitController::class, 'update'])->name('schedule_visit.update');
    route::delete('schedule_visit/{id}', [AdminScheduleVisitController::class, 'destroy'])->name('schedule_visit.destroy');

    route::delete('schedule_visit/user/{id}', [AdminScheduleVisitController::class, 'destroyUser'])->name('schedule_visit.user.destroy');



    // Route::resource('services', 'ServiceController');
    // Route::resource('testimonials', 'TestimonialController');

    // Route::get('galleries/album', 'GalleryController@album')->name('album');
    // Route::post('galleries/album/store', 'GalleryController@albumStore')->name('album.store');
    // Route::get('galleries/{id}/gallery', 'GalleryController@albumGallery')->name('album.gallery');
    // Route::post('galleries', 'GalleryController@Gallerystore')->name('galleries.store');

    // Route::get('message', 'DashboardController@message')->name('message');
    // Route::get('message/read/{id}', 'DashboardController@messageRead')->name('message.read');
    // Route::get('message/replay/{id}', 'DashboardController@messageReplay')->name('message.replay');
    // Route::post('message/replay', 'DashboardController@messageSend')->name('message.send');
    // Route::post('message/readunread', 'DashboardController@messageReadUnread')->name('message.readunread');
    // Route::delete('message/delete/{id}', 'DashboardController@messageDelete')->name('messages.destroy');
    // Route::post('message/mail', 'DashboardController@contactMail')->name('message.mail');
});

// Super Admin Controller
Route::group(['prefix' => 'staff', 'namespace' => 'staff', 'middleware' => ['staff'], 'as' => 'staff.'], function () {


    Route::post('logout', [StaffLoginController::class, 'logout'])->name('logout');
    Route::get('dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', [StaffDashboardController::class, 'profile'])->name('profile');
    Route::post('profile', [StaffDashboardController::class, 'profileUpdate'])->name('profile.update');

    Route::get('changepassword', [StaffDashboardController::class, 'changePassword'])->name('changepassword');
    Route::post('changepassword', [StaffDashboardController::class, 'changePasswordUpdate'])->name('changepassword.update');

    Route::get('properties', [StaffPropertyController::class, 'index'])->name('properties.index');
    Route::get('properties/filter/{status}', [StaffPropertyController::class, 'filterStatus'])->name('properties.filter');
    Route::get('properties/create', [StaffPropertyController::class, 'create'])->name('properties.create');
    Route::post('properties', [StaffPropertyController::class, 'store'])->name('properties.store');
    Route::get('properties/{slug}/edit', [StaffPropertyController::class, 'edit'])->name('properties.edit');
    Route::put('properties/{slug}', [StaffPropertyController::class, 'update'])->name('properties.update');
    route::delete('properties/{slug}', [StaffPropertyController::class, 'destroy'])->name('properties.destroy');
    Route::get('properties/{slug}', [StaffPropertyController::class, 'show'])->name('properties.show');
    Route::put('/properties/{id}/status', [StaffPropertyController::class, 'updateStatus'])->name('properties.updateStatus');
    Route::get('properties/view/{id}', [StaffPropertyController::class, 'view'])->name('properties.view');
    Route::post('properties/gallery/delete', [StaffPropertyController::class, 'galleryImageDelete'])->name('gallery-delete');


    // Property user list
    Route::get('properties/user/{id}', [StaffPropertyController::class, 'userList'])->name('properties.user');


    Route::get('user', [StaffUserController::class, 'index'])->name('user.index');
    Route::get('user/create', [StaffUserController::class, 'create'])->name('user.create');
    Route::post('user', [StaffUserController::class, 'store'])->name('user.store');
    Route::get('user/{id}/edit', [StaffUserController::class, 'edit'])->name('user.edit');
    Route::put('user/{id}', [StaffUserController::class, 'update'])->name('user.update');
    route::delete('user/{id}', [StaffUserController::class, 'destroy'])->name('user.destroy');
    Route::get('user/verify/{id}', [StaffUserController::class, 'getUserDetails'])->name('user.verify');
    Route::put('/user/{id}/update-status', [StaffUserController::class, 'updateStatus'])->name('user.updateStatus');

    Route::get('send-time-confirmation/{id}', [WhatsAppController::class, 'sendTimeConfirmation'])->name('owner.send-time-confirmation');
    Route::get('send-time-confirmation/{field_manager_id}/{property_id}', [WhatsAppController::class, 'sendTimeConfirmationFieldManager'])->name('field_manager.send-time-confirmation');
    Route::get('properties/{id}/view', [OwnerController::class, 'viewOwnerProperties'])->name('owner.properties');

    Route::get('send-email/{id}', [OwnerController::class, 'showEmailForm'])->name('owner.send-mail');
    Route::post('send-email', [OwnerController::class, 'sendEmail'])->name('owner.send.email');

    Route::get('owner', [OwnerController::class, 'index'])->name('owner.index');
    Route::get('addNumber', [OwnerController::class, 'add_number'])->name('owner.add.number');
    Route::post('owner/store/maskNumber', [OwnerController::class, 'storeMaskNumber'])->name('owner.store.maskNumber');
    Route::get('owner/create', [OwnerController::class, 'create'])->name('owner.create');
    Route::post('owner', [OwnerController::class, 'store'])->name('owner.store');
    Route::get('owner/{id}/edit', [OwnerController::class, 'edit'])->name('owner.edit');
    Route::put('owner/{id}', [OwnerController::class, 'update'])->name('owner.update');
    route::delete('owner/{id}', [OwnerController::class, 'destroy'])->name('owner.destroy');
    Route::get('owner/verify/{id}', [OwnerController::class, 'getOwnerDetails'])->name('owner.verify');
    Route::put('/owner/{id}/update-status', [OwnerController::class, 'updateStatus'])->name('owner.updateStatus');

    Route::get('owner/agreement/{id}', [OwnerController::class, 'getAgreementLog'])->name('owner.agreement_logs');
    Route::post('owner/document_uploading', [OwnerController::class, 'uploadDocument'])->name('owner.document_uploading');


    Route::get('field_manager', [FieldManagerController::class, 'index'])->name('field_manager.index');
    Route::get('field_manager/create', [FieldManagerController::class, 'create'])->name('field_manager.create');
    Route::post('field_manager', [FieldManagerController::class, 'store'])->name('field_manager.store');
    Route::get('field_manager/{id}/edit', [FieldManagerController::class, 'edit'])->name('field_manager.edit');
    Route::put('field_manager/{id}', [FieldManagerController::class, 'update'])->name('field_manager.update');
    route::delete('field_manager/{id}', [FieldManagerController::class, 'destroy'])->name('field_manager.destroy');



    Route::get('posts', [StaffPostController::class, 'index'])->name('posts.index');
    Route::get('posts/create', [StaffPostController::class, 'create'])->name('posts.create');
    Route::post('posts', [StaffPostController::class, 'store'])->name('posts.store');
    Route::get('posts/{slug}/edit', [StaffPostController::class, 'edit'])->name('posts.edit');
    Route::put('posts/{slug}', [StaffPostController::class, 'update'])->name('posts.update');
    route::delete('posts/{slug}', [StaffPostController::class, 'destroy'])->name('posts.destroy');
    Route::get('posts/{slug}', [StaffPostController::class, 'show'])->name('posts.show');



    Route::get('society', [SocietyController::class, 'index'])->name('society.index');
    Route::get('society/create', [SocietyController::class, 'create'])->name('society.create');
    Route::post('society', [SocietyController::class, 'store'])->name('society.store');
    Route::get('society/{slug}/edit', [SocietyController::class, 'edit'])->name('society.edit');
    Route::put('society/{slug}', [SocietyController::class, 'update'])->name('society.update');
    route::delete('society/{slug}', [SocietyController::class, 'destroy'])->name('society.destroy');
    Route::get('society/{slug}', [SocietyController::class, 'show'])->name('society.show');

    Route::get('locality', [LocalityController::class, 'index'])->name('locality.index');
    Route::get('locality/create', [LocalityController::class, 'create'])->name('locality.create');
    Route::post('locality', [LocalityController::class, 'store'])->name('locality.store');
    Route::get('locality/{slug}/edit', [LocalityController::class, 'edit'])->name('locality.edit');
    Route::put('locality/{slug}', [LocalityController::class, 'update'])->name('locality.update');
    route::delete('locality/{slug}', [LocalityController::class, 'destroy'])->name('locality.destroy');
    Route::get('locality/{slug}', [LocalityController::class, 'show'])->name('locality.show');



    Route::get('additional-details', [AdditionalDetailController::class, 'index'])->name('additional-details.index');
    Route::get('additional-details/create', [AdditionalDetailController::class, 'create'])->name('additional-details.create');
    Route::post('additional-details', [AdditionalDetailController::class, 'store'])->name('additional-details.store');
    Route::get('additional-details/{id}/edit', [AdditionalDetailController::class, 'edit'])->name('additional-details.edit');
    Route::put('additional-details/{id}', [AdditionalDetailController::class, 'update'])->name('additional-details.update');
    Route::delete('additional-details/{id}', [AdditionalDetailController::class, 'destroy'])->name('additional-details.destroy');



    Route::get('contact', [StaffContactController::class, 'index'])->name('contact.index');
    Route::get('contact/create', [StaffContactController::class, 'create'])->name('contact.create');
    Route::post('contact', [StaffContactController::class, 'store'])->name('contact.store');
    Route::get('contact/{slug}', [StaffContactController::class, 'show'])->name('contact.show');
    Route::get('contact/{slug}/edit', [StaffContactController::class, 'edit'])->name('contact.edit');
    Route::put('contact/{slug}', [StaffContactController::class, 'update'])->name('contact.update');
    route::delete('contact/{slug}', [StaffContactController::class, 'destroy'])->name('contact.destroy');
    Route::put('/contact/{id}/status', [StaffContactController::class, 'updateStatus'])->name('contact.updateStatus');

    Route::get('inquery', [StaffInqueryController::class, 'index'])->name('inquery.index');
    Route::get('inquery/create', [StaffInqueryController::class, 'create'])->name('inquery.create');
    Route::post('inquery', [StaffInqueryController::class, 'store'])->name('inquery.store');
    Route::get('inquery/{slug}', [StaffInqueryController::class, 'show'])->name('inquery.show');
    Route::get('inquery/{slug}/edit', [StaffInqueryController::class, 'edit'])->name('inquery.edit');
    Route::put('inquery/{slug}', [StaffInqueryController::class, 'update'])->name('inquery.update');
    route::delete('inquery/{slug}', [StaffInqueryController::class, 'destroy'])->name('inquery.destroy');
    Route::put('/inquery/{id}/status', [StaffInqueryController::class, 'updateStatus'])->name('inquery.updateStatus');

    Route::get('schedule_visit', [ScheduleVisitController::class, 'index'])->name('schedule_visit.index');

    Route::post('schedule_visit/add', [ScheduleVisitController::class, 'add'])->name('schedule_visit.add');
    Route::get('schedule_visit/create', [ScheduleVisitController::class, 'create'])->name('schedule_visit.create');
    Route::post('schedule_visit', [ScheduleVisitController::class, 'store'])->name('schedule_visit.store');

    Route::post('manual_visit', [ScheduleVisitController::class, 'manual_schedule_visit'])->name('schedule_visit.manual_visit');

    Route::get('schedule_visit/property', [ScheduleVisitController::class, 'getOwnersByProperty'])->name('schedule_visit.property');
    Route::get('schedule_visit/{id}/view', [ScheduleVisitController::class, 'view'])->name('schedule_visit.view');
    Route::get('schedule_visit/{id}/user', [ScheduleVisitController::class, 'userList'])->name('schedule_visit.user');
    Route::get('schedule_visit/{id}/sendTemplateUser', [ScheduleVisitController::class, 'sendConformationTemplateUser'])->name('schedule_visit.sendTemplateUser');


    Route::get('schedule_visit/{id}/edit', [ScheduleVisitController::class, 'edit'])->name('schedule_visit.edit');
    Route::put('schedule_visit/{id}', [ScheduleVisitController::class, 'update'])->name('schedule_visit.update');
    route::delete('schedule_visit/{id}', [ScheduleVisitController::class, 'destroy'])->name('schedule_visit.destroy');

    route::delete('schedule_visit/user/{id}', [ScheduleVisitController::class, 'destroyUser'])->name('schedule_visit.user.destroy');


    Route::get('notifications/unread-count', [ScheduleVisitController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/read/{id}', [ScheduleVisitController::class, 'markAsRead'])->name('notifications.read');

    // Route::get('notifications/schedule-visit/{id}', [ScheduleVisitController::class, 'scheduleVisit'])->name('notifications.schedule.visit');


    Route::get('schedule_properties', [SchedulePropertyController::class, 'index'])->name('schedule_properties.index');
    Route::get('schedule_properties/get-field-manager', [SchedulePropertyController::class, 'getFieldManagerDetails'])->name('schedule_properties.get-field-manager');
    Route::get('schedule_properties/{property_id}', [SchedulePropertyController::class, 'visit'])->name('schedule_properties.visit');
    Route::get('schedule_properties/{property_id}/view', [SchedulePropertyController::class, 'view'])->name('schedule_properties.view');
    Route::get('schedule_properties/create', [SchedulePropertyController::class, 'create'])->name('schedule_properties.create');
    Route::post('schedule_properties/update_field_manager', [SchedulePropertyController::class, 'update_field_manager'])->name('schedule_properties.update_field_manager');
    Route::post('schedule_properties', [SchedulePropertyController::class, 'store'])->name('schedule_properties.store');
    Route::get('schedule_properties/{id}/edit', [SchedulePropertyController::class, 'edit'])->name('schedule_properties.edit');
    Route::put('schedule_properties/{id}', [SchedulePropertyController::class, 'update'])->name('schedule_properties.update');
    route::delete('schedule_properties/{id}', [SchedulePropertyController::class, 'destroy'])->name('schedule_properties.destroy');

    Route::get('field-manager/{id}', [FieldManagerController::class, 'showLocation'])->name('field-manager.location');


    Route::get('/recordings', [RecordingController::class, 'showRecordings'])->name('recordings.index');

    Route::post('/make-call', [StaffContactController::class, 'makeCall'])->name('make.call');
    // https://admin.realtrust.in/staff/place-call/21/32



    // Route::get('/test-broadcast', function () {
    //     Log::info('Broadcasting event...');
    //     broadcast(new LocationUpdated(3, '12.9715987', '77.5945627')); // Replace 1 with the actual field manager ID
    //     Log::info('Event broadcasted!');
    //     return 'Event broadcasted!';
    // });
    // Route::get('/track-location/{id}', [FieldManagerController::class, 'trackLocation']);
});

// Route::get('/payment', function () {
//     return view('payment'); // Show payment form
// });

Route::get('field-manager/{id}', [FieldManagerController::class, 'showLocation'])->name('field-manager.location');
Route::get('inquery/{id}', [UserController::class, 'inquery'])->name('inquery');
Route::post('inquery', [UserController::class, 'submitUserInterest'])->name('inquery.submit');
Route::post('/notifications/markAsRead/{id}', [StaffDashboardController::class, 'markAsRead'])->name('notifications.markAsRead');

Route::get('/payment', [PaymentController::class, 'showPaymentForm']);

// Digital signature

Route::get('signature-pdf', [SignaturePDFController::class, 'index']);
Route::post('signature-pdf', [SignaturePDFController::class, 'upload'])->name('signature.upload');
