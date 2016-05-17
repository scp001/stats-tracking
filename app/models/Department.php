<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Department extends Eloquent{
	/**
	 * The database table used by the model
	 *
	 * @var string
	 */
	protected $table = 'department';
	
	/**
	 * Returns object containing all department ids, names, and shortnames
	 */
	protected function select_all() {
		return Department::get();
	}
	
	/**
	 * returns the department whose shortname is $sname
	 * @param $sname
	 */
	protected static function findByShortName($sname) {
		return Department::where("shortname", "=", $sname)->first()->toArray();
	}

}
?>