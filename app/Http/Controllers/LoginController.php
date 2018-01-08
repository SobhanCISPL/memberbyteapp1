<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Response;
use Session;
use URL;
use App\SocialLogin\SocialLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOTP;
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
            /* google / facebook - after redirection */
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
     * check user order / then send OTP & EMAIL/ After 24 hours sending new OTP & Email.
     *
     * @param
     * @return view / json
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
        $email_to = $request['data'];
        $userDetail = array(
            'email' => $email_to
        );

        $response = $this->getTotalOrders($userDetail, $request);

        if($response['total_order'] == 0){

            return response()->json(['success' => false, 'error_message' => $response['error_message'], 'param' => 404]);
            
        }elseif ($response['total_order'] > 0) {

            $fetch_ueser_deatils = $this->userModel->checkingUser($email_to);

            if($fetch_ueser_deatils == 1){
                return response()->json(['success' => 'True', 'message' => __('messages.OTP.SEND_OTP'), 'param' => 100]);
            }elseif($fetch_ueser_deatils == 2){
                return response()->json(['success' => 'True', 'message' => __('messages.USER_EXIST.USER_EXIST'), 'param' => 200]);
            }elseif($fetch_ueser_deatils == 3){
                return response()->json(['success' => 'True', 'message' => __('messages.OTP.NEW_OTP_SEND'), 'param' => 300]);
            }elseif($fetch_ueser_deatils == 'Exist') {
                return response()->json(['success' => 'False', 'error_message' => __('messages.USER_EXIST.EMAILID_EXIST'), 'param' => 400]);
            }
        }
    }
    /**
     * check OTP varification.
     *
     * @param
     * @return view / json
     */
    public function checkOtp (Request $request) 
    {
        $otp = $request['data'];

        $check_otp = $this->userModel->cehckingOtp($otp);

        if(count($check_otp) == 0){
            return response()->json(['success' => 'False', 'param' => 404]);
        }elseif(count($check_otp) > 0){
            return response()->json(['success' => 'True', 'param' => 100, 'user_details'=>$check_otp]);
        }
    }

    /**
     * change password . If user not exist then insert & is exist then just update password.
     *
     * @param
     * @return view / json
     */
    public function changePassword (Request $request) 
    {
        $password = md5($request['data']['confirm_pw']);
        $email_id = $request['data']['user_email_id'];

        $userDetail = array(
            'email' => $email_id
        );

        $condition = array(
            'email' => $email_id,
            'login_type' => 3
        );
        $check_user_exit_or_not = $this->userModel->getUser($condition);


        if(count($check_user_exit_or_not) === 0){

            $response = $this->getTotalOrders($userDetail, $request);

            $order_id = $response['order_details']['order_ids'][0];
            $first_name = $response['order_details']['order_details'][$order_id]['first_name'];
            $last_name = $response['order_details']['order_details'][$order_id]['last_name'];

            $check_user_for_normal_login = $this->userModel->basicLoginUserChecking($password,$email_id,$first_name,$last_name);

            if($check_user_for_normal_login == 1){

                $delet_register_user_from_otp_table = $this->userModel->deleteUser($email_id);
                if($delet_register_user_from_otp_table){
                    return response()->json(['success' => "true", 'param' => 200, 'message'=>__('messages.REGISTER.SUCCESSFULL_REGISTER') ]);
                }
            }
        }

        if(count($check_user_exit_or_not) > 0){

            $check_user_for_normal_login = $this->userModel->basicLoginUserChecking($password,$email_id);

            if($check_user_for_normal_login == 2){

                $delet_register_user_from_otp_table = $this->userModel->deleteUser($email_id);
                if($delet_register_user_from_otp_table){
                    return response()->json(['success' => "true", 'param' => 100, 'message'=>__('messages.REGISTER.UPDATE_PW') ]);
                }
                
            }
        }
    }

    /**
     * Auithenticate user basic login with email & password.
     *
     * @param
     * @return view / json
     */
    public function basicLogin (Request $request) 
    {
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
            $condition = array(
                'id' => $user_id
            );

            $values = array (
                'last_loggedin_at' => CURR_DATE_TIME_EST
            ) ;

            $update_last_login_status = $this->userModel->updateUser('', $condition, $values);

            if($update_last_login_status){

                $request->session()->put('login_flag', 'logged_in');
                $request->session()->put('userid', $check_user_exit_or_not[0]->email);
                $request->session()->put('login_type', $check_user_exit_or_not[0]->login_type);

                return response()->json(['success' => true, 'message'=>__('messages.LOGIN.SUCCESSFULL'), 'url'=>$this->dashboardUrl ]); 
            }

            
        }else{
            return response()->json(['success' => false, 'error_message'=>__('messages.ERROR_LOGIN.LOGIN_ERROR'), 'param'=>404 ]);
        }
    }

    /*Basic login - end*/

    /**
     * get user's total number of order
     *
     * @param $userDetail -> user detail in array, $request -> Request
     * @return array
     */

    private function getTotalOrders($userDetail, $request)
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
        
        $return = [
            'success' => true, 
            'message' => 'Order fetched', 
            'total_order' => count($response['data']['order_ids']),
            'order_details' => $response['data']
        ];

        return $return;
    }
}
