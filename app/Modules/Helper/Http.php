<?php

namespace App\Modules\Helper;

class Http
{

    private function __construct()
    {
        return;
    }

    public static function post($apiUrl, $params)
    {   
        $guzzleClient = new \GuzzleHttp\Client();
        try
        {
            $response = $guzzleClient->post($apiUrl, array('form_params' => $params));
            $response = $response->getBody()->getContents();
            
        } catch (\Exception $ex) {
            $response = $ex->getMessage();
            return $response;
        }
        parse_str($response, $_response);
        return $_response;
    }

}
