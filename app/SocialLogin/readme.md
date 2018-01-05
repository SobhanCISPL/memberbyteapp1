## Socialite package install
	composer require laravel/socialite

## Add Client For laravel
	add client details in -> config/services.php for any driver like google/facbook/github/twitter/LinkedIn/Bitbucket

	driver-name => [
        'client_id' => ******,
        'client_secret' => *******,
        'redirect' => ******,
    ],

## SocialLogin class 
	Methods of this class is usefull for ajax / angular http call 