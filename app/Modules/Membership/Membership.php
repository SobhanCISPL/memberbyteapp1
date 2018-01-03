<?php
namespace App\Modules\Membership;

use App\Modules\CRMS\Limelight\Limelight;

class Membership
{
	/**
	 *  Return response with error message
	 *
	 */
    protected function response($response)
    {
        //echo "<pre>"; print_r($response); echo "</pre>"; die();
        try {
            $responseArray["data"] = $response;
            if ($response["response_code"] == 100) {
                $responseArray["success"] = true;
                $responseArray["message"] = "Success from Limelight";
            } else {
                //TODO: make different error message
                $responseArray["success"] = false;
                switch ($response["response_code"]) {
                    case '200':
                        $responseArray["message"] = "Invalid Login Credential";
                        break;
                    case '300':
                        $responseArray["message"] = "Update failed due to third party rejection";
                        break;
                    case '301':
                        $responseArray["message"] = "Error updating affiliate data";
                        break;
                    case '320':
                        $responseArray["message"] = "Invalid Product Id";
                        break;
                    case '321':
                        $responseArray["message"] = "Existing Product Category Id Not Found";
                        break;
                    case '350':
                        $responseArray["message"] = "Invalid order Id supplied";
                        break;

                       
                    default:
                        $responseArray["message"] = $response;
                }
            }
        } catch (\Exception $e) {
            $responseArray['success'] = false;
            $responseArray["message"] = $e->getMessage();
        }
        return $responseArray;
    }
}

?>