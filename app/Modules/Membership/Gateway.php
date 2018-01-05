<?php
namespace App\Modules\Membership;

use App\Modules\CRMS\Limelight\Limelight;
use App\Modules\Membership\Membership;

final class Gateway extends Membership
{

	public function get($gateway_ids)
	{
		if (empty($gateway_ids)) {
            throw new \Exception("Gateway ID is missing", 1);
        }
        $response = $this->Limelight->getGatewayDetailByID($gateway_ids);
        return $this->response($response);
	}

}