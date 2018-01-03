<?php
namespace App\Modules\Membership;

use App\Modules\CRMS\Limelight\Limelight;
use App\Modules\Membership\Membership;

final class Orders extends Membership
{
    private $Limelight;
	function __construct($apiInfo) {
		$this->Limelight = Limelight::instance($apiInfo);
	}

    /**
     *  @param $order_id
     *  @return object|string $response
     */
    public function get($order_id)
    {
        $response = $this->Limelight->getOrderDetail($order_id);
        return $this->response($response);
    }

    /**
     *  @param $order_id
     *  @param $action first_name|last_name|shipping_state etc
     *  @param $value new value
     */
    public function update($order_id, $action, $value)
    {
        $response = $this->Limelight->orderUpdate($order_id, $action, $value);
        return $this->response($response);
    }

    /**
     *  @param $start_date format:MM/DD/YYYY
     *  @param $end_date format:MM/DD/YYYY
     *  @param $campaign_id optional
     *  @param $product_id optional
     *  @return object|string $response
     */

    public function find($start_date, $end_date, $criteria = 'all', $campaign_id = 'all', $product_id = 'all')
    {
        $response = $this->Limelight->orderFind($start_date, $end_date, $campaign_id, $search_type = 'all', $return_type = 'order_view', $criteria, $start_time = '00:00:00', $end_time = "23:59:59", $product_id);
        return $this->response($response);
    }

    public function reProcess($order_id)
    {
        $response = $this->Limelight->orderReprocess($order_id);
        return $this->response($response);
    }

    public function void($order_id)
    {
        $response = $this->Limelight->orderVoid($order_id);
        return $this->response($response);
    }

    public function create($order_info = array())
    {
        $response = $this->Limelight->createNewOrder($order_info);
        return $this->response($response);
    }

}
