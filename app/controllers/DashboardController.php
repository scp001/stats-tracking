<?php
	class DashboardController extends Controller {
		function showDepartments() {
			//$user_id = Login::GetUser()->id;
			//$check = RequestGroup::CurrentUserHasSession($user_id);
		//	if (!$check){
			//	return Redirect::to("api/begin");
			//}
			$departments = Login::getUser()->permission;
			$count = count($departments);
			//immediatly links you to correct dashboard if you only have permission for one department
			if ($count == 1) {
				$departments = array_keys($departments);
				$shortname = $departments[0];
				return Redirect::to($shortname . '/dashboard');
			} else {
				return View::make("dashboard.departments")->with(array(
					"departments" => $departments
				));
			}
		}
	}
?>