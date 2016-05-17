@extends("layouts.main")
@section("content")

@include("layouts.category-breadcrumb")	

<?php 
$is_admin = Helper::hasAdminPermissionForDept($dept_shortname);
?>
<h2>Request Method</h2>
<div class='request-radio btn-group'>
@if(!$pending)
	<?php
	foreach ($mediums as $id => $m) {
		$checked = (isset($medium) && $medium == $id) ? "checked='checked'" : "";
		echo "<label class='btn btn-primary btn-lg'>
				<input type='radio' id='medium$id' name='medium' value='$id' $checked> {$m['method_name']}
			</label>";
	}
	?>
@else
<h3>{{ isset($mediums[$medium]) ? $mediums[$medium]['method_name'] : "" }}</h3>
{{ Form::hidden('medium', $medium, array("id" => "medium")) }}
@endif
</div>
{{-- Form::hidden('session_btn', $session_btn, array("id" => "session_btn")) --}}
<h2>{{ $currentCategoryName }} Categories 
	@if ($is_admin)
		<a class='btn btn-info btn-xs' href='{{ Config::get('app.url') }}/{{ $dept_shortname }}/dashboard/charts
		@if ($parent_id != NULL)
			?cat_id={{ $parent_id }}
		@endif
		'>View Charts</a>
	@endif
</h2>
<?php $retro_date = isset($retro_date) ? $retro_date:date("Y-m-d");?>
@if ($is_admin)
	@if (!$pending)
		{{ Form::label('retroactive', 'Set Submission Date:') }}
		{{ Form::text('retroactive', $retro_date, array("id" => "retroactive")) }} 
		{{ Form::label('today', 'Today\'s date is  ' . date('Y-m-d')) }}
	@else
		<b>{{ "Current Submission Date: " . $retro_date . ' (Today\'s date is  ' . date('Y-m-d') . ')'}}</b>	
		{{ Form::hidden('retroactive', $retro_date, array("id" => "retroactive")) }}
	@endif
@endif
{{ Form::hidden('pending', $pending, array("id" => "pending")) }}
{{ Form::hidden('is_admin', $is_admin, array("id" => "is_admin")) }}
<form id="category-form" method="get" action="">
	<input type="hidden" id="hidden-cat-id" name="id" value=""/>
	<input type="hidden" id="hidden-medium-id" name="medium" value="{{ $medium }}"/>
	<input type="hidden" id="hidden-date" name="date" value=""/>
	<?php
	for ($i = 0; $i < count($categories); $i++) {
		if ($i % 4 == 0) echo ($i > 0 ? "</div>" : "") . "<div class='row'>";
		
		$cat = $categories[$i];
		echo "<div class='col-md-3'>";
		//if ($session_btn){
			echo "<button tabindex='0' class='category-grid-item' role='button' value='{$cat['id']}'>{$cat['name']}  ({$cat['count']} <span class='stat-pending'>+ {$cat['pending_count']}</span>)</button>";
		//}
		echo "</div>";
	}

	if (count($categories) > 0) {
		if (trim(Input::get("id", "")) != "") { // only have a back button if the category page is not the topmost level
			if ($i % 4 == 0) // Check to see if I need to create a new row for the back button
				echo "</div><div class='row'>";
			echo "<div class='col-md-3'><a class='back-btn' href='$backUrl'>(Back)</a></div>";
		}
		echo "</div>";
	} else 
		echo "<h4>There are no categories here or you do not have permission to view them.</h4><a class='col-md-2 back-btn' href='$backUrl'>(Back)</a>";
	?>
</form>
@stop

@section("javascript")
<script src="{{ asset('js/categories.js', Config::get('app.secure')) }}"></script>
@stop
