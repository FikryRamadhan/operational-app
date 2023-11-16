<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
	public function logout()
	{
		try {
			auth()->logout();
		} catch (\Exception $e) {}

		return redirect()->route('login');
	}
}
