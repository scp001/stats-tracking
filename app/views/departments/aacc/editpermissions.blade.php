@extends("layouts.main")

@section("content")
<h2>Add User Permission</h2>
<div class='row'>
	<div class='col-md-6 table-responsive'>
		{{ Form::open() }}
		<table class='add-user-table table table-bordered table-condensed'>
			<tr><th colspan="2">User</th><th colspan="2">Permission</th></tr>
			<tr>
				<td>{{ Form::text("user-field", '', array("class" => "full-fit", "autocomplete" => "off")) }}</td>
				<td>{{ Form::select("user-type", array("utor" => "UTORid", "utsc" => "UTSCid"), 'utor', array("class" => "full-fit")) }}</td>
				<td>{{ Form::select("user-role", array("regular" => "Regular User", "admin" => "Admin User"), 'regular', array("class" => "full-fit")) }}</td>
				<td>{{ Form::submit("Add", array("class" => "full-fit btn btn-primary btn-xs")) }}</td>
			</tr>
		</table>
		{{ Form::close() }}
	</div>
</div>

<h2>View Permissions</h2>
<div class='row'>
	<div class='col-md-9 table-responsive'>
		{{ Form::open() }}
		{{ Form::hidden('permission_id', '0') }}
		{{ Form::close() }}
		<table id='permission-table' class='permission-table table table-bordered table-hover tablesorter'>
			<thead><tr><th class="last_name header">Last name</th><th class="first_name header">First name</th><th class="permission header">Permission</th><th class="no-bg"></th></tr></thead>
			@foreach ($userList as $user)
				<tr>
					<td>{{{ $user["lastname"] }}}</td>
					<td>{{{ $user["firstname"] }}}</td>
					<td>{{{ $user["permission_level"] }}}</td>
					<td class='button-cell'>
						@if (Login::getUser()->peopleID != $user["id"])
						<div tabindex='0' class="glyphicon glyphicon-trash pointer permission-remove-btn" title="Remove this user permission" value="{{ $user['permission_id'] }}"></div>
						@endif
					</td>
				</tr>
			@endforeach
		</table>
	</div>
</div>
@stop

@section("javascript")
<script src="https://www1.utsc.utoronto.ca/webapps/quizzical/js/lib/jquery.tablesorter.min.js"></script>
<script>
	$(document).ready(function() {
		$(".permission-remove-btn").click(function() {
			if (!confirm("Are you sure you want to remove this permission?"))
				return false;

			$("input[name='permission_id']").val($(this).attr("value"));
			$("input[name='permission_id']").parent().submit();
		});
		$("#permission-table").tablesorter();
	});
</script>
@stop