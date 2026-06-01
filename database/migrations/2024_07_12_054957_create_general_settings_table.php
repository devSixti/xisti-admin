<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the table does not exist then create
        if (!Schema::hasTable('general_settings')) {
            Schema::create('general_settings', function (Blueprint $table) {
                $table->id();
                $table->string('website_name', '50')->nullable();
                $table->string('website_logo', '50')->nullable();
                $table->string('website_favicon', '50')->nullable();
                $table->text('address')->nullable();
                $table->string('contact_no', '15')->nullable();
                $table->string('email', '40')->nullable();
                $table->string('send_receive_email', '100')->nullable();
                $table->string('site_url', '191')->nullable();
                $table->string('copy_right', '191')->nullable();
                $table->string('facebook_link', '191')->nullable();
                $table->string('instagram_link', '191')->nullable();
                $table->string('linkedin_link', '191')->nullable();
                $table->string('map_key', '191')->nullable();
                $table->string('fcm_user_topic_name', '50')->nullable();
                $table->string('fcm_driver_topic_name', '50')->nullable();
                $table->string('user_playstore_link', '191')->nullable();
                $table->string('user_appstore_link', '191')->nullable();
                $table->string('driver_delivery_playstore_link', '191')->nullable();
                $table->string('driver_delivery_appstore_link', '191')->nullable();
                $table->double('used_user_discount')->default(0);
                $table->tinyInteger('used_user_discount_type')->default(0);
                $table->double('refer_user_discount')->default(0);
                $table->tinyInteger('refer_user_discount_type')->default(0);
                $table->string('about_us_youtube_link', '191')->nullable();
                $table->integer('user_timeout')->default(60);
                $table->tinyInteger('driver_algorithm')->default(0);
                $table->integer('max_driver_reassign')->default(0)->comment('0-not reassign,0<max limit assign');
                $table->integer('day_allow')->default(0)->comment('0-disable,0< no of day show record');
                $table->integer('send_mail')->default(0)->comment('1:mail send, 0: mail not send');
                $table->string('mail_site_name', '191')->nullable();
                $table->string('smtp_user_name', '191')->nullable();
                $table->string('smtp_password', '191')->nullable();
                $table->string('smtp_hostname', '191')->default('smtp.googlemail.com');
                $table->integer('smtp_port')->default(465);
                $table->string('smtp_encryption', '50')->default('ssl');
                $table->tinyInteger('is_otp_verification')->default(0)->comment('0=not-verify(1234),1=verify');
                $table->tinyInteger('otp_method')->default(0)->comment('0=default,1=twilio');
                $table->tinyInteger('rounding_amount_module')->default(0);
                $table->tinyInteger('cash_payment')->default(1)->comment('1:show ,0:hide');
                $table->tinyInteger('card_payment')->default(1)->comment('1:show ,0:hide');
                $table->tinyInteger('wallet_payment')->default(1)->comment('1:show ,0:hide');
                $table->tinyInteger('auto_settle_wallet')->default(0)->comment('0:no settle ,1:auto settle in wallet after complete');
                $table->tinyInteger('ride_eta_consider')->default(0)->comment('0:normal mean eta time consider ,1:eta time not consider last location consider');
                $table->text('app_key')->nullable();
                $table->tinyInteger('ride_otp')->default(0)->comment('0-no 1-yes');
                $table->string('address_lat', '50')->default(0);
                $table->string('address_long', '50')->default(0);
                $table->integer('login_timeout_time')->default(10080)->comment('please enter in minutes');
                $table->text('fcm_bearer_token')->nullable();
                $table->dateTime('fcm_bearer_token_expiry_date')->nullable();
                $table->integer('fcm_bearer_token_expiry_mins')->default(55);
                $table->tinyInteger('auto_approve')->default(0)->comment('1 - on , 0 - off');
                $table->double('min_cashout')->default(0.00);
                $table->double('max_cashout')->default(0.00);
                $table->double('driver_min_amount')->default(0.00);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
