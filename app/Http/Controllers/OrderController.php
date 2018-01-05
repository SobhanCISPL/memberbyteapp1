<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Membership\Orders;
use App\Modules\Membership\Gateway;
use Illuminate\Http\Request;
use Exception;

class OrderController extends Controller
{
	const apiInfo = array(
		'endpoint' => 'https://demoaws.limelightcrm.com',
		'username' => 'CISPLteam',
		'password' => 'QxW9xxMuuZSyvb',
	);

    public function orderView($orderId = '')
    {
        try {
            $Orders   = new Orders(self::apiInfo);
            $response = $Orders->get($orderId);
            if (isset($response['data']) && !empty($response['data'])) {
                $_response = array(
                    'billing_city'             => $response['data']['billing_city'],
                    'billing_country'          => $response['data']['billing_country'],
                    'billing_first_name'       => $response['data']['billing_first_name'],
                    'billing_last_name'        => $response['data']['billing_last_name'],
                    'billing_postcode'         => $response['data']['billing_postcode'],
                    'billing_state'            => $response['data']['billing_state'],
                    'billing_street_address'   => $response['data']['billing_street_address'],
                    'billing_street_address2'  => $response['data']['billing_street_address2'],
                    'campaign_id'              => $response['data']['campaign_id'],
                    'email_address'            => $response['data']['email_address'],
                    'first_name'               => $response['data']['first_name'],
                    'last_name'                => $response['data']['last_name'],
                    'order_confirmed'          => $response['data']['order_confirmed'],
                    'customer_id'              => $response['data']['customer_id'],
                    'order_total'              => $response['data']['order_total'],
                    'shipping_city'            => $response['data']['shipping_city'],
                    'shipping_country'         => $response['data']['shipping_country'],
                    'shipping_date'            => $response['data']['shipping_date'],
                    'shipping_first_name'      => $response['data']['shipping_first_name'],
                    'shipping_last_name'       => $response['data']['shipping_last_name'],
                    'shipping_postcode'        => $response['data']['shipping_postcode'],
                    'shipping_state'           => $response['data']['shipping_state'],
                    'shipping_street_address'  => $response['data']['shipping_street_address'],
                    'shipping_street_address2' => $response['data']['shipping_street_address2'],
                    'upsell_product_quantity'  => $response['data']['upsell_product_quantity'],
                    'shippable'                => $response['data']['shippable'],
                    'products'                 => $response['data']['products'],
                    'tracking_number'              =>  $response['data']['tracking_number'],

                );
                $response = $_response;
            }
            //pr($response, 0);
            return json_encode($response);
        } catch (Exception $e) {
            if (env('APP_DEBUG')) {
                pr($e->getMessage(), 1, 'Message');
                pr($e->getFile(), 1, 'File');
                pr($e->getLine(), 0, 'Line');
            }
        }
        
    }
    public function orderList(Request $request)
    {
        try {
        	$searchQry = $this->__commaSeperatedString($request->get('search_fields'));
            $Orders   = new Orders(self::apiInfo);
            $response = $Orders->find($request->get('start_date'), $request->get('end_date'), $searchQry);

            if (isset($response['success']) && $response['success']) {
                $orderIds                       = explode(',', $response['data']['order_ids']);
                $_response['data']['order_ids'] = $orderIds;

                $allOrders = json_decode($response['data']['data'], true);
                //pr($allOrders, 0);

                $gateway_ids = array_column($allOrders, 'gateway_id');
                $gateway_ids = implode(',', $gateway_ids);
                $Gateway = new Gateway(self::apiInfo);
                $gateway_details = $Gateway->get($gateway_ids);
                
                if ($gateway_details['success']) {
                    if ($gateway_details['data']['total_gateways'] == 1) {
                        $_gateway[$gateway_details['data']['gateway_id']] = $gateway_details['data'];
                    } else {
                        $_gateway = json_decode($gateway_details['data']['data'], true);
                    }
                }
                //pr($_gateway, 0);
                foreach ($allOrders as $orderId => $orderDetails) {
                    
                    $_response['data']['order_details'][$orderId] = array(
                        'billing_city'             => $orderDetails['billing_city'],
                        'billing_country'          => $orderDetails['billing_country'],
                        'billing_first_name'       => $orderDetails['billing_first_name'],
                        'billing_last_name'        => $orderDetails['billing_last_name'],
                        'billing_postcode'         => $orderDetails['billing_postcode'],
                        'billing_state'            => $orderDetails['billing_state'],
                        'billing_street_address'   => $orderDetails['billing_street_address'],
                        'billing_street_address2'  => $orderDetails['billing_street_address2'],
                        'campaign_id'              => $orderDetails['campaign_id'],
                        'email_address'            => $orderDetails['email_address'],
                        'first_name'               => $orderDetails['first_name'],
                        'last_name'                => $orderDetails['last_name'],
                        'order_confirmed'          => $orderDetails['order_confirmed'],
                        'customer_id'              => $orderDetails['customer_id'],
                        'order_total'              => $orderDetails['order_total'],
                        'shipping_city'            => $orderDetails['shipping_city'],
                        'shipping_country'         => $orderDetails['shipping_country'],
                        'shipping_date'            => $orderDetails['shipping_date'],
                        'shipping_first_name'      => $orderDetails['shipping_first_name'],
                        'shipping_last_name'       => $orderDetails['shipping_last_name'],
                        'shipping_postcode'        => $orderDetails['shipping_postcode'],
                        'shipping_state'           => $orderDetails['shipping_state'],
                        'shipping_street_address'  => $orderDetails['shipping_street_address'],
                        'shipping_street_address2' => $orderDetails['shipping_street_address2'],
                        'upsell_product_quantity'  => $orderDetails['upsell_product_quantity'],
                        'shippable'                => $orderDetails['shippable'],
                        'gateway_id'               => $orderDetails['gateway_id'],
                        'currency'                 => $_gateway[$orderDetails['gateway_id']]['gateway_currency'],
                        'products'                 => $orderDetails['products'],
                        'tracking_number'              =>  $orderDetails['tracking_number'],
                    );
                }
                $response = $_response;
            }
            //pr($response, 0);
            return json_encode($response);
        } catch (Exception $e) {
            if (env('APP_DEBUG')) {
                pr($e->getMessage(), 1, 'Message');
                pr($e->getFile(), 1, 'File');
                pr($e->getLine(), 0, 'Line');
            }
        }

    }

    public function orderUpdate($order_id = '218737,218737', $action = 'first_name,last_name', $value = 'test7,test2', $sync_all = 0)
    {
    	$action = $this->__commaSeperatedString($request->get('action'));
    	$value = $this->__commaSeperatedString($request->get('value'));
    	
        $Orders   = new Orders(self::apiInfo);
        $response = $Orders->update($order_id, $action, $value);
        pr($response, 0);
    }

    private function __commaSeperatedString($input = []) {
    	$output = "";
		if (is_array($input) && !empty($input)) {
            foreach ($input as $key => $value) {
                $output = $key . '=' . $value . ',';
            }
            $output = rtrim($output, ',');
        }
        return $output;
    }
}