<?php 
require_once ('core/init.php');
$user = new Controller();
$user->logout();

Redirect::to('LoginAuthentication.php');
 ?>