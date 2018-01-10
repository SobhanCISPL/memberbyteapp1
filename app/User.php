<?php

namespace App;

use DB;
use Exception;
use App\Email\Email;

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
        try{
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
        catch (Illuminate\Database\QueryException $ex)
        {
            throw new Exception($ex->getMessage());
        }
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
            $result = true;
            $values = [];
            $insertOrUpdate = 1; // insert -> 1, update -> 2
            $currentTime = time();
            $emailFire = false;
            $data = ['otp' => rand(111111,999999)];
            $query = DB::table($this->userOtpTable);
            $condition = ['email_id' => $email];
            $user = $this->checkUserInOtpTable($condition);
            $values = [
                'otp' => $data['otp'],
                'created_at' => CURR_DATE_TIME_EST
            ];
            if(count($user) > 0 && ($currentTime - strtotime($user[0]->created_at) > 60*60*24)){
                $emailFire = true;
                $insertOrUpdate = 2; 
            }
            if(count($user) == 0){
                $values['email_id'] = $email;
                $emailFire = true;
            }
            if(count($user) > 0 && ($currentTime - strtotime($user[0]->created_at) < 60*60*24)){
                return $result;
            }
            $mail = sendEmail($email,$data);
            if(!empty($mail)){
                $result = ($insertOrUpdate === 1) 
                ? $this->insertUser($query, $values) 
                : $this->updateUserForOtpTable($query, $condition, $values);
                if($result){
                    return $result;
                }
            }
            return false;
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

    public function checkingOtp ($otp, $email) 
    {
        $result = false;
        $values = [];
        try{
            $query = DB::table($this->userOtpTable);
            $condition = ['otp' => $otp, 'email_id' => $email];
            $user_otp_matched = $this->checkUserInOtpTable($condition);
            return $user_otp_matched;

        }catch(Exception $ex){
            throw new Exception($ex->getMessage());
        }
    }

    public function basicLoginUserChecking ($password, $email, $user_detail = [], $user_flag) 
    {
        try{
            $result = false;
            $values = [];
            $query = DB::table($this->table);
            $condition = ['email' => $email, 'login_type' => 3]; 
            if($user_flag == 1){
                $values = [
                    'name' => $user_detail['first_name'].' '.$user_detail['last_name'],
                    'email' => $email,
                    'login_type' => 3,
                    'password' => $password,
                    'created_at' => CURR_DATE_TIME_EST
                ];
                $result = $this->insertUser($query, $values);
            }
            else{
                $result = $this->updateUser($query, $condition, ['password' => $password]);
            }
            return $result;
        }catch(Exception $ex){
            throw new Exception($ex->getMessage());
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