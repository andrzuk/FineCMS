<?php

$main_layout_content = '

<html>
  <head>
    <meta charset="utf-8">
    <meta name="keywords" content="'.$this->get_metadata('main_keywords').'" />
    <meta name="description" content="'.$this->get_metadata('main_description').'" />
    <title>'.$this->get_metadata('main_title').'</title>
    <link href="css/'.$this->get_layout().'.css" rel="stylesheet">
    <link href="gallery/logo/favicon.ico" rel="icon">
    <link href="gallery/logo/favicon.ico" rel="shortcut icon"> 
  </head>
  <body>
      <div class="page-header">
        <span class="logo">
          <a href="index.php"><img src="'.$this->get_logo().'" id="logo" alt="logo" /></a>
        </span>
        <span class="header-links">
          <div class="links-list">
            '.$this->get_links().'
          </div>
          <div class="user-status">
            '.$this->get_logged().'
          </div>
          <div class="page-path">
            '.$this->get_path().'
          </div>
        </span>
      </div>

      <div class="page-content">
        '.$this->get_content().'
      </div>

      <div class="page-footer">
        '.$this->get_footer().'
      </div>

  </body>
</html>

';

?>

