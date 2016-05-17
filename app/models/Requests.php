<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class Requests extends Eloquent {
	/**
	 * The database table used by the model
	 *
	 * @var string
	 */
	protected $table = 'request';
	public $timestamps = false;
	
	/**
	 * Inserts a new request into the request table
	 * @param $catid
	 * @param $medium
	 * @param $user_id
	 * @param $comments
	 */
	public static function InsertRequest($catid, $medium, $user_id, $comments, $session_id, $retro_date){
		$data = array();
		$data["category_id"] = $catid;
		$data["requestmedium_id"] = $medium;
		$data["user_id"] = $user_id;
		$data["comments"] = $comments;
		$data["time_added"] = $retro_date != "" ? $retro_date : DB::raw("CURRENT_TIMESTAMP");
		$date["group_id"] = $session_id;
		Requests::insert($data);
	}
	
	public static function HasPendingRequests(){
		$user_id = Login::GetUser()->id;
		$pending = Requests::where("pending", "=", 1)->where("user_id", "=", "$user_id")->count();
		return $pending > 0;
	}
	
	/**
	 * returns the number of reqests for each category whose parent id is $cat_id
	 * if $count is set then it will return the total requests in the time frame $time
	 * @param $cat_id
	 * @param $time
	 * @param $count
	 */
	public static function SelectTimeAdded($cat_id, $dept_id, $time="%", $count=0) {
		if (!$count){
			return Requests::join('category','category.id', '=', 'request.category_id')
				->where('category.parent_id', '=', "$cat_id")->where("category.department_id", "=", "$dept_id")
				->select('category.id', 'category.name', 'request.time_added');
		} else {
			return Requests::join('category','category.id', '=', 'request.category_id')
			->where('category.parent_id', '=', "$cat_id")->where('time_added', 'LIKE', $time)->where("category.department_id", "=", "$dept_id")
			->select('category.id', 'category.name', 'request.time_added', DB::raw('COUNT(category.name) as count'))
			->groupby('category.name');
		}
	}
	
	/**
	 * The same as the above function but used for the higher level categories
	 * note this function is not usable for categories of a higher height than 2
	 * use category::getCategoryCounts to get middle height category counts
	 * @param $time
	 * @param $count
	 */
	public static function SelectTimeAddedAll($dept_id, $time="%", $count=0){
		if (!$count){
			return DB::table('category AS q')->join('request', 'q.id', '=', 'request.category_id')
									  ->join('category AS w', 'q.parent_id', '=', 'w.id')->where("q.department_id", "=", "$dept_id")
									  ->select('w.id', 'w.name', 'request.time_added');
		} else{
			return DB::table('category AS q')->join('request', 'q.id', '=', 'request.category_id')
			->join('category AS w', 'q.parent_id', '=', 'w.id')->where('request.time_added', 'LIKE', $time)
			->where("q.department_id", "=", "$dept_id")
			->select('w.id', 'w.name', 'request.time_added', DB::raw('COUNT(w.name) as count'))
			->groupby('w.name');
		}
	}
	/**
	 * This function replaces the two above functions. By calculating the height of the 
	 * category in the category hierarachy it determines how many inner joins need to be done.
	 * The query is built depending on what level the category is at, and what chart it is getting information for 
	 * @param $cat_id
	 * @param $dept_id
	 * @param $mediums
	 * @param $start
	 * @param $end
	 * @param $count
	 * @return query
	 */
	public static function SelectJoinData($cat_id, $dept_id, $mediums, $start, $end, $count=0){

		$height = Category::GetHeight($cat_id, $dept_id);
		$query = Requests::join('category','category.id', '=', 'request.category_id');
		
		$end_name = '';
		$table_name = 'a';
		$prev_table_name = "category";
		for ($x = 1; $x < $height; $x++){
			$query= $query->join("category AS $table_name","$prev_table_name.parent_id", '=', "$table_name.id");
			$end_name = $prev_table_name;
			$prev_table_name = $table_name;
			$table_name++;
		}
		// if $cat_id is 0 then we are getting all the data for this department 
		// and for the query to work we need $end_name to be $prev_table_name
		// we also do not need the constraint checking for the parent id
		if (!$cat_id){
			$end_name = $prev_table_name;
		}else{
			$query = $query->where("$end_name.parent_id", '=', "$cat_id");
		}
		$query = $query->where("category.department_id", "=", "$dept_id")
					   ->where("request.pending", "=", 0)
					   ->whereIn("request.requestmedium_id", $mediums)
					   //->where('request.time_added', "LIKE", "$time");
					   ->whereRaw('CAST(request.time_added AS date) between ? and ?', array($start, $end));
		
		// if $count is 0 then we are making a column chart
		// if $count is 1 we are making a pie chart
		// if $count is 2 we are making the comments table
		if (!$count){
			$query = $query->select("$end_name.id", "$end_name.name", 'request.time_added');
		} else if ($count == 1){
			// Query for getting the total requests
			$queryTotal = $query->select(DB::raw("COUNT($end_name.name) as total"));

			// Main query which uses the query for total requests
			$query = $query->select("$end_name.id", "$end_name.name", 'request.time_added', DB::raw("COUNT($end_name.name)/(" . $queryTotal->toSql() . ") * 100 as percentage"))
			->groupby("$end_name.name");

			// The current bindings only cover the bindings required for $queryTotal. Need to repeat the bindings so that it works for $query as well
			$query->addBinding($query->getBindings());
		} else if ($count == 2){
			$query = $query->join("request_medium", "request.requestmedium_id", "=", "request_medium.id")->whereNotNull("request.comments")
						   ->join("user", "request.user_id", "=", "user.id")
						   ->select("user.firstname", "user.lastname", "$prev_table_name.name as Category_Name", "category.name as Lower_Category_Name", "request.time_added as Time_Submitted", "request_medium.method_name as Request_Method", "request.comments");
		}

		return $query;
	} 
}