<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class DropDown extends Eloquent{
	/**
	 * The database table used by the model
	 *
	 * @var string
	 */
	protected $table = 'drop_down';
	
	public static function getDropdown(){
		return DropDown::select("category_id", "name")->get();
	}
}
?>