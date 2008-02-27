
<?php

require_once 'user.php';
require_once '../lib/config.php';

if (getRequestParameter("saving_user_data")){

	$new_user = (getRequestParameter("act") == 'new_user');

	$user->name = getRequestParameter("name");
	if($new_user){
		$user->login = getRequestParameter("login");
		$user->password = getRequestParameter("password");
		$user->password_cp = getRequestParameter("password_cp");
	}
	$user->address = getRequestParameter("address");
	$user->city = getRequestParameter("city");
	$user->email = getRequestParameter("email");

	if(!$new_user){
		global $USER;

		$USER->name = $user->name;
		$USER->address = $user->address;
		$USER->city = $user->city;
		$USER->email = $user->email;
		$ret = DBSaveUserData($USER, $new_user);
	}else{
		$ret = DBSaveUserData($user, $new_user);
	}
	if(is_string($ret)){

		echo getPageHeader('Novo usuário');
		PrintAlert($ret, 'center');
		echo getUserEditForm(true, $user);
		echo getPageFooter();
	}

}else if (getRequestParameter("act") == 'new_user'){
	echo getPageHeader('Novo usuário');
	echo getUserEditForm(true);
	echo getPageFooter();
}else if (getRequestParameter("act") == 'edit_user'){
	echo getPageHeader('Editing profile');
	echo getUserEditForm(false, $USER);
	echo getPageFooter();
}else if (getRequestParameter("act") == 'logout'){

	$_SESSION = array();
	session_destroy();

	Redirect("login/index.php");

}



?>