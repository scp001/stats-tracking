<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class RequestMedium extends Eloquent{
	/**
	 * The database table used by the model
	 *
	 * @var string
	 */
	protected $table = 'request_medium';
	
	/**
	 * returns all of the request mediums
	 */
	public static function SelectAll() {
		return RequestMedium::select();
	}
}
?>