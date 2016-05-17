$(document).ready(function() {
	
	var datepickerOptions = {
		dateFormat: "yy-mm-dd",
		showTimePicker: false,
		showOtherMonths: true,
		selectOtherMonths: true
	};
	
	$( "#start" ).datepicker(datepickerOptions);
	$( "#end" ).datepicker(datepickerOptions);
	$( "#start" ).datepicker("option", "onClose", function(selectedDate) {
		$("#end").datepicker("option", "minDate", selectedDate);
	});
	$( "#end" ).datepicker("option", "onClose", function(selectedDate) {
		$("#start").datepicker("option", "maxDate", selectedDate);
	});
	
	createCharts();
	$("#stats-filter-btn").click(createCharts);
});

function createCharts() {

	var start = $("#start").datepicker('getDate');
	var end = $("#end").datepicker('getDate');

	var y = start.getFullYear();
	var m = start.getMonth() +1;
	m = pad(m, 2);
	var d = start.getDate();
	d = pad(d, 2);
	start = y + "-" + m + "-" + d;
	
	y = end.getFullYear();
	m = end.getMonth() +1;
	m = pad(m, 2);
	d = end.getDate();
	d = pad(d, 2);
	end = y + "-" + m + "-" + d;
	
	var dept = $("#department").val();
	var category = $("#categories").val();
	var name = $("#dept_shortname").val();
	if (category != 0) {
		$('#back-link').attr('href', BASE_URL + name + '/dashboard?id='+ category);
	} else if (category == 0){
		$('#back-link').attr('href', BASE_URL + name + '/dashboard');
	}
	
	var checkboxes = document.getElementsByName('check');
	var mediums = [];
	for (var i=0; i<checkboxes.length;i++){
		if (checkboxes[i].checked){
			mediums.push(checkboxes[i].value);
		}
	}
	if (mediums.length > 0){
		createColumnChart(start, end, dept, category, mediums);
		createPieChart(start, end, dept, category, mediums);
		createCommentTable(start, end, dept, category, mediums)
		utils.hideMessage();
	} else {
		utils.showMessage("warning", "Please select at least one request method");
	}
}

