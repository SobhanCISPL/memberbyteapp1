<?php 
/*
* Class for google, facebook, twitter, git - login methods
*
*/

namespace App\SocialLogin;

use Laravel\Socialite\Facades\Socialite;

final class SocialLogin {

	public function authUrl($driver){
    return Socialite::driver($driver)->redirect()->getTargetUrl();
  }

  public function getUser($driver){
    return Socialite::driver($driver)->user();
  }

  public function getBigAvatar($user, $driver)
  {
    return ($driver == "google") ? $user->getAvatar()."0" : $user->avatar_original;
  }
  
}

?>