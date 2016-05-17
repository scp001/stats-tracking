@extends("layouts.main")
@section("content")
<br>
<div>

{{ Form::label('start', 'StartDate:') }}
{{ Form::text('start', $startdate, array("id" => "start")) }}
{{ Form::label('end', 'EndDate:') }}
{{ Form::text('end', $enddate, array("id" => "end")) }}

{{ Form::label('categories', 'Category:') }}
{{ Form::select('categories', $categories, $def_cat, array("id" => "categories")); }}

{{ Form::hidden('department', $department, array("id" => "department")) }}
{{ Form::hidden('dept_shortname', $dept_shortname, array("id" => "dept_shortname")) }}
@foreach ($mediums as $med)
	<label class='checkbox-inline'>
	<input type='checkbox' name='check' value="{{ $med['id'] }}" checked="checked"/> {{ $med['method_name'] }}
	</label>
@endforeach
{{ Form::button("Go", array("id" => "stats-filter-btn", "class" => "btn btn-primary btn-sm")) }}


</div>
<div class='row'>
	<div id="container" class='chart-container col-md-7'></div>
	<div id="piecontainer" class='chart-container col-md-5'></div>
</div>

<h3>Comments and Referrals Table {{ HTML::link(Config::get("app.url") . "/api/exportExcel", "Export to Excel", array("id" => "excel-link", "class" => "btn btn-info btn-xs")) }}</h3>

<div class='table-scroll' style="position: relative">
<table id="comment-table" class='table table-bordered tablesorter'>
</table>
</div>

<div class='row'>
	<div id="studentcontainer" class='chart-container col-md-12'></div>
</div>
<div class='col-md-3'>
{{
	HTML::link(Config::get("app.url") . "/$dept_shortname/dashboard", "Go to current category list", array("id" => "back-link", "class" => "back-btn"))
}}
</div>
<br><br><br><br>
@stop
@section('javascript')
<script src="https://www1.utsc.utoronto.ca/webapps/quizzical/js/lib/jquery.tablesorter.min.js"></script>
<script src="{{ asset('js/charts.js', Config::get('app.secure')) }}"></script>
@stop
