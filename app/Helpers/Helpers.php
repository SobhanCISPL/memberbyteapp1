<?php

use Illuminate\Support\Facades\Mail;

if(!function_exists('pr')){
	function pr($param = array(), $continue = true, $label = NULL){
		if (!empty($label))
		{
			echo '<p>-- ' . $label . ' --</p>';
		}

		echo '<pre>';
		print_r($param);
		echo '</pre><br />';

		if (!$continue)
		{
			die('-- code execution discontinued --');
		}
	}
}

if(!function_exists('toSql')){
	function toSql($queryBuilder = null)
	{
		if (($queryBuilder instanceof Illuminate\Database\Query\Builder))
		{
			$sql = $queryBuilder->toSql();
			$aBindings = $queryBuilder->getBindings();
			if (!empty($aBindings))
			{
				foreach ($aBindings as $binding)
				{
					$value = is_numeric($binding) ? $binding : "'" . $binding . "'";
					$sql = preg_replace('/\?/', $value, $sql, 1);
				}
			}
			return $sql;
		}
		return false;
	}
}

if(!function_exists('objToArray')){
	function objToArray($obj)
	{
		if($obj){
			return json_decode(json_encode($obj), true);
		}
		return false;
	}
}

if(!function_exists('sendEmail')){
	function sendEmail ($email='',$data=[]) {
		try{

			Mail::send('email.otp', $data, function($message) use ($email) {
	            $message->from(env('MAIL_USERNAME'),'Code Clouds Developer');

	            $message->to($email)->subject('Login OTP!');           
	        });
	        return 1;

		}catch(\Exception $e){

		    return response()->json(['code'=>500,'message'=>'error']);
		}
	}
}

if(!function_exists('errorShow')){
	function errorShow($ex)
	{
		if (env('APP_DEBUG')) {
			pr($ex->getMessage(), 1, 'Message');
			pr($ex->getFile(), 1, 'File');
			pr($ex->getLine(), 0, 'Line');
		}
	}
}
