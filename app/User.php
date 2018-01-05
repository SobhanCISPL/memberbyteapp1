<?php

namespace App;

use DB;
use Exception;
use App\Email\Email;

class User
{
    protected $table = 'users';
    protected $userOtpTable = "user_otp";

    public function createLogin($userDetail){
        try{
            $result = false;
            $values = [];
            $query = DB::table($this->table);
            $condition = ['email' => $userDetail['email']];
            $user = json_decode(json_encode($this->getUser($condition)), 1);
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
                if($user[0]['login_type'] !== $userDetail['login_type']){
                    throw new Exception('Email Id already exists', 999);
                    // return false;
                }
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
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
    }

     public function getUser($condition = [], $select = [] ){
        $query = DB::table($this->table);
        if(!empty($condition)){
            foreach($condition as $key => $value){
                $query->where($key, $value);
            }
        }
        if(!empty($select)){
            $query->select($select);
        }
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
        $user = $this->checkUserInOtpTable($condition);

        //check user with user table
        $condition1 = ['email' => $email ];
        $user1 = $this->getUser($condition1);
        //end
        
        $rand = rand(111111,999999);

        $this->Email = new Email();

        if(count($user) === 0 ){

            if (count($user1) === 0) {
                $data = array('otp'=>$rand);
                $mail = $this->Email->sendEmail($email,$data);
                if(!empty($mail)){
                    $values = [
                        'email_id' => $email,
                        'otp' => $rand,
                        'created_at' => CURR_DATE_TIME_EST
                    ];
                    $result = $this->insertUser($query, $values);

                    return 1;
                }

            }elseif(count($user1) > 0){
                if($user1[0]->login_type == 3){
                    $data = array('otp'=>$rand);
                    $mail = $this->Email->sendEmail($email,$data);
                    if(!empty($mail)){
                        $values = [
                            'email_id' => $email,
                            'otp' => $rand,
                            'created_at' => CURR_DATE_TIME_EST
                        ];
                        $result = $this->insertUser($query, $values);

                        return 1;
                    }
                }else{
                    return "Exist";
                }
            }

            
        }elseif(count($user) > 0){

            if (time() - strtotime($user[0]->created_at) > 60*60*24) {

                if (count($user1) === 0) {

                    $data = array('otp'=>$rand);
                    $mail = $this->Email->sendEmail($email,$data);
                    if(!empty($mail)){
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
                    }

                    
                }elseif (count($user1) > 0) {
                    if($user1[0]->login_type == 3){
                        $data = array('otp'=>$rand);
                        $mail = $this->Email->sendEmail($email,$data);
                        if(!empty($mail)){
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
                        }
                    }else{
                        return "Exist";
                    }
                }
                

            } else {
                //less than 24 hours

                if(count($user1) === 0){
                    return 2;
                }elseif (count($user1) > 0) {
                    if($user1[0]->login_type == 3){
                        return 2;
                    }else{
                        return "Exist";
                    }
                }
               
            }
        }
    }

    /**
    * get user opt row(s)
    *
    * @param $condition -> array, $select -> array
    * @return array
    */
    public function checkUserInOtpTable($condition = [], $select = []){
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
        $user_otp_matched = $this->checkUserInOtpTable($condition);
        return $user_otp_matched;
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

            if(!empty($result)){
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
