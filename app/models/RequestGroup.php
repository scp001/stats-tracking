<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class RequestGroup extends Eloquent{
	
	protected $table = "request_group";
	public $timestamps = false;
	
	public static function NumberofStudents($start, $end, $dept_id, $mediums){
		$grouped = RequestGroup::selectRaw("DATE(end_time) as time, COUNT(DATE(end_time)) as count")->groupby(DB::raw("DATE(end_time)"))
		->whereRaw('CAST(end_time AS date) between ? and ?', array($start, $end))->whereNotNull("end_time")
		->where("department_id", "=", "$dept_id")->whereIn("requestmedium_id", $mediums);
		return $grouped;
	}
	
	public static function CurrentUserHasSession($user_id){
		$current = RequestGroup::where("user_id", "=", "$user_id")->whereNull("end_time")->count();
		return $current > 0;
	}
	
	public static function EndUserSession($user_id, $medium, $retro_date){
		$data = array();
		$data["end_time"] = $retro_date != 0 ? $retro_date : DB::raw("CURRENT_TIMESTAMP");
		RequestGroup::where("user_id", "=", "$user_id")->whereNull("end_time")->update($data);
		RequestGroup::where("user_id", "=", "$user_id")->whereNull("requestmedium_id")->update(array("requestmedium_id" => $medium));
		Requests::where("user_id", "=", "$user_id")->update(array("pending" => 0));
	}
	
	public static function StartUserSession($dept_id){
		$user_id = Login::GetUser()->id;
		$session_id = RequestGroup::insertGetId(array("user_id" => "$user_id", "start_time" => DB::raw("CURRENT_TIMESTAMP"), "department_id" => $dept_id));
		//session_start();
		//$_SESSION["session_id"] = array("session_id" => $session_id);
		Session::flash("success", "New Session started");
		return $session_id;
	}
	
	public static function CancelUserSession($user_id){
		RequestGroup::where("user_id", "=", "$user_id")->whereNull("end_time")->delete();
		Requests::where("user_id", "=", "$user_id")->where("pending", "=", 1)->delete();
	}
	
	public static function getSessionDepartment($user_id){
		return RequestGroup::join('department', "request_group.department_id", "=", "department.id")
		->select("department.shortname")->where("user_id", "=", "$user_id")->whereNull("end_time");
	}
}
?>