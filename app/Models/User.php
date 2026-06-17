<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'users';
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function generateAccessToken($id)
    {
        $this->access_token = random_int(1, 99) . date('siHYdm') . random_int(1, 99);
        $this->save();
        return $this->access_token;
    }

    /**
     * Keep the existing mobile token when the same device signs in again.
     * Prevents invalidating a stored session after inactivity.
     */
    public function refreshAccessTokenForDevice(?string $incomingDeviceToken): void
    {
        $incoming = trim((string) $incomingDeviceToken);
        $existingDevice = trim((string) ($this->device_token ?? ''));
        $hasToken = $this->access_token !== null && $this->access_token !== '';

        if ($hasToken && $incoming !== '' && $existingDevice !== '' && hash_equals($existingDevice, $incoming)) {
            return;
        }

        $this->generateAccessToken($this->id);
    }

    public function InviteCode($id, $name)
    {
        $this->invite_code = $this->random_strings(7);
        $this->unique_id = md5(microtime());
        $this->save();
        return $this->invite_code;
    }

    public function random_strings($length_of_string)
    {

        // String of all alphanumeric character
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        // Shuffle the $str_result and returns substring
        // of specified length
        return substr(str_shuffle($str_result),
            0, $length_of_string);
    }


    public static function ContactNumber2Stars($string)
    {
        if(\Request::get('is_restrict_admin') != 1)
        {
            return $string;
        }
//        $coverWith = function($string, $char, $number) {
//            return substr($string, 0, -4) . str_repeat($char, 4);
//        };
        //return $string;
        $cha = strlen($string) - 4;
        $first = 0;
        $last = $cha;
        $rep = '*';
        $begin = substr($string, 0, $first);
        $middle = str_repeat($rep, strlen(substr($string, $first, $last)));
        $end = substr($string, $last);
        $stars = $begin . $middle . $end;
        return $stars;
    }

    public static function Email2Stars($string)
    {
        if(\Request::get('is_restrict_admin') != 1)
        {
            return $string;
        }

        $first = 2;
        $f_char = strlen($string) - $first;
        //return substr($string, 0, -$f_char) . str_repeat("*", $f_char);
        $first_string = substr($string, $first);
        $last_char = strlen($first_string) - 4;
        $last = $last_char;
        $rep = '*';
        $begin = substr($string, 0, -$f_char);
//        $begin = substr($string, 0, $first);
        $middle = str_repeat($rep, strlen(substr($string, $first, $last)));
        $end = substr($first_string, $last);
        $stars = $begin . $middle . $end;
        return $stars;
    }
}
