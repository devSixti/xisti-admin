<?php

namespace App\Http\Controllers\Auth;

use App\Classes\NotificationClass;
use App\Http\Controllers\Controller;


class RegisterController extends Controller
{
    //use RegistersUsers;
    private $notificationClass;
    protected $redirectTo = '/home';


    public function __construct(NotificationClass $notificationClass){
        $this->notificationClass = $notificationClass;
        $this->middleware('guest');

    }


    protected function create(array $data){
        return "";
    }
}
