<?php

$main_layout_content = '

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="'.$this->get_metadata('main_keywords').'" />
    <meta name="description" content="'.$this->get_metadata('main_description').'" />
    <meta name="author" content="'.$this->get_metadata('main_author').'" />
    <title>'.$this->get_metadata('main_title').'</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/grid.css" rel="stylesheet">
    <link href="css/prettify.css" rel="stylesheet">
    <link href="css/default.css" rel="stylesheet">
    <link href="css/'.$this->get_layout().'.css" rel="stylesheet">
    <link href="gallery/logo/favicon.ico" rel="icon">
    <link href="gallery/logo/favicon.ico" rel="shortcut icon"> 
    <base href="'.$this->get_metadata('base_domain').'" target="_self" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/'.$this->get_layout().'.js"></script>
  </head>

  <body>
		
    <div class="page-header">	
      <header>
        <nav role="navigation" class="navbar navbar-default navbar-fixed-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a id="page-logo-brand" href="index.php" class="navbar-brand"><span><img src="'.$this->get_logo().'" alt="'.$this->get_metadata('company_name').'"></span> &nbsp; '.$this->get_metadata('company_name').'</a>
            </div>
            '.$this->get_links().'
          </div>
        </nav>
      </header>
    </div>

    <div class="page-content">
      '.$this->get_options().
        $this->get_content().'
    </div>

    <div class="page-footer">
      '.$this->get_footer().'
    </div>
	
  </body>
  
</html>

';

?>

