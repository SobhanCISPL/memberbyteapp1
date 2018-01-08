<?php
if (!defined('CURR_DATE_TIME_EST'))
	define('CURR_DATE_TIME_EST', date('Y-m-d H:i:s'));

if (!defined('THREE_MONTHS_BACK_DATE_TIME_EST')){
	$date = date('Y-m-d H:i:s', strtotime('-3 months'));
	define('THREE_MONTHS_BACK_DATE_TIME_EST', $date);
}

if (!defined('API_URL_201')){
	// if (App::isLocal())
	// 	{
	$api_url = "https://201clicks.localhost.com/api/";
		// }
	define('API_URL_201', $api_url);
}