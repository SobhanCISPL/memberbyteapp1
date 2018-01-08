<?php

namespace App;

use DB;
use Exception;
use App\Email\Email;
// use App\Helpers\Helpers as Helper;

class User
{
    protected $table = 'users'; // user table
    protected $userOtpTable = "user_otp"; // usr otp table for basic login

    /**
     * Create Login and insert or update user detail for google/facebook.
     *
     * @param
     * @return boolean
     */
    public function createLogin($userDetail)
    {
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
                    throw new Exception(__('messages.USER_EXIST.EMAILID_EXIST'), 999);
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

    /**
     * Get user(s) from user table
     *
     * @param
     * @return array
     */
    public function getUser($condition = [], $select = [] )
    {
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

    /**
     * Insert user(s) into user table
     *
     * @param
     * @return boolean
     */
    public function insertUser($query, $values)
    {
        return $query->insert($values); 
    }

    /**
     * Update user detail into user table
     *
     * @param
     * @return boolean
     */
    public function updateUser($query='', $condition=[], $values)
    {

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

    public function updateUserForOtpTable($query='', $condition=[], $values)
    {

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

    public function checkingUser($email)
    {
        try{
            $result = false;
            $values = [];
            $query = DB::table($this->userOtpTable);
            $condition = ['email_id' => $email];
            $user = $this->checkUserInOtpTable($condition);

            //check user with user table
            $condition1 = ['email' => $email ];
            $user1 = $this->getUser($condition1);
            //end
            
            $rand = rand(111111,999999);

            if(count($user) === 0){
                if(count($user1) === 0){
                    $data = array('otp'=>$rand);
                    $mail = sendEmail($email,$data);
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
                        $mail = sendEmail($email,$data);
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
 
            }elseif (count($user) > 0) {
                if(count($user1) === 0){
                    if (time() - strtotime($user[0]->created_at) > 60*60*24) {
                        $data = array('otp'=>$rand);
                        $mail = sendEmail($email,$data);
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
                    }else {
                        return 2;
                    }
                }elseif(count($user1) > 0) {
                    if($user1[0]->login_type == 3){
                        if (time() - strtotime($user[0]->created_at) > 60*60*24) {
                            $data = array('otp'=>$rand);
                            $mail = sendEmail($email,$data);
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
                        }else {
                            return 2;
                        }
                    }else{
                        return "Exist";
                    }
                }
            }

            

        }catch(Exception $ex){
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
        
    }

    /**
    * get user opt row(s)
    *
    * @param $condition -> array, $select -> array
    * @return array
    */
    public function checkUserInOtpTable($condition = [], $select = [])
    {
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

    public function cehckingOtp ($otp) 
    {
        try{
            $result = false;
            $values = [];
            $query = DB::table($this->userOtpTable);
            $condition = ['otp' => $otp ];
            $user_otp_matched = $this->checkUserInOtpTable($condition);
            return $user_otp_matched;

        }catch(Exception $ex){
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
        
    }

    public function basicLoginUserChecking ($password,$email_id,$first_name='',$last_name='') 
    {
        try{
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
                    'created_at' => CURR_DATE_TIME_EST
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

        }catch(Exception $ex){
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
        
    }

    /**
     * delete user from User_OTP table
     *
     * @param
     * @return boolean
     */
    public function deleteUser ($email)
    {
        $delete = DB::table($this->userOtpTable)->where('email_id', '=', $email)->delete();
        return $delete;
    }
}
