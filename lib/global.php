<?php

//global $db;
//
define('log_type_information',0);
define('log_type_warning',1);
define('log_type_error',2);

require_once 'config.php';
//function &getDatabase(){
//
//	require 'lib/Database.class.php';
//
//	static $db;
//
//	//session_register($db);
//
//	if(!isset($db)){
//		//echo 'cria';
//		$db = new Database();
//
//	}else{
//		//echo 'Já tava lah';
//	}
//	return $db;
//
//}

function getString($key){

	global $config, $string;

	require_once_relative('lang/'. $config->language . '.php' );

	$ret = null;

	if(array_key_exists($key, $string)){
		$ret = $string[$key];
	}

	if(!hasValue($ret)){
		require_once_relative('lib/htmlbuild.php');
		//$ret = "["<a href='". $config->www_root .  ."'>$key</a>]";
		$ret = "[" .  getLinkHtml('lang/langedit.php?lang_key=' . $key , $key)."]";
	}

	return $ret;
}


function getConfig($key){



	$ret = $config[$key];

	return $ret;
}

function debugging($str, $type = log_type_information){

	file_put_contents("c:\\tmp\\log.txt", $str . "\n", FILE_APPEND );
	//echo ;
	//printf($str);
	//TODO: Store data;

}


/**
 * Generate and return a random string of the specified length.
 *
 * @param int $length The length of the string to be created.
 * @return string
 */
function random_string ($length=15) {
	$pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$pool .= 'abcdefghijklmnopqrstuvwxyz';
	$pool .= '0123456789';
	$poollen = strlen($pool);
	mt_srand ((double) microtime() * 1000000);
	$string = '';
	for ($i = 0; $i < $length; $i++) {
		$string .= substr($pool, (mt_rand()%($poollen)), 1);
	}
	return $string;
}

function getFormatedDate($date, $format = "d/m/Y h:i:s"){

	$ret = '';

	if(is_numeric($date)){
		$ret = date($format, $date);
	}else{
		$ret = date($format, strtotime($date));
	}
	return $ret;
	//TODO: Cada país deve ter um formato de data e hora.
	//return $date;
}

function getCurrentDateTime(){
	//return time();
	//return date("d/m/Y h:i:s");
	return date("Y-m-d h:i:s");

	// return date_create();
}

function getRequestParameter($key, $required = false){
	$ret = NULL;
	try{
		if(array_key_exists($key, $_REQUEST)){
			$ret = $_REQUEST[$key];
		}
	}
	catch(Exception $e){
		$ret = null;
	}
	return $ret;
}


function UserLogged(){

	global $USER;

	if(!VerifySession()){
		return false;
	}

	if(isset($USER->id)){
		return true;
	}else{
		debugging("Usuário podre!");
		return false;
	}
}

function hasValue($value){
	if(isset($value)){

		$value = trim($value);

		if(is_numeric($value) && $value == 0) {
			return false;
		}
		if($value !== ''){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}

function Redirect($RelativeUrl, $Message = '', $Timeout = 0){
	//ob_start();
	global $config;

	//ob_flush();

	if(substr($RelativeUrl,0,7) == "http://"){
		$fullurl =$RelativeUrl;
	}
	else{
		$fullurl =$config->www_root.$RelativeUrl;
	}

	$delay = $Timeout;

	//try header redirection first
	//@header($_SERVER['SERVER_PROTOCOL'] . ' 303 See Other'); //302 might not work for POST requests, 303 is ignored by obsolete clients
	if($delay == 0){
		@header('Location: '. $fullurl);
	}
	//another way for older browsers and already sent headers (eg trailing whitespace in config.php)
//	echo '	<html> 	<head> ';
	@header('Content-Type: text/html; charset=utf-8');
	//echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	@header('http-equiv="refresh" content="'. $delay .'; url='. $fullurl .'"');
	//echo '<meta http-equiv="refresh" content="'. $delay .'; url='. $fullurl .'" />';
	if($delay == 0){
		echo '<script type="text/javascript">'. "\n" .'//<![CDATA['. "\n". "location.replace('". $fullurl . "');". "\n". '//]]>'. "\n". '</script>';   // To cope with Mozilla bug
	}else{
		echo '
			<script type="text/javascript">
			//<![CDATA[
			
			  function redirect() {
			      document.location.replace("'. $fullurl .'");
			  }
			  setTimeout("redirect()", '. ($delay * 1000) .');
			//]]>
			</script>
 			';
	}
	echo '<link href="'.$config->www_root.'/css/theme.css" rel="stylesheet" type="text/css" />';
	
//	echo '	</head> ';
	echo '<div style="text-align:center">';
	echo '<div><h2>'. $Message .'</h2></div>';
	echo '<div><a href="'. $fullurl .'">'. getString('continue') .'</a></div>';
	echo '</div>';
//	echo '</html> ';
	die;
}

function VerifySession(){

	global $SESSION;

	if(isset($SESSION->valid)){

		return true;

	}else{
		//		ob_clean();
		//		require $config->local_root.'login/index.php';
		//		die;
		debugging("Sessão podre!");
		//Redirect('login/index.php');
		return false;
	}
}

function require_once_relative($path){
	global $config;
	require_once  $config->local_root . $path;
}

function require_relative($path){
	global $config;
	require  $config->local_root . $path;
}

?>