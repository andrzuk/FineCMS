/*
 *  Custom JavaScript functions - DEFAULT
 */

$(document).ready(function() {
  $('div.page-content').css({ 'background-color': '#f0f3f6' });
});

function ajax_load_static()
{
	var selector = document.getElementById('image_selector');
	var sel_item = selector.options[selector.selectedIndex].value;
	var sel_text = selector.options[selector.selectedIndex].text;

	if (sel_item)
	{
		var request_object = new XMLHttpRequest();
		request_object.open('POST', 'application/lib/ajax/get_image.php', true);
		request_object.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		request_object.onreadystatechange = function() {
			if (request_object.readyState == 4 && request_object.status == 200)
			{
				document.getElementById('image_container').innerHTML = request_object.responseText;
			}
		}
		send_params = 'id=' + sel_item + '&name=' + sel_text;
		request_object.send(send_params);
		document.getElementById('image_container').innerHTML = '<img src="img/ajax/spinner.png" class="ListImage" style="width: 100%; height: 100%;" alt="Loading..." />';
	}
}

function ajax_load()
{
	var selector = document.getElementById('image_selector');
	var sel_item = selector.options[selector.selectedIndex].value;

	if (sel_item)
	{
		var request_object = new XMLHttpRequest();
		request_object.open('GET', 'application/lib/ajax/get_image_data.php?id=' + sel_item, true);
		request_object.setRequestHeader("Content-type", "text/plain");
		request_object.onreadystatechange = function() {
			if (request_object.readyState == 4 && request_object.status == 200)
			{
				document.getElementById("image_container").innerHTML = '<img class="ListImage" style="width: 100%; height: 100%;" src="data:image/jpeg;base64,' + request_object.responseText + '"/>';
			}
		}
		request_object.send(null);
	}
}

function disable_buttons()
{
	var elements = document.getElementsByTagName('input');

	for (i = 0; i < elements.length; i++)
	{
		var element = elements[i];

		if (element.type == 'submit') element.disabled = true;
	}
}

function disable(obj)
{
	obj.disabled = true;
}
