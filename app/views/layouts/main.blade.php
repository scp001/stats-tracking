<!doctype html>
<html>
<head>
	<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="http://code.highcharts.com/highcharts.src.js"></script>
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>-->

	<script src="{{ asset('vendors/jquery-2.1.3.min.js', Config::get('app.secure')) }}"></script>
	<script src="{{ asset('vendors/highcharts-4.0.4/js/highcharts.src.js', Config::get('app.secure')) }}"></script>
	<script src="{{ asset('vendors/highcharts-4.0.4/js/exporting.src.js', Config::get('app.secure')) }}"></script>

	<link rel="stylesheet" href="{{ asset('vendors/bootstrap-3.3.2/css/bootstrap.min.css', Config::get('app.secure')) }}" />
	<script src="{{ asset('vendors/bootstrap-3.3.2/js/bootstrap.min.js', Config::get('app.secure')) }}"></script>
	
	<link rel="stylesheet" href="{{ asset('vendors/jquery-ui-1.11.2.custom/jquery-ui.min.css', Config::get('app.secure')) }}" />
	<script src="{{ asset('vendors/jquery-ui-1.11.2.custom/jquery-ui.min.js', Config::get('app.secure')) }}"></script>
	
	
	<script src="{{ asset('js/statstracking.js', Config::get('app.secure')) }}"></script>
	<link rel="stylesheet" type="text/css" href="https://www.utsc.utoronto.ca/_includes/application/css/hf.css" />
	<link rel="stylesheet" type="text/css" href="{{ asset('css/main.css', Config::get('app.secure')) }}" />

	<script type="text/javascript">
	var BASE_URL = "{{ Config::get('app.url')}}/"; 
	</script>

	<title>Stats Tracking</title>
</head>

<body>
	@include("layouts.header")

	@include("layouts.notifications")

	<div class="content-container">
		<div class="content" role="main">
			@yield("content")
		</div>
	</div>
	@include("layouts.footer")
	@yield("javascript")
</body>
</html>