<?php

$main_template_content = 

$this->get_content() . 

'
<style>
	div.page-content { padding: 100px 20px 0px 20px; }
</style>
<p style="text-align: center; font-size: small;">
	<a href="index.php?route=password">Nie pamiętam hasła logowania. Zresetuj hasło.</a>
</p>
<p style="text-align: center; font-size: small;">
	<a href="index.php?route=register">Nie mam konta w serwisie. Zarejestruj konto.</a>
</p>
<script>
	$(document).ready(function() {
		setTimeout(function() {
			$("input#login").focus();
		}, 500);
	});
</script>
'
.

$this->show_message();

?>
