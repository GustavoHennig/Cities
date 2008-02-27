<?php


require_once dirname(__FILE__).'/../lib/config.php';
global $config;



class Login{

	function UserLogin($login, $password){

		global $USER, $SESSION;
		if(!isset($login) || !isset($password)){
			echo 'Dados inválidos';
			return false;
		}else{

			$ret = $this->DBGetLogin($login, $password);
			
			if($ret){	$USER = $ret;
				$SESSION->valid = true;
				debugging('Sessão iniciada.');
				//require_relative('map/view.php');
				
				//fopen($config->wwwroot . "lib/cron.php", "r");
				$this->RunCron();
				return true;
			}
		}
		
		return false;
	}

	function RunCron(){
		require_relative('lib/cron.php');
	}
	
	function DBGetLogin($login, $password){
			
		//require_once_relative('lib/Database.class.php');
		
		global $db;
		//		$dbPass =  $db->getFieldValue('user', 'login', $login, 'password');
		//
		//		if($dbPass){
		//			return $dbPass === md5($password);
		//		}else{
		//			return false;
		//		}

		$sql = " select * from user where login = '$login'";

		$rs = &$db->getResultSet($sql);

		if($rs){
			if($o = $rs->FetchNextObj()) {
				if($o->password == md5($password)){
					$o->dt_last_access =  getCurrentDateTime();
					$db->StoreRecord('user', $o);
					return $o;
				}
			}
		}
		return false;

	}
	function getLoginForm(){
		
		require_once_relative ('edit/EditForm.class.php');

		$form = new EditForm();
		$form->setTitle(getString("login"));
		$form->setAction('index.php');

		$str = $form->getFormStart();

		$form->AddField("username",getString("user"),"username");
		$form->AddField("password",getString("password"),"password","password");

		$form->AddToolItem("Login");

		$str .= $form->getString();
		$str .= $form->getFormEnd();

		return $str;
	}
}

?>