<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Exception;
use Session;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class ApiController extends Controller
{
	protected $curl;

	public function __construct(){
		$this->curl = new Client(['verify'=> false]); //GuzzleHttp\Client
		$this->apiUrl = API_URL_201;
	}

	/**
     * Fetch site data from 201clicks
     *
     * @param
     * @return json
    */
	public function fetchData()
	{
		try{
			$subDomain = $this->getSubDomain();
			$result = $this->curl->post($this->apiUrl . 'test-memberbyte/',[
				'form_params' => [
					'sub_domain' => $subDomain
				]
			]);
			$data = $result->getBody();
			// if($data['success']){
			// }
			return Response::json(['success' => true, 'message' => '']);
		}
		catch(Exception $ex){
			if (env('APP_DEBUG')) {
				pr($ex->getMessage(), 1, 'Message');
				pr($ex->getFile(), 1, 'File');
				pr($ex->getLine(), 0, 'Line');
			}
			return Response::json(['success' => false, 'error_message' => $ex->getMessage(), 'error_code' => $ex->getCode()]);
		}
	}

	/**
     * Get Sub-domain
     *
     * @param
     * @return string
    */
	private function getSubDomain()
	{
		$domain = $_SERVER["SERVER_NAME"];
		$fragments = explode('.', $domain); 
		$subDomain = '';
		if (count($fragments) == 1) 
		{
			return $subDomain;
		} 
		$domain = preg_replace('/(^https?:\/\/)/i', '', $domain); 
		$domain = preg_replace('/:.+$/i', '', $domain); 
		preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs );

		$domainFragments = explode('.', $regs['domain']);

		$subDomain=array_diff($fragments,$domainFragments);
		if(empty($subDomain)){
			return '';
		}
		return $subDomain[0];
	}

	/**
     * Static session create
     *
     * @param
     * @return json
    */
	public function sessionStor(){
		$settings = [
			'generalSettings' => [
				'job_name' => 'Alpha Male Dynamics',
				'site' => 'https://getalphamale.com/',
				'sub_domain' => 'amdcsr',
				'type' => 'trial',
				'support_email' => 'support@alphamaledynamics.com',
				'support_phone' => '1 877 201 7535',
				'auto_approve_after' => ''
			],
			'rmaSetting' => [
				'return_days_allowed' => '30',
				'return_address' => "PO Box 52079 
				Phoenix, AZ 85072",
				'is_rma_terms' => false,
			],
			'trialExtensionSetting' => [
				'extend_trial_applied' => true,
				'extend_trial_day_limit' => '10',
				'extend_trial_auto' => true,
				'is_trial_terms' => false,
			],
			'refundSetting' => [
				'claim_refund_applied' => true,
				'claim_refund_auto' => false,
				'is_refund_terms' => false,
				'refund_terms' => '',
			],
			'themeSettings' => [
				'theme_bkcolor' => 'rgb(61, 133, 198)',
				'theme_text_color' => 'rgb(255, 255, 255)',
				'site_logo' => "https://d3l0o0cz6re48r.cloudfront.net/1499798153-easy-cancel.png",
				'product_logo' => "https://d3l0o0cz6re48r.cloudfront.net/1499787408-easy-cancel.png",
			]
		];
		Session::put('settings', $settings);

		$sessionArray = Session::get('settings', []);
		// Session::flush();
		return Response::json(['success' => true, 'data' => $sessionArray, 'message' => 'Session data fetched']);
	}

	/**
     * Get order options from 201clicks data / static session data
     *
     * @param
     * @return json
    */
	public function orderOptions()
	{
		try{
			$sessionData = Session::get('settings', []);

			$settings = $rmaSetting = $trialExtensionSetting = $refundSetting = [];
			array_walk($sessionData, function (&$val, $key) use (&$settings)
			{
				if(!empty($val)){
					if($key == 'trialExtensionSetting'){
						$settings[$key] = [
							'option_name' => 'Trial Extension',
							'enable' => $val['extend_trial_applied'],
							'auto_approve' => $val['extend_trial_auto'],
						];
					}
					if($key == 'refundSetting'){
						$settings[$key] = [
							'option_name' => 'Refund',
							'enable' => $val['claim_refund_applied'],
							'auto_approve' => $val['claim_refund_auto'],
						];
					}
				}
			});	
			return Response::json(['success' => true, 'message' => '', 'settings' => $settings]);
		}
		catch(Exception $ex){
			return Response::json(['success' => false, 'error_message' => $ex->getMessage(), 'error_code' => $ex->getCode()]);
		}
	}

}