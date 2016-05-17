$(document).ready(function() {
	if ($("#is_admin").val()){
		var datepickerOptions = {
			dateFormat: "yy-mm-dd",
			showTimePicker: false,
			showOtherMonths: true,
			selectOtherMonths: true,
			maxDate: new Date(),
			onSelect: function(dateText){
				//console.log(dateText);
				var start = $("#retroactive").datepicker('getDate');
				var y = start.getFullYear();
				var m = start.getMonth() +1;
				m = pad(m, 2);
				var d = start.getDate();
				d = pad(d, 2);
				start = y + "-" + m + "-" + d;
				$("#hidden-date").val(start);
				$("#category-form").submit();
			}
		};
	
		$( "#retroactive" ).datepicker(datepickerOptions);
	}
});

function pad(n, width, z) {
	  z = z || '0';
	  n = n + '';
	  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

// Category
$(".category-grid-item").on('click', function(e) {
	e.preventDefault();
	var checkedMedium = $("input[name='medium']:checked").val();
	if (!checkedMedium){
		checkedMedium = $("#medium").val();
	}
	// if medium radio button is not selected (value is undefined), return false
	if (checkedMedium == undefined) {
		utils.showMessage("danger", "Error: Please choose a request method");
		return false;
	}

	// Set hidden input
	$("#hidden-cat-id").val($(this).attr("value"));
	// Form submit
	if ($("#is_admin").val()){
		if (!$("#pending").val()){
			var start = $("#retroactive").datepicker('getDate');
			var y = start.getFullYear();
			var m = start.getMonth() +1;
			m = pad(m, 2);
			var d = start.getDate();
			d = pad(d, 2);
			start = y + "-" + m + "-" + d;
			$("#hidden-date").val(start);
		}else{
			$("#hidden-date").val($("#retroactive").val());
		}
	}
	$("#category-form").submit();

});

$("input[name='medium']").change(function () {
	utils.hideMessage();
	$("#hidden-medium-id").val($("input[name='medium']:checked").val());
});




// Category-item
// $(".stat-add-comment").click(function(e) {
// 	e.preventDefault();
// });

$(".inc-btn").click(function(e) {
	e.preventDefault();
	
	var self = this;
	var checkedMedium = $("#medium").val();
	var commentId = $(".comment-container", $(this).parent()).attr("id");
	var dropdownId = $(".dropdown-container", $(this).parent()).attr("id");
	var $commentTxtObj = $("textarea.stat-comment", $("#" + commentId));
	var commentVal = $commentTxtObj.length == 1 ? $commentTxtObj.val() : "";
	var retro_date = $("#is_admin").val() ? $("#retro_date").val() : '';
	
	if (commentId && commentVal.trim().length == 0) {
		utils.showMessage("warning", "Please ensure that the comments are filled out, then press the button to save the stat");
		$(self).blur();
		return false;
	} else {
		utils.hideMessage();
	}
	
	if (dropdownId){
		commentVal = document.getElementById(dropdownId);
		commentVal = commentVal.options[commentVal.selectedIndex].text;
	}
	$.ajax({
		url: BASE_URL + "api/addRequest",
		method: "PUT",
		data: {
			"category": $(this).attr("value"),
			"medium": checkedMedium,
			"comments": commentVal,
			"retro_date": retro_date
		},
		beforeSend: function() {
			// if medium radio button is not selected (value is undefined), return false
			if (!checkedMedium) {
				utils.showMessage("danger", "Error: Please choose a request method from the previous page");
				$("input[name='medium']").change(utils.hideMessage);
				return false;
			}
		},
		success: function(res) {
			var $countObj = $(".stat-pending", $(self).parent());
			// var origColor = $countObj.css("color");
			// $countObj.css("color", "green");
			$countObj.text("+ " + (parseInt($countObj.text().replace("+", "").trim()) + 1));
			// setTimeout(function(){
			// 	$countObj.css("color", origColor);
			// }, 1000);

			$commentTxtObj.val('');
			$(self).blur();
		},
		error: function(res) {
			utils.showMessage("danger", "Error: " + res.responseText);
		}
	});
});
