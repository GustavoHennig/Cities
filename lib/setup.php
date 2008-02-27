<?php
/**
 * setup.php - Sets up sessions, connects to databases ...
 */

//Guarda informações do usuário logado
global $USER;

//Grava a cidade corrente
global $CITY;
global $TOOLBAR;

//Configuração
global $config;

//Translation
global $string;

/**
 * Definition of session type
 * @global object(session) $SESSION
 */
global $SESSION;

/**
 * Definition of db type
 * @global object(db) $db
 */
global $db;


error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

/// 	First try to detect some attacks on older buggy PHP versions
if (isset($_REQUEST['GLOBALS']) || isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
	die('Fatal: Illegal GLOBALS overwrite attempt detected!');
}


//define("LOCAL_ROOT", dirname(__FILE__)."/../" );

date_default_timezone_set($config->date_time_zone);

require_once('Database.class.php'); // Cities Database

$db = new Database();


/// Carregar aqui as bibliotecas utilizadas por padrão/global
require_once('global.php');
require_once('htmlbuild.php');

//TODO: Test it
///// Set up session handling
//    if(empty($CFG->respectsessionsettings)) {
//
//            // Some distros disable GC by setting probability to 0
//            // overriding the PHP default of 1
//            // (gc_probability is divided by gc_divisor, which defaults to 1000)
//            if (ini_get('session.gc_probability') == 0) {
//                ini_set('session.gc_probability', 1);
//            }
//
//            if (!empty($CFG->sessiontimeout)) {
//                ini_set('session.gc_maxlifetime', $CFG->sessiontimeout);
//            }
//
//            if (!file_exists($CFG->dataroot .'/sessions')) {
//                make_upload_directory('sessions');
//            }
//            ini_set('session.save_path', $CFG->dataroot .'/sessions');
//
//    }


/// PHP bug
@ini_set('pcre.backtrack_limit', 20971520);  // 20 MB


/// Load up global environment variables
class object {};

//discard session ID from POST, GET and globals to tighten security,
//this session fixation prevention can not be used in cookieless mode
unset(${'cities_session'});
unset($_GET['cities_session']);
unset($_POST['cities_session']);

//ini_set('session.save_handler', 'files');
//ini_set('session.cookie_domain', 'gustavoh.co.cc');
session_name('cities_session');
session_set_cookie_params(0, "/cities/");
//session_cache_limiter('none');

require_once_relative('map/City.class.php');
require_once_relative('map/Toolbar.class.php');


@session_start();



if (! isset($_SESSION['SESSION'])) {
	$_SESSION['SESSION'] = new object();
	$_SESSION['SESSION']->session_test = random_string(10);
//	if (!empty($_COOKIE['cities_session_test'])) {
//		$_SESSION['SESSION']->has_timed_out = true;
//	}
//	setcookie('cities_session_test', $_SESSION['SESSION']->session_test, 0);
//	$_COOKIE['cities_session_test'] = $_SESSION['SESSION']->session_test;
	//echo "CRIU SESSÃO";
}else{
	debugging("SESSION jah estava lah.");
}

if (! isset($_SESSION['USER']))    {
	$_SESSION['USER'] = new object();
}




if (! isset($_SESSION['CITY']))    {
	$_SESSION['CITY'] = new City();
}

if (! isset($_SESSION['TOOLBAR']))    {
	$_SESSION['TOOLBAR'] = new Toolbar();
}



$SESSION = &$_SESSION['SESSION'];   // Makes them easier to reference
$USER    = &$_SESSION['USER'];
$CITY    = &$_SESSION['CITY'];
$TOOLBAR = &$_SESSION['TOOLBAR'];


///// now do a session test to prevent random user switching - observed on some PHP/Apache combinations,
///// disable checks when working in cookieless mode
//if (!empty($_COOKIE['cities_session_test'])) {
//	if ($SESSION != NULL) {
//		if (empty($_COOKIE['cities_session_test'])) {
//			echo "report_session_error();";
//		} else if (isset($SESSION->session_test) && $_COOKIE['cities_session_test'] != $SESSION->session_test) {
//			echo "report_session_error();";
//		}
//	}
//}

?>