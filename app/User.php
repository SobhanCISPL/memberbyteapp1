<?php

namespace App;

use DB;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOTP;

class User
{
    protected $table = 'users';
    protected $userOtpTable = "user_otp";

    public function __construct(){

    }

    public function createUser($userDetail){
        try{
            $result = false;
            $values = [];
            $query = DB::table($this->table);
            $condition = ['email' => $userDetail['email']];
            $user = $this->getUser($condition);
            if(count($user) === 0 ){
                $values = [
                    'name' => !empty($userDetail['name']) ? $userDetail['name'] : null,
                    'email' => $userDetail['email'],
                    'profile_image' => $userDetail['profile_image_url'],
                    'login_type' => $userDetail['login_type'],
                    'created_at' => CURR_DATE_TIME_EST,
                    'last_loggedin_at' => CURR_DATE_TIME_EST
                ];
                $result = $this->insertUser($query, $values);
            }
            elseif(count($user) > 0){
                $user = json_decode(json_encode($user),1);
                $values = [
                    'last_loggedin_at' => CURR_DATE_TIME_EST
                ];
                $condition = ['id' => $user[0]['id']];
                $result = $this->updateUser($query, $condition, $values);
            }
            return $result;
        }
        catch(Exception $ex){
            throw new Exception($ex->getMessage());
        }
    }

    public function getUser($condition = [], $select = []){
        $query = DB::table($this->table);
        if(!empty($condition)){
            foreach($condition as $key => $value){
                $query->where($key, $value);
            }
        }
        if(!empty($select)){
            $query->select(implode(',', $select));
        }
        // print_r($query->get()->toArray());
        // die();
        return $query->get()->toArray();
    }

    public function insertUser($query, $values){
        return $query->insert($values); 
    }

    public function updateUser($query='', $condition=[], $values){

        if($query === ''){
            $query = DB::table($this->table);
        }

        if(!empty($condition)){
            foreach($condition as $key => $value){
                $query->where($key, $value);
            }
        }
        return $query->update($values);

    }

    public function updateUserForOtpTable($query='', $condition=[], $values){

        if($query === ''){
            $query = DB::table($this->userOtpTable);
        }

        if(!empty($condition)){
            foreach($condition as $key => $value){
                $query->where($key, $value);
            }
        }
        return $query->update($values);

    }

    public function checkingUser($email){
        $result = false;
        $values = [];
        $query = DB::table($this->userOtpTable);
        $condition = ['email_id' => $email ];
        $user = $this->getUser1($condition);

        $rand = rand(1999,9999);

        if(count($user) === 0){
            $data = array('otp'=>$rand);
            Mail::send('otp', $data, function($message) use ($email) {
                $message->from('sobhan.das@documentscanner.in','Code Clouds Developer');

                $message->to($email)->subject('Login OTP!');           
            });

            $values = [
                'email_id' => $email,
                'otp' => $rand,
                'created_at' => CURR_DATE_TIME_EST
            ];
            $result = $this->insertUser($query, $values);

            return 1;
        }elseif(count($user) > 0){

            if (time() - strtotime($user[0]->created_at) > 60*60*24) {

                $data = array('otp'=>$rand);
                Mail::send('otp', $data, function($message) use ($email) {
                    $message->from('sobhan.das@documentscanner.in','Code Clouds Developer');

                    $message->to($email)->subject('Login OTP!');           
                });

                $user = json_decode(json_encode($user),1);
                $values = [
                    'otp' => $rand,
                    'created_at' => CURR_DATE_TIME_EST
                ];
                $condition = ['id' => $user[0]['id']];
                $result = $this->updateUserForOtpTable($query, $condition, $values);

                if($result){
                    return 3;
                }

            } else {
                //less than 24 hours
               return 2;
            }
        }
    }

    public function getUser1($condition = [], $select = []){
        $query = DB::table($this->userOtpTable);
        if(!empty($condition)){
            foreach($condition as $key => $value){
                $query->where($key, $value);
            }
        }
        if(!empty($select)){
            $query->select(implode(',', $select));
        }
        return $query->get()->toArray();
    }

    public function cehckingOtp ($otp) {
        $result = false;
        $values = [];
        $query = DB::table($this->userOtpTable);
        $condition = ['otp' => $otp ];
        $user_otp_matched = $this->getUser1($condition);

        if(count($user_otp_matched) === 0){
            return 0;
            exit;
        }elseif(count($user_otp_matched) > 0){
            return $user_otp_matched;
            exit;
        }
    }

    public function basicLoginUserChecking ($password,$email_id,$first_name='',$last_name='') {
        $pw = $password;
        $email = $email_id;

        $result = false;
        $values = [];
        $query = DB::table($this->table);
        $condition = ['email' => $email, 'login_type' => 3];
        $user = $this->getUser($condition);
        if(count($user) === 0 ){
            $values = [
                'name' => $first_name.' '.$last_name,
                'email' => $email,
                'login_type' => 3,
                'password' => $pw,
                'created_at' => CURR_DATE_TIME_EST,
                'last_loggedin_at' => CURR_DATE_TIME_EST
            ];
            $result = $this->insertUser($query, $values);
            if($result){
                return 1;
            }
        }elseif(count($user) > 0){
            $user = json_decode(json_encode($user),1);
            $values = [
                'password' => $pw
            ];
            $condition = ['id' => $user[0]['id']];
            $result = $this->updateUser($query, $condition, $values);

            if($result){
                return 2;
            }
        }
    }


    //use for delete user from User_OTP table
    public function deleteUser ($email){
        $delete = DB::table($this->userOtpTable)->where('email_id', '=', $email)->delete();
        return $delete;
    }
}
