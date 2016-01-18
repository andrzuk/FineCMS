<?php

$main_template_content = $this->get_content() . $this->show_message() .
'
<script type="text/javascript">

$(document).ready(function(){
	$("#months_1").click();
});

</script>
'
;

?>
