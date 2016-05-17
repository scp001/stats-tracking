<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Category extends Eloquent{
	/**
	 * The database table used by the model
	 *
	 * @var string
	 */
	 protected $table = 'category';
	 
	/**
	 * Returns a SQL object that containg all the categories 
	 * whose parent is is $n
	 * @param $n
	 * @param $dept_id
	 */
	 public static function selectByParentId($n, $dept_id=NULL) {
	 	if ($n == NULL) {
	 		$sub_cats = Category::whereNull("parent_id");
	 	} else {
	 		$sub_cats = Category::where("parent_id", "=", "$n");
	 	}

	 	if ($dept_id) {
	 		$sub_cats = $sub_cats->where("department_id", "=", "$dept_id");
	 	}

	 	$sub_cats = $sub_cats->orderBy("ordering")->select("id", "parent_id", "name");
	 	return $sub_cats;
	 }
	 
	 /**
	  * Returns the number of children the category with id 
	  * $n has. Used to determine if category $n is a 
	  * parent
	  * @param $n
	  */
	 public static function getNumberOfChildren($n) {
	 	return Category::where("parent_id", "=", "$n")->count();
	 }
	 /**
	  * Returns a SQL object that containg all the categories 
	  * whose parent is is $n. This function also returns the number of 
	  * requests for each category that have been submitted today
	  * @param $n
	  * @param $dept_id
	  * @param $today
	  */
	 public static function selectByParentIdWithCount($n, $dept_id=NULL, $today=NULL) {
	 	if ($n == NULL) {
	 		$sub_cats = Category::whereNull("parent_id");
	 	} else {
	 		$sub_cats = Category::where("parent_id", "=", "$n");
	 	}

	 	if ($dept_id) {
	 		$sub_cats = $sub_cats->where("department_id", "=", "$dept_id");
	 	}

	 	if ($today) {
	 		$sub_cats = $sub_cats->leftJoin("request", function ($join) {
	 			$join->on("category.id", "=", "request.category_id");
	 			$join->on(DB::raw("DATE(request.time_added)"), "=", DB::raw("CURDATE()"));
	 		});
	 	} else 
	 		$sub_cats = $sub_cats->leftJoin("request", "category.id", "=", "request.category_id");

	 	$sub_cats = $sub_cats->groupBy("category.id")->orderBy("ordering")
			->select("category.id", "parent_id", "category.name", DB::raw("count(request.id) as n"));
	 	
	 	return $sub_cats;
	 }

	/**
	 * If cat_id is an id, return true if the category with that id is a leaf category.
	 * If cat_id is an array of ids, return true if all the categories are a leaf category.
	 * @param  $cat_id
	 * @return boolean
	 */
	 public static function isLeafCategory($cat_id) {
	 	// cat_id can be the category id, or an array of category ids.

	 	// Get all leaf categories
	 	$leaves = Category::select("id")->whereRaw("id not in (select distinct parent_id from category where parent_id is not null)")
	 			->get()->keyBy("id")->toArray();

	 	if (is_array($cat_id)) {
	 		for ($i = 0; $i < count($cat_id); $i++)
	 			if (!array_key_exists($cat_id[$i], $leaves))
	 				return false;
	 		return true;
	 	}
	 	return array_key_exists($cat_id, $leaves);
	 }

	 /**
	  * Returns an ordered php array of objects that shows the hierarchy from the 
	  * top-level category to the category with the given id
	  * @param $id
	  */
	 public static function getAncestorTrail($id) {

	 	// Get all categories and have their id be the key of the array
	 	$categories = Category::all()->keyBy('id')->toArray();


	 	if (!array_key_exists($id, $categories))
	 		return NULL;

	 	$result = array();
	 	$cur = $categories[$id];
	 	while ($cur["parent_id"] != NULL) {
	 		$result[] = $cur;
	 		$cur = $categories[$cur["parent_id"]];
	 	}
	 	$result[] = $cur;
	 	return array_reverse($result);
	 }

	 /**
	  * determines the number of requests for each category. The number of requests for parent
	  * categories is the sum of all the child requests
	  * @param $dept_id
	  * @param $parent_id
	  * @return associative array
	  */
	 public static function getCategoryCounts($dept_id, $parent_id=NULL, $retro_date=null) {
	 	/*
			If category is a leaf category, then it's value is the # of requests for the current date.
			Else the value is the sum of all it's children categories.

			Do this using a recursive helper function
	 	*/
	 	
	 	$retro_date = $retro_date == date("Y-m-d")? null:$retro_date;
	 	$user_id = Login::GetUser()->id;
		$starterCounts = Category::leftJoin("request", function ($join) use ($retro_date) {
	 			$join->on("category.id", "=", "request.category_id");
	 			if (!isset($retro_date)){
	 				$join->on(DB::raw("DATE(request.time_added)"), "=", DB::raw("CURDATE()"));
	 			}else{
	 				$join->on(DB::raw("DATE(request.time_added)"), "=", DB::raw("DATE('" .$retro_date . "')"));
	 			}
	 			// $join->on("request.pending", "=", DB::raw("0"));
	 		})->where("category.department_id", $dept_id)
			  ->groupBy("category.id")
			  ->select("category.*", 
			  			DB::raw("sum(case when request.id is not null and request.pending=0 then 1 else 0 end) as count"),
			  			DB::raw("sum(case when request.id is not null and request.pending=1 and request.user_id = $user_id then 1 else 0 end) as pending_count"),
			  			DB::raw("case when category.id in (select distinct category_id from drop_down, category where drop_down.category_id = category.id) then 1 else 0 end as need_dropdown")
			  		)
			  ->orderBy("category.ordering")
			  ->get()->keyBy("id")->toArray();

		$arr = array();
		foreach ($starterCounts as $key => $value) {
			if ($value["count"] != 0 || $value["pending_count"] != 0)
				$arr[] = $key;
		}
		$results = Category::recursiveDoCount($arr, $starterCounts);
		return array_filter($results, function($val) use ($parent_id) {
			return $val["parent_id"] == $parent_id;
		});
	 }

	 /**
	  * recursive helper function that goes through the array of ids and
	  * updates their parent categories (at the same time, creating an array of ids to 
	  * traverse next)
	  * @param $workingSet
	  * @param $data
	  * @return eloquent builder
	  */
	 private static function recursiveDoCount($workingSet, $data) {
	 	$newWorkingSet = array();
	 	if (count($workingSet) <= 0)
	 		return $data;

	 	foreach ($workingSet as $id) {
	 		$cat = $data[$id];

	 		if ($cat["parent_id"] != null) {
	 			$data[$cat["parent_id"]]["count"] += $cat["count"];
	 			$data[$cat["parent_id"]]["pending_count"] += $cat["pending_count"];

	 			if (!in_array($cat["parent_id"], $newWorkingSet))
	 				$newWorkingSet[] = $cat["parent_id"];
	 		}
	 	}

	 	return Category::recursiveDoCount($newWorkingSet, $data);
	 }
	 
	 /**
	  * given a category id it determines how high up the category is in the category
	  * hierarchy
	  * @param $cat_id
	  * @param $dept_id
	  * @return number
	  */
	 public static function Getheight($cat_id, $dept_id){
	 	if (!$cat_id){ 
	 		$categories = Category::whereNull('parent_id')->where("category.department_id", "=", "$dept_id")->get()->toArray();
	 		$current = $categories[0]['id'];
	 	} else {
	 		$current = $cat_id;
	 	}
	 	$categories = Category::leftjoin("category AS w", "category.id", "=", "w.parent_id")
	 	->select("category.id", "category.name", "w.id as child")->where("category.department_id", "=", "$dept_id")
	 	->get()->keyby("id")->toArray();
	 	//return $categories;
	 	
	 	$count = 0;
	 	while ($current != NULL){		
	 		$current = $categories["$current"]["child"];
	 		$count +=1;
	 	}

	 	return $count;
	 }

	
 	public static function getDivisionForCat($cat_id) {
		$result = Category::where("id", $cat_id)->select("division_id")->get()->toArray();
		return count($result) > 0 ? $result[0]["division_id"] : null;
	}	

	public static function getDivisionsForParent($parent_id) {
		$result = Category::where("parent_id", $parent_id)->whereNotNull("division_id")->select("division_id")
					->distinct()->orderBy("division_id")->get()->toArray();
		return $result;
	}
}
?>