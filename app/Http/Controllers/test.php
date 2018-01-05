<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Membership\Orders;
use App\Modules\CRMS\Konnektive\Order;


class test extends Controller
{

    public function index($crm='')
    {
    	if ($crm == 'kon') {
    		return $this->konnektive();
    	}
    	//$users = DB::select('select * from users');
    	//print_r($users);
    	$apiInfo = array(
			'endpoint' => 'https://demoaws.limelightcrm.com',
			'username' => 'CISPLteam',
			'password' => 'QxW9xxMuuZSyvb'
		);
		// echo "Create Order ::";
		$Orders = new Orders($apiInfo);
		
        try {
            // $res = $Orders->create(array('firstName' => 'abc', 'product_qty' => '2', 'productId' => 3));
            // echo "<pre>"; print_r($res); echo "</pre>";
            echo "Get Customer Orders ::";
            $res = $Orders->get('216624');
            echo "<pre>"; print_r($res); echo "</pre>";
        } catch (\Exception $e) {
            echo $e->getMessage();
            echo "<br>".$e->getFile();
            echo "<br>".$e->getLine();

            // Helper::pr($ex->getMessage(), 1, 'Message');
            // Helper::pr($ex->getFile(), 1, 'File');
            // Helper::pr($ex->getLine(), 0, 'Line');
        }
		
		
		// echo "Get Order details ::";
		// $res = $Orders->get('218720');
		// echo "<pre>"; print_r($res); echo "</pre>";

    }

    public function konnektive()
    {
	 	$api_info = ['username' => '201devAPI', 'password' => 'bNeq2qyk@123'];
        $params   = [
            'startDate'      => '12/21/2017',
            'endDate'        => '12/21/2017',
            'resultsPerPage' => '200',
        ];
        $customer = Order::instance($api_info);
        $response = $customer->orderQuery($params);  
        
		echo "<pre>"; print_r($response); echo "</pre>";
    }
    
}
