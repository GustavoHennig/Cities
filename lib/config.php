<?php

unset($config);

$config = new stdClass();

$config->language = 'en';
$config->map_size =  45;
$config->www_root = 'http://localhost/cities/';
//$config->www_root = 'http://192.168.224.2/cities/';
//$config->local_root = 'C:/_Desenv/Ides_Tools/php/e_workspace/cities/';
$config->local_root = dirname(__FILE__)."/../";//'C:/wamp/www/cities/';
$config->img_path = $config->www_root . "fotos_cities/"; 
$config->date_time_zone = 'America/Sao_Paulo';
$config->default_start_money = 10000;
$config->b_terrain_free = array(1,8,9); //Don't change it
$config->b_terrain_locked = 2; //Don't change it
$config->b_terrain_destructed = 4; //Don't change it
$config->duracao_turno_horas = 1; //Change to 12
$config->turnos_to_level_upgrade = 2000;
$config->patrimonio_to_level_upgrade = 20000; 

require_once 'setup.php';


?>