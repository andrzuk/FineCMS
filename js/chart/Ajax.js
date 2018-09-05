/*
 * Chart.js / AJAX
 */

function update_chart(period)
{
	console.log("Period: %s.", period);
	
	var i = 0;
	var labels = new Array();
	var counters = new Array();
	var xhttp = null;
	
	if (window.XMLHttpRequest) {
		xhttp = new XMLHttpRequest();
	}
	else {
		xhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			var response_info = xhttp.responseXML.getElementsByTagName("period");
			var date_from = response_info[0].getElementsByTagName("date_from")[0].childNodes[0].nodeValue;
			var date_to = response_info[0].getElementsByTagName("date_to")[0].childNodes[0].nodeValue;
			var response_elements = xhttp.responseXML.getElementsByTagName("item");
			for (i = 0; i < response_elements.length; i++) {
				labels[i] = response_elements[i].getElementsByTagName("date_label")[0].childNodes[0].nodeValue;
				counters[i] = response_elements[i].getElementsByTagName("date_counter")[0].childNodes[0].nodeValue;
			}
			console.log("Received elements: %d.", response_elements.length);
			document.getElementById("period").innerHTML = date_from + " ~ " + date_to;
			var canvas = document.getElementById("chart");
			var ctx = canvas.getContext("2d");
			var statChartData = {
				labels: labels,
				datasets: [{
					fillColor: "rgba(255, 206, 86, 0.25)",
					strokeColor: "rgba(151, 187, 205, 1.0)",
					highlightFill: "rgba(151, 187, 205, 0.75)",
					highlightStroke: "rgba(151, 187, 205, 1.0)",
					data: counters
				}]
			}
			if (i > 0) {
				window.myBar = new Chart(ctx).Line(statChartData, {
					animation: true,
					showTooltips: false,
					responsive: true,
					bezierCurve: true,
					pointDot: true,
					pointDotRadius: 2
				});
			}
			else {
				ctx.clearRect(0, 0, canvas.width, canvas.height);
				var image = new Image();
				image.onload = function () {
					ctx.drawImage(image, (canvas.width - image.width) / 2, (canvas.height - image.height) / 2);
				};
				image.src = "./img/empty_chart.png";
			}
		}
	};
	xhttp.open("GET", "./stat/get_chart_data.php?type=" + period, true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send();
}

function horizontal_chart()
{
	var i = 0;
	var labels = new Array();
	var counters = new Array();
	var xhttp = null;
	
	if (window.XMLHttpRequest) {
		xhttp = new XMLHttpRequest();
	}
	else {
		xhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			var response_elements = xhttp.responseXML.getElementsByTagName("item");
			for (i = 0; i < response_elements.length; i++) {
				labels[i] = response_elements[i].getElementsByTagName("ip_label")[0].childNodes[0].nodeValue;
				counters[i] = response_elements[i].getElementsByTagName("ip_counter")[0].childNodes[0].nodeValue;
			}
			console.log("Received elements: %d.", response_elements.length);
			var canvas = document.getElementById("chart");
			var ctx = canvas.getContext("2d");
			var statChartData = {
				labels: labels,
				datasets: [{
					fillColor: "rgba(255, 206, 86, 0.25)",
					strokeColor: "rgba(151, 187, 205, 1.0)",
					highlightFill: "rgba(151, 187, 205, 0.75)",
					highlightStroke: "rgba(151, 187, 205, 1.0)",
					data: counters
				}]
			}
			if (i > 0) {
				window.myBar = new Chart(ctx).HorizontalBar(statChartData, {
					animation: true,
					showTooltips: false,
					responsive: true
				});
			}
			else {
				ctx.clearRect(0, 0, canvas.width, canvas.height);
				var image = new Image();
				image.onload = function () {
					ctx.drawImage(image, (canvas.width - image.width) / 2, (canvas.height - image.height) / 2);
				};
				image.src = "./img/empty_chart.png";
			}
		}
	};
	xhttp.open("GET", "./stat/get_stat_data.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send();
}

function pressure_chart()
{
	var lastId = 0;
	var data_SYS = [], data_DIA = [], data_Pulse = [];
	
	$.ajax({
		type: 'GET',
		url: './ajax/get_pressure_data.php',
		success: function(data) {
			if (!jQuery.isEmptyObject(data)) {
				$.each(data, function(i, pomiar) {
					data_SYS.push({ x: i + 1, y: pomiar.sys });
					data_DIA.push({ x: i + 1, y: pomiar.dia });
					data_Pulse.push({ x: i + 1, y: pomiar.pulse });
					lastId = i + 1;
				});
				myChart.update();
			}
		}
	});
	
	var ctx = document.getElementById("chart");
	var myChart = new Chart(ctx, {
		type: 'line',
		data: {
			datasets: [
			  {
				label: 'SYS',
				backgroundColor: 'rgba(255, 99, 132, 0.2)',
				borderColor: 'rgba(255,99,132,1)',
				fill: false,
				data: data_SYS
			  },
			  {
				label: 'DIA',
				backgroundColor: 'rgba(54, 162, 235, 0.2)',
				borderColor: 'rgba(54, 162, 235, 1)',
				fill: false,
				data: data_DIA
			  },
			  {
				label: 'Pulse',
				backgroundColor: 'rgba(255, 159, 64, 0.2)',
				borderColor: 'rgba(255, 159, 64, 1)',
				fill: false,
				data: data_Pulse
			  }
			]
		},
		options: {
			scales: {
				xAxes: [{
					type: 'linear',
					position: 'bottom'
				}]
			}
		}
	});
	
	$("input#save").on("click", function() {
		var season = $("select#season").val();
		var sys = parseInt($("input#sys").val());
		var dia = parseInt($("input#dia").val());
		var pulse = parseInt($("input#pulse").val());
		$.ajax({
			type: 'POST',
			url: './ajax/add_pressure_data.php',
			data: { season: season, sys: sys, dia: dia, pulse: pulse },
			success: function(response) {
				if (response.success) {
					data_SYS.push({ x: lastId + 1, y: sys });
					data_DIA.push({ x: lastId + 1, y: dia });
					data_Pulse.push({ x: lastId + 1, y: pulse });
					data_SYS.splice(0, 1);
					data_DIA.splice(0, 1);
					data_Pulse.splice(0, 1);
					for (var i = 0; i < lastId; i++) {
						data_SYS[i].x--;
						data_DIA[i].x--;
						data_Pulse[i].x--;
					}
					myChart.update();
					document.getElementById("avg-sys").innerHTML = response.average.sys;
					document.getElementById("avg-dia").innerHTML = response.average.dia;
					document.getElementById("avg-pulse").innerHTML = response.average.pulse;
					document.getElementById("season").value = season == 'R' ? 'W' : 'R';
					document.getElementById("sys").value = "";
					document.getElementById("dia").value = "";
					document.getElementById("pulse").value = "";
					$("div#message-success").text(response.message);
					$("div#message-success").css({ display: 'block' });
					$("div#message-error").css({ display: 'none' });
				}
				else {
					$("div#message-error").text(response.message);
					$("div#message-success").css({ display: 'none' });
					$("div#message-error").css({ display: 'block' });
				}
			}
		});
	});
}
