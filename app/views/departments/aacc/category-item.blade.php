@extends("layouts.main")
@section("content")

@include("layouts.category-breadcrumb")
<?php 
$is_admin = Helper::hasAdminPermissionForDept($dept_shortname);
?>
<h2>{{ $currentCategoryName }} Sub-Categories 
	@if ($is_admin)
	<a href='{{ Config::get('app.url') }}/{{ $dept_shortname }}/dashboard/charts?cat_id={{ $parent_id }}' class='btn btn-info btn-xs'>View Charts</a>
	@endif
</h2>

<h3>Request Method: {{ isset($mediums[$medium]) ? $mediums[$medium]['method_name'] : "" }}</h3>
@if($is_admin)
	{{ Form::label('retroactive', 'Submission Date: ' . $retro_date) }} 
	{{ Form::hidden('retro_date', $retro_date, array("id" => "retro_date")) }}
@endif
{{ Form::hidden('is_admin', $is_admin, array("id" => "is_admin")) }}
<div>
	<div class="clearfix row">
		<div class='pull-left col-md-6 col-sm-6 col-xs-12'>
		<?php
			for ($i = 0; $i < count($categories); $i++) {
				$cat = $categories[$i];
				if ($cat["division_id"] and $divisionSides[$cat['division_id']] == "left") {
					$color = "division-color-1";
					echo "<div>";
					echo "<button class='category-container stat-container stat-btn inc-btn pointer $color' value='{$cat['id']}'>";
					echo "<span class='stat-category stat-left'>{$cat['name']}</span>";
					echo "<span class='stat-right'>";
					echo "<span class='stat-num'>{$cat['count']}</span> <span class='stat-pending'>+ {$cat['pending_count']}</span> 
						</span>";
					echo "</button>";
					if ($cat['require_comment']) {
						echo "<div id='comment$i' class='aa category-container comment-container'><textarea class='stat-comment' placeholder='Enter comment here (required)'></textarea></div>";
					}
					echo "</div>";
				}
			}
		?>
		</div>
		<div class='pull-right col-md-6 col-sm-6 col-xs-12'>
		<?php
			for ($i = 0; $i < count($categories); $i++) {
				$cat = $categories[$i];
				if ($cat["division_id"] and $divisionSides[$cat['division_id']] == "right") {
					$color = "division-color-2";
					echo "<div>";
					echo "<button class='category-container stat-container stat-btn inc-btn pointer $color' value='{$cat['id']}'>";
					echo "<span class='stat-category stat-left'>{$cat['name']}</span>";
					echo "<span class='stat-right'>";
					echo "<span class='stat-num'>{$cat['count']}</span> <span class='stat-pending'>+ {$cat['pending_count']}</span> 
						</span>";
					echo "</button>";
					if ($cat['require_comment']) {
						echo "<div id='comment$i' class='aa category-container comment-container'><textarea class='stat-comment' placeholder='Enter comment here (required)'></textarea></div>";
					}
					echo "</div>";
				}
			}
		?>
		</div>
	</div>

<?php
	$j = 0;
	for ($i = 0; $i < count($categories); $i++) {
		$cat = $categories[$i];
		if (!$cat["division_id"]) {
			if ($j % 2 == 0) echo ($i > 0 ? "</div>" : "") . "<div class='row'>";
			$j++;
			echo "<div class='col-md-6'>";
			echo "<div>";

			echo "<button class='category-container stat-container stat-btn inc-btn pointer' value='{$cat['id']}'>";
			echo "<span class='stat-category stat-left'>{$cat['name']}</span>"; 
			echo "<span class='stat-right'>";
			echo "<span class='stat-num'>{$cat['count']}</span> <span class='stat-pending'>+ {$cat['pending_count']}</span>
				</span>";
			echo "</button>";
			if ($cat["need_dropdown"]){
				echo "<div class='category-container comment-container'><select class ='dropdown-container' id='dropdown$i'>";
					foreach ($dropdown as $drop){
						if ($drop["category_id"] == $cat["id"]){
							echo "<option value={$drop['name']}>{$drop['name']}</option>";
						}
					}
				echo "</select></div>";
			}
			if ($cat['require_comment']) {
				echo "<div id='comment$i' class='category-container comment-container'><textarea class='stat-comment' placeholder='Enter comment here (required)'></textarea></div>";
			}
			echo "</div></div>";
		}
	}
	$url = Config::get('app.url');
	$retro_date = !$is_admin? 0:$retro_date;
	if (count($categories) > 0) {
		if ($j % 2 == 0) // Check to see if I need to create a new row for the back button
			echo "</div><div class='row'>";
		echo "<div class='col-md-6'><a class='back-btn' href='$backUrl'>(Back)</a>";
		echo "<br><a class='btn btn-info col-md-6 btn-lg' href='$url/api/finish/$medium/$retro_date'>Finish Session</a>
		      <a class='btn btn-danger col-md-6 btn-lg' href='$url/api/cancel'>Cancel Session</a></div>";
	} else {
		echo "<h4>There are no categories here or you do not have permission to view them.</h4><a class='col-md-2 back-btn' href='$backUrl'>(Back)</a>
			  <a class='btn btn-info col-md-6 btn-lg' href='$url/api/finish/$medium/$retro_date'>Finish Session</a>
		      <a class='btn btn-danger col-md-6 btn-lg' href='$url/api/cancel'>Cancel Session</a>";
	}
?>


</div>

{{ Form::hidden('medium', $medium, array("id" => "medium")) }}
@stop

@section("javascript")
<script src="{{ asset('js/categories.js', Config::get('app.secure')) }}"></script>
@stop