<?php
	class APIController extends Controller{
		
		function AddRequest(){
			// session_start();
			$session_id = RequestGroup::CurrentUserHasSession(Login::getUser()->id); //isset($_SESSION["session_id"]) ? $_SESSION["session_id"]["session_id"] : NULL;
			$cat = Input::get('category');
			$med = Input::get('medium');
			$comments = Input::get('comments', NULL);
			$retro_date = Input::get('retro_date', NULL);
			
			if (trim($comments) == '')
				$comments = NULL;
			
			// Checks that the request medium exists before inserting
			$mediumids = RequestMedium::SelectAll()->get()->keyBy("id")->keys();
			if ($med != NULL && !in_array($med, $mediumids)) {
				return Response::make("Please select a legitimate request medium", 403);
			}

			// Check that the category exists, is a leaf category, and the user has permission to use
			$category = Category::find($cat);
			if (!Category::isLeafCategory($cat) or !Helper::hasRegularPermissionForDept($category->department_id))
				return Response::make("You do not have permission to do this request, or the category is not valid.", 403);
			
			if (!$session_id){
				return Response::make("you must start a session before submitting", 403);
			}

			Requests::InsertRequest($cat, $med, Login::getUser()->id, $comments, $session_id, $retro_date);
			return Response::make("Success", 200);
		}
		
		function finishSession($medium, $retro_date){
			$user_id = Login::GetUser()->id;
			RequestGroup::EndUserSession($user_id, $medium, $retro_date);
			$_SESSION["session_id"] = array("session_id" => NULL);
			return Redirect::to("dashboard");
		}
		
		/*function beginSession(){
			$user_id = Login::GetUser()->id;
			$session_id = RequestGroup::StartUserSession($user_id);
			session_start();
			$_SESSION["session_id"] = array("session_id" => $session_id);
			Session::flash("success", "New Session started");
			return Redirect::to('dashboard');
		}*/
		
		function cancelSession(){
			$user_id = Login::GetUser()->id;
			RequestGroup::CancelUserSession($user_id);
			return Redirect::to("dashboard");
		}
		
		function GetMonthlyColumnData($dept_id, $start, $end, $cat_id, $mediums) {
			$mediums = explode(",", $mediums);
			
			//Check cat_id is valid and exists
			if ($cat_id != 0 && Category::getNumberOfChildren($cat_id) == 0){
				return Response::make("Please select a legitimate category", 403);
			}
			
			//Check that logged in user has permission to view the categories for this department
			if (!Helper::hasAdminPermissionForDept($dept_id)){
				return Response::make("You do not have access to that department's charts", 403);
			}
	
			//collects all categories with parent_id=$cat_id
			$categories = Category::SelectByParentId($cat_id == 0 ? NULL: $cat_id, $dept_id)->get();
			$stats = array();

			$days = floor((strtotime($end) - strtotime($start))/(60*60*24));
			if ($days < 0){
				return Response::make("Make sure your start date is before your end date", 403);
			}
			$days++;
			$dates = APIController::getDatesFromRange($start, $end);
			//$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			
			//fills stats array with new arrays in the format
			//array("name" => category name, "counts" => array())
			//where counts array has as many elements as days in the month
			foreach ($categories as $cat){
				$stat = array();
				$stat["name"] = $cat->name;
				$stat["data"] = array_fill(0, $days, 0);
				array_push($stats, $stat);
			}
			/** for num students
			 * 
			 */
			$students = array();
			$students["name"] = "number of unique students";
			$students["data"] = array_fill(0, $days, 0);
			$name="";
			if ($cat_id == 0){
				/**
				 * this works with 2 levels of categories
				 */
				//$requests = Requests::SelectTimeAddedAll($dept_id)->get();
				//$requests = json_decode(json_encode($requests), true);
				$name = "all categories";
				//$requests = $requests->toArray();
			}else{
				$name = Category::select("name")->where("id" , "=", "$cat_id")->get();
				$name = $name[0]["name"];
				/**
				 * this works with 2 levels of categories
				 */
				//$requests = Requests::SelectTimeAdded($cat_id, $dept_id)->get();
				//$requests = $requests->toArray();
			}
			/**
			 * this works with more than 2 levels of categories
			 */
			//$time = "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "%";
			$requests = Requests::SelectJoinData($cat_id, $dept_id, $mediums, $start, $end)->get()->toArray();
			/** for num students
			 * */
			$groups = RequestGroup::NumberofStudents($start, $end, $dept_id, $mediums)->get()->toArray();
			
			//$requests = $requests->whereIn("request.requestmedium_id", $mediums);
			//increments numbers in counts array based on number of requests
			$amount = 0;
			foreach ($requests as $req){	
				foreach ($stats as &$stat){
					if ($stat["name"]== $req["name"]){
						$date = floor((strtotime($req["time_added"]) - strtotime($start))/(60*60*24));
						$stat["data"][$date] += 1;
						$amount +=1;
						break;
					}
				}
			}
			/**
			 * for num students
			 */
			$total = 0;
			foreach ($groups as $group){
				$date = floor((strtotime($group["time"]) - strtotime($start))/(60*60*24));
				$students["data"][$date] = (int) $group["count"];
				$total += $group["count"];
			}
			return Response::make(array(json_encode($stats), $name, $dates, $amount, $total, $students), 200);
			
		}
		
		/**
		 * returns an array of strings that depicting the dates within the 
		 * timeframe $start to $end in the form of "Jan-01"
		 * @param $start
		 * @param $end
		 */
		function getDatesFromRange($start, $end){

			$dates = array();
			
			$cur = $start;
			while($cur <= $end){
				$dates[] = date('M-d', strtotime($cur));
				$cur = date('Y-m-d', strtotime($cur . '+1 days'));
			}
			return $dates;
		}
		
		function GetMonthlyPieData($dept_id, $start, $end, $cat_id, $mediums){
			$mediums = explode(",", $mediums);
			// Check cat_id is valid and exists
			if ($cat_id != 0 && Category::getNumberOfChildren($cat_id) == 0){
				return Response::make("Please select a legitimate category", 403);
			}
			
			//Check that logged in user has permission to view the categories for this department
			if (!Helper::hasAdminPermissionForDept($dept_id)){
				return Response::make("You do not have access to that department's charts", 403);
			}

			//collects all categories with parent_id=$cat_id
			$categories = Category::SelectByParentId($cat_id == 0 ? NULL : $cat_id, $dept_id)->get();
			$stats = array();
			//fills the stats array with arrays in this
			//format: array("name of category", 0)
			foreach ($categories as $cat){
				$stat = array($cat->name, 0);
				array_push($stats, $stat);
			}
			
			//$time = "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "%";
			
			//collects the sum of each individual category in 
			//the request table
			/**
			 * works for just 2 levels of categories
			 */
			
			if ($cat_id == 0){
				//$requests = Requests::SelectTimeAddedAll($dept_id, $time, 1)->get();
				//$requests = json_decode(json_encode($requests), true);
				$name = "all categories";
			}else{
				//$requests = Requests::SelectTimeAdded($cat_id,$dept_id, $time, 1)->get();
				//$requests = $requests->toArray();
				$name = Category::select("name")->where("id" , "=", "$cat_id")->get();
				$name = $name[0]["name"];
			}
			/**
			 * works for more than two levels of categories
			 */
			$requests = Requests::SelectJoinData($cat_id, $dept_id, $mediums, $start, $end, 1)->get()->toArray();

			//updates the stats array with the 
			//value from the datatbase
			$amount = 0;
			foreach ($requests as $req){
				foreach ($stats as &$stat){
					if ($stat[0]== $req["name"]){
						$stat[1] = (float) $req["percentage"];
						break;	
					}
				}
				// $amount += $req["count"];
			}

			//turns database values into percentages
			/*if ($amount != 0){
				foreach ($stats as &$stat){
					$stat[1] = $stat[1]*100/$amount;
				}
			}*/
			return Response::make(array(json_encode($stats), $start, $end, $name), 200);
		}
		
		function getMonthlyTableData($dept_id, $start, $end, $cat_id, $mediums){
			
			$mediums = explode(",", $mediums);
			$comments = Requests::SelectJoinData($cat_id, $dept_id, $mediums, $start, $end, 2)->orderBy("request.time_added", "desc");
			//$count = count($comments->groupby("group_id")->get()->toArray());
			$comments = $comments->get()->toArray();
			foreach ($comments as &$comment) {
				$comment["comments"] = HTML::entities($comment["comments"]); // escape html characters before sending to view
			}

			session_start();
			$_SESSION["comments"] = array("start" => $start, "end" => $end, "data" => $comments); //$comments;
			return Response::json($comments);
		}
		
		function GetExcel(){
			session_start();
			$comments = isset($_SESSION["comments"]) ? $_SESSION["comments"]["data"] : NULL;
			$start = isset($_SESSION["comments"]) ? $_SESSION["comments"]["start"] : date("Y-m-d");
			$end = isset($_SESSION["comments"]) ? $_SESSION["comments"]["end"] : date("Y-m-d");

			if ($comments == NULL){
				Session::flash("warning", "There is no data to export");
				return Redirect::back();
			}
			foreach ($comments as &$comment) {
				$comment["comments"] = html_entity_decode($comment["comments"], ENT_QUOTES, "UTF-8");
			}
			$filename = "stats_{$start}_{$end}";
			Excel::create($filename, function($excel) use ($comments){
				$excel->sheet('comments', function($sheet) use ($comments){
					$sheet->fromArray($comments);
				});
			})->export('xls');
			return Response::make('Success', 200);
		}
		
	}
?>