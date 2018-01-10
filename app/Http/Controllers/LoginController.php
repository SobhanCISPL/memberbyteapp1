<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Response;
use Session;
use URL;
use App\SocialLogin\SocialLogin;
use Illuminate\Http\Request;
use App\Http\Controllers\OrderController;

class LoginController extends Controller
{
    protected $redirectUrl, $dashboardUrl, $driver, $orderController, $userModel;

    public function __construct()
    {
        $this->redirectUrl  = URL::to('/');
        $this->dashboardUrl = URL::to('/') . '/app';
        $this->userModel    = new User();
        $this->orderController   = new OrderController();
        $this->SocialLogin = new SocialLogin();
    }

    /**
     * Show login view / after google/facebook login redirection to dashboard.
     *
     * @param
     * @return view / json
     */

    public function index(Request $request)
    {
        $userDetail = [];
        try {
            if (
                $request->session()->has('login_flag') &&
                $request->session()->get('login_flag') === 'logged_in'
            ) {
                $user  = json_decode( json_encode( $this->userModel->getUser(
                    ['email' => $request->session()->get('userid')],
                    ['login_type']
                )), 1);

                if($user[0]['login_type'] === $request->session()->get('login_type')){
                    return redirect($this->dashboardUrl);
                }
            }
            /* start - google / facebook - after redirection */
            if ($request->has('code')) {
                $this->driver = $request->session()->get('login_driver');
                $detail = $this->SocialLogin->getUser($this->driver);
                $userDetail = $this->filterUserDetail($detail, $this->driver);

                /*start check any order exists with user email*/
                $response = $this->getTotalOrders($userDetail, $request);
                if($response['success'] === false && $response['total_order'] === 0){
                    $request->session()->forget('login_driver');
                    $this->userSessionForget($request);
                    return view('index', ['data' => $response]);
                }
                /*end check any order exists with user email*/

                $user = $this->userModel->createLogin($userDetail);
                if ($user) {
                    $request->session()->forget('login_driver');
                    $this->userSessionForget($request);
                    $request->session()->put('login_flag', 'logged_in');
                    $request->session()->put('userid', $userDetail['email']);
                    $request->session()->put('login_type', $userDetail['login_type']);
                    return redirect($this->dashboardUrl);
                }
            }
            /* end - google / facebook - after redirection */
            return view('index');
        } catch (Exception $ex) {
            $request->session()->forget('login_driver');
            $this->userSessionForget($request);
            $data = ['success' => false, 'error_message' => ($ex->getCode() == 999) ? $ex->getMessage() : ''];
            return view('index', ['data' => $data]);
        }
    }

    /**
     * Login method
     *
     * @param
     * @return array -> with login auth url for google/facebook login
     */

    public function login(Request $request)
    {
        try {
            $this->driver = $request->input('type');
            $request->session()->put('login_driver', $this->driver);
            $authUrl = $this->SocialLogin->authUrl($this->driver);
            return Response::json(['success' => true, 'authUrl' => $authUrl]);
        } catch (Exception $ex) {
            return Response::json(['success' => false, 'error_message' => $ex->getMessage()]);
        }
    }

    /**
     * User details filter to save to the db
     *
     * @param $detail -> user detail full array
     * @return array
     */

    private function filterUserDetail($detail, $driver)
    {
        $userDetail = [
            'id'                => '',
            'name'              => '',
            'email'             => '',
            'userType'          => '',
            'profile_image_url' => '',
        ];
        $userDetail['id']                = $detail->getId();
        $userDetail['name']              = $detail->getName();
        $userDetail['email']             = $detail->getEmail();
        $userDetail['profile_image_url'] = $this->SocialLogin->getBigAvatar($detail, $driver);

        switch ($this->driver) {
            case 'google':
            $userDetail['login_type'] = 1;
            break;
            case 'facebook':
            $userDetail['login_type'] = 2;
            break;
            case 'basic':
            $userDetail['login_type'] = 3;
            break;
        }
        return $userDetail;
    }

    /**
     * Logout
     *
     * @param
     * @return json
     */
    public function logout(Request $request)
    {
        $this->userSessionForget($request);
        return Response::json(['success' => true, 'message' => 'Successfuly Logout', 'url' => $this->redirectUrl]);
    }

    /**
     * Flush user session
     *
     * @param
     * @return json
     */
    public function userSessionForget($request)
    {
        $request->session()->forget('login_flag');
        $request->session()->forget('userid');
        $request->session()->forget('login_type');
    }

    /*Basic login - start*/

    public function checkUser (Request $request) 
    {
        try{
            $email_to = $request['data'];
            $userDetail = ['email' => $email_to];
            $orderDetails =[];
            $check_email_in_user_table = $this->userModel->getUser($userDetail); 
            $userCountFlag = count($check_email_in_user_table) === 0 || 
            (count($check_email_in_user_table) > 0 && $check_email_in_user_table[0]->login_type == 3) 
            ? 1 : 0;

            if($userCountFlag == 0){
                return Response::json(['success' => false, 'error_message' => __('messages.USER_EXIST.EMAILID_EXIST')]);
            }
            $oldUserCheck = (count($check_email_in_user_table) > 0) ? 1 : 0; //if user exists or not in user table
            $data = ['user_detail' => [], 'user_flag' => 2]; //user_flag : 1->create user, 2->update user; (DO NOT REMOVE)
            if($oldUserCheck === 0){
                $orderDetails = $this->getTotalOrders($userDetail, $request);

                if($orderDetails['total_order'] == 0){
                    return response()->json(['success' => false, 'error_message' => $orderDetails['error_message']]);
                }
                $data['user_detail'] = $orderDetails['user_details'];
                $data['user_flag'] = 1 ;
            }

            $response = $this->userModel->checkingUser($email_to);
            if($response === false){
                return Response::json(['success' => false, 'error_message' => __('messages.DEFAULT_ERROR_MESSAGE')]);
            }
            return Response::json(['success' => true, 'message' => __('messages.OTP.OTP_SEND'), 
                'data' => $data ]);
        }
        catch (Exception $ex) {
            if (env('APP_DEBUG')) {
                pr($ex->getMessage(), 1, 'Message');
                pr($ex->getFile(), 1, 'File');
                pr($ex->getLine(), 0, 'Line');
            }
            return Response::json(['success' => false, 'error_message' => ($ex->getCode() == 999) ? $ex->getMessage() : '']);
        }
    }

