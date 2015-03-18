<?php

session_start();

$_SESSION['install_mode'] = TRUE;

header('Location: ../index.php?route=install');
exit;

?>
