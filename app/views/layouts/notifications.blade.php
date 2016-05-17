<div class="message-panel alert alert-dismissable">
	<button type="button" class="close" data-hide="alert" aria-hidden="true" onclick="$('.message-panel').hide()">&times;</button>
	<span class="message-panel-content"></span>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissable">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	{{ $message }}
</div>
{{ Session::forget('success') }}
@endif

@if ($message = Session::get('danger'))
<div class="alert alert-danger alert-dismissable">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	{{ $message }}
</div>
{{ Session::forget('danger') }}
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-dismissable">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	{{ $message }}
</div>
{{ Session::forget('warning') }}
@endif

@if ($message = Session::get('info'))
<div class="alert alert-info alert-dismissable">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	{{ $message }}
</div>
{{ Session::forget('info') }}
@endif