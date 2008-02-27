<?php


//require_once dirname(__FILE__).'/lib/config.php';
//global $config, $USER, $SESSION;
//
//echo " id: $USER->id <BR />
//id: $USER->name <BR />
//	valid: $SESSION->valid <BR />
//";



session_name("testedesessao");
session_start(); // iniciamos a sessão aqui, podendo usar também session_resgiter(); que ficaria implicita a chamada a session start()
if (!isset($_SESSION['count'])) { // aqui, testamos se a sessão cujo valor 'count', já existe
	$_SESSION['count'] = 0;// se não (!), ele inicia a sessão com o valor 'count'
	$s = &$_SESSION['count'];
} else {
	$s = &$_SESSION['count'];
	$s++;
	//$_SESSION['count']++; // se já existe a sessão, ele incremeta o contador da sessão (++)
}

echo $s;




?>