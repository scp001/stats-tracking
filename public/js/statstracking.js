// global utility functions
var utils = {};
utils.showMessage = function (type, message) {
	// Type being one of the bootstrap alert types (success, info, warning, danger)
	var classes = "alert-success alert-info alert-warning alert-danger";
	$(".message-panel .message-panel-content").text(message);
	$(".message-panel").removeClass(classes).addClass("alert-" + type).slideDown("fast");
}
utils.hideMessage = function () {
	$(".message-panel .message-panel-content").text('');
	$(".message-panel").slideUp("fast");
}

$(document).ready(function () {
	// For notifications, so that after dismissing them, they are not removed from DOM
	$("[data-hide]").on("click", function() {
		$("." + $(this).attr("data-hide")).hide();
	});
});
