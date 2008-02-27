<?php

require_once dirname(__FILE__).'/../lib/config.php';

$idcity = getRequestParameter("idcity");
$perc = getIdCityProgress($idcity);

if($perc){
	echo $perc;
}
else{
	echo 0;
}

function getIdCityProgress($idcity){
	
	global $db;

	$sql = "
	select count(*) as cnt from city_building
	where id_city = $idcity
	
	";
//debugging($sql);
	$max = 2004;
	
	
	if($rs = $db->getResultSet($sql)){
		if($obj = $rs->FetchNextObj()){
			$cnt = $obj->cnt;
		}

	}else{
		debugging("Erro ao efetuar consulta na base de dados");
	}
	
	$ret = round((($cnt * 100) / $max), 0);
	
	return $ret;
	
}



?>