

<?php


require_once 'lib/config.php';

if(UserLogged()){
	Redirect("map/view.php");
}else{
	Redirect("login/index.php");
}



?>