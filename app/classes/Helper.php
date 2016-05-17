<?php
/**
 * Class Helper
 */
class Helper
{
	public static function getAsset($fileRoute) {
		$url = Config::get('app.url');
		return "$url/$fileRoute";
	}

	/**
	 * Since doing "new DateTime('now')" is buggy, so I created this function to help with the complicated code..
	 * That will cause off-by-one error for some reason..
	 * @return DateTime
	 */
	public static function getCurrentDateTime() {
		return new DateTime((new DateTime('now'))->format("Y-m-d H:i:s"));
		//return new DateTime((new DateTime('2014-11-03'))->format("Y-m-d H:i:s"));
	}


	/**
	 * Parse the errors from validator
	 * @param $validator
	 * @param null $additionalMsg
	 * @return string
	 */
	public static function getAllErrorMessages($validator, $additionalMsg = null)
	{
		$str = '';
		$messages = $validator->messages()->toArray();
		$num = 0;
		foreach ($messages as $msg) {
			foreach ($msg as $m) {
				$str .= ++$num . '. ' . $m.'<br />';
			}
		}

		if($additionalMsg) {
			foreach($additionalMsg as $msg) {
				$str .= ++$num . '. ' . $msg . '<br />';
			}
		}

		return $str;
	}

	/**
	 * Simply for decoration and fun =D
	 * @param $string
	 * @return string
	 */
	public static function statstrackingSays($string)
	{
		return "Stats Tracking: " . $string;
	}

	/**
	 * Simply for decoration and fun =D
	 * @param $string
	 * @return string
	 */
	public static function systemSays($string)
	{
		$string = preg_replace("/[^a-zA-Z 0-9]+/", "_", $string);       //  Replace punctuations.. with underscore..
		$string = str_replace(" ", "_", $string);
		return "SYSTEM_" . strtoupper($string);
	}


	/**
	 * Function sole purpose is to omit the detailed time in date strings
	 * Can be disabled by changing the line
	 *      'return substr($dateStr, 0, 10);'
	 * To
	 *      'return $dateStr;'
	 * @param $dateStr
	 * @return string
	 */
	public static function internalDateFormatter($dateStr) {
		return substr($dateStr, 0, 10);
	}

	/**
	 * @param DateTime $date
	 * @param int $delta
	 * @param bool $returnStr           Return DateTime on true, return String otherwise..
	 * @return mixed
	 */
	public static function modifyDate($date, $delta, $returnStr = false) {
		if($delta > 0 ) {
			$date->modify('+' . strval($delta) . " days");
		} else if($delta < 0) {
			$date->modify('-' . strval($delta) . " days");
		}

		if($returnStr) {
			return $date->format('Y-m-d H:i:s');
		}
		return $date;
	}

	/**
	 * Take two strings, as time, then calculate the absolute differences
	 * between two dates in seconds..
	 * @param string $first
	 * @param string $second
	 * @param string $unit
	 * @return int
	 */
	public static function DateDiff($first, $second, $unit = "d") {
		if($unit == 's') {
			return strtotime($first) - strtotime($second);
		} elseif($unit == 'd') {
			return date_diff(new DateTime($first), new DateTime($second))->format('%a');
		}
	}


	/**
	 * Return true if the logged in user has ADMIN permission to the given department (given via id or shortname)
	 * @param int or string $dept_id
	 * @return boolean
	 */
	public static function hasAdminPermissionForDept($dept_id) {
		return Helper::hasGivenPermissionForDept($dept_id, ADMIN);
	}

	/**
	 * Return true if the logged in user has REGULAR permission to the given department (given via id or shortname).
	 * Note that this assumes that admins has access to anything a regular user has.
	 * @param int or string $dept_id
	 * @return boolean
	 */
	public static function hasRegularPermissionForDept($dept_id) {
		return Helper::hasGivenPermissionForDept($dept_id, array(ADMIN, REGULAR));
	}

	/**
	 * Return true if the logged in user has a specific permission to the given department (given via id or shortname).
	 * If $perm is an array, then go through the array and check that the user has one of them
	 * @param int or string (when it is the shortname) $dept_id
	 * @param string or array or string $perm
	 * @return boolean
	 */
	private static function hasGivenPermissionForDept($dept_id, $perm) {
		/*
		// Get the department associated with the id
		$dept = Department::find($dept_id);

		// The id isn't valid, deny user
		if (!$dept)
			return false;

		$shortname = $dept->shortname;
		
		// Check that the user has that shortname in their permission and that the permission level is ADMIN
		$user = Login::getUser();
		if (!$user || !array_key_exists($shortname, $user->permission)) 
			return false;

		if (is_array($perm)) {
			foreach ($perm as $p) {
				if ($user->permission[$shortname][PERMISSION] == $p)
					return true;
			}
			return false;
		}
		return $user->permission[$shortname][PERMISSION] == $perm;
		*/
		

		$user = Login::getUser();

		// only show relevant user permission (matching dept_id)
		$userPerm = array_first($user->permission, function($key, $val) use ($dept_id) {
			return $val["department_id"] == $dept_id || $val["shortname"] == $dept_id;
		}, NULL); // built in laravel helper function to return first element that satisfies truth test

		if (!$user || !$userPerm) 
			return false;

		$shortname = $userPerm["shortname"];

		if (is_array($perm)) {
			foreach ($perm as $p) {
				if ($user->permission[$shortname][PERMISSION] == $p)
					return true;
			}
			return false;
		}
		return $user->permission[$shortname][PERMISSION] == $perm;
	}
}
