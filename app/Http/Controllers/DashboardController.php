<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use URL;
use Exception;
use App\User;

class DashboardController extends Controller
{
	protected $redirect_url, $dashboard_url;

	public function __construct(){
	}

	public function user(Request $request){
		try{
			$user_model = new User;
			$user_detail = $user_model->getUser(
				[
					'email' => $request->session()->get('userid'),
					'login_type' => $request->session()->get('login_type'),
				], 
				['name', 'profile_image', 'email'])
			;
			$user_detail = json_decode(json_encode($user_detail), 1);
			return Response::json(['success' => true, 'message' => 'success' , 'data' => $user_detail]);
		}
		catch(Exception $ex){
			return Response::json(['success' => false, 'error_message' => $ex->getMessage()]);
		}

	}
}
