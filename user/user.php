<?php

require_once '../edit/EditForm.class.php';
require_once '../lib/config.php';


function getUserEditForm($new = false, $user = null){

	$form = new EditForm();

	if($new){
		$form->setTitle(getString("new_user"));
		$form->AddField("act","","act","hidden","new_user");
	}else{
		$form->setTitle(getString("editting_user"));
		$form->AddField("act","","act","hidden","edit_user");
	}

	$form->setAction('index.php');
	if($user != null){
		$login = $user->login;
		$name = $user->name;
		$email = $user->email;
		$address = $user->address;
		$city = $user->city;
	}else{
		$login = '';
		$name = '';
		$email = '';
		$address = '';
		$city = '';
		
	}

	$form->AddField("login",getString("login"),"login",'text',$login);
	if($new){
		$form->AddField("password", getString("password"),"password","password");
		$form->AddField("password_cp", getString("password_ver"),"password_cp","password");
	}
	$form->AddField("name",getString("name"),"name",'text',$name);
	$form->AddField("email","E-mail","email",'text',$email);
	$form->AddField("address", getString("address"),"address",'text',$address);
	$form->AddField("city", getString("city"),"city",'text',$city);

	$form->AddField("saving_user_data","","saving_user_data","hidden","saving_user_data");
	
	
	//$form->AddField("id","Nome","id");

	$form->AddToolItem(getString("save"));

	$str = $form->getFormStart();
	$str .= $form->getString();
	$str .= $form->getFormEnd();
	return   $str;

}

function DBSaveUserData($user, $new_user){

	global $db, $USER;

	//if(!isset($USER->id)){

		if(!hasValue($user->name)){
			return 'Nome obrigatório';
		}
		if($new_user){

			if(!hasValue($user->login)){
				return 'Login obrigatório';
			}
			if(!hasValue($user->password)){
				return 'Senha obrigatória';
			}
			if($user->password != $user->password_cp){
				return "As senhas informadas precisam ser iguais.";
			}

			if($db->RecordExists('user', $user->login, 'login')){
				return "Login já está sendo usado por outro usuário, escolha outro login.";
			}
			if($db->RecordExists('user', $user->email, 'email')){
				return "Email já está sendo usado por outro usuário.";
			}
				
			$user->password = md5($user->password );

			//		$user->address
			//		$user->city
			$user->dt_inscr =  getCurrentDateTime();

			$user->money =  getConfig('default_start_money');
				
		}

		if(!hasValue($user->email)){
			return 'Email obrigatório';
		}

		if(!strripos($user->email, '@') || !strripos($user->email, '.')){
			return "Email inválido.";
		}





		$db->StoreRecord('user', $user);

		require '../index.php';

	//}else{
		//$USER->name = get

	//}



}

function DBGetUserCity($user){

	global $db;
	$sql = " select * from city where mayor_user = $user->id ";

	if($rs = $db->getResultSet($sql)){
		if($o = $rs->FetchNextObj()){

			return $o;
		}else{
			return false;
		}

	}else{
		return false;

	}

}

?>