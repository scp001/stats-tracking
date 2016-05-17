<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (!Login::isLoggedIn()) {
		if (Request::ajax())
			return Response::make('Unauthorized. You are not logged in, or your session has timed out.', 401);
		Session::flash("danger", "Please log in.");
		return Redirect::to("login");
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
/*
|-----------------------------------------------------------------------
|   Permission Filters
|-----------------------------------------------------------------------
|
|
| The Permission Filters  use the permission model to determine if a user
| can access a certain route. If we can't it redirects them back to where they were.
| 
*/

Route::filter('permission',function($route){
	$permissions = Login::GetUser()->permission;
	
	$department = explode("/", $route->getPath());
	//var_dump($permissions);
	//die();
	if (!array_key_exists($department[0], $permissions)){
		Session::flash("danger", "You do not have access to that department");
		return Redirect::to('dashboard');
	}
});

Route::filter('adminonly', function($route) {
	$permissions = Login::GetUser()->permission;
	$shortname = explode("/", $route->getPath())[0];

	if (!Helper::hasAdminPermissionForDept($shortname)) {
		return View::make("global.nopermission");
	}
});

Route::filter('chart', function($route){
	$department = explode("/", $route->getPath());
	$dept = $department[0];
	if (Login::getUserPermissionLevel($dept) != ADMIN){
		Session::flash("danger", "You do not have permission to see the charts");
		return Redirect::back();
	}
});

Route::filter('start', function (){
	$user_id = Login::GetUser()->id;
	$check = RequestGroup::CurrentUserHasSession($user_id);
	if (!$check){
		return Redirect::back();
	}
	
});

Route::filter('finish', function(){
	$user_id = Login::GetUser()->id;
	$check = RequestGroup::CurrentUserHasSession($user_id);
	if ($check){
		return Redirect::back();
	}
	
});

Route::filter('multi_department', function(){
	$user_id = Login::GetUser()->id;
	$check = RequestGroup::CurrentUserHasSession($user_id);
	if ($check){
		$dept = RequestGroup::getSessionDepartment($user_id)->get()->toArray();
		$dept = $dept[0]["shortname"];
		return Redirect::to("$dept/dashboard");
	}
});