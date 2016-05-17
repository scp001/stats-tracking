<?php

/**
 * Class Login
 */
class Login extends Eloquent
{
	// Interfaces

	private $user;


	static function Instance() {
		return new Login();
	}

	protected function saveAllSessions()
	{
		// checking database before serialize
		$res = $this->user->find($this->user->peopleID);

		if (isset($res)) {
			$this->user['attributes'] = $res['attributes'];
			$this->user['original'] = $res['original'];
		} else {
			// Re-identify this user as guest..
			$temp = new User();
			$temp->familyname = $this->user->familyname;
			$temp->givennames = $this->user->givennames;
			$temp->peopleID = $this->user->peopleID;
			$temp->utorId = $this->user->utorid;
			$temp->permission = $this->user->permission;
			$this->user = $temp;
		}
		Session::put('user', $this->user);
	}

	protected function setUserCredential($profile)
	{
		
		if ($profile->peopleID) {
			$familyname = $profile->familyname;
			$givennames = $profile->givennames;
			$peopleID = $profile->peopleID;
			$utorId = $profile->utorid;
		
			$permission = Permission::getPermission($peopleID);
			$permission = $permission->get()->keyBy("shortname")->toArray();
			$this->user = new User();
			$this->user->familyname = $familyname;
			$this->user->givennames = $givennames;
			$this->user->peopleID = $peopleID;
			$this->user->utorId = $utorId;
			$this->user->permission = $permission;
		}
	}

	/**
	 * Return whether the current user is logged in
	 */
	public static function isLoggedIn()
	{
		$user = Session::get('user');

		return !empty($user) ? true : false;
	}

	/**
	 * Return current user
	 */
	public static function getUser()
	{
		$user = Session::get('user');

		return !empty($user) ? $user : false;
	}


	/**
	 * Return the current user's permission level for a given department (via shortname)
	 */
	public static function getUserPermissionLevel($shortname) {
		if (!Login::isLoggedIn()) return false;

		$permission = Login::GetUser()->permission;
		if (array_key_exists($shortname, $permission)) {
			$per = $permission["$shortname"][PERMISSION];
			if (strcmp($per, ADMIN) == 0){
				return ADMIN;
			} else if (strcmp($per, REGULAR) == 0){
				return REGULAR;
			}
		} 
		return false;
	}
	
	public function Authenticate($userid, $password)
	{
		$ret = false;
	
		$data["pageTitle"] ="statstracking";
		$data["referer"]="";
		$data["userid"]=$userid;
		$data["password"]=$password;
		$param = http_build_query($data);
	
		if (!Session::has('INTRANET_SESSION_ID')) {
			Session::put('INTRANET_SESSION_ID', md5($data["userid"] . time())); ;
		}
	
		$sessionId = Session::get('INTRANET_SESSION_ID');
	
		if($sessionId == null) {
			return false;
		}
	
		if (App::environment(LOCAL,BETA))
		{
			$session_str = "DEMOUTSCwebPHPSESSID=". $sessionId;
			$WEBSRV_URL = WEBSRV_BETA_URL;
			$url = INTRANET_BETA;
		}
		else
		{
			$session_str = "UTSCwebPHPSESSID=". $sessionId;
			$WEBSRV_URL = WEBSRV_URL;
			$url =INTRANET;
		}
	
		$arr_header = array(	"Cookie: cookietester=1; {$session_str};",
		"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
		"Connection: keep-alive"
				);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$param);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_SSLVERSION,3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $arr_header);
		$ret = curl_exec($ch);
		curl_close($ch);
	
	
		$WEBSERVICES_URL = $WEBSRV_URL ."/GetProfileWithCourseSections?"
				.http_build_query(array(
						'response' => 'application/json',
						'sessionID' => $sessionId
				));
	
		$terms = array(0);
		foreach ($terms as $term) {
			$WEBSERVICES_URL .= "&term=" . $term;
		}

		$arrContextOptions=array(
				"ssl"=>array(
						"verify_peer"=>false,
						"verify_peer_name"=>false,
				),
		);

		// Suppress exceptions with @..
		$content = @file_get_contents($WEBSERVICES_URL, false, stream_context_create($arrContextOptions));

		// If failed to get file ==> web services has gone wrong probably..
		if($content === FALSE) {
			// He's dead, Jim!
			Session::flash('danger', Helper::systemSays("web services unavailable"));
			return false;
		}

		// Everything goes right.. decode the content..
		$profile = json_decode($content)->return;

		// Is he clean?
		if ($profile && $profile->peopleID > 0)
		{
			self::setUserCredential($profile);
			self::saveAllSessions();
			return true;
		}

		// Apparently he is not clean..
		Session::flash('danger', Helper::statstrackingSays('Incorrect login name or password.'));
		return false;
	}
	
	/**
	* Logoff
	*/
	public function logOff()
	{
		if (App::environment(LOCAL,BETA)) {
			$WEBSRV_URL = WEBSRV_BETA_URL;
		} else {
			$WEBSRV_URL = WEBSRV_URL;
		}
	
		$WEBSERVICES_URL = $WEBSRV_URL ."/Logoff?"
				.http_build_query(array(
						'response' => 'application/json',
						'sessionID' => Session::get('session_id')));
					
		$arrContextOptions=array(
				"ssl"=>array(
						"verify_peer"=>false,
						"verify_peer_name"=>false,
				),
		);

		// Logoff from Intranet.
		$logged_off = json_decode(file_get_contents($WEBSERVICES_URL, false, stream_context_create($arrContextOptions)))->return;

		// Flush Laravel session.
		return Session::flush();
	}
	

}
