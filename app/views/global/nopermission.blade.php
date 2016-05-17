@extends("layouts.main")

@section("content")
<div class='col-md-6 center-block' style='float:none'>
	<h2>Unauthorized</h2>
	<h3>You do not have permission to view this page.</h3>
	<a href="{{ route('dashboard') }}" class='btn btn-info'>Dashboard</a>
</div>
@stop