<?php

$sql_script = array(
	array(
		'drop_constraints' => array(
			'ALTER TABLE `images` DROP FOREIGN KEY `fk_images_users`;',
			'ALTER TABLE `categories` DROP FOREIGN KEY `fk_categories_users`;',
			'ALTER TABLE `pages` DROP FOREIGN KEY `fk_pages_users`;',
			'ALTER TABLE `user_roles` DROP FOREIGN KEY `fk_roles_users`;',
			'ALTER TABLE `user_roles` DROP FOREIGN KEY `fk_roles_functions`;',
			'ALTER TABLE `archives` DROP FOREIGN KEY `archives_users`;',
			'ALTER TABLE `archives` DROP FOREIGN KEY `archives_pages`;',
		),
	),
	array(
		'drop_tables' => array(
			'DROP TABLE IF EXISTS `admin_functions`;',
			'DROP TABLE IF EXISTS `archives`;',
			'DROP TABLE IF EXISTS `categories`;',
			'DROP TABLE IF EXISTS `configuration`;',
			'DROP TABLE IF EXISTS `excludes`;',
			'DROP TABLE IF EXISTS `hosts`;',
			'DROP TABLE IF EXISTS `images`;',
			'DROP TABLE IF EXISTS `logins`;',
			'DROP TABLE IF EXISTS `pages`;',
			'DROP TABLE IF EXISTS `searches`;',
			'DROP TABLE IF EXISTS `users`;',
			'DROP TABLE IF EXISTS `user_messages`;',
			'DROP TABLE IF EXISTS `user_roles`;',
			'DROP TABLE IF EXISTS `visitors`;',
		),
	),
	array(
		'create_tables' => array(
			"
				CREATE TABLE IF NOT EXISTS `admin_functions` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `function` varchar(128) NOT NULL,
				  `meaning` varchar(512) NOT NULL,
				  `module` varchar(32) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `module` (`module`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;			
			",
			"
				CREATE TABLE IF NOT EXISTS `archives` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `page_id` int(11) unsigned NOT NULL,
				  `main_page` tinyint(1) NOT NULL,
				  `system_page` tinyint(1) NOT NULL,
				  `category_id` int(11) unsigned NOT NULL,
				  `title` varchar(512) CHARACTER SET utf8 NOT NULL,
				  `contents` longtext CHARACTER SET utf8,
				  `description` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
				  `author_id` int(11) unsigned NOT NULL,
				  `visible` tinyint(1) NOT NULL,
				  `modified` datetime NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `page_id` (`page_id`),
				  KEY `fk_pages_users` (`author_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;			
			",
			"
				CREATE TABLE IF NOT EXISTS `categories` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `parent_id` int(11) unsigned NOT NULL,
				  `section` tinyint(1) NOT NULL,
				  `permission` int(11) NOT NULL,
				  `item_order` int(11) NOT NULL,
				  `caption` varchar(128) CHARACTER SET utf8 NOT NULL,
				  `link` varchar(1024) CHARACTER SET utf8 NOT NULL,
				  `page_id` int(11) unsigned NOT NULL,
				  `visible` tinyint(1) NOT NULL,
				  `target` tinyint(1) NOT NULL,
				  `author_id` int(11) unsigned NOT NULL,
				  `modified` datetime NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `page_id` (`page_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `configuration` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `key_name` varchar(30) NOT NULL,
				  `key_value` varchar(1024) NOT NULL,
				  `meaning` varchar(128) DEFAULT NULL,
				  `field_type` int(11) NOT NULL,
				  `active` tinyint(1) NOT NULL,
				  `modified` datetime NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `key` (`key_name`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `excludes` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `visitor_ip` varchar(20) NOT NULL,
				  `active` tinyint(1) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `visitor_ip` (`visitor_ip`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `hosts` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `server_ip` varchar(20) NOT NULL,
				  `server_name` varchar(256) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `server_ip` (`server_ip`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `images` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `owner_id` int(11) unsigned NOT NULL,
				  `file_format` varchar(32) NOT NULL,
				  `file_name` varchar(512) NOT NULL,
				  `file_size` int(11) NOT NULL,
				  `picture_width` int(11) NOT NULL,
				  `picture_height` int(11) NOT NULL,
				  `modified` datetime NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `fk_images_users` (`owner_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `logins` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `agent` varchar(250) NOT NULL,
				  `user_ip` varchar(20) NOT NULL,
				  `user_id` int(11) unsigned NOT NULL,
				  `login` varchar(128) NOT NULL,
				  `password` varchar(128) NOT NULL,
				  `login_time` datetime NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `pages` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `main_page` tinyint(1) NOT NULL,
				  `system_page` tinyint(1) NOT NULL,
				  `category_id` int(11) unsigned NOT NULL,
				  `title` varchar(512) CHARACTER SET utf8 NOT NULL,
				  `contents` longtext CHARACTER SET utf8,
				  `description` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
				  `author_id` int(11) unsigned NOT NULL,
				  `visible` tinyint(1) NOT NULL,
				  `modified` datetime NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `category_id` (`category_id`),
				  KEY `fk_pages_users` (`author_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `searches` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `agent` varchar(250) NOT NULL,
				  `user_ip` varchar(20) NOT NULL,
				  `search_text` varchar(128) NULL,
				  `search_time` datetime NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `users` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `user_login` varchar(32) NOT NULL,
				  `user_password` varchar(48) NOT NULL,
				  `user_name` varchar(64) NOT NULL,
				  `user_surname` varchar(128) NOT NULL,
				  `email` varchar(128) NOT NULL,
				  `status` tinyint(2) NOT NULL DEFAULT '3',
				  `registered` datetime NOT NULL,
				  `logged_in` datetime NOT NULL,
				  `modified` datetime NOT NULL,
				  `logged_out` datetime NOT NULL,
				  `active` tinyint(1) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `user_login` (`user_login`),
				  UNIQUE KEY `email` (`email`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `user_messages` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `client_ip` varchar(20) NOT NULL,
				  `client_name` varchar(128) NOT NULL,
				  `client_email` varchar(256) NOT NULL,
				  `message_content` longtext NOT NULL,
				  `requested` tinyint(1) NOT NULL,
				  `send_date` datetime NOT NULL,
				  `close_date` datetime NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `user_roles` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) unsigned NOT NULL,
				  `function_id` int(11) unsigned NOT NULL,
				  `access` tinyint(1) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `user_function` (`user_id`,`function_id`),
				  KEY `fk_roles_users` (`user_id`),
				  KEY `fk_roles_functions` (`function_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;
			",
			"
				CREATE TABLE IF NOT EXISTS `visitors` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `visitor_ip` varchar(20) NOT NULL,
				  `http_referer` text,
				  `request_uri` text NOT NULL,
				  `visited` datetime NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;
			",
		),
	),
	array(
		'fill_data' => array(
			"
				INSERT INTO `admin_functions` (`id`, `function`, `meaning`, `module`) VALUES
				(1, 'admin', 'Admin Panel', 'admin'),
				(2, 'config', 'Konfiguracja', 'config'),
				(3, 'template', 'Szablon strony', 'template'),
				(4, 'style', 'Styl strony', 'style'),
				(5, 'users', 'Użytkownicy', 'users'),
				(6, 'ACL', 'Access Control List', 'roles'),
				(7, 'visitors', 'Odwiedziny', 'visitors'),
				(8, 'gallery', 'Galeria', 'images'),
				(9, 'categories', 'Kategorie', 'categories'),
				(10, 'pages', 'Strony', 'pages'),
				(11, 'sites', 'Opisy', 'sites'),
				(12, 'messages', 'Wiadomości', 'messages'),
				(13, 'searches', 'Wyszukiwania', 'searches'),
				(14, 'logins', 'Logowania', 'logins'),
				(15, 'excludes', 'Wykluczenia adresów', 'excludes');
			",
			"
				INSERT INTO `configuration` (`id`, `key_name`, `key_value`, `meaning`, `field_type`, `active`, `modified`) VALUES
				(1, 'logo_image', 'gallery/logo/1', 'obrazek logo w nagłówku strony', 1, 1, :save_time),
				(2, 'main_title', :main_title, 'tytuł strony internetowej', 2, 1, :save_time),
				(3, 'main_description', :main_description, 'meta tag descriptions nagłówka strony', 2, 1, :save_time),
				(4, 'main_keywords', :main_keywords, 'meta dane keywords strony internetowej', 2, 1, :save_time),
				(5, 'main_author', 'application logic & design: Andrzej Żukowski', 'autor serwisu - logiki biznesowej i designu', 2, 1, :save_time),
				(6, 'base_domain', :base_domain, 'domena (adres) serwisu', 1, 1, :save_time),
				(7, 'page_footer', '<a href=\"https://www.facebook.com/WlasnaStronaInternetowa\" target=\"_blank\"><img src=\"img/footer/facebook.png\" alt=\"facebook\" title=\"Znajdź nas na Facebooku\" /></a> <a href=\"http://www.linkedin.com/profile/view?id=93739159&trk=hb_tab_pro_top\" target=\"_blank\"><img src=\"img/footer/linkedin.png\" alt=\"linkedin\" title=\"Znajdź nas na LinkedIn\" /></a> <a href=\"https://twitter.com/andy_zukowski\" target=\"_blank\"><img src=\"img/footer/twitter.png\" alt=\"twitter\" title=\"Znajdź nas na Twitterze\" /></a> <a href=\"https://plus.google.com/u/0/113303165754486219878\" target=\"_blank\"><img src=\"img/footer/google_plus.png\" alt=\"google+\" title=\"Znajdź nas na Google Plus\" /></a><div class=\"FooterCopyright\">&copy; {_year_} MyMVC <a href=\"https://plus.google.com/113303165754486219878?rel=author\" class=\"FooterLink\" target=\"_blank\">Andrzej Żukowski</a></div>', 'treść w stopce strony', 2, 1, :save_time),
				(8, 'social_buttons', '<span class=\"distance\"></span><span class=\"st_twitter\" displayText=\"&nbsp;\" st_url=\"{{_url_}}\" st_title=\"{{_title_}}\" title=\"Udostępnij na Twitterze\"></span><span class=\"st_googleplus\" displayText=\"&nbsp;\" st_url=\"{{_url_}}\" st_title=\"{{_title_}}\" title=\"Udostępnij w Google+\"></span><span class=\"st_facebook\" displayText=\"&nbsp;\" st_url=\"{{_url_}}\" st_title=\"{{_title_}}\" title=\"Udostępnij na Facebooku\"></span><span class=\"st_linkedin\" displayText=\"&nbsp;\" st_url=\"{{_url_}}\" st_title=\"{{_title_}}\" title=\"Udostępnij w LinkedIn\"></span>', 'przyciski społecznościowe w paskach tytułu artykułów', 2, 1, :save_time),
				(9, 'page_template_default', 'default', 'domyślny (nawigacja i treść) szablon strony (nazwa templatki i stylu)', 1, 1, :save_time),
				(10, 'page_template_extended', 'extended', 'rozszerzony (nawigacja, kategorie i treść) szablon strony (nazwa templatki i stylu)', 1, 1, :save_time),
				(11, 'page_template_admin', 'admin', 'administracyjny (tylko treść) szablon strony (nazwa templatki i stylu)', 1, 1, :save_time),
				(12, 'links_panel_visible', 'true', 'panel górny z linkami widoczny', 3, 1, :save_time),
				(13, 'navbar_panel_visible', 'true', 'górny panel nawigacji widoczny', 3, 1, :save_time),
				(14, 'categories_panel_visible', 'true', 'boczny panel nawigacji widoczny', 3, 1, :save_time),
				(15, 'path_panel_visible', 'true', 'panel ze scieżką strony widoczny', 3, 1, :save_time),
				(16, 'logged_panel_visible', 'true', 'panel z nazwiskiem zalogowanego usera widoczny', 3, 1, :save_time),
				(17, 'options_panel_visible', 'true', 'panel menu kontekstowego widoczny', 3, 1, :save_time),
				(18, 'display_list_rows', '20', 'liczba wierszy listy na jednej stronie', 1, 1, :save_time),
				(19, 'description_length', '50', 'maksymalna długość opisu pozycji na liście znalezionych', 1, 1, :save_time),
				(20, 'page_pointer_band', '4', 'liczebność (połowa) paska ze wskaźnikami stron w pasku nawigacji', 1, 1, :save_time),
				(21, 'send_new_message_report', 'true', 'wysyłanie e-mailem raportów do admina o pojawieniu się nowej wiadomości', 3, 1, :save_time),
				(22, 'email_sender_name', 'Mail Manager', 'nazwa konta e-mailowego serwisu', 1, 1, :save_time),
				(23, 'email_sender_address', :email_sender_address, 'adres konta e-mailowego serwisu', 1, 1, :save_time),
				(24, 'email_admin_address', :email_admin_address, 'adres e-mail administratora serwisu', 1, 1, :save_time),
				(25, 'email_report_address', :email_report_address, 'adres e-mail odbiorcy raportów', 1, 1, :save_time),
				(26, 'email_report_subject', 'Raport serwisu', 'temat maila raportującego zdarzenie', 1, 1, :save_time),
				(27, 'email_report_body_1', 'Raport o zdarzeniu w serwisie', 'treść maila rapotującego - część przed zmiennymi', 2, 1, :save_time),
				(28, 'email_report_body_2', '(brak)', 'treść maila rapotującego - część za zmiennymi', 2, 1, :save_time),
				(29, 'email_remindpwd_subject', 'Nowe hasło do konta', 'temat generowanego maila z nowym hasłem', 1, 1, :save_time),
				(30, 'email_remindpwd_body_1', 'Na Twoją prośbę przesyłamy Ci nowe hasło logowania.', 'treść generowanego maila z nowym hasłem - przed hasłem', 2, 1, :save_time),
				(31, 'email_remindpwd_body_2', 'Zaloguj się, a następnie zmień hasło na swoje własne.', 'treść generowanego maila z nowym hasłem - za hasłem', 2, 1, :save_time);
			",
			"
				INSERT INTO `pages` (`id`, `main_page`, `system_page`, `category_id`, `title`, `contents`, `description`, `author_id`, `visible`, `modified`) VALUES
				(1, 1, 0, 0, 'Strona główna', '<h1>Witamy w systemie FineCMS!</h1><h3>Oddajemy w Państwa ręce nasz najnowszy produkt - System Zarządzania Treścią, czyli tzw. CMS (Content Management System).</h3><h4>Jest to system przeznaczony do zarządzania stroną internetową z poziomu przeglądarki internetowej.</h4><h3>Skąd nazwa FineCMS?</h3><p>Ponieważ jest to prosty, szybki, przyjazny dla użytkownika, a więc \"fajny\" CMS, oparty na autorskim frameworku w architekturze MVC (Model - View - Controller), w której zadania operacji na danych (model), przygotowania i prezentacji stron (widok) oraz sterowanie przepływem i logika aplikacji (kontroler) są od siebie odseparowane. Dzięki takiej budowie ułatwione jest utrzymanie i rozwój aplikacji. Dołączanie kolejnych funkcjonalności jest dziecinnie proste i polega na dodaniu trzech klas - po jednej na każdą warstwę - oraz oczywiście wypełnieniu ich właściwym do zaplanowanych zadań kodem.</p><h3>Czym się wyróżnia?</h3><p>System, który Państwo oglądają, służy do szybkiego stawiania stron www, ich łatwej rozbudowy i aktualizacji. Nasz CMS wyróżnia się wyjątkową prostotą i szybkością działania. Obsługa jest łatwa, intuicyjna i przyjemna. Podobnie jak w przypadku innych produktów tej klasy, możliwości naszego systemu obejmują takie funkcje, jak tworzenie dowolnej ilości podstron, dynamiczne zarządzanie nawigacją strony (główne linki strony), budowanie własnej galerii zdjęć, system zarządzania kontami użytkowników oraz ich prawami dostępu do zasobów serwisu, czy wreszcie formularz kontaktowy wraz z systemem zarządzania nadesłanymi wiadomościami, wyszukiwarka artykułów, a nawet system raportowania różnych aspektów działania strony, np. śledzenie odwiedzin.</p><h3>Dlaczego warto zdecydować się na FineCMS?</h3><p>Wybór systemu FineCMS jako platformy dla swojej strony internetowej jest doskonałym posunięciem. Użytkownicy, którzy stale rozbudowują lub aktualizują własne strony, z pewnością już od pierwszego momentu odczują satysfakcję i komfort pracy z systemem. Jego prostota i minimalizm interfejsu stanowią ogromną zaletę, ponieważ nie ma tu niepotrzebnych, nigdy nie używanych funkcji, zaś nauka obsługi trwa praktycznie parę minut.</p>', 'Strona główna serwisu', 1, 1, :save_time),
				(2, 0, 1, 0, 'Kontakt', '<table align=\"center\" width=\"600\"><tr><td style=\"text-align: justify; padding: 0px 0px 20px 0px;\">Zachęcamy Państwa do kontaktu z nami. Proszę do nas napisać, wypełniając poniższy formularz. Każda wiadomość będzie uważnie przeczytana i rozpatrzona. Dziękujemy!</td></tr></table>', 'Kontakt z serwisem', 1, 1, :save_time),
				(3, 0, 0, 0, 'Regulamin serwisu', 'Regulamin serwisu.', 'Regulamin serwisu', 1, 1, :save_time),
				(4, 0, 0, 0, 'Polityka plików cookies', 'Polityka plików cookies.', 'Polityka plików cookies.', 1, 1, :save_time);
			",
			"
				INSERT INTO `users` (`id`, `user_login`, `user_password`, `user_name`, `user_surname`, `email`, `status`, `registered`, `logged_in`, `modified`, `logged_out`, `active`) VALUES
				(1, :admin_login, :admin_password, :first_name, :last_name, :email_admin_address, 1, :save_time, :save_time, :save_time, :save_time, 1);
			",
			"
				INSERT INTO `user_roles` (`id`, `user_id`, `function_id`, `access`) VALUES
				(1, 1, 1, 1),
				(2, 1, 2, 1),
				(3, 1, 3, 1),
				(4, 1, 4, 1),
				(5, 1, 5, 1),
				(6, 1, 6, 1),
				(7, 1, 7, 1),
				(8, 1, 8, 1),
				(9, 1, 9, 1),
				(10, 1, 10, 1),
				(11, 1, 11, 1),
				(12, 1, 12, 1),
				(13, 1, 13, 1),
				(14, 1, 14, 1),
				(15, 1, 15, 1);
			",
		),
	),
	array(
		'create_constraints' => array(
			'
				ALTER TABLE `images`
				  ADD CONSTRAINT `fk_images_users` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`);
			',
			'
				ALTER TABLE `categories`
				  ADD CONSTRAINT `fk_categories_users` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);
			',
			'
				ALTER TABLE `pages`
				  ADD CONSTRAINT `fk_pages_users` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);
			',
			'
				ALTER TABLE `user_roles`
				  ADD CONSTRAINT `fk_roles_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
				  ADD CONSTRAINT `fk_roles_functions` FOREIGN KEY (`function_id`) REFERENCES `admin_functions` (`id`);
			',
			'
				ALTER TABLE `archives`
				  ADD CONSTRAINT `archives_users` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
				  ADD CONSTRAINT `archives_pages` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`);
			',
		),
	),
);

?>
