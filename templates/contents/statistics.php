<?php

$mode = isset($_GET['mode']) ? $_GET['mode'] : NULL;

if ($mode == 'ip')
{
	$main_template_content = $this->get_content() . $this->show_message() .
	'
	<script type="text/javascript">

		$(document).ready(function(){
			horizontal_chart();
		});

	</script>
	'
	;
}
else
{
	$main_template_content = $this->get_content() . $this->show_message() .
	'
	<script type="text/javascript">

		$(document).ready(function(){
			$("#months_1").click();
		});

	</script>
	'
	;
}

?>
