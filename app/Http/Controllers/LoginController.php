<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Google_Client;
use Google_Service_Plus;
use URL;
use Exception;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOTP;
use App\Http\Controllers\OrderController;
// use App\SocialLogin\Facebook;
// use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    protected $redirect_url, $dashboard_url, $google_client_id, $google_client_secret, $google_client, $plus;

    public function __construct(){
        $this->user_model = new User();

        $this->redirect_url = URL::to('/');
        $this->dashboard_url = URL::to('/') . '/app';

        //google client create
        $this->google_client_id = "18158992706-lj2bpjmc1s6jj0v6r7fma4ka3b3t7adt.apps.googleusercontent.com";
        $this->google_client_secret = "ET_frq0q1sme5pelfSNk_3Xq";
        $this->googleClientCreate();


        // $this->fb_client_id = "159875864623260";
        // $this->fb_client_secret = "98ac2a675db3e8d756063aa0162edc66";
    }

    /**
     * Create google client for user google login.
     *
     * @param  
     * @return true/false
     */
    private function googleClientCreate(){
        $this->google_client = new Google_Client();
        $this->google_client->setClientId($this->google_client_id);
        $this->google_client->setClientSecret($this->google_client_secret);
        $this->google_client->setRedirectUri($this->redirect_url);
        $this->google_client->setScopes('email');
        $this->plus = new Google_Service_Plus($this->google_client);
        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,),));
        $this->google_client->setHttpClient($guzzleClient);
        return true;
    }

    /**
     * Show login page / after login redirection to dashboard.
     *
     * @param  
     * @return view
     */

    public function index(Request $request){
        try{
            if(
                $request->session()->has('loginFlag') && 
                $request->session()->get('loginFlag') === 'logged_in'){
                return redirect($this->dashboard_url);
            }
            if ($request->has('code')) { // google login
                $userDetail = [];
                $access_token = $this->googleAuthenticateAccesstoken($request->input('code'));
                $userDetail = $this->googleSetAccessToken($access_token);

                if(empty($userDetail)){
                    throw new Exception('Something went wrong');
                }
                $user = $this->user_model->createUser($userDetail);
                if($user){
                    $request->session()->put('loginFlag', 'logged_in');
                    return redirect($this->dashboard_url);
                }
            }

            return view('index');
        }
        catch(Exception $ex){
            return Response::json(['success' => false, 'error_message' => $ex->getMessage()]);
        }
    }

    /**
     * Login method
     *
     * @param  
     * @return login auth url for google/facebook login
     */

    public function login(Request $request){

        try{
            $method = $request->input('type');
            // $social = new social;
            // $authUrl = $social->$method();
            $authUrl = $this->$method();
            return Response::json(['success' => true, 'authUrl' => $authUrl]);
        }
        catch (Exception $ex)
        {
            return Response::json(['success' => false, 'error_message' => $ex->getMessage()]);
        }
    }

    /**
     * Google login page url create
     *
     * @param 
     * @return 
     */

    private function facebook(){
        try{

            $fb_url = new Facebook();
            $url = $fb_url->redirect();
            return $url;
        }
        catch (Exception $ex)
        {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Google login page url create
     *
     * @param 
     * @return 
     */

    private function google(){
        try{
            return $this->google_client->createAuthUrl();
        }
        catch (Exception $ex)
        {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * 
     *
     * @param 
     * @return
     */
    private function googleAuthenticateAccesstoken($code = null)
    {
        try{
            $access_token_array = [];
            $this->google_client->authenticate($code);
            $access_token_array = $this->google_client->getAccessToken();
            return $access_token_array['access_token'];
        }
        catch(Exception $ex){
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * 
     *
     * @param 
     * @return
     */
    private function googleSetAccessToken($accesstoken = null)
    {
        try{
            $userDetail = [
                'id' => '',
                'name' => '',
                'email' => '',
                'domainName' => '',
                'userType' => '',
                'profile_image_url' => '',
                'login_type' => 1  //for google user 1 , for facebook user 2 , else 3
            ];
            $this->google_client->setAccessToken($accesstoken);
            $me = $this->plus->people->get('me');
            $userDetail['email']  = $me['emails'][0]['value'];
            $emailFragments = explode('@', $userDetail['email']);
            $userDetail['domainName'] = array_pop($emailFragments);
            $userDetail['id'] = $me['id'];
            $userDetail['name'] = $me['displayName'];
            $userDetail['profile_image_url'] = $me['image']['url'];
            return $userDetail;
        }
        catch(Exception $ex){
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Logout
     *
     * @param 
     * @return url
     */
    public function logout(Request $request){
        $request->session()->forget('loginFlag');
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
        }
    }

    public function checkOtp (Request $request) {
        $otp = $request['data'];

        $check_otp = $this->user_model->cehckingOtp($otp);

        if(count($check_otp) > 0){
            return response()->json(['success' => 'True', 'param' => 100, 'user_details'=>$check_otp]);
        }
        if(count($check_otp) == 0){
            return response()->json(['success' => 'False', 'param' => 404]);
        }
    }

    public function changePassword (Request $request) {
        $orderController   = new OrderController();
        $password = md5($request['data']['confirm_pw']);
        $email_id = $request['data']['user_email_id'];

        $todays_date = CURR_DATE_TIME_EST;

        $condition = array(
            'email' => $email_id,
            'login_type' => 3
        );
        $check_user_exit_or_not = $this->user_model->getUser($condition);


        if(count($check_user_exit_or_not) === 0){


            $start_date = date('m/d/Y', strtotime('-3 months'));
            $end_date = date('m/d/Y', strtotime(CURR_DATE_TIME_EST)) ;

             $request->request->add(['start_date' => $start_date, 'end_date' => $end_date, 'search_fields' => ['email'=>$email_id]]);

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

    public function basic_login (Request $request) {
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
            return response()->json(['success' => 'True', 'message'=>"Login Successfuly'", 'url'=>$this->dashboard_url ]);
        }else{
            return response()->json(['success' => 'False', 'error_message'=>"Username or Password is wrong."]);
        }
    }
}