    public function checkOtp (Request $request) 
    {
        $otp = $request['otp'];
        $email = $request['email'];

        $check_otp = $this->userModel->checkingOtp($otp, $email);

        if(count($check_otp) == 0){
            return Response::json(['success' => false, 'error_message' => __('messages.OTP.ERROR_OTP')]);
        }
        return Response::json(['success' => true, 'message' => __('message.OTP.OTP_VERIFIED'), 'user_details' => $check_otp]);
    }

    public function changePassword (Request $request) 
    {
        try{
            $password = md5($request['data']['confirm_pw']);
            $email_id = $request['data']['user_email_id'];
            $user_detail = $request['data']['login_detail']['user_detail'];
            $user_flag = $request['data']['login_detail']['user_flag']; //user create:1 or update:2 (DO NOT REMOVE)
            $userDetail = ['email' => $email_id];
            $condition = array(
                'email' => $email_id,
                'login_type' => 3
            );
            $user_table_update = $this->userModel->basicLoginUserChecking($password,$email_id,$user_detail, $user_flag);
            if($user_table_update !== false){
                $delet_register_user_from_otp_table = $this->userModel->deleteUser($email_id);
                if($delet_register_user_from_otp_table){
                    return response()->json(['success' => true, 
                        'message'=>($user_flag == 1) ?__('messages.REGISTER.SUCCESSFULL_REGISTER') : __('messages.REGISTER.UPDATE_PW')]);
                }
            }
        }
        catch (Exception $ex) {
            if (env('APP_DEBUG')) {
                pr($ex->getMessage(), 1, 'Message');
                pr($ex->getFile(), 1, 'File');
                pr($ex->getLine(), 0, 'Line');
            }
            return Response::json(['success' => false, 'error_message' => ($ex->getCode() == 999) ? $ex->getMessage() : '']);
        }
    }

    public function basicLogin (Request $request) 
    {
        try{
            $email = $request['data']['email'];
            $password = md5($request['data']['pw']);
            $condition = array(
                'email' => $email,
                'password' => $password,
                'login_type' => 3
            );
            $check_user_exit_or_not = $this->userModel->getUser($condition);
            if(!empty($check_user_exit_or_not)){
                $user_id = $check_user_exit_or_not[0]->id;
                $condition = ['id' => $user_id];
                $values = ['last_loggedin_at' => CURR_DATE_TIME_EST];
                $update_last_login_status = $this->userModel->updateUser('', $condition, $values);
                if($update_last_login_status){
                    $request->session()->put('login_flag', 'logged_in');
                    $request->session()->put('userid', $check_user_exit_or_not[0]->email);
                    $request->session()->put('login_type', $check_user_exit_or_not[0]->login_type);
                    return response()->json(['success' => true, 'message'=>__('messages.LOGIN.SUCCESSFULL'), 'url'=>$this->dashboardUrl ]); 
                }
            }
            return response()->json(['success' => false, 'error_message'=>__('messages.ERROR_LOGIN.LOGIN_ERROR')]);
        }
        catch (Exception $ex) {
            if (env('APP_DEBUG')) {
                pr($ex->getMessage(), 1, 'Message');
                pr($ex->getFile(), 1, 'File');
                pr($ex->getLine(), 0, 'Line');
            }
            return Response::json(['success' => false, 'error_message' => ($ex->getCode() == 999) ? $ex->getMessage() : '']);
        }
    }

    /*Basic login - end*/

    /**
     * get user's total number of order
     *
     * @param $userDetail -> user detail in array, $request -> Request
     * @return array
     */

   private function getTotalOrders($userDetail, $request, $saveSession = false)
    {
        $startDate = date('m/d/Y', strtotime(THREE_MONTHS_BACK_DATE_TIME_EST));
        $endDate = date('m/d/Y', strtotime(CURR_DATE_TIME_EST)) ;
        $return = [];
        $request->request->add(
            [
                'start_date' => $startDate, 
                'end_date' => $endDate, 
                'search_fields' => ['email' => $userDetail['email']],
                'return_type' => "null" 
            ]);
        $response = json_decode($this->orderController->orderList($request), true);
        if(isset($response['success']) && $response['success'] === false){
            $return = ['success' => false, 'error_message' => '', 'total_order' => 0, 'error_code' => 999];
            if($response['data']['response_code'] == 333){
                $return['error_message'] = __('messages.LOGIN.NO_ORDER_FOUND');
            }
            else{
                $return['error_message'] = __('messages.LOGIN.TRY_AGAIN_LATER');
            }
            return $return;
        }
        $order_id = $response['data']['order_ids'][0];
        $first_name = $response['data']['order_details'][$order_id]['first_name'];
        $last_name = $response['data']['order_details'][$order_id]['last_name'];
        
        $return = [
            'success' => true, 
            'message' => 'Order fetched', 
            'total_order' => count($response['data']['order_ids']),
            'user_details' => ['first_name' => $first_name, 'last_name' => $last_name],
        ];
        return $return;
    }
}
