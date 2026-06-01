<?php

namespace App\Console\Commands;

use App\Classes\NotificationClass;
use App\Models\ProviderDocuments;
use App\Models\TransportDriverDetails;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DocumentExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document_expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = request()->get('general_settings');
        if($settings != NULL) {
            $first_warning = $settings->doc_expiry_warning_one > 0 ? $settings->doc_expiry_warning_one : 1;
            $second_warning = $settings->doc_expiry_warning_two > 0 ? $settings->doc_expiry_warning_two : 2;
            $third_warning = $settings->doc_expiry_warning_three > 0 ? $settings->doc_expiry_warning_three : 3;
            $current_date = date('Y-m-d');

            $providers = User::query()->select('users.id as user_id','provider_documents.id as document_id','provider_documents.expiry_date','users.device_token as device_token',
                DB::raw('DATE_SUB(provider_documents.expiry_date,INTERVAL '.$first_warning.' DAY) as first_expiry_warning'),
                DB::raw('DATE_SUB(provider_documents.expiry_date,INTERVAL '.$second_warning.' DAY) as second_expiry_warning'),
                DB::raw('DATE_SUB(provider_documents.expiry_date,INTERVAL '.$third_warning.' DAY) as third_expiry_warning'))
                ->join('provider_documents','provider_documents.user_id','=','users.id')
                ->join('required_documents','required_documents.id','=','provider_documents.req_document_id')
                ->where('users.status',1)
                ->where('required_documents.contains_expiry',1)
                ->where('required_documents.status',1)
                ->whereNotNull('provider_documents.expiry_date')
                ->whereNull('users.deleted_at');

            $first_expiry_warning = (clone $providers)->having( 'first_expiry_warning','=',$current_date);
            $second_expiry_warning = (clone $providers)->having("second_expiry_warning",'=',$current_date);
            $third_expiry_warning = (clone $providers)->having("third_expiry_warning",'=',$current_date);

            $first_expiry_array =(clone $first_expiry_warning)->get()->toArray();
            $second_expiry_array = (clone $second_expiry_warning)->get()->toArray();
            $third_expiry_array = (clone $third_expiry_warning)->get()->toArray();
            $common_array = [];
            $common_array = array_merge($first_expiry_array,$second_expiry_array,$third_expiry_array);
            $access_doc_ids = array_column($common_array,'document_id');
//            ProviderDocuments::query()->whereIn('id',$access_doc_ids)->update(['is_access' => 1]);

            $notification_class = new NotificationClass();

            $first_expiry_tokens = array_unique(array_column($first_expiry_array, 'device_token'));
            if(!empty($first_expiry_tokens)){
                $notification_class->sendExpiryNotification($first_expiry_tokens,$first_warning);
            }

            $second_expiry_tokens = array_unique(array_column($second_expiry_array, 'device_token'));
            if(!empty($second_expiry_tokens)){
                $notification_class->sendExpiryNotification($second_expiry_tokens,$second_warning);
            }

            $third_expiry_tokens = array_unique(array_column($third_expiry_array, 'device_token'));
            if(!empty($third_expiry_tokens)){
                $notification_class->sendExpiryNotification($third_expiry_tokens,$third_warning);
            }

            $expired_data = (clone $providers)->whereDate('provider_documents.expiry_date','<',$current_date)->groupBy('user_id')->get()->toArray();
            if($expired_data != NULL){
                $user_ids = array_column($expired_data,'user_id');
                $device_tokens = array_column($expired_data,'device_token');
                User::query()->whereIn('id',$user_ids)->update(['driver_current_status'=>0]);
//                TransportDriverDetails::query()->whereIn('user_id',$user_ids)->update(['is_doc_approved' => 0]);

                $doc_ids = (clone $providers)->whereDate('provider_documents.expiry_date','<',$current_date)->pluck('document_id');
                ProviderDocuments::query()->whereIn('id',$doc_ids)->update(['status' => 3]);
                $notification_class->sendExpiryNotification($device_tokens);
            }
        }
    }
}
