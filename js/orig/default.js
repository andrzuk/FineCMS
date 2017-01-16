/*
 *  Custom JavaScript functions - DEFAULT
 */

$(document).ready(function() {
  $('div.page-content').css({ 'background-color': '#f0f3f6' });
  $('a.logo img.img-logo').fadeOut(function() {
    $(this).fadeIn();
  });
});

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
