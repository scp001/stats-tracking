<?php

class LoginController extends Controller {

	/**
	 * Login page
	 * @return mixed
	 */
	public function showLogin()
	{
		if (Login::isLoggedIn())
			return Redirect::to("dashboard");
		return View::make('login.showLogin');
	}

	public function postLogin()
	{
		$utorId  = Input::get('username');
		$password = Input::get('password');

		$login = Login::Instance();
		$ret = $login->Authenticate($utorId, $password);

		// Failed credential
		if ($ret == false) {
			return Redirect::to('login')->withInput();
		}
		
		$departments = Login::getUser()->permission;
		$count = count($departments);

		// user has permission to exactly 1 department
		if ($count == 1) {
			$shortname = array_keys($departments)[0];
			if ($departments[$shortname][PERMISSION] == ADMIN)
				return Redirect::to($shortname . '/dashboard/charts'); 
			else if ($departments[$shortname][PERMISSION] == REGULAR)
				return Redirect::to($shortname . '/dashboard');
		} 

		// user has permission for multiple (or zero) departments
		return View::make("dashboard.departments")->with(array(
					"departments" => $departments
				));
	}

	public function logOff()
	{
		$login = Login::Instance();
		$login->logOff();

		Session::flash('success', 'You have successfully logged out.');
		return Redirect::to('login');
	}
}
