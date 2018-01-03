<?php 
namespace App\SocialLogin;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

final class Facebook {
	public function redirect(){
        $authUrl = Socialite::driver('facebook')->redirect()->getTargetUrl();

        // return response()->json(['success' => true, 'authUrl' => $authUrl]);
        return $authUrl;
    }
    /**
     * Return a callback method from fresponse()->json(acebook api.
     *
     * @return callback URL from facebook
     */
    public function callback(){
       $user = Socialite::driver('facebook')->user();
       echo "<pre>";
       print_r($user);
    } 
}

?>