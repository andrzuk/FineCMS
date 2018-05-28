<?php

$main_template_content = '

	<style>
		div.page-content { padding: 100px 20px 0px 20px; }
	</style>

	<table width="100%" style="margin: 0 0 50px 0;">
		<tr>
			<td>&nbsp;</td>
			<td colspan="2" style="border-bottom: 1px solid #ccc;">
				<h3 class="panel-title" style="padding-bottom: 30px; color: #c00; font-weight: bold;">
					<img src="img/32x32/webmaster_tools.png" alt="admin panel" />
					<span style="padding-left: 20px;">Panel administratora</span>
				</h3>
			</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="80%">
			'
				.$this->get_content().
			'
			</td>
			<td width="10%">&nbsp;</td>
		</tr>
	</table>
	
	'	.$this->show_message().
	'

';

?>

