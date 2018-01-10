<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Response;
use Exception;
use Session;

class ApiController extends Controller
{
	protected $curl, $apiMethods;

	public function __construct(){
		$this->curl = new Client(['verify'=> false]); //GuzzleHttp\Client
		$this->apiUrl = API_URL_201;
		$this->apiMethods = [
			'test_method' => '/test-memberbyte/',
			'get_data_by_subdomain' => '/test-memberbyte/',
			'auto_approve_false' => '',
		];
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
			if($subDomain != ''){
				$result = $this->curl->post($this->apiUrl . $this->apiMethods['test_method'],
					[
						'form_params' => [
							'sub_domain' => $subDomain
						]
					]
				);
				$data = json_decode($result->getBody(), 1);
				if($data['success'] === true){
					$this->sessionStor($data['data']);
					return Response::json(['success' => true, 'message' => __('messages.API_201CLICKS.SUCCESSFULL_FETCHING')]);
				}
			}
			return Response::json(['success' => false, 'error_message' => __('messages.DEFAULT_ERROR_MESSAGE')]);
		}
		catch(Exception $ex){
			if (env('APP_DEBUG')) {
				pr($ex->getMessage(), 1, 'Message');
				pr($ex->getFile(), 1, 'File');
				pr($ex->getLine(), 0, 'Line');
			}
			return Response::json(['success' => false, 'error_message' => $ex->getMessage()]);
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

		$subDomain = array_diff($fragments,$domainFragments);
		if(empty($subDomain)){
			return '';
		}
		return $subDomain[0];
	}

	/**
     * Session create
     *
     * @param
     * @return json
    */
	public function sessionStor($data = array()){

		$settings = [];
		$settingsKeys = ['memberbyteDetail', 'rmaSetting', 'trialExtensionSetting', 'refundSetting', 'themeSettings'];

		$memberbyteDetail = ['member_byte_id', 'member_byte_name', 'subdomain_slug', 'user_id', 'crm_type', 'site_url', 'site_category', 'offer_type', 'support_email', 'support_phone', 'auto_approve_after', 'created_at'];

		$rmaSetting = ['return_address', 'return_days_allowed', 'is_rma_terms', 'rma_terms'];

		$trialExtensionSetting = ['extend_trial_applied', 'extend_trial_auto', 'extend_trial_day_limit', 'is_trial_terms', 'trial_terms'];

		$refundSetting = ['claim_refund_applied', 'claim_refund_auto', 'is_refund_terms', 'refund_terms'];

		$themeSettings = ['offer_domain_dominant_color', 'offer_domain_dominant_color_text', 'site_logo', 'product_logo'];

		if(!empty($data)){
			array_walk($settingsKeys, function (&$settingsKey)
			{
				array_walk(${$settingsKey}, function (&$val) use (&$data, &$settings, &$settingsKey)
				{
					$settings[$settingsKey][$val] = $data[$val];
				});
			});
		}
		else{
			$settings = [
				'memberbyteDetail' => [
					'member_byte_id' => 1,
					'member_byte_name' => 'test 7',
					'subdomain_slug' => 's',
					'user_id' => 11,
					'crm_type' => 0,
					'site_url' => 'https://getalphamale.com/',
					'site_category' => 132,
					'offer_type' => 'trial',
					'sub_domain' => 'amdcsr',
					'support_email' => 'test4@codeclouds.com',
					'support_phone' => '2692626262',
					'auto_approve_after	' => 12,
					'created_at' => '2018-01-05 10:39:08',
				],
				'rmaSetting' => [
					'return_days_allowed' => 32,
					'return_address' => 'test',
					'is_rma_terms' => false,
					'rma_terms' => 'cscs',
				],
				'trialExtensionSetting' => [
					'extend_trial_applied' => true,
					'extend_trial_day_limit' => 15,
					'extend_trial_auto' => true,
					'is_trial_terms' => true,
					'trial_terms' => 'csdsc',
				],
				'refundSetting' => [
					'claim_refund_applied' => true,
					'claim_refund_auto' => false,
					'is_refund_terms' => true,
					'refund_terms' => 'cscdsvd',
				],
				'themeSettings' => [
					'offer_domain_dominant_color' => 'rgb(61, 133, 198)',
					'offer_domain_dominant_color_text' => 'rgb(255, 255, 255)',
					'site_logo' => "https://d3l0o0cz6re48r.cloudfront.net/1499798153-easy-cancel.png",
					'product_logo' => "https://d3l0o0cz6re48r.cloudfront.net/1499787408-easy-cancel.png",
				]
			];
		}
		Session::put('settings', $settings);

		$sessionArray = Session::get('settings', []);
		// Session::flush();
		return Response::json(['success' => true, 'data' => $sessionArray, 'message' => 'Session data fetched']);
	}

}