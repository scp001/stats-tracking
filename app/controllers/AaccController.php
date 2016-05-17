<?php
	use Illuminate\Support\Collection;
	
	class AaccController extends Controller {
		protected $dept_id; // the ID of row in the department table that corresponds to the AA&CC department
		protected $dept_name;
		protected $dept_shortname;
		
		//This constructor function will apear in every department controller 
		function AaccController() {
			$department = Department::findByShortName("aacc");
			$this->dept_id = $department["id"];
			$this->dept_name = $department["name"];
			$this->dept_shortname = $department["shortname"];
		}
		
		function showCategories() {
			$user_id = Login::GetUser()->id;
			$session_btn = RequestGroup::CurrentUserHasSession($user_id);
			if (!$session_btn){
				$check = RequestGroup::StartUserSession($this->dept_id);
			}
			//var_dump($session_btn);die();
			//var_dump($check);die();
			$n = Input::get("id", NULL);
			$retro_date = Input::get("date", null);

			//confirms that the categoryID is appropriate and not added in the URL
			$numChildren = Category::getNumberOfChildren($n);
			if ($n != NULL && $numChildren == 0){
				Session::flash("danger", "Please use the buttons provided to select the category");
				return Redirect::to($this->dept_shortname . '/dashboard');
			}

			if (Login::GetUser()->permission["aacc"][PERMISSION] == ADMIN && $retro_date == ""){
				$retro_date = date("Y-m-d");
			}
			$pending = Requests::HasPendingRequests();
			//confirms the validatity to the date given
			if ($retro_date != "" && (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$retro_date) || date("Y-m-d", strtotime($retro_date)) != $retro_date
			 || !(date('Y-m-d') >= date("Y-m-d", strtotime($retro_date))))){
				Session::flash("danger", "please do not use the url to select a date. The current session has been cancelled to preserve data");
				return Redirect::to('api/cancel');
			}
			
			if ($pending && Session::get('date', null) != $retro_date){
				Session::flash("danger", "please do not use the url to select a date. The current session has been cancelled to preserve data");
				return Redirect::to('api/cancel');
			}
			Session::put('date', $retro_date);
			
			$allMediums = RequestMedium::SelectAll()->orderby("ordering")->get()->keyBy("id");
			//confirms that the Request MediumID is appropriate and not added in the URL
			$mediumids = $allMediums->keys();
			$med = Input::get("medium", isset($mediumids[0]) ? $mediumids[0] : NULL);//Session::get("requestmedium", isset($mediumids[0]) ? $mediumids[0] : NULL));
			//$med = Input::get("medium", Session::get("requestmedium", null));

			if ($med != NULL && !in_array($med, $mediumids)){
				Session::flash("danger", "Please select a valid request method");
				return Redirect::to($this->dept_shortname . '/dashboard');
			}
			else {
				Session::put("requestmedium", $med);
			}
			$categories = Category::getCategoryCounts($this->dept_id, $n, Helper::hasAdminPermissionForDept($this->dept_shortname)? $retro_date:null);
			$dropdown = DropDown::getDropdown();
			
			$backUrl = $n == NULL ? Config::get("app.url") . "/dashboard" : "?" . http_build_query(array(
						'id' => Category::find($n)->parent_id,
						'medium' => $med,
						'date' => $retro_date
					));;

			// Set breadcrumb trail and current category name
			$trail = Category::getAncestorTrail($n);
			$currentCatName = count($trail) == 0 ? "" : $trail[count($trail) - 1]["name"];

			// Assign each division a side (left/right)
			$divisionSides = array();
			$divisions = Category::getDivisionsForParent($n);
			for ($i = 0; $i < count($divisions); $i++) {
				$divisionSides[$divisions[$i]["division_id"]] = $i % 2 == 0 ? "left" : "right";
			}
			
			//echo "<pre>";
			//var_dump(array_values($categories));
			$viewOptions = array(
				"categories" => array_values($categories),
				"divisionSides" => $divisionSides,
				"backUrl" => $backUrl,
				"trail" => $trail,
				"parent_id" => $n,
				"currentCategoryName" => $currentCatName,
				"mediums" => $allMediums->toArray(),
				"dept_shortname" => $this->dept_shortname,
				"medium" => $med,
				"pageHeading" => $this->dept_name,
				//"session_btn" => $session_btn,
				"dropdown" => $dropdown,
				"pending" => $pending,
				"retro_date" => $retro_date
			); 
			
			if (count($categories) > 0 && Category::isLeafCategory(array_keys($categories))) {
				return View::make("departments.{$this->dept_shortname}.category-item")->with($viewOptions);
			}
			return View::make("departments.{$this->dept_shortname}.category")->with($viewOptions);
		}
		
		function AaccChartSetUp() {
			// if (!Helper::hasAdminPermissionForDept($this->dept_id)) {
			// 	return View::make("global.nopermission");
			// }

			$cats = Category::SelectByParentId(NULL, $this->dept_id);
			$cats = $cats->get()->toArray();
			$categories = array(0 => "All");
			foreach ($cats as $cat) {
				$categories[$cat["id"]] = $cat["name"];
			}
			$meds = RequestMedium::select()->orderby("ordering")->get();
			$start = date("Y") . "-" . date("m") . "-01";
			$end = date("Y") . "-" . date("m") . "-" . date("t");
			return View::make("departments.{$this->dept_shortname}.charts")->with(array(
				"categories" => $categories,
				"mediums" => $meds,
				"department" => $this->dept_id,
				"startdate" => $start,
				"enddate" => $end,
				"def_cat" => Input::get("cat_id", 0),
				"dept_shortname" => $this->dept_shortname,
				"pageHeading" => $this->dept_name
			));
		}
		


		function showEditPermissionsPage() {
			// if (!Helper::hasAdminPermissionForDept($this->dept_id)) {
			// 	return View::make("global.nopermission");
			// }

			// Get the users and their permission details
			$users = User::getUserPermissionDetails($this->dept_id);
			$users = $users->orderBy("user.lastname")->orderBy("user.firstname")->get()->toArray();
			return View::make("departments.{$this->dept_shortname}.editpermissions")->with(array(
				"userList" => $users,
				"dept_shortname" => $this->dept_shortname,
				"pageHeading" => $this->dept_name
			));
		}


		function handlePermissionPost() {
			// if (!Helper::hasAdminPermissionForDept($this->dept_id)) {
			// 	return View::make("global.nopermission");
			// }

			$users = User::getUserPermissionDetails($this->dept_id);

			/*
			 * Delete permission
			 */
			if (Input::has("permission_id")) {
				// Remove that permission if it exists and does not belong to logged in user
				$userPerm = $users->where("permission.id", Input::get("permission_id"));

				if (!$userPerm->count()) {
					Session::flash("danger", "You cannot remove a permission that doesn't exist, or belongs to another department");
				} else {
					$userPerm = $userPerm->first();
					// Check that the user isn't removing themself
					if ($userPerm->id == Login::getUser()->peopleID) {
						Session::flash("danger", "You cannot delete permissions for yourself");
					}
					// All is good, remove the permission
					else {
						$success = Permission::removePermission(Input::get("permission_id"));
						if ($success)
							Session::flash("success", "Successfully removed the permission for " . $userPerm->firstname . " " . $userPerm->lastname);
						else
							Session::flash("danger", "Unexpected error when trying to remove the permission. Please try again later.");
					}
				}
			} 
			/*
			 * Add new permission
			 */
			else if (Input::has("user-field") and Input::has("user-type") and Input::has("user-role")) {
				// Validate that the user type is exactly either utsc or utor, and that user role is exactly either ADMIN (constant) or REGULAR
				$type = strtolower(trim(Input::get("user-type")));
				$role = strtolower(trim(Input::get("user-role")));
				$username = strtolower(trim(Input::get("user-field")));
				if (!($type == "utsc" or $type == "utor") or !($role == ADMIN or $role == REGULAR)) {
					Session::flash("danger", "Invalid user type or permission type");
				} else {
					// Do intranet call to get peopleID, etc
					$profile = WebService::getProfile($username, $type);
					if (!$profile) {
						Session::flash("danger", "The entered user does not exist");
					} else if ($profile["peopleID"] == Login::getUser()->peopleID) {
						Session::flash("danger", "You cannot change your own permission");
					} else {
						User::addUserIfNotExist($profile["peopleID"], $type == "utor" ? $username : "", $type == "utsc" ? $username : "", $profile["givennames"], $profile["familyname"]);
						Permission::addPermissionOrUpdate($profile["peopleID"], $this->dept_id, $role);

						Session::flash("success", "Successfully updated permission for " . $profile["givennames"] . " " . $profile["familyname"]);
					}
				}
			} else {
				Session::flash("warning", "Invalid request");
			}
			return Redirect::back(); // redirect so that browser refresh does not re-submit form
		}

	}
?>