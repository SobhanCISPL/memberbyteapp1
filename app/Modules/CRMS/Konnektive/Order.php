<?php

namespace App\Modules\CRMS\Konnektive;

use App\Modules\CRMS\Konnektive\Konnektive;

final class Order extends Konnektive
{

	public function orderQuery($params){
		
		$this->section = 'order';
		$this->method = 'query';
		$this->fields = $params;
		$response = $this->__post($this->section,$this->method);
		$response = json_decode($response);
		return $response;
		
		
	}

	public function leadsImport($params){
		

		$this->section = 'leads';
		$this->method = 'import';
		$this->fields = $params;
		$response = $this->__post($section,$method);
		$response = json_decode($response);
		return $response;
	}

	public function orderPreauth($params){
		
		$this->section = 'order';
		$this->method = 'preauth';
		$this->fields = $params;
		$response = $this->__post($section,$method);
		$response = json_decode($response);
		return $response;
	} 

	public function orderImport($params){
		
		$this->section = 'order';
		$this->method = 'import';
		$this->fields = $params;
		$response = $this->__post($section,$method);
		$response = json_decode($response);
		return $response;
	}

	public function upsaleImport($params){
		
		$this->section = 'upsale';
		$this->method = 'import';
		$this->fields = $params;
		$response = $this->__post($section,$method);
		$response = json_decode($response);
		return $response;
	}

	public function orderConfirm($params){
		
		$this->section = 'order';
		$this->method = 'confirm';
		$this->fields = $params;
		$response = $this->__post($section,$method);
		$response = json_decode($response);
		return $response;
	}

	public function orderRefund($params){
		
		$this->section = 'order';
		$this->method = 'refund';
		$this->fields = $params;
		$response = $this->__post($section,$method);
		$response = json_decode($response);
		return $response;
	}

	public function orderCancel($params){
		
		$this->section = 'order';
		$this->method = 'cancel';
		$this->fields = $params;
		$response = $this->__post($section,$method);
		$response = json_decode($response);
		return $response;
	}

	public function orderQa($params){
		
		$this->section = 'order';
		$this->method = 'qa';
		$this->fields = $params;
		$response = $this->__post($section,$method);
		$response = json_decode($response);
		return $response;
	}

	public function fulfillmentUpdate($params){
		
		$this->section = 'fulfillment';
		$this->method = 'update';
		$this->fields = $params;
		$response = $this->__post($section,$method);
		$response = json_decode($response);
		return $response;
	}

	public function orderRerun($params){
		
		$this->section = 'order';
		$this->method = 'rerun';
		$this->fields = $params;
		$response = $this->__post($section,$method);
		$response = json_decode($response);
		return $response;
	}


}
