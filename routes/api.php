<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\UpdateRegisterController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ReportIssueController;
use App\Http\Controllers\Api\SharedRideController;
use App\Http\Controllers\Api\Transport\UserController;
use App\Http\Controllers\HeatMapController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'setLocaleLang'], function () {
    Route::middleware('api')->group(function () {

        if (! app()->environment('production')) {
            Route::post('/test-email-templates', [CustomerApiController::class,'postTestEmailTemplates'])->name('post:customer:test_email_templates');
        }
        Route::post('/wompi/webhook', [CustomerApiController::class,'postWompiWebhook'])->name('post:wompi:webhook');

        // Bootstrap endpoints (no app Authorization header — client uses --dart-define=XISTI_APP_KEY)
        Route::prefix('customer')->group(function () {
            Route::post('/app-version-check', [CustomerApiController::class, 'postAppVersionCheck'])->name('post:app-version-check');
            Route::post('/market-config', [CustomerApiController::class, 'postMarketConfig'])->name('post:market-config');
            Route::post('/country-and-currency-list', [CustomerApiController::class, 'postCountryAndCurrencyList'])->name('post:customer:country-and-currency-list');
            Route::post('/facebook-delete-customer',[CustomerApiController::class,'postFacebookUserDataDeletion'])->name('post:user:facebook_data_deletion');
        });

        Route::middleware(['mobile.app'])->group(function () {

            // Maps during signup/OTP: credentials only (no verified_at). Rate-limited per user/session.
            Route::middleware(['mobile.credentials', 'throttle:60,1', 'throttle:daily-map-call-limit'])->group(function () {
                Route::post('/google-map', [CustomerApiController::class,'postFirebaseSecurityRules'])->name('post:customer:firebase_security_rules');
                Route::post('/google-autocomplete-places', [CustomerApiController::class, 'postAutocompleteGooglePlaces']);
                Route::post('/google-place-detail', [CustomerApiController::class, 'postGooglePlaceDetails']);
                Route::post('/google-route-detail', [CustomerApiController::class, 'postGoogleRouteDetails']);
            });

            Route::prefix('customer')->group(function () {

                Route::middleware('throttle:customer-login')->group(function () {
                    Route::post('/login', [LoginController::class,'postCustomerLogin'])->name('post:customer:login');
                    Route::post('/register', [RegisterController::class,'postCustomerRegister'])->name('post:customer:register');
                });
                Route::post('/finger-login', [LoginController::class,'postCustomerFingerLogin'])->middleware('throttle:60,60')->name('post:customer:finger-login');

                Route::middleware(['mobile.credentials', 'throttle:customer-otp'])->group(function () {
                    Route::post('/contact-verification', [UpdateRegisterController::class,'postCustomerContactVerification'])->name('post:customer:contact_verification');
                });
                Route::middleware(['mobile.credentials', 'throttle:customer-resend-otp'])->group(function () {
                    Route::post('/resend-otp-verification', [UpdateRegisterController::class,'postCustomerResendOtpVerification'])->name('post:customer:resend_otp_verification');
                });

                // Logout: idempotent; only clears session when access_token matches.
                Route::post('/logout', [LogoutController::class,'postCustomerLogout'])->name('post:customer:logout');

                Route::middleware('mobile.user')->group(function () {
                    Route::post('/edit-profile', [UpdateRegisterController::class,'postUpdateCustomerDetails'])->name('post:customer:update_details');
                    Route::post('/change-password', [ResetPasswordController::class,'postCustomerChangePassword'])->name('post:customer:change_password');
                    Route::post('/update-country-and-currency', [UpdateRegisterController::class,'postCustomerUpdateCountryAndCurrency'])->name('post:customer:update-country-and-currency');

                    Route::post('/mass-notification-list', [CustomerApiController::class,'postCustomerMassNotificationList'])->name('post:customer:get_mass_notification_list');

                    Route::post('/search-wallet-transfer-user-list', [CustomerApiController::class,'postCustomerSearchWalletTransferUserList'])->name('post:customer:search_wallet_transfer_user_list');
                    Route::middleware('throttle:30,1')->group(function () {
                        Route::post('/wallet-transfer', [CustomerApiController::class,'postCustomerWalletToWalletTransfer'])->name('post:customer:wallet_to_wallet_transfer');
                        Route::post('/add-wallet-balance', [CustomerApiController::class,'postCustomerAddWalletBalance'])->name('post:customer:add_wallet_balance');
                    });

                    Route::post('/change-contact-number', [UpdateRegisterController::class,'postCustomerChangeContactNumber'])->name('post:customer:change_contact_number');

                    Route::post('/support-pages', [CustomerApiController::class,'postMyCheckoutSupportPages'])->name('post:support_pages');

                    Route::post('/active-mode',[CustomerApiController::class,'postUserActiveMode'])->name('post:customer:active_mode');
                    Route::post('/driver-status',[CustomerApiController::class,'postDriverStatus'])->name('post:driver:status');

                    Route::post('/get-bank-details', [UserController::class,'postGetDriverBankDetails'])->name('post:driver:get_bank_history');
                    Route::post('/update-bank-details', [UserController::class,'postUpdateDriverBankDetails'])->name('post:driver:update_bank_history');

                    Route::post('/customer-details',[CustomerApiController::class,'postCustomerDetails'])->name('post:customer:details');
                    Route::post('/remove-account',[CustomerApiController::class,'postUserRemoveAccount'])->name('post:customer:remove_account');
                    Route::post('/home', [CustomerApiController::class,'postHomepage'])->name('post:customer:homepage');

                    Route::post('/vehicle-service-list', [UserController::class,'postVehicleServiceList'])->name('post:driver:vehicle_service_list');
                    Route::post('/required-document-list', [UserController::class,'postRequiredDocumentList'])->name('post:driver:required_document_list');
                    Route::post('/upload-document', [UserController::class,'postUploadDocument'])->name('post:driver:upload_document');
                    Route::post('/service-register', [UserController::class,'postServiceRegister'])->name('post:driver:service_register');
                    Route::post('/get-vehicle-details', [UserController::class,'postDriverGetVehicleDetails'])->name('post:driver:get_vehicle_details');

                    Route::post('/ride-booking', [UserController::class,'postTransportRideBooking'])->name('post:customer:transport_ride_booking');
                    Route::post('/shared-ride-create-offer', [SharedRideController::class, 'postCreateOffer'])->name('post:driver:shared_ride_create_offer');
                    Route::post('/shared-ride-search', [SharedRideController::class, 'postPassengerSearch'])->name('post:customer:shared_ride_search');
                    Route::post('/shared-ride-join', [SharedRideController::class, 'postJoinOffer'])->name('post:customer:shared_ride_join');
                    Route::post('/shared-ride-my-offers', [SharedRideController::class, 'postMyOffers'])->name('post:driver:shared_ride_my_offers');
                    Route::post('/shared-ride-fare-estimate', [SharedRideController::class, 'postFareEstimate'])->name('post:driver:shared_ride_fare_estimate');
                    Route::post('/available-ride-request', [UserController::class,'postAvailableRideRequest'])->name('post:driver:available_ride_request');
                    Route::post('/update-current-lat-long', [UserController::class,'postDriverUpdateCurrentLatLong'])->name('post:driver:update_current_lat_long');
                    Route::post('/update-current-status', [UserController::class,'postDriverUpdateCurrentStatus'])->name('post:driver:update_current_status');
                    Route::post('/update-driver-availability-modes', [UserController::class,'postUpdateDriverAvailabilityModes'])->name('post:driver:update_availability_modes');
                    Route::post('/driver-bid', [UserController::class,'postDriverBid'])->name('post:driver:bid');
                    Route::post('/driver-bid-list', [UserController::class,'postDriverBidList'])->name('post:driver:bid:list');

                    Route::post('/update-price', [UserController::class,'postUpdatePrice'])->name('post:customer:update:price');
                    Route::post('/decline-request', [UserController::class,'postDeclineRequest'])->name('post:customer:decline:request');
                    Route::post('/get-ride-status', [UserController::class,'postGetRideStatus'])->name('post:driver:get:ride:status');
                    Route::post('/cancel-ride', [UserController::class,'postCancelRide'])->name('post:customer:cancel:ride');
                    Route::post('/log-sos-trigger', [UserController::class,'postLogSosTrigger'])->name('post:customer:log_sos_trigger');
                    Route::post('/accept-ride', [UserController::class,'postAcceptRide'])->name('post:customer:accept:ride');

                    Route::post('/get-driver-running-service', [UserController::class,'postDriverGetRunningService'])->name('post:driver:get_running_service');
                    Route::post('/get-customer-running-service', [CustomerApiController::class,'postCustomerGetRunningService'])->name('post:customer:get_running_service');

                    Route::post('/ride-receipt-details', [UserController::class,'postTransportRideReceiptDetails'])->name('post:customer:transport_ride_receipt_details');
                    Route::post('/ride-details', [UserController::class,'postDriverRideDetails'])->name('post:driver:ride_details');
                    Route::post('/update-ride-status', [UserController::class,'postDriverUpdateRideStatus'])->name('post:driver:update_ride_status');
                    Route::post('/ride-user-rating', [UserController::class,'postTransportRideUserRating'])->name('post:customer:transport_ride_user_rating');
                    Route::post('/ride-payment', [UserController::class,'postTransportOrderPayment'])->name('post:customer:ride_payment');

                    Route::post('/user-ride-history', [UserController::class,'postTransportRideHistory'])->name('post:customer:transport_ride_history');
                    Route::post('/driver-ride-history', [UserController::class,'postDriverRideHistory'])->name('post:driver:ride_history');
                    Route::post('/driver-earning', [UserController::class,'postDriverEarning'])->name('post:driver:earning');
                    Route::post('/ride-feedback', [UserController::class,'postGetDriverFeedback'])->name('post:driver:get_driver_feedback');

                    Route::post('/add-card', [CustomerApiController::class,'postCustomerAddCard'])->name('post:customer:add_card');
                    Route::post('/delete-card', [CustomerApiController::class,'postCustomerRemoveCard'])->name('post:customer:remove_card');
                    Route::post('/card-list', [CustomerApiController::class,'postCustomerCardList'])->name('post:customer:card_list');

                    Route::post('/wallet-transaction', [CustomerApiController::class,'postCustomerWalletTransaction'])->name('post:customer:wallet_transaction');
                    Route::post('/get-wallet-balance', [CustomerApiController::class,'postCustomerGetWalletBalance'])->name('post:customer:get_wallet_balance');

                    Route::post('/get-ride', [UserController::class,'postDriverGetRide'])->name('post:driver:get_ride');
                    Route::post('/get-driver-list', [UserController::class,'postGetDriverList'])->name('post:user:get_driver_list');
                    Route::post('/get-refer-info', [CustomerApiController::class,'postReferInfo'])->name('post:customer:get_refer_info');
                    Route::post('/request-cash-out', [CustomerApiController::class,'postDriverRequestCashout'])->name('post:driver:request_cash_out');
                    Route::post('/ride-pricing', [CustomerApiController::class,'postRidePricing'])->name('post:driver:ride_pricing');
                    Route::post('/heat-map', [HeatMapController::class,'postDriverHeatMap'])->name('post:driver:heat_map');
                    Route::post('/driver-home', [UserController::class,'postDriverHome'])->name('post:driver_home');
                    Route::post('/hail-ride-booking', [UserController::class,'postHailRideBooking'])->name('post:driver:hail_ride_booking');
                    Route::post('/driver-accept-ride', [UserController::class,'postDriverAcceptRide'])->name('post:driver:accept_ride');

                    Route::prefix('report-issue')->group(function () {
                        Route::post('/faqs', [ReportIssueController::class,'postReportIssueFaqsList'])->name('post:report_issue_faqs_list');
                        Route::post('/draft', [ReportIssueController::class,'postReportIssueDraft'])->name('post:report_issue_draft');
                        Route::post('/update', [ReportIssueController::class,'postUpdateReportIssue'])->name('post:update_report_issue');
                        Route::post('/upload-image', [ReportIssueController::class,'postReportIssueUploadImage'])->name('post:report_issue_upload_image');
                        Route::post('/remove-image', [ReportIssueController::class,'postReportIssueRemoveImage'])->name('post:report_issue_remove_image');
                        Route::post('/details', [ReportIssueController::class,'postReportIssueDetails'])->name('post:report_issue_details');
                        Route::post('/history', [ReportIssueController::class,'postReportIssueHistory'])->name('post:report_issue_on_demand_history');
                        Route::post('/general-history', [ReportIssueController::class,'postGeneralReportIssueHistory'])->name('post:report_issue_general_history');
                        Route::post('/chat-photos', [ReportIssueController::class, 'uploadChatPhoto']);
                        Route::post('/delete-chat-photos', [ReportIssueController::class, 'deleteChatPhoto']);
                    });

                    Route::post('/add-address', [CustomerApiController::class,'postCustomerAddAddress'])->name('post:customer:add_address');
                    Route::post('/edit-address', [CustomerApiController::class,'postCustomerEditAddress'])->name('post:customer:edit_address');
                    Route::post('/delete-address', [CustomerApiController::class,'postCustomerDeleteAddress'])->name('post:customer:delete_address');
                    Route::post('/address-list', [CustomerApiController::class,'postCustomerAddressList'])->name('post:customer:address_list');
                    Route::post('/start-request', [UserController::class,'postDriverStartRequest'])->name('post:driver:start_request');
                    Route::post('/update-device-token', [CustomerApiController::class,'postUpdateDeviceToken'])->name('post:provider:update_token');
                });
            });
        });
    });
});
