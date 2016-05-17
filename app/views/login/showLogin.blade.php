@extends("layouts.main")
@section("content")

<div class="login-form">
	<h2>Login</h2>
	{{ Form::open() }}
	<div>
		{{ Form::label("username", "UTOR/UTSCid") }}
		{{ Form::text("username", Input::old("username") ?: "") }}
	</div>
	<div>
		{{ Form::label("password", "Password") }}
		{{ Form::password("password") }}
	</div>
	<div>
		{{ Form::submit("Log In", array('class' => 'btn btn-primary')) }}
	</div>
	{{ Form::close() }}
</div>
@stop
