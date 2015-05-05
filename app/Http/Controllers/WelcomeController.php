<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class WelcomeController extends Controller {

	public function index()
	{
        dd(\Session::all());
        dd(\Auth::user()->toArray());
		return view('welcome');
	}
}
