<?php


require_once dirname(__FILE__).'/../lib/config.php';


//define("SECONDS_ON_A_HOUR", 3600);

function Run(){


	//TODO: Abaixo:
	/*

	Não efetuar tratamentos no executa referenta a hora.
	Regras do executa para cada cidade:
	Somar receita de cada construção
	Subtrair despesa de cada construção
	Construção que não possui rua nos N cells ao redor não somam receita, isso deve ser reportado.


	Registrar timestamp no final da execução


	*/
	global $db, $config;

	/*
	 Conta quando tempo que o cron não é executado.
	 Executa somatório receitas e despesas a cada 12 horas
	 Executa a rotina novamente sempre que tiver atrazo.
	 * */

	$time = time();
	$duracao_turno_horas = $config->duracao_turno_horas;

	$sec_ina_hour = 3600;//(int)SECONDS_ON_A_HOUR;
	//$cur = getCurrentDateTime();

	if($rs = $db->getResultSet(getSqlCitySums(0, $time - ($duracao_turno_horas * $sec_ina_hour)))){


		while($o = $rs->FetchNextObj()){

			if(!isset($o->last_cron)){
				$o->last_cron = $time;
				$horasrestantes = $duracao_turno_horas;
			}else{
				$horasrestantes = ($time - $o->last_cron) / $sec_ina_hour;
			}

			$times = 0;

			while($horasrestantes >= $duracao_turno_horas){
				//TODO: Tramento por cidade aqui:

				$o->last_cron = $o->last_cron + ($duracao_turno_horas * $sec_ina_hour);
				CityCron($o, $times);
				$horasrestantes -= $duracao_turno_horas;
				$times++;
			}
		}

	}else{
		debugging("Erro ao efetuar consulta na base de dados");
	}

}

function getSqlCitySums($city = 0, $last_cron = ''){

	$sql = '';

	/*
c.money,
	c.citizens,
	c.last_cron,
	c.turns,
	c.posx,
	c.posy
* */
	
	
	$sql .= "
select 
--	*

	sum(b.power) as sum_power,
	sum(b.water)as sum_water,
	sum(b.housing)as sum_housing,
	sum(b.work)as sum_work,
	sum(b.pollution)as sum_pollution,
	sum(b.income)as sum_income,
	sum(b.expense)as sum_expense,
	c.*,
	c.id as idcity


from 
	city c 
inner join city_building  cb on
	c.id = cb.id_city

inner join building b on
	cb.id_building =  b.id


where 

	part in (0,1)
";

	if($city > 0){
		$sql .= " and id_city = $city ";
	}

	if($last_cron != ''){
		$sql .= " and (c.last_cron < '$last_cron' or c.last_cron is null) ";
	}

	$sql .= "
group by 
	c.id
	 
	
	";



	return $sql;
}

function DBUpdateCityMoney($idcity, $last_cron, $money, $power, $water, $citizens, $turns){

	$sql = '';

	$sql .= " update city set
	last_cron = '$last_cron',
	money = '$money',
	power = '$power',
	water = '$water',
	turns = '$turns',
	citizens = '$citizens'

	where id = $idcity ";


	global $db;

	$db->ExecuteSql($sql);

}




function UpdateCityFarFromStreet($idcity = 0){

	global $db, $config;
	$sql = "";


	$sql .= "select
	cb.id, 
	cb.id_city, 
	id_building, 
	posx, 
	posy, 
	size_h, 
	size_v,
	far_from_street
	
	from city_building cb
inner join building b on
	cb.id_building = b.id
where 
	cb.part in (0,1) and
	b.building_type not in (1,2) ";

	if($idcity > 0){
		$sql .= " and  id_city = $idcity ";
	}

	if($rs = $db->getResultSet($sql)){

		while($o = $rs->FetchNextObj()){
			UpdateFarFromStreet($o);
		}
	}


}


function UpdateFarFromStreet($obj_citybuilding){


	global $db, $config;

	$raio = 3;

	$sql = "";
	$sql .= "
select count(*) cnt from city_building cb
inner join building b on
	cb.id_building = b.id and
	b.building_type = 2
where ";
	$sql .= "	(posX between (". ($obj_citybuilding->posx - $raio) . ") and ";
	$sql .= " 		 (". ($obj_citybuilding->posx + $obj_citybuilding->size_h - 1 + $raio) . ")) and ";
	$sql .= "	(posY between (". ($obj_citybuilding->posy - $raio) . ") and ";
	$sql .= " 		 (". ($obj_citybuilding->posy + $obj_citybuilding->size_v - 1 + $raio) . ")) ";
	$sql .= " and id_city = $obj_citybuilding->id_city ";

	$ffs = 1;
	if($rs = $db->getResultSet($sql)){
		if($o = $rs->FetchNextObj()){
			if(isset($o) && isset($o->cnt)){
				$ffs = ($o->cnt > 0 ? 0 : 1);
			}
		}else{
			debugging("city info could'nt be loadeds");
		}
	}

	//Atualiza somente se tiver alguma mudança
	if(((int)$obj_citybuilding->far_from_street) != ((int)$ffs)){
		$sql = " update city_building set far_from_street = $ffs where id = $obj_citybuilding->id ";
		$db->ExecuteSql($sql);
	}


}

