@extends("layouts.main")

@section("content")
<div class='col-md-6 center-block' style='float:none'>
	<h2>Page Not Found</h2>
	<h3>The page you requested could not be found.</h3>
	<a href="{{ route('dashboard') }}" class='btn btn-info'>Dashboard</a>
</div>
@stop