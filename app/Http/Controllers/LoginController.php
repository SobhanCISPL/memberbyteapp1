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
    protected $redirect_url, $dashboard_url, $google, $facebook, $driver;

    public function __construct()
    {

        $this->user_model    = new User();
        $this->redirect_url  = URL::to('/');
        $this->dashboard_url = URL::to('/') . '/app';
        $this->SocialLogin = new SocialLogin();

    }

    /**
     * Show login page / after login redirection to dashboard.
     *
     * @param
     * @return view
     */

    public function index(Request $request)
    {
        $userDetail = [];
        try {
            if (
                $request->session()->has('loginFlag') &&
                $request->session()->get('loginFlag') === 'logged_in'
            ) {
                $user  = json_decode( json_encode( $this->user_model->getUser(
                    ['email' => $request->session()->get('userid')],
                    ['login_type']
                )), 1);

                if($user[0]['login_type'] === $request->session()->get('login_type')){
                    return redirect($this->dashboard_url);
                }
            }
            if ($request->has('code')) {
                $this->driver = $request->session()->get('loginDriver');
                $detail = $this->SocialLogin->getUser($this->driver);
                $userDetail = $this->filterUserDetail($detail, $this->driver);

                if (empty($userDetail)) {
                    throw new Exception('Something went wrong');
                }
                $user = $this->user_model->createLogin($userDetail);
                if ($user) {
                    $request->session()->flush();
                    $request->session()->regenerate();
                    $request->session()->put('loginFlag', 'logged_in');
                    $request->session()->put('userid', $userDetail['email']);
                    $request->session()->put('login_type', $userDetail['login_type']);
                    return redirect($this->dashboard_url);
                }
            }
            return view('index');
        } catch (Exception $ex) {
            $data = ['success' => false, 'error_message' => $ex->getMessage()];
            return view('index', ['data' => $data]);
            return Response::json(['success' => false, 'error_message' => $ex->getMessage(), 'redirect_url' => $this->redirect_url]);
        }
    }

    /**
     * Login method
     *
     * @param
     * @return login auth url for google/facebook login
     */

    public function login(Request $request)
    {
        try {
            $this->driver = $request->input('type');
            $request->session()->put('loginDriver', $this->driver);
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
     * @return []
     */

    public function filterUserDetail($detail, $driver){
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
        $request->session()->flush();
        $request->session()->regenerate();
        // $request->session()->forget('loginFlag');
        return Response::json(['success' => true, 'message' => 'Successfuly Logout', 'url' => $this->redirect_url]);
    }

    public function checkUser (Request $request) {

        $email_to = $request['data'];
        $fetch_ueser_deatils = $this->user_model->checkingUser($email_to);

        if($fetch_ueser_deatils == 1){
            return response()->json(['success' => 'True', 'message' => 'OTP send to the user successfully', 'param' => 100]);
        }elseif($fetch_ueser_deatils == 2){
            return response()->json(['success' => 'True', 'message' => 'User exit into User_otp table', 'param' => 200]);
        }elseif($fetch_ueser_deatils == 3){
            return response()->json(['success' => 'True', 'message' => 'New OTP send to the user successfully', 'param' => 300]);
        }elseif($fetch_ueser_deatils == 'Exist') {
            return response()->json(['success' => 'False', 'error_message' => 'Username is already exist.', 'param' => 400]);
        }
    }

    public function checkOtp (Request $request) {
        $otp = $request['data'];

        $check_otp = $this->user_model->cehckingOtp($otp);

        if(count($check_otp) == 0){
            return response()->json(['success' => 'False', 'param' => 404]);
        }elseif(count($check_otp) > 0){
            return response()->json(['success' => 'True', 'param' => 100, 'user_details'=>$check_otp]);
        }
        
    }

    public function changePassword (Request $request) {
        $orderController   = new OrderController();
        $password = md5($request['data']['confirm_pw']);
        $email_id = $request['data']['user_email_id'];

        $condition = array(
            'email' => $email_id,
            'login_type' => 3
        );
        $check_user_exit_or_not = $this->user_model->getUser($condition);


        if(count($check_user_exit_or_not) === 0){

            $start_date = date('m/d/Y', strtotime('-3 months'));
            $end_date = date('m/d/Y', strtotime(CURR_DATE_TIME_EST)) ;

             $request->request->add(['start_date' => $start_date, 'end_date' => $end_date, 'search_fields' => ['email'=>$email_id],'return_type' => "null" ]);

             

            $response = json_decode($orderController->orderList($request));
            // print_r($response);
            // die();

            if(isset($response->data->response_code) && $response->data->response_code == 333){
                $delet_not_found_user = $this->user_model->deleteUser($email_id);
                if($delet_not_found_user){
                    return response()->json(['success' => "False", 'param' => 333, 'error_message'=>"No orders found. So you don't have permission for login."]);
                }  

            }elseif(count($response->data->order_ids) > 0){
                $order_id = $response->data->order_ids[0];
                $first_name = $response->data->order_details->$order_id->first_name;
                $last_name = $response->data->order_details->$order_id->last_name;

                $check_user_for_normal_login = $this->user_model->basicLoginUserChecking($password,$email_id,$first_name,$last_name);

                if($check_user_for_normal_login == 1){

                    $delet_register_user_from_otp_table = $this->user_model->deleteUser($email_id);
                    if($delet_register_user_from_otp_table){
                        return response()->json(['success' => "true", 'param' => 200, 'message'=>"Register successfully. Please login now."]);
                    }
                }
            }
        }

        if(count($check_user_exit_or_not) > 0){
            $check_user_for_normal_login = $this->user_model->basicLoginUserChecking($password,$email_id);

            if($check_user_for_normal_login == 2){

                $delet_register_user_from_otp_table = $this->user_model->deleteUser($email_id);
                if($delet_register_user_from_otp_table){
                    return response()->json(['success' => "true", 'param' => 100, 'message'=>"Password updated successfully. Please login now."]);
                }
                
            }
        }
    }

    public function basicLogin (Request $request) {
        $email = $request['data']['email'];
        $password = md5($request['data']['pw']);

        $condition = array(
            'email' => $email,
            'password' => $password,
            'login_type' => 3
        );

        $check_user_exit_or_not = $this->user_model->getUser($condition);

        if(!empty($check_user_exit_or_not)){

            $request->session()->put('loginFlag', 'logged_in');
            $request->session()->put('userid', $check_user_exit_or_not[0]->email);
            $request->session()->put('login_type', $check_user_exit_or_not[0]->login_type);

            return response()->json(['success' => true, 'message'=>"Login Successfuly'", 'url'=>$this->dashboard_url ]);
        }else{
            return response()->json(['success' => false, 'error_message'=>"Username or Password is wrong.", 'param'=>404 ]);
        }
    }
}
