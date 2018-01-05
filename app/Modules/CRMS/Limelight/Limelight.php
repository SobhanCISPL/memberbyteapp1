<?php

namespace App\Modules\CRMS\Limelight;

use App\Modules\Helper\Http;

class Limelight
{
    private $endPoint;
    private $userName;
    private $passWord;
    private $http_verb;
    private $fields;
    private $apiUrl;
    public $header_required = false;
    private static $inst    = null;

    private function __construct($endPoint, $userName, $passWord)
    {
        $this->endPoint = $endPoint;
        $this->userName = $userName;
        $this->passWord = $passWord;
    }

    /**
     * @param array $apiInfo (must have endpoint, username, password)
     * @return \App\Modules\CRMS\Limelight
     */
    public static function instance($api_info)
    {
        if (empty($api_info['endpoint']) || empty($api_info['username']) || empty($api_info['password'])) {
            throw new \Exception('API credential could not be blank.', 9999);
        }
        if (self::$inst === null) {
            $calledClassName = get_called_class();
            self::$inst      = new $calledClassName(
                $api_info['endpoint'], $api_info['username'], $api_info['password']
            );
        }
        return self::$inst;
    }

    /**
     * @param  $order_id [, $force_gateway][, $preserve_force_gateway]
     * @return $response
     */
    public function forceBillOrder($order_id, $force_gateway = '', $preserve_force_gateway = 0)
    {
        try {
            $response = "";
            if (!empty($order_id)) {
                $this->fields = [
                    'order_id' => $order_id,
                ];
                if (!empty($force_gateway)) {
                    $this->fields['forceGatewayId']         = $force_gateway;
                    $this->fields['preserve_force_gateway'] = $preserve_force_gateway;
                }
                $response = $this->__post('order_force_bill');
            }
            return $response;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
        
    }

    public function orderUpdate($order_id, $action, $value, $sync_all=0)
    {
        try {
            $response = "";
            if (!empty($order_id) && !empty($action) && !empty($value)) {
                $this->fields = [
                    'order_ids' => $order_id,
                    'sync_all'  => $sync_all,
                    'actions'   => $action,
                    'values'    => $value,
                ];
                $response = $this->__post('order_update');
            }
            return $response;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
        
    }

    /**
     *  @param $start_date format:MM/DD/YYYY
     *  @param $end_date format:MM/DD/YYYY
     *  @param $campaign_id optional
     *  @return object|string $response
     */
    public function orderFindUpdated($start_date, $end_date, $campaign_id = 'all', $group_keys = [], $start_time = '00:00:00', $end_time = "23:59:59")
    {
        try {
           $response = "";
            if (!empty($start_date) && !empty($end_date)) {
                $this->fields = [
                    'campaign_id' => $campaign_id,
                    'start_date'  => $start_date,
                    'end_date'    => $end_date,
                    'start_time'  => $start_time,
                    'end_time'    => $end_time,
                ];
                if (!empty($group_keys) && is_array($group_keys)) {
                    $this->fields['group_keys'] = implode(',', $group_keys);
                }
                $response = $this->__post('order_find_updated');
            }
            return $response; 
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
    }

    /**
     *    @param $start_date, $end_date [, $campaign_id]
     *    @return object|string $response
     */
    public function orderFind($start_date, $end_date, $campaign_id = 'all', $search_type = 'all', $return_type = 'order_view', $criteria = 'all', $start_time = '00:00:00', $end_time = "23:59:59", $product_id = 'all')
    {
        try {
            $response = "";
            if (!empty($start_date) && !empty($end_date)) {
                $this->fields = [
                    'campaign_id' => $campaign_id,
                    'start_date'  => $start_date,
                    'end_date'    => $end_date,
                    'start_time'  => $start_time,
                    'end_time'    => $end_time,
                    'search_type' => $search_type,
                    'criteria'    => $criteria,
                    'return_type' => $return_type,
                ];
                if (!empty($product_id)) {
                    $this->fields['product_ids'] = $product_id;
                }
                $response = $this->__post('order_find');
            }
            return $response;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
        
    }

    /**
     *    @param $order_id
     *    @return object|string $response
     */
    public function orderReprocess($order_id)
    {
        try {
            $response = "";
            if (!empty($order_id)) {
                $this->fields = [
                    'order_id' => $order_id,
                ];
                $response = $this->__post('order_reprocess');
            }
            return $response;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
    }

    /**
     *  @param $order_id
     *  @param $status optional start|stop|reset
     *  @return object|string $response
     */

    public function orderUpdateRecurring($order_id, $status = 'stop')
    {
        try {
            $response = "";
            if (!empty($order_id)) {
                $this->fields = [
                    'order_id' => $order_id,
                    'status'   => $status,
                ];
                $response = $this->__post('order_update_recurring');
            }
            return $response;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
        
    }

    /**
     *  @param $order_id
     *  @param $amount
     *  @param $keep_recurring optional 0|1
     *  @return object|string $response
     */

    public function refundOrder($order_id, $amount, $keep_recurring = 1)
    {
        try {
            $response = "";
            if (!empty($order_id) && !empty($amount)) {
                $this->fields = [
                    'order_id'       => $order_id,
                    'amount'         => $amount,
                    'keep_recurring' => $keep_recurring,
                ];
                $response = $this->__post('order_refund');
            }
            return $response;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
    }

    /**
     *    @param $prospect_id
     *    @return object|string $response
     */

    public function getProspectDetail($prospect_id)
    {
        try {
            $response = "";
            if (!empty($prospect_id)) {
                $this->fields = [
                    'prospect_id' => $prospect_id,
                ];
            }
            $response = $this->__post('prospect_view');
            return $response;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
        
    }

    /**
     *    @param $order_id
     *    @return object|string $response
     */
    public function getOrderDetail($order_id)
    {
        try {
            $response = "";
            if (!empty($order_id)) {
                $this->fields = [
                    'order_id' => is_array($order_id) ? implode(',', $order_id) : $order_id,
                    //'return_format' => 'json',
                ];
            }
            $response = $this->__post('order_view');
            return $response;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
    }

    public function getShippingDetails($campaign_id = 'all', $search_type = 'any', $return_type = 'shipping_method_view')
    {
        try {
            $this->fields = [
                'campaign_id' => $campaign_id,
                'search_type' => $search_type,
                'return_type' => $return_type,
            ];
            $response = $this->__post('shipping_method_find');
            return $response;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
        
    }

    public function getShippingDetailByID($shipping_id)
    {
        try {
            $response = "";
            if (!empty($shipping_id)) {
                $this->fields = [
                    'shipping_id' => $shipping_id,
                ];
            }
            $response = $this->__post('shipping_method_view');
            return $response;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
    }

    public function getGatewayDetailByID($gateway_id)
    {
        try {
            $response = "";
            if (!empty($gateway_id)) {
                $this->fields = [
                    'gateway_id' => $gateway_id,
                    //'return_format' => 'json',
                ];
            }
            $response = $this->__post('gateway_view');
            return $response;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
        
    }

    public function createNewOrder($order_info = [])
    {
        $response = "";

        try {
            $this->fields = [
                'firstName'             => $order_info['firstName'],
                'lastName'              => $order_info['lastName'],
                'shippingAddress1'      => $order_info['shippingAddress1'],
                'shippingCity'          => $order_info['shippingCity'],
                'shippingState'         => $order_info['shippingState'],
                'shippingZip'           => $order_info['shippingZip'],
                'shippingCountry'       => $order_info['shippingCountry'],
                'phone'                 => $order_info['phone'],
                'email'                 => $order_info['email'],
                'creditCardType'        => $order_info['creditCardType'],
                'creditCardNumber'      => $order_info['creditCardNumber'],
                'expirationDate'        => $order_info['expirationDate'],
                'CVV'                   => $order_info['CVV'],
                'tranType'              => $order_info['tranType'],
                'ipAddress'             => $order_info['ipAddress'],
                'productId'             => $order_info['productId'],
                'campaignId'            => $order_info['campaignId'],
                'shippingId'            => $order_info['shippingId'],
                'billingSameAsShipping' => $order_info['billingSameAsShipping'],
            ];
        

            if (isset($order_info['shippingAddress2'])) {
                $this->fields["shippingAddress2"] = $order_info['shippingAddress2'];
            }
            if (isset($order_info['AFID'])) {
                $this->fields["AFID"] = $order_info['AFID'];
            }
            if (isset($order_info['SID'])) {
                $this->fields["SID"] = $order_info['SID'];
            }
            if (isset($order_info['AFFID'])) {
                $this->fields["AFFID"] = $order_info['AFFID'];
            }
            if (isset($order_info['C1'])) {
                $this->fields["C1"] = $order_info['C1'];
            }
            if (isset($order_info['C2'])) {
                $this->fields["C2"] = $order_info['C2'];
            }
            if (isset($order_info['C3'])) {
                $this->fields["C3"] = $order_info['C3'];
            }
            if (isset($order_info['AID'])) {
                $this->fields["AID"] = $order_info['AID'];
            }
            if (isset($order_info['OPT'])) {
                $this->fields["OPT"] = $order_info['OPT'];
            }
            if (isset($order_info['click_id'])) {
                $this->fields["click_id"] = $order_info['click_id'];
            }
            if (isset($order_info['dynamic_product_price'])) {
                $dynamic_product                  = 'dynamic_product_price_' . $order_info['productId'];
                $this->fields["$dynamic_product"] = $order_info['dynamic_product_price'];
            }
            if (isset($order_info['product_qty'])) {
                $product_qty                  = 'product_qty_' . $order_info['productId'];
                $this->fields["$product_qty"] = $order_info['product_qty'];
            }
            if (isset($order_info['click_id'])) {
                $this->fields["click_id"] = $order_info['click_id'];
            }
            if (isset($order_info['notes'])) {
                $this->fields["notes"] = $order_info['notes'];
            }
            if (isset($order_info['forceGatewayId'])) {
                $this->fields["forceGatewayId"] = $order_info['forceGatewayId'];
            }
            if (isset($order_info['upsellCount']) && !empty($order_info['upsellCount'])) {
                $this->fields["upsellCount"] = $order_info['upsellCount'];
            } else {
                $this->fields["upsellCount"]      = 0;
                $this->fields["upsellProductIds"] = $order_info['upsellProductIds'];
            }
            if (isset($order_info['preserve_force_gateway'])) {
                $this->fields["preserve_force_gateway"] = $order_info['preserve_force_gateway'];
            }
            if (isset($order_info['createdBy'])) {
                $this->fields["createdBy"] = $order_info['createdBy'];
            }
            if (isset($order_info['master_order_id'])) {
                $this->fields["master_order_id"] = $order_info['master_order_id'];
            }
            if (isset($order_info['cascade_enabled'])) {
                $this->fields["cascade_enabled"] = $order_info['cascade_enabled'];
            }
            if (isset($order_info['cascade_override'])) {
                $this->fields["cascade_override"] = $order_info['cascade_override'];
            }
            if (isset($order_info['promoCode'])) {
                $this->fields["promoCode"] = $order_info['promoCode'];
            }
            if (isset($order_info['sessionId'])) {
                $this->fields["sessionId"] = $order_info['sessionId'];
            }

            switch ($order_info['creditCardType']) {
                case 'checking':
                case 'eft_germany':
                    $this->fields["checkAccountNumber"] = $order_info['checkAccountNumber'];
                    $this->fields["checkRoutingNumber"] = $order_info['checkRoutingNumber'];
                    break;
                case 'sepa':
                    $this->fields["sepa_iban"] = $order_info['sepa_iban'];
                    $this->fields["sepa_bic"]  = $order_info['sepa_bic'];
                    break;
                case 'eurodebit':
                    $this->fields["eurodebit_acct_num"]  = $order_info['eurodebit_acct_num'];
                    $this->fields["eurodebit_route_num"] = $order_info['eurodebit_route_num'];
                    break;
                case 'Paypal':
                case 'Amazon':
                    $this->fields["alt_pay_payer_id"] = $order_info['alt_pay_payer_id'];
                case 'IcePay':
                    $this->fields["alt_pay_token"] = $order_info['alt_pay_token'];
                    break;

            }

            if (strtolower($order_info['billingSameAsShipping']) == 'no') {
                $billingArray = array(
                    'billingAddress1' => $order_info['billingAddress1'],
                    'billingCity'     => $order_info['billingCity'],
                    'billingState'    => $order_info['billingState'],
                    'billingZip'      => $order_info['billingZip'],
                    'billingCountry'  => $order_info['billingCountry'],
                );
                if (isset($order_info['billingFirstName'])) {
                    $billingArray["billingFirstName"] = $order_info['billingFirstName'];
                }
                if (isset($order_info['billingLastName'])) {
                    $billingArray["billingLastName"] = $order_info['billingLastName'];
                }
                if (isset($order_info['billingAddress2'])) {
                    $billingArray["billingAddress2"] = $order_info['billingAddress2'];
                }
                $this->fields = array_merge($this->fields, $billingArray);
            }
            $response = $this->__post('NewOrder');
            return $response;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function createNewOrderCardOnFile($order_info)
    {
        if (empty($order_info)) {
            throw new \Exception('Order fields are empty.', 999);
        } else {
            $this->fields = $order_info;
            $response     = $this->__post('NewOrderCardOnFile');
        }
        return $response;
    }

    public function orderVoid($order_id)
    {
        try {
            $response = "";
            if (!empty($order_id)) {
                $this->fields = [
                    'order_ids' => $order_id,
                ];
                $response = $this->__post('order_void');
            }
            return $response;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 9999);
        }
    }

    private function __post($method)
    {
        $this->endPoint           = preg_replace("/^(https?:\/\/)?/", "https://", $this->endPoint);
        $this->http_verb          = 'POST';
        $this->apiUrl             = rtrim($this->endPoint, '/') . (in_array($method, ['NewOrder', 'NewOrderCardOnFile', 'NewOrderWithProspect']) ? '/admin/transact.php' : '/admin/membership.php');
        $this->fields['method']   = $method;
        $this->fields['username'] = trim($this->userName);
        $this->fields['password'] = trim($this->passWord);
        return Http::post($this->apiUrl, $this->fields);
    }

}
