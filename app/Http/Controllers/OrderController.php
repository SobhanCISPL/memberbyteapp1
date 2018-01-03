<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Membership\Orders;
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
		$Orders   = new Orders(self::apiInfo);
		$response = $Orders->get($orderId);
		if (isset($response['data']) && !empty($response['data'])) {
			$productArray = [];
			foreach ($response['data']['products'] as $key => $productDetails) {
				$productArray[$productDetails['product_id']] = array(
					'name'                      => $productDetails['name'],
					'product_qty'               => $productDetails['product_qty'],
					'price'                     => $productDetails['price'],
					'is_recurring'              => $productDetails['is_recurring'],
					'recurring_date'            => $productDetails['recurring_date'],
					'next_subscription_product' => $productDetails['next_subscription_product'],
				);
			}
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
				'products'                 => $productArray,

			);
			$response = $_response;
		}
        //pr($response, 0);
		return json_encode($response);
	}
	public function orderList(Request $request)
	{
		$startDate = $request->get('start_date');
		$endDate = $request->get('end_date');
		$searchBy = $request->get('search_fields');
		$searchQry = '';
		if (is_array($searchBy) && !empty($searchBy)) {
			foreach ($searchBy as $key => $value) {
				$searchQry = $key . '=' . $value . ',';
			}
			$searchQry = rtrim($searchQry, ',');
		}
		$Orders   = new Orders(self::apiInfo);
		$response = $Orders->find($startDate, $endDate, $searchQry);
		if (isset($response['success']) && $response['success']) {
			$orderIds                       = explode(',', $response['data']['order_ids']);
			$_response['data']['order_ids'] = $orderIds;

			$allOrders = json_decode($response['data']['data'], true);
            //pr($allOrders, 1);
			foreach ($allOrders as $orderId => $orderDetails) {
				$productArray = [];
				foreach ($orderDetails['products'] as $key => $productDetails) {
					$productArray[$productDetails['product_id']] = array(
						'name'                      => $productDetails['name'],
						'product_qty'               => $productDetails['product_qty'],
						'price'                     => $productDetails['price'],
						'is_recurring'              => $productDetails['is_recurring'],
						'recurring_date'            => $productDetails['recurring_date'],
						'next_subscription_product' => $productDetails['next_subscription_product'],
					);
				}
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
					'products'                 => $productArray,
				);
			}
			$response = $_response;
		}
			// pr($response, 1);
		return json_encode($response);
	}
}
