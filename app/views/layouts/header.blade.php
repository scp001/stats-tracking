@section("header")
	<div class="utsc-header">
		<?php
		$utscHeader = file_get_contents("https://www.utsc.utoronto.ca/_includes/application/_header.html");

		if (Login::isLoggedIn()) {
			$name = Login::getUser()->givennames . " " . Login::getUser()->familyname;
			$position = Login::getUserPermissionLevel(isset($dept_shortname) ? $dept_shortname : null);
			$logoutUrl = Config::get('app.url') . "/logout";
			$logoutText = "Log Out";
		} else {
			$name = "GUEST";
			$position = "";
			$logoutUrl = Config::get('app.url') . "/login";
			$logoutText = "Log In";
		}
		$title = "Stats Tracking" . (isset($pageHeading) ? " - $pageHeading" : "");
		

		$utscHeader = str_replace("[--NAME--]", $name, $utscHeader);
		$utscHeader = str_replace("[--POSITION--]", $position, $utscHeader);
		$utscHeader = str_replace("[--LOGOUT_URL--]", $logoutUrl, $utscHeader);
		$utscHeader = str_replace("[--LOGOUT_TEXT--]", $logoutText, $utscHeader);
		// $utscHeader = str_replace("[--APP_TITLE--]", $title, $utscHeader);
		$utscHeader = str_replace("[--DROPDOWN--]", "", $utscHeader);
		echo $utscHeader;
		?>
	</div>

		<nav class="navbar" role="navigation">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<h2 class= "pull-left">{{ $title }}</h2>
			</div>
			<div class="collapse navbar-collapse navbar-right">
				<ul class='nav navbar-nav'>
					@if (Login::isLoggedIn())

						@if (isset($dept_shortname) && array_key_exists($dept_shortname, Login::getUser()->permission))
							<li><a href='{{ Config::get("app.url") . "/$dept_shortname/dashboard" }}'>Categories</a></li>

							@if (Helper::hasAdminPermissionForDept($dept_shortname))
								<li><a href='{{ Config::get("app.url") . "/$dept_shortname/dashboard/charts" }}'>Charts</a></li>
								<li><a href='{{ Config::get("app.url") . "/$dept_shortname/dashboard/edit-permissions" }}'>User Permissions</a></li>
							@endif
						@endif


						@if (count(Login::getUser()->permission) > 1)
							<!--<li><a href="{{ Config::get('app.url') . '/dashboard' }}">Departments</a></li>-->
						@endif

						<!-- <li><a href="{{ Config::get('app.url') . '/logout' }}">Logout</a></li>-->
					@endif
				</ul>
			</div>
		</nav>
	</div>
@show