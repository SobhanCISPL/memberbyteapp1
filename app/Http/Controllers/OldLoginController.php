<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Google_Client;
use Google_Service_Plus;
use URL;
use Exception;
use App\User;

class OldLoginController extends Controller
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
                    $request->session()->put('userid', $userDetail['email']);
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
}
