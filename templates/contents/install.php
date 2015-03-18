<?php

$main_template_content = 

'
<h3 style="text-align: center; padding: 0px 0px 10px 0px; color: red;">Instalacja serwisu</h3>

<table width="100%">
	<tr class="install">
		<td style="vertical-align: top; padding: 0px 10px 0px 0px;">
			<p style="color: #0c0;">Pierwsze trzy kroki instalacji, tj. skopiowanie skryptów na serwer, utworzenie bazy danych oraz ustawienie połączenia z bazą danych, zostały wykonane poprawnie.</p>
			<p>Jednak serwis nie został jeszcze do końca zainstalowany. Aby ukończyć instalację serwisu, proszę wykonać kroki oznaczone kolorem czerwonym:</p>
			<ol>
				<li style="color: #ccc;">
					Za pomocą <b>FTP</b> wgrać skrypty aplikacji na swój serwer do katalogu <b>public_html</b>.
				</li>
				<li style="color: #c00;">
					Za pomocą <b>ChMod</b> zmienić atrybuty na wartość <b>777</b> dla katalogów: <ul><li><b>install</b>,</li><li><b>gallery/images</b>.</li></ul>
				</li>
				<li style="color: #ccc;">
					Utworzyć na swoim serwerze bazę danych (pustą). Należy zapamiętać podane przy zakładaniu bazy parametry dostępu, tj. <b>host</b>, <b>user</b>, <b>password</b> oraz <b>baza</b>. Dla podanego usera przydzielić następujące <b>uprawnienia</b>: <ul><li><b>Dane</b> - <b>SELECT</b>, <b>INSERT</b>, <b>UPDATE</b>, <b>DELETE</b>,</li><li><b>Struktura</b> - <b>CREATE</b>, <b>ALTER</b>, <b>INDEX</b>, <b>DROP</b>.</li></ul>
				</li>
				<li style="color: #ccc;">
					Wyedytować <b>plik konfiguracji</b> serwisu - sekcję dot. połączenia z bazą danych, podając w niej parametry <b>host</b>, <b>user</b>, <b>password</b> oraz <b>baza</b> takie same, jak podczas tworzenia bazy.
				</li>
				<li style="color: #ccc;">
					Uruchomić instalator serwisu, otwierając stronę <b>http://{domena_serwisu}/install</b>. Wprowadzić podstawowe informacje konfiguracyjne serwisu za pomocą formularza <b>Ustawienia serwisu</b>. Formularz będzie widoczny tylko wtedy, gdy w pliku konfiguracji zostanie prawidłowo ustawione połączenie z bazą danych.
				</li>
				<li style="color: #c00;">
					Po wypełnieniu i zapisaniu formularza cofnąć uprawnienia usera dot. <b>Struktury</b>, tj. <b>CREATE</b>, <b>ALTER</b>, <b>INDEX</b>, <b>DROP</b>. Następnie przejść do strony głównej serwisu <b>http://{domena_serwisu}</b>.
				</li>
				<li style="color: #c00;">
					Zalogować się na konto administratora, używając podanych w formularzu konfiguracyjnym <b>logina</b> oraz <b>hasła</b>, i rozpocząć zarządzanie serwisem.
				</li>
			</ol>
			<p style="color: #00c;">Gratulujemy doskonałego wyboru i życzymy przyjemnej obsługi systemu!</p>
		</td>
		<td style="vertical-align: top; padding: 5px 0px 0px 10px;">
			'.
			$this->get_content().
			'
		</td>
	</tr>
</table>
'.

$this->show_message();

?>
