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
        if (!Schema::hasTable('user_ride_booking')) {
            Schema::create('user_ride_booking', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->nullable();
                $table->unsignedInteger('driver_id')->nullable();
                $table->unsignedInteger('vehicle_type_id');
                $table->unsignedInteger('vehicle_service_id');
                $table->bigInteger('ride_no')->nullable();
                $table->dateTime('pickup_datetime', precision: 0);
                $table->dateTime('destination_datetime', precision: 0)->nullable();
                $table->tinyInteger('ride_type')->default(0)->comment('0=ride_now, 1=schedule_ride');
                $table->text('pickup_address');
                $table->string('pickup_lat', 191);
                $table->string('pickup_long', 191)->nullable();
                $table->text('destination_address');
                $table->string('destination_latlong', 191);
                $table->string('driver_name', 60)->nullable();
                $table->string('user_name', 100)->nullable();
                $table->string('vehicle_service_name', 30)->nullable();
                $table->double('vehicle_cost_for_km')->nullable();
                $table->string('total_distance', 10);
                $table->double('total_distance_amount')->default(0.00);
                $table->string('eta', 20);
                $table->float('tip')->default(0.00);
                $table->double('refer_discount')->default(0.00);
                $table->double('sub_total')->default(0.00);
                $table->double('min_bargain_amt')->default(0.00);
                $table->double('offered_price')->default(0.00);
                $table->double('total_pay')->default(0.00);
                $table->float('driver_amount')->default(0.00);
                $table->double('admin_commission')->default(0.00);
                $table->tinyInteger('payment_type')->default(1);
                $table->tinyInteger('payment_status')->default(0)->comment('0=pending, 1=completed');
                $table->integer('promo_code')->default(0)->comment('0=notused');
                $table->tinyInteger('completed_by')->default(0)->comment('0 = not competed by admin, 1 = completed by admin');
                $table->string('cancel_by', 10)->nullable();
                $table->string('cancel_reason', 100)->nullable();
                $table->tinyInteger('status')->default(0)->comment('0=pending, 1=accepted, 2=schedule-accepted, 3=arrived, 4=cancelled, 5=running, 6=drop, 7=payment, 8=rating, 9=completed, 10=failed');
                $table->tinyInteger('driver_algorithm')->default(0);
                $table->tinyInteger('driver_pay_settle_status')->default(0);
                $table->mediumInteger('otp')->nullable();
                $table->string('additional_request', 191)->nullable();
                $table->integer('no_of_retry')->default(0);
                $table->timestamp('retry_time')->nullable();
                $table->tinyInteger('user_refund_status')->default(0)->comment('	0: not refund, 1:refund done');
                $table->double('cancel_charge')->default(0.00);
                $table->double('refund_amount')->default(0.00);
                $table->integer('is_driver_reassign')->default(0)->comment('0-notassign,0<-count reassign driver');
                $table->integer('user_rating_status')->default(0)->comment('0-pending,1-applied');
                $table->tinyInteger('driver_gender')->default(0)->comment('0=both,1=male,2=female');
                $table->timestamp('ride_time_out')->nullable();
                $table->integer('user_refer_history_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_ride_booking');
    }
};
