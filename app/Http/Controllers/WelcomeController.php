<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class WelcomeController extends Controller {

	public function index()
	{
		return view('master');
	}
}
