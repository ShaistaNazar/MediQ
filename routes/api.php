<?php

use Illuminate\Http\Request;

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

Route::get('get_session_id', 'UserController@getSessionId');
Route::get('send_sms', 'UserController@sendSms');

Route::get('/easyPasia', 'PaymentController@easyPasia');

// TODO: Test Code 
// Route::get('thanks', function (Request $request) {
// 	return view('thanks');
// });

Route::post('create_downloads','UserController@createDownloads');

Route::get('makePromotion', 'OrdersController@makePromotion');

Route::get('get_cities', 'CityController@cities');
Route::post('socialPhoneVerification', 'UserController@socialPhoneVerification');

//SearchMedicines
Route::get('search_medicine', 'MedicineController@searchMedicine');
//medicine categories list
Route::get('get_medicine_categories', 'MedicineController@getMedicineCategories');
//get all pharmacy medicines
Route::get('medicines_list', 'MedicineController@medicinesList');
Route::get('other_medicines', 'MedicineController@otherMedicines');
Route::get('search_other_medicines', 'MedicineController@searchOtherMedicines');

// //upload prescription 
// Route::post('upload_prescription', 'MedicineController@UploadPrescription');
//get medicine details
Route::get('get_medicine_details', 'MedicineController@getMedicineDetails');
//banner images
Route::get('banner_images', 'UserController@bannerImages');
//banner images
Route::get('in_demand_products', 'OrdersController@inDemandProducts');
Route::get('in_demand_med_cat', 'OrdersController@inDemandMedCat');
Route::get('in_demand_test_cat', 'OrdersController@inDemandTestCat');
Route::get('in_demand_equipment', 'OrdersController@inDemandEquipment');

Route::get('labs_list', 'LabsController@labsList');
Route::get('nearby_labs', 'LabsController@UserNearbyLabs');
Route::get('test_category_list', 'LabsController@getTestCategoryList');
Route::get('test_list', 'LabsController@getTestList');
Route::get('search_test', 'LabsController@searchTest');

Route::get('home_medical_services_list', 'HomeServiceController@getHomeMedicalServices');

Route::get('search_home_service_providers', 'HomeServiceController@searchMedicalService');
Route::get('search_medical_equipment', 'HomeServiceController@searchMedicalEquipment');
Route::get('medical_equipment', 'HomeServiceController@MedicalEquipment');

Route::get('search_medical_service', 'HomeServiceController@searchMedicalService');
Route::get('search_service_provider', 'HomeServiceController@searchMedicalService');
Route::get('get_service_types', 'HomeServiceController@getAllServiceTypes');

// user Module Start

Route::post('signup', 'UserController@Signup');
Route::post('social_signup', 'UserController@signupUserBySocial');
Route::post('signin', 'UserController@userLogin');
Route::post('user_activate', 'UserController@activateUserAccount');
Route::post('resend_code', 'UserController@resendActivationCode');
Route::post('reset_password', 'UserController@resetPassword');
Route::post('forgot_password', 'UserController@forgotPassword');
// Route::get('order_history_equ', 'OrdersController@getEquOrdersByUserId');
// Route::get('order_history_test', 'OrdersController@getTestOrdersByUserId');

// jazzCash
Route::get('/jazz_checkout/{token}', 'PaymentController@jazzCheckout');
Route::post('jazzCashResponse', 'PaymentController@apiResponse');

Route::get('otc', 'PaymentController@otc')->name('otc');

Route::get('paymentResponse', 'PaymentController@jazzResponse')->name('paymentResponse');
Route::get('paymentResponseFail', 'PaymentController@jazzResponseFail')->name('paymentResponseFail');

Route::get('jazzSuccess', function (Request $request) {
	return view('jazzSuccess');
});
Route::get('jazzfail', function (Request $request) {
	return view('jazzfail');
});

Route::get('get_all_rental', 'RentalController@getAllRental');

// End

Route::post('re_order', 'OrdersController@reOrder');

Route::group(['middleware' => 'auth:api'], function () {

	//easyPasia
	Route::post('/easyPaisaPaymentStatus', 'PaymentController@easyPaisaPaymentStatus');
	Route::post('/easyPaisaPayment', 'PaymentController@easyPaisaPayment');
	Route::post('hash', 'PaymentController@createHash');
	Route::post('confirmOrder', 'OrdersController@confirmOrder');

	Route::post('upload_prescription', 'MedicineController@UploadPrescription');

	Route::get('serviceOrderHistory', 'OrdersController@serviceOrder');
	Route::get('rental_history', 'RentalController@rentalHistory');

	Route::post('new_order', 'OrdersController@newOrder');
	Route::post('order_service', 'HomeServiceController@orderService');
	Route::post('order_rent', 'RentalController@orderRent');

	Route::get('paymentauth', 'PaymentController@paymentAuth');


	Route::post('upload_profile_picture', 'UserController@uploadProfilePicture');
	// User Module
	Route::get('logout', 'UserController@userLogout');
	Route::get('get_profile', 'UserController@getUserProfile');
	Route::post('change_password',  'UserController@changePassword');
	Route::post('edit_profile', 'UserController@updateUserProfile');

	Route::get('view_profile_picture', 'UserController@viewProfilePicture');
	// one signal device id
	Route::post('set_player_id',  'UserController@setUserPlyerID');
	// End

	// check_promotion
	Route::get('check_promotion', 'OrdersController@checkPromotion');
	Route::get('confirm_promotion', 'OrdersController@confirmPromotion');

	Route::get('get_user_test_reports', 'LabsController@getUserTestReports');

	// orders
	Route::get('lab_reports', 'LabsController@getTestUserLabReports');

	//shipping details

	Route::post('add_or_update_shipping_details', 'ShippingController@addOrUpdateShippingDetails');
	Route::get('get_shipping_details', 'ShippingController@getShippingDetails');

	// notifications

	Route::get('get_notifications', 'NotificationController@getNotifications');
	Route::post('read_notifications', 'NotificationController@readNotifications');
	Route::post('seen_notifications', 'NotificationController@seenNotifications');

	// Lab Module

	Route::post('update_player_id', 'UserController@updatePlayerId');

	// Home Service Module
	Route::get('order_history_med', 'OrdersController@getMedOrdersByUserId');


	//Billing Details
	Route::post('add_billing_details', 'BillingDetailsController@addOrUpdateBillingDetails');
	Route::get('get_billing_details', 'BillingDetailsController@getBillingDetails');
});

Route::fallback(function () {
	// return Response::json(ResponseUtil::makeError("Page Not Found.", "Error"), 404);
	return response()->json(['error' => 'Unauthenticated.'], 401);

	$response = ['status' => 'error', 'message' => 'You pass invalid token'];

	return response()->json($response);
});
