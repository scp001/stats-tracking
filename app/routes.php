<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/



Route::get('login', array('as' => 'login', "uses" => 'LoginController@showLogin'));
Route::post('login', array('before' => 'csrf', "uses" => 'LoginController@postLogin'));//
Route::get('logout', array('as' => 'logout', 'uses' => 'LoginController@logOff'));


// Route group for all the routes that require the user to be logged in
Route::group(array("before" => "auth"), function () {
	Route::get('dashboard', array("before" => "multi_department", 'as' => 'dashboard', 'uses' => 'DashboardController@showDepartments'));

	Route::get('aacc/dashboard', array('as' => 'aacc.dashboard', "before" => "permission", 'uses' => 'AaccController@showCategories'));
	Route::get('aacc/dashboard/charts', array('uses' => 'AaccController@AaccChartSetUp', 'before' => 'adminonly'));
	Route::get('aacc/dashboard/edit-permissions', array('uses' => 'AaccController@showEditPermissionsPage', 'before' => 'adminonly'));
	Route::post('aacc/dashboard/edit-permissions', array('uses' => 'AaccController@handlePermissionPost', 'before' => 'csrf|adminonly'));


	/** API **/
	Route::put('api/addRequest', array("before" => "start", "uses" => "APIController@AddRequest"));
	Route::get('api/finish/{medium}/{retro_date}', array("uses" => "APIController@finishSession"))
	->where(array("dept_id" => "[0-9]+", "start" => "[0-9]{4}(-)[0-9]{2}(-)[0-9]{2}"));
	//Route::get('api/begin', array("as" => "begin", "before" => "finish", "uses" => "APIController@beginSession"));
	Route::get('api/cancel', array("uses" => "APIController@cancelSession"));

	//Route::get('api/getMonthlyColumnData/{dept_id}/{year}/{month}/{cat_id}/{mediums}', array('uses' => 'APIController@GetMonthlyColumnData'))
	//->where(array("dept_id" => "[0-9]+", "year" => "[0-9]{4}", "month" => "[0-9]+", "catid" => "[0-9]+"));
	//Route::get('api/getMonthlyPieData/{dept_id}/{year}/{momth}/{cat_id}/{mediums}', array('uses' => 'APIController@GetMonthlyPieData'))
	//->where(array("dept_id" => "[0-9]+", "year" => "[0-9]{4}", "month" => "[0-9]+", "catid" => "[0-9]+"));

	
	Route::get('api/getMonthlyColumnData/{dept_id}/{start}/{end}/{cat_id}/{mediums}', array('uses' => 'APIController@GetMonthlyColumnData'))
			->where(array("dept_id" => "[0-9]+", "start" => "[0-9]{4}(-)[0-9]{2}(-)[0-9]{2}", "end" => "[0-9]{4}(-)[0-9]{2}(-)[0-9]{2}", "catid" => "[0-9]+"));
	Route::get('api/getMonthlyPieData/{dept_id}/{start}/{end}/{cat_id}/{mediums}', array('uses' => 'APIController@GetMonthlyPieData'))
			->where(array("dept_id" => "[0-9]+", "start" => "[0-9]{4}(-)[0-9]{2}(-)[0-9]{2}", "end" => "[0-9]{4}(-)[0-9]{2}(-)[0-9]{2}", "catid" => "[0-9]+"));
			
	Route::get('api/getMonthlyTableData/{dept_id}/{start}/{end}/{cat_id}/{mediums}', array('uses' => 'APIController@GetMonthlyTableData'))
			->where(array("dept_id" => "[0-9]+", "start" => "[0-9]{4}(-)[0-9]{2}(-)[0-9]{2}", "end" => "[0-9]{4}(-)[0-9]{2}(-)[0-9]{2}", "catid" => "[0-9]+"));
	Route::get('api/exportExcel', array("uses" => "APIController@GetExcel"));
});

Route::get('/', array('uses' => function() {
	return Redirect::to('dashboard');
}));
?>
