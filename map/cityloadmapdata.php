<?php

require_once dirname(__FILE__).'/../lib/config.php';
require_once_relative("map/City.class.php");

global $config, $CITY;

debugging ("CITYLOADMAPDATA");


if(!UserLogged()){
	debugging("usuário não autenticado");
	Redirect("login/index.php", getString("user_not_autenticated"),5);
}

if($p_cityid = getRequestParameter("cityid")){
	$CITY->setCityFromId($p_cityid);
	$CITY->setSize($config->map_size);
	$CITY->isLoaded = true;
	$CITY->DBMakeCityBuildingCache();
	$CITY->getMapData();
}

if(isset($CITY) && $CITY->isLoaded && isset($CITY->city)){
	
}

?>