function DBGetLostIncome($idcity){

	global $db;

	$sql = "
	select sum(b.income) as ded_income
	from city_building cb inner join building b on
	cb.id_building = b.id
	where
	far_from_street = 1 and
	part in (0,1) and
	id_city = $idcity
	";

	if($rs = $db->getResultSet($sql)){
		if($o = $rs->FetchNextObj()){
			return $o->ded_income;
		}else{
			debugging("erros");
		}
	}

	return 0;
}
function CityCron(&$city, $times = 0){



	/*
	 Atualiza o campo far from street, que significa se um objeto está longe de uma estrada
	 *
	 * */
	if($times == 0){
		UpdateCityFarFromStreet($city->idcity);
	}
	/* Calcs lost income from building far from street  */
	$lostincome = DBGetLostIncome($city->idcity);

	/* Update neighbors */
	if(!$city->neighbor1 ||
	!$city->neighbor2 ||
	!$city->neighbor3 ||
	!$city->neighbor4 ||
	!$city->neighbor5 ||
	!$city->neighbor6){
		DetectAndUpdateNeighbors($city);
	}




	$citizens = 0;

	//TODO: Implementar margem de erro, não deveria ser um cálculo exato
	if($city->sum_work < $city->sum_housing){
		$citizens = $city->sum_work;
	}else{
		$citizens = $city->sum_housing;
	}


	//Somente pessoas trazem dinheiro, então as receitas devem ser proporcionais ao nro de habitantes
	if($city->sum_work > 0){
		$fator_populacional = $citizens / $city->sum_work;
	}else{
		$fator_populacional = 0;
	}
	$city->money = $city->money + (($city->sum_income - $lostincome)  * $fator_populacional) - $city->sum_expense;
	$city->turns ++;

	//Grava alterações na base
	DBUpdateCityMoney($city->idcity, $city->last_cron, $city->money, $city->sum_power, $city->sum_water, $citizens, $city->turns);
}

function DetectAndUpdateNeighbors(&$city){

	/*
	 *
	 *       O
	 *  nb6 O O nb1
	 *     O   O
	 * nb5 O   O nb2
	 *     O   O
	 *  nb4 O O nb3
	 *       O
	 *
	 * X = Linha Vertial Inclinada para a direita
	 * Y = Linha horizontal
	 * arquivo: celulas.odg ou png.
	 *
	 * */

	$sql = "";

	if($nb1 = getCityIdFromPos($city->posx , $city->posy + 1 )){
		$sql .= " neighbor1 = $nb1, ";
	}
	if($nb2 = getCityIdFromPos($city->posx + 1, $city->posy)){
		$sql .= " neighbor2 = $nb2, ";
	}
	if($nb3 = getCityIdFromPos($city->posx + 1, $city->posy - 1)){
		$sql .= " neighbor3 = $nb3, ";
	}
	if($nb4 = getCityIdFromPos($city->posx, $city->posy - 1)){
		$sql .= " neighbor4 = $nb4, ";
	}
	if($nb5 = getCityIdFromPos($city->posx -1, $city->posy)){
		$sql .= " neighbor5 = $nb5, ";
	}
	if($nb6 = getCityIdFromPos($city->posx -1, $city->posy + 1)){
		$sql .= " neighbor6 = $nb6, ";
	}


	if($sql != ""){
		$sql = substr($sql,0,strlen($sql) - 2);

		$sql = " update city set " . $sql;
		$sql .= " where id = $city->id ";

		global $db;
		$db->ExecuteSql($sql);

	}

}

function getCityIdFromPos($x, $y){
	global $db;

	$sql = "
	select * from city
	where
	posx = $x and
	posy = $y 

	";

	if($rs = $db->getResultSet($sql)){
		if($obj = $rs->FetchNextObj()){
			return $obj->id;
		} else{
			return false;
		}

	}else{
		debugging("Erro ao efetuar consulta na base de dados");
	}

	return false;
}


?>