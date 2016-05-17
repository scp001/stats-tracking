<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class Permission extends Eloquent {
	/**
	 * The database table used by the model
	 *
	 * @var string
	 */
	protected $table = 'permission';
	public $timestamps = false;
	
	/**
	 * Returns an array consisting of the permission level and respective department
	 * for the user with id $user_id
	 * @param $user_id
	 * @return array in the format array(array(dept1 => admin/regular, dept2 =>admin/regular))
	 */
	public static function getPermission($user_id){
		return Permission::select("department_id" ,"department.name","department.shortname", "permission_level")->join('department', "department.id", "=", "permission.department_id")
							  ->join('user', "user.id", "=", "permission.user_id")->where("user_id", "=", "$user_id");
	}
	/*
	public static function GetDepartmentViaPermission($user_id){
		return Permission::select("department.name", "department.shortname")->join("department", "department.id", "=", "permission.department_id")
								->join('user', "user.id", "=", "permission.user_id")->where("permission.user_id", "=", "$user_id")
								->get();
	}*/


	/*
	 * Remove all of a given user's permissions for a specific department
	 *
	 */
	public static function removePermission($perm_id) {
		$affectedRows = Permission::where("id", $perm_id)->delete();
		return $affectedRows > 0;
	}



	/*
	 * This function will first check if the user already has a permission for the department. If he/she already has a permission,
	 * the permission level will be updated. Otherwise, create a new permission for the user.
	 */
	public static function addPermissionOrUpdate($user_id, $dept_id, $level) {
		$perm = Permission::where("user_id", $user_id)->where("department_id", $dept_id);
		$count = $perm->count();
		if ($count) {
			// update
			$perm->update(array("permission_level" => $level));
		} else {
			// insert
			Permission::insert(array("user_id" => $user_id, "department_id" => $dept_id, "permission_level" => $level));
		}
	}
}