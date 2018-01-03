<?php
namespace App\Modules\CRMS\Konnektive;

class Konnektive
{
    private $endPoint = 'https://api.konnektive.com';
    private $userName;
    private $password;
    private $httpVerb;
    protected $fields;
    private $apiUrl;
    protected $rule;
    public $headerRequired = false;

    protected function __construct($userName, $password)
    {
        $this->userName = $userName;
        $this->password = $password;
    }
    /**
     *
     * @param array $apiInfo (must have endpoint, username, password)
     * @return \App\api_lib\LimelightApi
     */
    public static function instance($apiInfo)
    {
        $calledClassName = get_called_class();
        if (empty($apiInfo['username']) || empty($apiInfo['password'])) {
            return null;
        }
        static $inst = null;
        if ($inst === null) {
            $inst = new $calledClassName($apiInfo['username'], $apiInfo['password']);
        }
        return $inst;
    }

    protected function __post($section, $method)
    {
        $this->httpVerb           = 'POST';
        $this->apiUrl             = $this->endPoint . '/' . $section . '/' . $method . '/';
        $this->fields['loginId']  = $this->userName;
        $this->fields['password'] = $this->password;
        return $this->request();
    }

    protected function get($section, $method)
    {
        $this->httpVerb           = 'GET';
        $this->apiUrl             = $this->endPoint . '/' . $section . '/' . $method . '/';
        $this->fields['loginId']  = $this->userName;
        $this->fields['password'] = $this->password;

        return $this->request();
    }

    private function request()
    {
        try
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->httpVerb);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->fields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $content = curl_exec($ch);
            $header  = curl_getinfo($ch);
            $error   = curl_error($ch);
            curl_close($ch);
            if (!empty($error)) {
                throw new \Exception($error);
            }
            $response = ($this->headerRequired) ? ['content' => $content, 'header' => $header] : $content;
        } catch (\Exception $ex) {
            $response = $ex->getMessage();
        }
        return $response;
    }

}
