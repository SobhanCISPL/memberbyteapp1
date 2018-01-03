<?php

namespace App\Modules\CRMS\Konnektive;

use App\Modules\CRMS\Konnektive\Konnektive;

final class Report extends Konnektive
{
 
    public function reportsMidSummary($param)
    {
        
        $section='reports';
        $method='mid-summary';
        $this->fields= $param;
        $response     = $this->__post($section, $method);
        $response = json_decode($response);
        return $response;
        
    }
    public function reportsRetention($param)
    {
     
        $section='reports';
        $method='retention';
        $this->fields= $param;
        $response     = $this->__post($section, $method);
        $response = json_decode($response);
        return $response;
        
    }
    public function campaignQuery($param)
    {
        
        $section='campaign';
        $method='query';
        $this->fields= $param;
        $response     = $this->__post($section, $method);
        $response = json_decode($response);
        return $response;
        
    }
}