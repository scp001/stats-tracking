@extends("layouts.main")

@section("content")


@if (count($departments) > 0) 
	<h2>Choose a department</h2>
	<ul class='list-group row'>
	@foreach ($departments as $dept)
		<!-- echo "<div><a href='/{$dept['shortname']}/dashboard'>{$dept['name']}</a></div>"; -->
		 {{ "<li class='list-group-item col-md-6'><a href='{$dept['shortname']}/dashboard'>{$dept['name']}</a></li>" }}
	@endforeach
	</ul>
@else 
	<h2>You do not have permission to use this application.</h2>
@endif


@stop