function pad(n, width, z) {
	  z = z || '0';
	  n = n + '';
	  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function createColumnChart( start, end, dept, cat, mediums) {
	var $container = $("#container");
	var $studentcon = $("#studentcontainer");
	$.ajax({
		//url: BASE_URL + "api/getMonthlyColumnData/" + dept + "/" + year + "/" + month + "/" + cat + "/" + mediums,
		url: BASE_URL + "api/getMonthlyColumnData/" + dept + "/" + start + "/" + end + "/" + cat + "/" + mediums,
		method: "get",
		data: {},
		before: function() {
			$container.highcharts().destroy();
			$container.text("Loading...");
		},
		success: function(json) {
			$container.highcharts({
				chart: {
					type: 'column'
				},
				title: {
					text: '<b>Daily Breakdown for ' + json[1] + '</b>'  
				},
				subtitle: {
					text: "Number of Total Requests: " + json[3] 
				},
				tooltip: {
		            formatter: function () {
						return "<b>" + this.series.name + "</b> " + this.y + " out of " + this.point.stackTotal;
					}
		        },
				xAxis: {
					categories: json[2],
		        	labels: {
		        		rotation: -90
		        	}
				},
				legend: {
					labelFormatter: function() {
					    var total = 0;
					    for(var i=this.yData.length; i--;) { total += this.yData[i]; };
					    return  this.name + ' (' + total + ')';
					}
				},
				yAxis: {
					min: 0,
					minRange: 5,
					allowDecimals: false,
					title: { text: "Total Issues Reported" },
					stackLabels: { enabled: true }
				},
				plotOptions: {
		            column: {
		                stacking: 'normal'
		            },
					series: {
						events: {
							legendItemClick: function() {
								chart = $('#container').highcharts();
								var total = 0;
							    for(var i=this.yData.length; i--;) { total += this.yData[i]; };
								json[3] = this.visible ? json[3] - total: json[3] + total;
								chart.setTitle(undefined, {text: "Number of Total Requests: " + json[3]});
							}
						}
					}
		        },
				series: $.parseJSON(json[0]),
				credits: { enabled: false }
			});
			$studentcon.highcharts({
				chart: {
					type: 'column'
				},
				title: {
					text: '<b>Unique Requests</b>'  
				},
				subtitle: {
					text: "Number of Total Unique Requests: " + json[4] 
				},
				tooltip: {
		            formatter: function () {
		            	var chart = $("#container").highcharts();
						return "<b>" + this.series.name + "</b> " + this.y + " out of " + json[4];
					}
		        },
				xAxis: {
					categories: json[2],
		        	labels: {
		        		rotation: -90
		        	}
				},
				legend: {
					enabled: false
				},
				yAxis: {
					min: 0,
					minRange: 5,
					allowDecimals: false,
					title: { text: "Total Unique requests" },
					stackLabels: { enabled: true }
				},
				plotOptions: {
		            column: {
		                stacking: 'normal'
		            }
		        },
				series: [json[5]], //$.parseJSON(
				credits: { enabled: false }
			});
		},
		error: function(res) {
			utils.showMessage("danger", "Error: " + res.responseText);
		}	
	});	
}

function createPieChart(start, end, dept, catid, mediums) {
	var $container = $("#piecontainer");
	$.ajax({
		//url: BASE_URL + "api/getMonthlyPieData/" + dept + "/" + year + "/" + month + "/" + catid + "/" + mediums,
		url: BASE_URL + "api/getMonthlyPieData/" + dept + "/" + start + "/" + end + "/" + catid + "/" + mediums,
		method: "get",
		data: {},
		before: function() {
			$container.highcharts().destroy();
			$container.text("Loading...");
		},
		success: function(json) {
			$container.highcharts({
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: 1,
					plotShadow: false
				},
				title: { text: "<b>Total Breakdown for "+ json[3] + "</b>"},
				subtitle: { text: "between " + json[1] + " and " + json[2]},
				tooltip: {
		            pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
		        },
				plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                if (this.point.y > 0)
                                    return "<b>" + this.point.name + "</b>: " + (parseFloat)(this.point.percentage).toFixed(2) + "%";
                            },
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        },
                        showInLegend: true
                    }
                },
				series: [{
					type: "pie",
					name: "AA&CC Request Data",
					data: $.parseJSON(json[0])
				}],
				credits: { enabled: false }
			});
		},
		error: function(res) {
			utils.showMessage("danger", "Error: " + res.responseText);
		}	
	});
}

function createCommentTable(year, month, dept, cat, mediums){
	$.ajax({
		url: BASE_URL + "api/getMonthlyTableData/" + dept + "/" + year + "/" + month + "/" + cat + "/" + mediums,
		method: "get",
		data: {},
		success: function(json){
				var table = [];
				table.push("<thead><tr><th class='category_name header'>Category Name</th>");
				table.push("<th class='lower_category_name header'>Lower Category Name</th>");
				table.push("<th class='user_name header'>User Name</th>");
				table.push("<th class ='time_submitted header'>Time submitted</th>");
				table.push("<th class='request_method header'>Request Method</th>");
				table.push("<th class='comments header'>Comments</th></tr></thead>");
				for ( var i = 0; i < json.length; i++){
					table.push("<tr><td>" + json[i].Category_Name + " </td>");
					table.push("<td>" + json[i].Lower_Category_Name + " </td>");
					table.push("<td>" + json[i].lastname + ", " + json[i].firstname + "</td>");
					table.push('<td>' +json[i].Time_Submitted + " </td>");
					table.push('<td>' + json[i].Request_Method + " </td>");
					table.push('<td>' + json[i].comments + " </td></tr>");
				}
				$("#comment-table").html(table.join(""));
				$("#comment-table").tablesorter();
		}
	});
}
