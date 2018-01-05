<?php
namespace App\Modules\Membership;

use App\Modules\CRMS\Limelight\Limelight;

class Membership
{
    protected $Limelight, $limelight_responses_json;
    public function __construct($apiInfo) {
        $this->Limelight = Limelight::instance($apiInfo);

        $this->limelight_responses_json = file_get_contents(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'CRMS' . DIRECTORY_SEPARATOR . 'Limelight' . DIRECTORY_SEPARATOR . 'limelight_response.json'
        );
    }
 /**
  *  Return response with error message
  *
  */
    protected function response($response)
    {
        $response_list = json_decode($this->limelight_responses_json, true);
        try {
            $responseArray["data"] = $response;
            if (isset($response["response_code"])) { //Response From Limelight
                if ($response["response_code"] == 100) {
                    $responseArray["success"] = true;
                    $responseArray["message"] = "Success from Limelight";
                } else {
                    $responseArray["success"] = false;
                    $responseArray["message"] = $response_list[$response["response_code"]];
                }            
            } else {    //Exception arises before API Call
                $responseArray['success'] = false;
                $responseArray["message"] = $response;
            }
            
        } catch (\Exception $e) {
            $responseArray['success'] = false;
            $responseArray["message"] = $e->getMessage();
        }
        return $responseArray;
    }
}

?>