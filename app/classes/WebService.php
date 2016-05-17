<?php

class WebService extends Eloquent
{
	protected function getProfile($username, $type) {	
		$WEBSRV_URL = App::environment(LOCAL, BETA) ? WEBSRV_BETA_URL : WEBSRV_URL;
		$WEBSERVICES_URL = $WEBSRV_URL . "/GetProfile?response=application/json&accountID=" . $username . "@" . $type;

		$content = @file_get_contents($WEBSERVICES_URL, false);

		if($content === FALSE) {
			Session::flash('danger', Helper::systemSays("web services unavailable"));
			return false;
		}
		$profile = json_decode($content)->return;
		if ($profile && isset($profile->peopleID) && $profile->peopleID > 0) {
			return json_decode(json_encode($profile), true);
		}
		return false;
	}
}