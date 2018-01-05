<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use URL;
use Exception;
use App\User;

class ProfileController extends Controller
{
	protected $redirect_url, $dashboard_url;

	public function __construct(){
	}

	public function edit(Request $request){
		try{
			$condition =  ['email' => $request->input('old_email')];
			$values =  $request->input('submite_details');
			$user_model = new User;
			$user_detail = $user_model->updateUser('', $condition, $values);
			$request->session()->put('userid', $values['email']);
			$data = $user_model->getUser(['email' => $values['email']], ['name', 'profile_image', 'email']);
			return Response::json(['success' => true, 'message' => 'Profile updated.', 'data' => $data]);
		}
		catch(Exception $ex){
			return Response::json(['success' => false, 'error_message' => $ex->getMessage()]);
		}

	}
}
