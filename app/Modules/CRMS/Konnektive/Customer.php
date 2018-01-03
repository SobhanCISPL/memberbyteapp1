<?php
namespace App\Modules\CRMS\Konnektive;
use App\Modules\CRMS\Konnektive\Konnektive;

final class Customers extends Konnektive
{

    public function customerQuery($params)
    {
        $section='customer';
        $method='query';
        $this->fields = $params;
        $response = $this->__post($section,$method);
        $response = json_decode($response);
        return $response;
        
        
    }

    public function customerAddnote($params)
    {
        $section='customer';
        $method='addnote';
        $this->fields = $params;
        $response = $this->__post($section,$method);
        $response = json_decode($response);
        return $response;
        
        
    }

    public function customerUpdate($params)
    {
        $section='customer';
        $method='update';
        $this->fields = $params;
        $response = $this->__post($section,$method);
        $response = json_decode($response);
        return $response;
        
    }
    
    public function customerHistory($params)
    {
        $section='customer';
        $method='history';
        $this->fields = $params;
        $response = $this->__post($section,$method);
        $response = json_decode($response);
        return $response;
        
    }

    public function customerBlacklist($params)
    {  
        $section='customer';
        $method='blacklist';
        $this->fields = $params;
        $response = $this->__post($section,$method);
        $response = json_decode($response);
        return $response;
        
    }


    public function customerContracts($params)
    {  
        $section='customer';
        $method='contracts';
        $this->fields = $params;
        $response = $this->__post($section,$method);
        $response = json_decode($response);
        return $response;
        
    }
}
?>