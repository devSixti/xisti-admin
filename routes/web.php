<?php

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

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminLocaleController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Auth\AuthPagesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HeatMapController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderWiseChatController;
use App\Http\Controllers\ReportIssueController;
use App\Http\Controllers\TransportController;
use Illuminate\Support\Facades\Auth;

Route::get('/firebase-messaging-sw.js', function () {
    return response()
        ->view('firebase-messaging-sw.js')
        ->header('Content-Type', 'application/javascript; charset=UTF-8')
        ->header('Service-Worker-Allowed', '/');
})->name('get:firebase.messaging.sw');

Route::get('/', function () {
//    return view('comingsoonpage');
    return view('homepage');
})->name('get:homepage');

Route::get('/{guards}/auth/{provider}', [LoginController::class,'socialAuthLogin']);
Route::get('/auth/{provider}/callback', [LoginController::class,'handleCallback'])->name('get:auth_callback');
Route::post('/webhook/wompi', [CustomerApiController::class,'postWompiWebhook'])->name('post:wompi:webhook:alias');

//support pages routes
Route::get('/terms-and-conditions', [HomeController::class,'getTermsAndConditions'])->name('get:terms-and-conditions');
Route::get('/privacy-policy', [HomeController::class,'getPrivacyPolicy'])->name('get:privacy-policy');
Route::get('/disclaimer', [HomeController::class,'getDisclaimer'])->name('get:disclaimer');
Route::get('/faq', [HomeController::class,'getFaq'])->name('get:faq');

// Static payment status pages for mobile app WebView
Route::get('/payments/wompi/redirect', [HomeController::class,'getWompiPaymentRedirect'])->name('payment.wompi.redirect');
Route::get('/payments/success', function() { return view('success'); })->name('payment.success');
Route::get('/payments/failed', function() { return view('failed'); })->name('payment.failed');
//end support pages routes
Route::get('/deletion/{reference}', [HomeController::class,'postDataDeletionStatus'])->name('get:deletion_status');
Route::get('/provider-documents/{filename}', [HomeController::class,'getfile'])->name('get.file');
/* -----------------------------------For Play-Store, App-Store Upload account_deletion------------------------------ */
Route::get('/account-deletion/login', [HomeController::class,'getAccountDeletion'])->name('get:account:deletion:login');
Route::post('/account-deletion/login', [HomeController::class,'postAccountDeletion'])->name('post:account:deletion:login');

Route::get('/account-deletion/verification', [HomeController::class,'getAccountDeletionVerification'])->name('get:account:deletion:verification');
Route::post('/account-deletion/verification', [HomeController::class,'postAccountDeletionVerification'])->name('post:account:deletion:verification');

Route::get('/account-deletion/resend-verification-code', [HomeController::class,'getAccountDeletionRensendVerificationCode'])->name('get:account:deletion:resend-verification-code');

Route::get('/account-deletion/profile', [HomeController::class,'getAccountDeletionProfile'])->name('get:account:deletion:profile');

Route::get('/account-deletion/logout/{guard}', [HomeController::class,'getAccountDeletionLogout'])->name('get:account:deletion:logout');

// Delete Account
Route::post('/account-deletion/delete-account', [HomeController::class,'postAccountDeletionDeleteAccount'])->name('post:account:deletion:delete-account:logout');
/// Social Login
Route::get('{guards}/auth_account_delete/{provider}', [HomeController::class,'redirectToProvider']);
Route::get('/auth_account_delete/{provider}/callback', [HomeController::class,'handleCallback']);

Route::get('/ride-invoice-download/{ride_id}/{provider_type}/{provider_id}', [HomeController::class,'getRideInvoiceDownload'])
    ->name('get:ride-invoice-download')
    ->middleware('throttle:60,1');

/* -----------------------------------End For Play-Store, App-Store Upload account_deletion-------------------------- */

Route::get('heat-map/{driver_id}', array(HeatMapController::class,'postDriverWebViewHeatMap'))->name('heat.map');
Route::post('post-heat-map', array(HeatMapController::class,'postAjaxDriverWebViewHeatMap'))->name('post:heat.map');
Route::get('cancel-heat-map', array(HeatMapController::class,'postDriverWebViewCancelHeatMap'))->name('cancel.heat.map');

//======= Super Admin =======//

