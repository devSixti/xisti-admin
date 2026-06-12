<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    private $firebase;
    private $chat_domain;

    /* To connect with the firebase real time database */
    public function __construct(){
        $get_host = request()->getHost();
        $this->chat_domain = preg_replace("/[\s_\-\.]/", "-",$get_host);
        $databaseUrl = config('xisti.firebase_database_url')
            ?: config('firebase-cloud-messaging.database_url');
        if (empty($databaseUrl)) {
            throw new \RuntimeException('FIREBASE_DATABASE_URL is not configured.');
        }

        $serviceAccountPath = config('firebase-cloud-messaging.service_account_path');
        $factory = new Factory;
        if (is_string($serviceAccountPath) && is_readable($serviceAccountPath)) {
            $factory = $factory->withServiceAccount($serviceAccountPath);
        } else {
            $factory = $factory->withServiceAccount(config('firebase-cloud-messaging.configurations'));
        }

        $this->firebase = $factory->withDatabaseUri($databaseUrl)->createDatabase();
    }

    /* Deleting the chat when order is completed*/
    public function deleteOrderChat($order_no,$order_id,$chat_type='order_chat'){
//        info("deleteOrderChat");
        $chat_order_id = $this->CreateOrderNumberForChat($order_no,$order_id);
//        info($chat_order_id);
//        info($this->chat_domain."/".$chat_type."/".$chat_order_id);
        $this->firebase->getReference($this->chat_domain."/".$chat_type."/".$chat_order_id)->remove();
        return 1;
    }

    /** Best-effort chat cleanup; never blocks ride status transitions. */
    public function safeDeleteOrderChat($order_no, $order_id, $chat_type = 'order_chat'): void
    {
        try {
            $this->deleteOrderChat($order_no, $order_id, $chat_type);
        } catch (\Throwable $e) {
            \Log::warning('safeDeleteOrderChat: firebase chat delete failed', [
                'order_no' => $order_no,
                'order_id' => $order_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    //creating order number for the chat
    public function CreateOrderNumberForChat($value1,$value2){
        return $value1.'-'.$value2;
    }

    public function fetchChatHistory($order_no,$order_id,$chat_type){
        //order_chat for chat wise order & ticket_chat for ticket wise chat
        $chat_order_id = $this->CreateOrderNumberForChat($order_no,$order_id);
        $chat_history = $this->firebase->getReference($this->chat_domain."/".$chat_type."/".$chat_order_id);

//        return response()->json($chat_history->getValue());
        return $chat_history->getValue();
    }
}
