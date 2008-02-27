<?php

require_once dirname(__FILE__).'/../lib/config.php';
global $config;

require_once_relative('/login/Login.class.php');

$username = getRequestParameter('username');
$password = getRequestParameter('password');

$login =  new Login();

if(hasValue($username) && hasValue($password)){
	if($login->UserLogin($username, $password)){
		Redirect('map/citylist.php');
		//require_relative('/map/view.php');
		
	}else{
		print_login_form($login, 'Usuário ou senha inválidos');	
	}
}else{
	print_login_form($login);
}

function print_login_form(&$login, $message=''){
		echo getPageHeader('Cities Login');
		echo '<p>'. $message .'</p>';
		echo $login->getLoginForm();
		echo getLinkHtml('user/index.php?act=new_user', getString("new_user?"), 'center');
		echo getPageFooter();	
}

?>