Route::middleware(['setAdminLocale'])->group(function () {
    Route::post('/admin/locale', [AdminLocaleController::class, 'update'])->name('post:admin:locale');

Route::group(['middleware' => 'revalidate'], function () {
    Route::get('/admin/login', [AuthPagesController::class,'getAdminLogin'])->name('get:admin:login');
    Route::post('/admin/login', [LoginController::class,'postSuperAdminLogin'])->name('post:admin:update_super_admin_login');
});
Route::prefix('admin')->group(function () {
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::get('/logout/{admin}', [LoginController::class,'logout'])->name('admin:logout');
        Route::group(['middleware' => 'revalidate'], function () {
            Route::get('/vehicle-type-list', [TransportController::class,'getTransportVehicleTypeList'])->name('get:admin:vehicle_type_list');
            Route::group(['middleware' => 'adminrole'], function () {
                //test-mail
                Route::get('/test-mail', [AdminController::class,'getAdminTest_Mail'])->name('get:admin:test_mail');
                Route::get('/change-password', [ResetPasswordController::class,'getAdminChangePassword'])->name('get:admin:change_password');
                Route::post('/change-password', [ResetPasswordController::class,'postAdminChangePassword'])->name('post:admin:change_password');
                Route::get('/dashboard', [AdminController::class,'getAdminDashboard'])->name('get:admin:dashboard');

                //world currency list
                Route::get('/world-currency-list', [AdminController::class,'getAdminWorldCurrencyList'])->name('get:admin:world_currency_list');
                Route::post('/world-currency-list', [AdminController::class,'postAdminWorldCurrencyList'])->name('post:admin:world_currency_list');

                //user
                Route::get('/customer-list', [AdminController::class,'getAdminUserList'])->name('get:admin:user_list');
                Route::get('/add-customer', [AdminController::class,'getAdminAddUser'])->name('get:admin:add_user');
                Route::get('/edit-customer/{slug}', [AdminController::class,'getAdminEditUser'])->name('get:admin:edit_user');
                Route::get('/delete-customer', [AdminController::class,'getAdminDeleteUser'])->name('get:admin:delete_user');
                Route::post('/update-customer', [AdminController::class,'postAdminUpdateUser'])->name('post:admin:update_user');
                Route::get('/customer-order-list/{id}', [AdminController::class,'postAdminCustomerOrderList'])->name('post:admin:customer_order_list');
                Route::get('/customer-wallet-transaction/{id}', [AdminController::class,'postAdminCustomerWalletTransaction'])->name('post:admin:customer_wallet_transaction');
                Route::get('/provider-wallet-transaction/{id}', [AdminController::class,'postAdminProviderWalletTransaction'])->name('get:admin:provider_wallet_transaction');
                Route::get('/update-customer-wallet-transaction', [AdminController::class,'postAdminUpdateCustomerWalletTransaction'])->name('get:admin:update_customer_wallet_transaction');
                Route::get('/update-provider-wallet-transaction', [AdminController::class,'postAdminUpdateProviderWalletTransaction'])->name('get:admin:update_provider_wallet_transaction');
                //ajax user status update
                Route::get('/update-user-status', [AdminController::class,'getAdminUpdateUserStatus'])->name('get:ajax:admin:update_user_status');
                Route::get('/user-list-new', [AdminController::class,'getAdminUserListNew'])->name('get:admin:user_list_new');
                //user review list
                Route::get('/customer-review-list/{user_id?}', [AdminController::class,'getAdminUserReviewList'])->name('get:admin:user_review_list');
                Route::get('/update-user-review-status', [AdminController::class,'getAdminUpdateUserReviewStatus'])->name('get:ajax:admin:update_user_review_status');
                Route::get('/delete-customer-review', [AdminController::class,'getAdminDeleteUserReview'])->name('get:admin:delete_user_review');

                //ajax required- documents status update
                Route::get('/update-required-documents-status', [AdminController::class,'getAjaxUpdateAdminRequiredDocumentStatus'])->name('get:ajax:admin:update_required_document_status');

                //ajax required- Expiry status update
                Route::get('/update-documents-expiry-status', [AdminController::class,'getAjaxUpdateAdminRequiredDocumentExpiryStatus'])->name('get:ajax:admin:update_document_expiry_status');


                //ajax provider approved reject document
                Route::get('/approved-reject-provider-document', [AdminController::class,'getAjaxUpdateAdminApprovedRejectProviderDocument'])->name('get:ajax:admin:update_approved_reject_provider_document');
                //get general settings
                Route::get('/site-setting', [AdminController::class,'getAdminGeneralSetting'])->name('get:admin:general_setting');
                Route::post('/site-setting', [AdminController::class,'postAdminUpdateGeneralSetting'])->name('post:admin:update_general_setting');
                //get general settings
                Route::get('/app-version-setting', [AdminController::class,'getAdminAppVersionSetting'])->name('get:admin:app_version_setting');
                Route::post('/app-version-setting', [AdminController::class,'postAdminUpdateAppVersionSetting'])->name('post:admin:update_app_version_setting');
                //get push notification
                Route::get('/push-notification', [AdminController::class,'getAdminPushNotification'])->name('get:admin:push_notification');
                Route::post('/push-notification', [AdminController::class,'postAdminUpdatePushNotification'])->name('post:admin:update_push_notification');
                Route::post('/push-notification/events', [AdminController::class,'postAdminSavePushEventTemplates'])->name('post:admin:save_push_event_templates');
                Route::get('/delete-push-notification', [AdminController::class,'getAdminDeletePushNotification'])->name('get:admin:delete_push_notification');


                //pages route
                Route::get('/support-page-list', [AdminController::class,'getAdminSupportPages'])->name('get:admin:support_pages');
                Route::get('/add-support-page', [AdminController::class,'getAdminAddPages'])->name('get:admin:add_pages');
                Route::get('/edit-support-page/{page_id}', [AdminController::class,'getAdminEditPages'])->name('get:admin:edit_pages');
                Route::post('/update-support-page', [AdminController::class,'postAdminUpdateSupportPages'])->name('post:admin:update_pages');
                Route::get('/delete-support-page', [AdminController::class,'getAdminDeleteSupportPages'])->name('get:admin:delete_support_page');

                Route::get('/about-us', [AdminController::class,'getAdminAboutPages'])->name('get:admin:about-us');
                Route::get('/contact-us', [AdminController::class,'getAdminContactUsPages'])->name('get:admin:contact-us');
                Route::get('/faq', [AdminController::class,'getAdminFaqPages'])->name('get:admin:faq');

                Route::post('/update', [AdminController::class,'postAdminUpdateSupportPages'])->name('post:admin:update_pages');


                //Geo Fencing Restricted areas
                Route::get('/restricted-area-list', [AdminController::class,'getAdminRestrictedAreaList'])->name('get:admin:restricted_area_list');
                Route::get('/add-restricted-area', [AdminController::class,'getAdminAddRestrictedArea'])->name('get:admin:add_restricted_area');
                Route::get('/edit-restricted-area/{id}', [AdminController::class,'getAdminEditRestrictedArea'])->name('get:admin:edit_restricted_area');
                Route::post('/update-restricted-area', [AdminController::class,'postAdminUpdateRestrictedArea'])->name('post:admin:update_restricted_area');
                Route::get('/update-restricted-area-status', [AdminController::class,'postAdminUpdateRestrictedAreaStatus'])->name('get:admin:update_restricted_area_status');
                Route::get('/delete-restricted-area', [AdminController::class,'getAdminDeleteRestrictedArea'])->name('get:admin:delete_restricted_area');

                //Email Templates
                Route::get('/email-templates', [AdminController::class,'getEmailTemplatesList'])->name('get:admin:email_templates');
                Route::get('/add-email-templates', [AdminController::class,'getAdminAddEmailTemplates'])->name('get:admin:add_email_templates');
                Route::get('/edit-email-templates/{id}', [AdminController::class,'getAdminEditEmailTemplates'])->name('get:admin:edit_email_templates');
                Route::post('/update-email-templates', [AdminController::class,'postAdminUpdateEmailTemplates'])->name('post:admin:update_email_templates');
                Route::get('/update-email-templates-status', [AdminController::class,'postAdminUpdateEmailTemplatesStatus'])->name('get:admin:update_email_templates_status');
                Route::get('/delete-email-templates', [AdminController::class,'getAdminDeleteEmailTemplates'])->name('get:admin:delete_email_templates');

                //get language lists
                Route::get('/language-lists', [AdminController::class,'getAdminLanguageLists'])->name('get:admin:language_lists');
                Route::post('/language-lists', [AdminController::class,'postAdminUpdateLanguageLists'])->name('post:admin:update_language-lists');
                Route::get('/language-lists-status', [AdminController::class,'getAdminUpdateLanguageLists'])->name('get:ajax:admin:language_lists_status');

                //get language constant
                Route::get('/language-constant', [AdminController::class,'getAdminLanguageConstant'])->name('get:admin:language_constant');
                Route::post('/language-constant', [AdminController::class,'postAdminUpdateLanguageConstant'])->name('post:admin:update_language_constant');
                Route::get('/edit-language-constant/{id}', [AdminController::class,'getAdminEditLanguageConstant'])->name('post:admin:edit_language_constant');
                Route::get('/user-change-password', [AdminController::class,'getUpdateUserChangePassword'])->name('get:admin:user_change_password');
                Route::get('/provider-change-password', [AdminController::class,'getUpdateProviderChangePassword'])->name('get:admin:provider_change_password');

                Route::get('/service-setting', [TransportController::class,'getTransportServiceSetting'])->name('get:admin:service_setting');
                Route::post('/update-service-setting', [TransportController::class,'postUpdateTransportServiceSetting'])->name('post:admin:update_service_setting');

                Route::get('/ride-list/{status}', [TransportController::class,'getTransportServiceRideList'])->name('get:admin:ride_list');
                Route::get('/ride-lists-new', [TransportController::class,'getTransportRideList'])->name('get:admin:ride_list_new');
                Route::get('/ride-detail/{id}', [TransportController::class,'getTransportRideDetails'])->name('get:admin:ride_details');

                Route::get('/vehicle-type', [TransportController::class,'getTransportServiceCategoryVehicleType'])->name('get:admin:vehicle_type');
                Route::get('/add-vehicle-type', [TransportController::class,'getAddTransportVehicleType'])->name('get:admin:add_vehicle_type');
                Route::get('/edit-vehicle-type/{id}', [TransportController::class,'getEditTransportVehicleType'])->name('get:admin:edit_vehicle_type');
                Route::get('/delete-vehicle-type', [TransportController::class,'getDeleteTransportVehicleType'])->name('get:admin:delete_vehicle_type');
                Route::post('/update-vehicle-type', [TransportController::class,'postUpdateTransportVehicleType'])->name('post:admin:update_transport_vehicle_type');
                Route::get('/update-vehicle-type-status', [TransportController::class,'getAjaxUpdateTransportVehicleTypeStatus'])->name('get:ajax:admin:update_vehicle_type_status');

                Route::get('/vehicle-services', [TransportController::class,'getTransportVehicleService'])->name('get:admin:vehicle_service');
                Route::get('/add-vehicle-service', [TransportController::class,'getAddTransportVehicleService'])->name('get:admin:add_vehicle_service');
                Route::get('/edit-vehicle-service/{id}', [TransportController::class,'getEditTransportVehicleService'])->name('get:admin:edit_vehicle_service');
                Route::get('/delete-vehicle-service', [TransportController::class,'getDeleteTransportVehicleService'])->name('get:admin:delete_vehicle_service');
                Route::post('/update-vehicle-service', [TransportController::class,'postUpdateTransportVehicleService'])->name('post:admin:update_transport_vehicle_service');
                Route::get('/update-vehicle-service-status', [TransportController::class,'getAjaxUpdateTransportVehicleServiceStatus'])->name('get:ajax:admin:update_vehicle_service_status');

                Route::get('/required-document-list/', [AdminController::class,'getRequiredDocumentList'])->name('get:admin:required_document_list');
                Route::get('/add-required-document/', [AdminController::class,'getAddRequiredDocument'])->name('get:admin:add_required_document');
                Route::get('/edit-document/{id}', [AdminController::class,'getEditRequiredDocument'])->name('get:admin:edit_required_document');
                Route::post('/update-document', [AdminController::class,'postUpdateRequiredDocument'])->name('post:admin:update_required_document');

                //earings reports
                Route::get('/earning-report', [TransportController::class,'getTransportEarningReport'])->name('get:admin:earning_report');
                Route::get('/driver-payment-settled', [TransportController::class,'postTransportDriverPaymentSettled'])->name('post:admin:driver_payment_settled');
                Route::post('/earning-report', [TransportController::class,'PostTransportSearchEarningReport'])->name('post:admin:search_earning_report');

                Route::get('/provider-document/{user_id}', [TransportController::class,'getTransportProviderDocument'])->name('get:admin:transport_provider_document');

                //upload document
                Route::post('upload-provider-document', [TransportController::class,'postDriverTransportServiceDocument'])->name('post:admin:transport_upload_provider_document');
                Route::post('update-provider-expiry_date', [TransportController::class,'postAdminUpdateProviderDocumentExpiry'])->name('post:admin:transport_update_provider_document_expiry');


                //driver vehicle type details
                Route::get('/provider-vehicle-details/{id}', [TransportController::class,'getEditTransportProviderVehicleDetails'])->name('get:admin:edit_transport_provider_vehicle_details');
                Route::post('/update-provider-vehicle-details', [TransportController::class,'postUpdateTransportProviderVehicleDetails'])->name('post:admin:update_transport_provider_vehicle_details');
                Route::get('/provider-list/{status}', [TransportController::class,'getTransport_Blocked_ProviderList'])->name('get:admin:transport_service_provider_list');


                Route::get('/provider-review-list/{driver_id}', [TransportController::class,'getTransportProviderReviewList'])->name('get:admin:transport_provider_review_list');
                Route::get('/provider-ride-list/{driver_id}', [TransportController::class,'getTransportProviderRideList'])->name('get:admin:transport_provider_ride_list');
                Route::get('/single-provider-ride-lists-new', [TransportController::class,'getTransportSingleProviderRideList'])->name('get:admin:single_provider_ride_list_new');

                Route::get('/add-driver', [TransportController::class,'getAddTransportDriver'])->name('get:admin:add_transport_service_driver');
                Route::get('/edit-driver/{id}', [TransportController::class,'getEditTransportDriver'])->name('get:admin:edit_transport_service_driver');
                Route::post('/update-driver', [TransportController::class,'postUpdateTransportDriver'])->name('post:admin:update_transport_service_driver');


                Route::get('/drivers-list/approved', [TransportController::class,'getTransportProvidersApprovedList'])->name('get:admin:transport_service_approved_providers_list');
                Route::get('/drivers-list/un-approved', [TransportController::class,'getTransportProvidersUnApprovedList'])->name('get:admin:transport_service_un_approved_providers_list');
                Route::get('/drivers-list/blocked', [TransportController::class,'getTransportProvidersBlockedList'])->name('get:admin:transport_service_blocked_providers_list');
                Route::get('/drivers-list/rejected', [TransportController::class,'getTransportProvidersRejectedList'])->name('get:admin:transport_service_rejected_providers_list');

                Route::get('/provider-list-new', [TransportController::class,'getTransportProviderListNew'])->name('get:admin:transport_service_provider_list_new');

                //ajax transport provider status(approved/blocked) change
                Route::get('/update-provider-status', [TransportController::class,'getUpdateTransportProviderStatus'])->name('get:admin:transport_update_provider_status');

                Route::get('/{slug}/review-list/', [TransportController::class,'getTransportProviderRideReviewList'])->name('get:admin:transport_provider_ride_review_list');
                Route::get('/delete-provider-ride-review', [TransportController::class,'getDeleteTransportProviderRideReview'])->name('get:admin:delete_transport_provider_ride_review');
                Route::get('/update-provider-ride-review-status', [TransportController::class,'getAjaxUpdateProviderRideReviewStatus'])->name('get:ajax:admin:update_transport_provider_ride_review_status');

                Route::get('/update-ride-status', [TransportController::class,'getTransportUpdateRideStatus'])->name('get:admin:transport_update_ride_status');

                //god's view
                Route::get('/provider-location-on-map', [TransportController::class,'getAdminTransportProviderLocation'])->name('get:admin:transport_provider_location');
                Route::get('/all-location-on-map', [TransportController::class,'getAdminTransportAllProviderLocation'])->name('get:admin:transport_all_provider_location');
                Route::get('/available-location-on-map', [TransportController::class,'getAdminTransportAvailableProviderLocation'])->name('get:admin:transport_available_provider_location');
                Route::get('/ride-start-location-on-map', [TransportController::class,'getAdminTransportRideStartProviderLocation'])->name('get:admin:transport_ride_start_provider_location');
                Route::get('/ride-reached-location-on-map', [TransportController::class,'getAdminTransportRideReachedProviderLocation'])->name('get:admin:transport_ride_reached_provider_location');
                Route::get('/ride-enroute-location-on-map', [TransportController::class,'getAdminTransportRideEnrouteProviderLocation'])->name('get:admin:transport_ride_enroute_provider_location');
                Route::get('/location-on-map', [TransportController::class,'getAdminTransportLocationOnMap'])->name('get:admin:transport_location_on_map');
                Route::get('/search-provider-on-map', [TransportController::class,'getAdminTransportSearchProviderOnMap'])->name('get:admin:transport_search_provider_on_map');

                //Referral
                Route::get('/referral-list', [AdminController::class,'getReferralList'])->name('get:admin:referral_list');
                Route::get('/referral-list-new', [AdminController::class,'getReferralListNew'])->name('get:admin:referral_list_new');

                //Customer Referred List
                Route::get('/referred-list/{id}', [AdminController::class,'getReferredList'])->name('get:admin:referred_list');
                Route::get('/referred-list-new/{id}', [AdminController::class,'getReferredListNew'])->name('get:admin:referred_list_new');

                //Cash-out Module
                Route::get('/cash-out', [TransportController::class,'getTransportServiceCashOutList'])->name('get:admin:transport_cash_out_list');
                Route::get('/cash-out-new', [TransportController::class,'getTransportCashOutList'])->name('get:admin:transport_cash_out_list_new');
                Route::get('/update-cash-out-status', [TransportController::class,'getUpdateTransportCashOutStatus'])->name('get:admin:transport_update_cash_out_status');

                //City Area list in Geo Fencing
                Route::get('/city-area-list', [AdminController::class,'getAdminCityAreaList'])->name('get:admin:city_area_list');
                Route::get('/add-city-area', [AdminController::class,'getAdminAddCityArea'])->name('get:admin:add_city_area');
                Route::get('/edit-city-area/{id}', [AdminController::class,'getAdminEditCityArea'])->name('get:admin:edit_city_area');
                Route::post('/update-city-area', [AdminController::class,'postAdminUpdateCityArea'])->name('post:admin:update_city_area');
                Route::get('/update-city-area-status', [AdminController::class,'postAdminUpdateCityAreaStatus'])->name('get:admin:update_city_area_status');
                Route::get('/delete-city-area', [AdminController::class,'getAdminDeleteCityArea'])->name('get:admin:delete_city_area');
                // city admin List ajax code
                Route::get('/city-admin-list-ajax', [AdminController::class,'getAjaxAdminCityAreaList'])->name('get:ajax:admin:city_area_list');

                /*--------------------------------------------------------- City Admin ---------------------------------------------------------*/
                //city admin
                Route::get('/city-admin-list', [AdminController::class,'getAdminCityAdminList'])->name('get:admin:city_admin_list');
                Route::get('/add-city-admin', [AdminController::class,'getAdminAddCityAdmin'])->name('get:admin:add_city_admin');
                Route::get('/edit-city-admin/{admin_id}', [AdminController::class,'getAdminEditCityAdmin'])->name('get:admin:edit_city_admin');
                Route::post('/update-city-admin', [AdminController::class,'postAdminUpdateCityAdmin'])->name('post:admin:update_city_admin');
                Route::get('/delete-city-admin', [AdminController::class,'getAdminDeleteCityAdmin'])->name('get:admin:delete_city_admin');
                // city admin List ajax code
                Route::get('/city-admin-list-new', [AdminController::class,'getAdminCityAdminListNew'])->name('get:admin:city_admin_list_new');
                /*--------------------------------------------------------- End City Admin ---------------------------------------------------------*/
                Route::get('/transport-heat-map', [HeatMapController::class,'getAdminTransportHeatMap'])->name('get:admin:transport_heat_map');

                Route::prefix('sos')->group(function () {
                    // Manage SOS list
                    Route::get('/manage', [AdminController::class, 'showSos'])->name('get:admin:sos');

                    // Fetch SOS list for datatable via AJAX
                    Route::get('/fetch-sos-list', [AdminController::class, 'getSosList'])->name('get:admin:sos_list');

                    // Add SOS form
                    Route::get('/add', [AdminController::class, 'addSos'])->name('get:admin:add_sos');

                    // Edit SOS form
                    Route::get('/edit/{id}', [AdminController::class, 'editSos'])->name('get:admin:edit_sos');

                    // Add or update SOS record
                    Route::post('/saveUpdateSos', [AdminController::class, 'saveUpdateSos'])->name('post:admin:save_update_sos');

                    // Delete SOS record
                    Route::get('/delete-sos', [AdminController::class, 'getDeleteSos'])->name('get:admin:delete_sos');

                    // Update SOS status via AJAX call
                    Route::get('/updateSosStatus', [AdminController::class, 'updateSosStatus'])->name('get:admin:update_sos_status');

                    Route::get('/trigger-logs', [AdminController::class, 'showSosTriggerLogs'])->name('get:admin:sos_trigger_logs');
                    Route::get('/fetch-sos-trigger-logs', [AdminController::class, 'getSosTriggerLogList'])->name('get:admin:sos_trigger_logs_list');
                });

                //available ride screen distance filter
                Route::get('/search-radius', [AdminController::class,'getAdminSearchRadius'])->name('get:admin:search_radius_list');
                Route::post('/update-search-radius', [AdminController::class,'postUpdateSearchRadius'])->name('post:admin:update_search_radius');
                Route::get('/delete-search-radius', [AdminController::class,'postDeleteSearchRadius'])->name('get:ajax:admin:delete_search_radius');

                //Report issue
                Route::group(['prefix' => 'report-issue'], function () {
                    //Send message Notification to user to firebase live database
                    Route::get('/send-message-notification/', [ReportIssueController::class, 'sendMessageNotification'])->name('get:admin:send_message_notification');
                    //set web token of admin
                    Route::get('/admin-web-token', [ReportIssueController::class, 'updateWebToken'])->name('get:admin:update_web_token');
                    //Upload image as a message in firebase
                    Route::post('/upload-chat-image', [ReportIssueController::class, 'uploadChatImage'])->name('get:admin:upload_chat_image');
                    //form for Report issue setting
                    Route::get('/setting', [ReportIssueController::class, 'getAdminReportIssueSetting'])->name('get:admin:report_issue_setting');
                    //updating the report issue setting
                    Route::post('/setting', [ReportIssueController::class, 'postAdminUpdateReportIssueSetting'])->name('post:admin:report_issue_setting');
                    //ajax report issue update status
                    Route::get('report-issue/update-report-issue-status', [ReportIssueController::class, 'updateReportIssuesStatus'])->name('get:ajax:admin:update_report_issue_status');
                    //Manage Report issues
                    Route::get('/{slug}', [ReportIssueController::class, 'showReportIssues'])->name('get:admin:report_issue');

                    Route::get('/chat/{id}', [ReportIssueController::class, 'showReportIssueChat'])->name('get:admin:report_issue_chat');
                    //Fetch Report issues with AJAX
                    Route::get('/fetch-report-issue/{providerType}', [ReportIssueController::class, 'getReportIssue'])->name('get:ajax:admin:fetch_report_issue');

                    //Faqs
                    Route::group(['prefix' => 'faqs'], function () {
                        //Manage Faqs
                        Route::get('/manage', [ReportIssueController::class, 'showFaq'])->name('get:admin:faqs');
                        //Fetch Faqs from datatable with AJAX
                        Route::get('/fetchFaqs', [ReportIssueController::class, 'getFaq'])->name('get:ajax:admin:fetch_faq_plan_lists');
                        //Add Faqs form
                        Route::get('/add', [ReportIssueController::class, 'addFaq'])->name('get:admin:add_faq');
                        //Edit Faqs form
                        Route::get('/edit/{id}', [ReportIssueController::class, 'editFaq'])->name('get:admin:edit_faq');
                        //Update or add Faqs record
                        Route::post('/saveUpdateFaqs', [ReportIssueController::class, 'saveUpdateFaq'])->name('post:admin:update_faq');
                        //Update Faqs status via ajax call
                        Route::get('/updateFaqsStatus', [ReportIssueController::class, 'updateFaqStatus'])->name('get:ajax:admin:update_faq_status');
                        //Delete Faqs
                        Route::get('/delete', [ReportIssueController::class, 'deleteFaq'])->name('get:admin:delete_faq');
                    });
                    //report issue details manage
                    Route::get('{id}/{provider_id}', [ReportIssueController::class, 'showReportDetails'])->name('get:admin:detailed_report');
                });

                Route::get('/chat-history/{order_id}', [OrderWiseChatController::class,'getOrderWiseChat'])->name('get:admin:get_order_wise_chat');
                Route::get('/fetch-chats', [OrderWiseChatController::class,'getOrderWiseChatAjax'])->name('get:admin:get_order_wise_chat_ajax');

            });
        });
    });
});
});
