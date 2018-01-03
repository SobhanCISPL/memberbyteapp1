<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use URL;
use Exception;

class DashboardController extends Controller
{
    protected $redirect_url, $dashboard_url;

    public function __construct(){
        // $this->user_model = new User();
        // $this->redirect_url = URL::to('/');
        // $this->dashboard_url = URL::to('/') . '/app';
    }
}
