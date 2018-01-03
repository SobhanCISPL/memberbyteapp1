<?php

namespace App\Modules\CRMS\Konnektive;
use App\Modules\CRMS\Konnektive\Konnektive;

final class Transaction extends Konnektive
{

    public function transactionsQuery($params)
    {
        
        $section      = 'transactions';
        $method       = 'query';
        $this->fields = $params;
        $response     = $this->__post($section, $method);
        $response = json_decode($response);
        return $response;
    }

    public function cbdataList($params)
    {
        $section      = 'cbdata';
        $method       = 'list';
        $this->fields = $params;
        $response     = $this->__post($section, $method);
        $response = json_decode($response);
        return $response;
    }

    public function cbdataQuery($params)
    {
        
        $section      = 'cbdata';
        $method       = 'query';
        $this->fields = $params;
        $response     = $this->__post($section, $method);
        $response = json_decode($response);
        return $response;
    }

    public function transactionsUpdate($params)
    {
       
        $section      = 'transactions';
        $method       = 'update';
        $this->fields = $params;
        $response     = $this->__post($section, $method);
        $response = json_decode($response);
        return $response;
    }

    public function transactionsRefund($params)
    {
       
        $section      = 'transactions';
        $method       = 'refund';
        $this->fields = $params;
        $response     = $this->__post($section, $method);
        $response = json_decode($response);
        return $response;
    }

}
