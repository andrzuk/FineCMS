<?php

$main_template_content = 

$this->get_content() . 

'
<style>
	div.page-content { padding: 100px 20px 0px 20px; }
</style>
<p style="text-align: center; font-size: small;">
	<a href="index.php?route=login">Mam konto w serwisie. Przejdź do logowania.</a>
</p>
<p style="text-align: center; font-size: small;">
	<a href="index.php?route=password">Nie pamiętam hasła logowania. Zresetuj hasło.</a>
</p>
<script>
	$(document).ready(function() {
		setTimeout(function() {
			$("input#name").focus();
		}, 500);
	});
</script>
'
.

$this->show_message();

?>
