<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public $familyname;
	public $givennames;
	public $peopleID;
	public $position;
	public $strPosition;
	public $studentId;
	public $email;
	public $utorId;
	public $permission;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = array('password', 'remember_token');
	

	protected $fillable = array('id', 'firstname', 'lastname');


	/*
	 * Return a query builder object containing columns in user table, along with their permissions.
	 * Optional constraint to only view users with permission to a given department.
	 *
	 */
	public static function getUserPermissionDetails($dept_id=null) {
		$result = User::join("permission", "user.id", "=", "permission.user_id")
						->select("user.*", "permission.id as permission_id", "permission.department_id", "permission.permission_level");
		if ($dept_id != null) {
			$result->where("permission.department_id", $dept_id);
		}
		return $result;
	}



	public static function addUserIfNotExist($id, $utorid, $utscid, $firstname, $lastname) {
		$user = User::find($id);
		if (!$user) {
			User::insert(array("id" => $id, "firstname" => $firstname, "lastname" => $lastname));
		}
	}
}
