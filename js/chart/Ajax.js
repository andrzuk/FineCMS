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
				labels : labels,
				datasets : [
					{
						fillColor : "rgba(151,187,205,0.5)",
						strokeColor : "rgba(151,187,205,0.8)",
						highlightFill : "rgba(151,187,205,0.75)",
						highlightStroke : "rgba(151,187,205,1)",
						data : counters
					},
				]
			}
			if (i > 0) {
				window.myBar = new Chart(ctx).Line(statChartData, {
					animation: true,
					showTooltips: false,
					responsive: true,
					bezierCurve: false
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
				labels : labels,
				datasets : [
					{
						fillColor : "rgba(151,187,205,0.5)",
						strokeColor : "rgba(151,187,205,0.8)",
						highlightFill : "rgba(151,187,205,0.75)",
						highlightStroke : "rgba(151,187,205,1)",
						data : counters
					},
				]
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
