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
			$userModel = new User;
			$userDetail = $userModel->updateUser('', $condition, $values);
			if(!$userDetail){
				return Response::json(['success' => false, 'error_message' => __('messages.USER.PROFILE_UPDATE_FAILED')]);
			}
			$request->session()->put('userid', $values['email']);
			$data = $userModel->getUser(['email' => $values['email']], ['name', 'profile_image', 'email']);
			return Response::json(['success' => true, 'message' => 'Profile updated.', 'data' => $data]);
		}
		catch(Exception $ex){
			errorShow($ex);
			return Response::json([
				'success' => false, 
				'error_message' => ($ex->getCode() == 999) ? $ex->getMessage() : __('messages.DEFAULT_ERROR_MESSAGE')
			]);
		}

	}
}
