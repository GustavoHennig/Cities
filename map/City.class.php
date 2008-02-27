<?php
/*
 * @author Gustavo Augusto Hennig
 * @version  alpha
 * @license All rights reserved
 * @package map

 * */
//require_once_relative('\lib\adodb5\adodb.inc.php');

class City

{
	public $Width;
	public $Height;

	//public $CityId;
	public $Name;
	public $Description;
	public $city;

	public $isLoaded;
	public $Level = 0;

	public  $new_city = false;
	private $cbListCache1 = array();
	private $cbListCache2 = array();
	/*
	 A largura precisa ser ímpar, para os cáculos funcionarem com precisão.
	 @param Largura do mapa
	 * */
	function setSize($width){
		$this->Height = ($width *  1.5) - .5;
		$this->Width = $width;
	}

	/* Prints maps
	 @return string html table as a map
	 */
	function printMap(){

		echo BuildGrid();
	}

	function setCity(&$city){
		$this->city = &$city;
	}

	function BuildGrid(){




		$str = ' <TABLE id="tb_map" border="0" cellspacing="0"> ';

		// Utilizado para calcular o triangulo
		$z = $this->Trunc($this->Width/2);

		$alt_ponta = $this->Height / 2 - .5;
		$alt_meio =  $this->Height - ($alt_ponta * 2);
		$DBCellCache = true;

		//Imprime começo
		for($linha = 1; $linha <= $alt_ponta; $linha++){

			$str .= $this->getTableRowStart();
			for($col = 1; $col <= $this->Width; $col++){


				$visible =($z < $col && ($this->Width - $z) >=  $col );

				$str .= $this->getCellData($linha, $col, $visible);

			}
			$str .= $this->getTableRowEnd();
			$z--;
		}

		//Imprime o meio
		for($linha = $alt_ponta+1; $linha < $alt_ponta + $alt_meio; $linha++){
			$str .= $this->getTableRowStart();
			for($col = 1; $col <= $this->Width; $col++){
				$str .= $this->getCellData($linha, $col, true);
			}
			$str .= $this->getTableRowEnd();
		}

		//Imprime o fim
		for($linha = $alt_ponta + $alt_meio; $linha <= $this->Height; $linha++){

			$str .= $this->getTableRowStart();
			for($col = 1; $col <= $this->Width; $col++){

				$visible =($z < $col && ($this->Width - $z) >=  $col );

				$str .= $this->getCellData($linha, $col, $visible);

			}
			$str .= $this->getTableRowEnd();
			$z++;
		}

		$DBCellCache = false;
		/*
		 for($linha = 1; $linha <= $this->Height; $linha++){


			$str .= '<TR>/n';
			$y=false;
			for($col = 1; $col <= $this->Width; $col++){
			$zcnt ++;
			$str .= '<TD>';
			if($z > 0){

			//Imprime a parte de cima
			$str .= "ii $col";
			//$str .= "w $this->Width";
			$str .= "z $z";
			$str .= ">>".($this->Width - $z)."<<";

			if($z < $col && ($this->Width - $z) >=  $col ){

			$str .= '<b>Teste</b>';
			}else{
			$str .= '-';
			}

			}else{
			//Entrar nesse else esse número de vezes:
			//Nro Linhas * 2 - $this->Height
			//Depois passar a incrementar o z até q a altura seja alcançada.
			//if($this->Height - ($cnt_linhas * 2) > $linha){

			$str .= "hcnt ".($cnt_linhas);

			if($cnt_linhas == 0){
			$cnt_linhas = $linha;
			}
			if(//$linha > ($this->Height - $cnt_linhas) &&
			$linha <= ($this->Height - $cnt_linhas)){
			$str .= 'Teste2';
			}else{
			$y = true;
			$str .= '-';

			}
			}
			$str .= $this->getCellData($linha, $col);
			$str .= '</TD>';
			}
			if($y){
			echo "OOOOOOOOOOOIIIIIIIII";
			if($z < 0){
			$z = 0;
			}
			$z++;
			$y = false;
			}else{
			$str .= "z--";
			$z--;
			}
			$str .= '</TR>/n';
			}
			*/

		$str .= '</TABLE>';

		return $str;


	}

	function getTableRowStart($row=0){
		$str = '<TR>';

		return $str;
	}
	function getTableRowEnd($row=0){

		$str = "</TR>\n";
		return $str;
	}

	function getCellData($row, $column, $visible, $id_city_building = 0){

		$city_building = new stdClass();
		$building = new stdClass();

		if($visible){
			$cell = $this->DBGetCellData($row, $column, $city_building, $building, $id_city_building);
		}
		$str = "<TD ";

		if($visible){
			$str .= ' id="'.$city_building->id.'" ';
			$str .= ' class="clicable_cell" ';
		}

		//		$str .= "style='";
		//
		////		if($visible){
		////			//tr .= ";background-color:Maroon";
		////		}else{
		////			$str .= "background-color:Gray";
		////		}
		//		$str .= "' ";

		if($visible){
			$str .= ' onclick="return jsClickOnMap(this.id)" ';
		}


		$str .= ">";
		if($visible){
			if(hasValue($city_building->id_parent)){
				if(((int)$city_building->part) == 0){
					debugging("Parte indefinida.");
					//throw new Exception("Parte indefinida.");
				}

				$id_b = $this->DBGetIdBuildingFromCityBuilding($city_building->id_parent);

				if(!hasValue($id_b)){
					debugging("Não encontrado id building.");
				}
				//if($city_building->part == 1){
				//if($building->size_h > 1 || $building->size_v > 1){
				$parts = $this->DBGetParts($id_b, $city_building->part);

				try{
					$obj_part = $parts[(int)$city_building->part];
					$str .= getImgHtml('map/16/parts/'. $obj_part->img_url,"x");
				}catch(Exception $e){
					debugging($e->getMessage(), log_type_error);
				}
				//}
			}else{
				$str .= getImgHtml('map/16/'.$building->img_url,"x");
			}
		}
		$str .= "</TD>";

		return $str;


	}
	function MultiCellUpdate(){

	}
	public $cellIdsToUpdate;

	function DBGetIdBuildingFromCityBuilding($id_city_building){

		global $db;

		$sql = '';
		$sql .= " select * from city_building where id = $id_city_building";

		if($rs = $db->getResultSet($sql)){
			if($o = $rs->FetchNextObj()){
				if(isset($o->id_building)){
					return $o->id_building;
				}
			}
		}else{
			debugging("Erro ao efetuar consulta na base de dados");
		}
		return null;
	}

	function Trunc($vlr){
		return $vlr - .5;
	}
	function DBGetParts($id_building, $part_number ){

		global $db;

		$part_number = (int)$part_number;

		$sql = " select * from building_part ";
		$sql .= " where id_building = $id_building ";
		if(isset($part_number)){
			$sql .= " and part_number = $part_number ";
		}
		$sql .= " order by part_number ";

		$ret =  array();

		if($rs = $db->getResultSet($sql)){
			while($o = $rs->FetchNextObj()){
				$ret[(int)$o->part_number] = $o;
			}
		}else{
			debugging("Erro ao efetuar consulta na base de dados");
		}
		return $ret;

	}

	function DBCreateCity($city){

		global $db, $config;
		//$city = new stdClass();
		//$city->id = 0;
		//$city->name = $pcity->Name;
		//$city->description = $pcity->Description;
		$city->dt_created = getCurrentDateTime();
		$city->money = $config->default_start_money;

		//TODO: Encontrar e vincular vizinhos
		$city->neighbor1 = null;
		$city->neighbor2 = null;
		$city->neighbor3 = null;
		$city->neighbor4 = null;
		$city->neighbor5 = null;
		$city->neighbor6 = null;
		$this->DBGetNextFreeXY($city->posx, $city->posy);


		$this->setSize($config->map_size);

		$city->witdh =$this->Width;
		$city->height =$this->Height;

		//$city->mayor_user = $USER->id;

		if(!$db->StoreRecord('city', $city)){
			return "Não gravou no banco.";
		}else{
			return $db->getLastInsertedId();
		}

	}

	function GetCBFromCache($column, $row, $id_city_building = 0){

		if($id_city_building == 0){
			if(array_key_exists((int)$column ."_". (int)$row, $this->cbListCache2)){
				//debugging("in cache col row");
				$ret = $this->cbListCache2[(int)$column ."_". (int)$row];
				if($ret){
					return $ret;
				}
			}else{
				//debugging("NOt in cache col row");
			}
		}else{
			if(array_key_exists(((int)$id_city_building), $this->cbListCache1)){
				//debugging("in cache id");
				$ret = $this->cbListCache1[(int)$id_city_building];
				if($ret){
					return $ret;
				}
			}else{
				//debugging("NOt in cache id");
			}
		}
		//debugging("Poor cache!");
		return false;

	}

	function DBGetCityBuilding($cityId, $column, $row, $id_city_building = 0){
		//	$id_building,

		global $db;


		$ret =  $this->GetCBFromCache($column, $row, $id_city_building);
		if($ret){
			return $ret;
		}

		$sql = " select
		cb.*, 
		b.free_to_build 
		
		from city_building cb
		inner join building b on
		cb.id_building = b.id
		
		where ";

		if($id_city_building == 0){
			$sql .= " id_city = ". $cityId ." and
			posX = $column and
			posY = $row ";
		}else{
			$sql .=  " cb.id = $id_city_building ";
		}

		//id_building = $id_building and

		if($rs = $db->getResultSet($sql)){
			$obj = $rs->FetchNextObj();
			if($obj){
				$this->SaveCBToCache($obj);
				return $obj;
			} else{
				return null;
			}

		}else{
			debugging("Erro ao efetuar consulta na base de dados");
		}
		return null;
			
	}

	function SaveCBToCache($cb){


		$this->cbListCache1[((int)$cb->id)] = &$cb;
		$this->cbListCache2[((int)$cb->posX) . "_" . ((int)$cb->posY)] = &$cb;
		//		if(array_key_exists((int)$cb->id)){
		//
		//		}else{
		//			$cbListCache
		//		}
	}
	function DBMakeCityBuildingCache(){

		global $db;

		$sql = " select
			cb.*, 
			b.free_to_build 
		from city_building cb 
		inner join building b on
		cb.id_building = b.id
		where ";


		$sql .= " id_city = ". $this->city->id ;


		$this->new_city = false;
		//id_building = $id_building and

		if($rs = $db->getResultSet($sql)){
			$cnt = 0;
			while($obj = $rs->FetchNextObj()){
				$this->SaveCBToCache($obj);
				$cnt++;
			}
			if($cnt == 0){
				$this->new_city = true;
			}
		}else{
			debugging("Erro ao efetuar consulta na base de dados");
		}
	}

	function DBGetCellDataFromId($id){
		global $db;

		$sql = " select * from city_building where id = $id ";



		if($rs = $db->getResultSet($sql)){

			if($city_building = $rs->FetchNextObj()){
				$city_building;
			}else{
				debugging("building: $id not found");
			}

		}




	}

	function DBGetCellData($row, $column, &$city_building, &$ret_building, $id_city_building = 0){


		global $db, $config;
		//$stl = " select * from city where id = $this->CityId";
		$obj = $this->DBGetCityBuilding($this->city->id,  $column, $row, $id_city_building);
		//$config->b_terrain_free,
			
		if(!$obj){

			$obj = new stdClass();
			$obj->id_city = $this->city->id;

			$idx = array_rand($config->b_terrain_free,1);

			$obj->id_building = $config->b_terrain_free[$idx];
			$obj->posX = $column;
			$obj->posY = $row;
			$obj->part = 0;
			$obj->dt_created = getCurrentDateTime();
			$obj->id_parent = null;

			$sql = " insert into city_building (id_city, id_building, posX, posY, part, dt_created)
			values (
			'$obj->id_city',
			'$obj->id_building',
			'$obj->posX',
			'$obj->posY',
			'$obj->part', ";

			$sql .= $db->getFormattedValue("dt_created", $obj->dt_created);

			$sql .= ")";

			$db->ExecuteSql($sql);
			$obj->id = $db->getLastInsertedId();
			//$db->StoreRecord(" ", $obj);
			//return $obj;
		}

		//$obj = DBGetCityBuilding($this->city->id, $column, $row);

		if(!isset($obj->id_building)){
			debugging("Falha na busca do city_building");
			die;
		}
			
		$sql = " select * from building where id = $obj->id_building ";

		if($rs = $db->getResultSet($sql)){

			if($building = $rs->FetchNextObj()){
				$building;
			}else{
				debugging("building: $obj->id_building not found");
			}
		}

		$city_building = $obj;
		$ret_building = $building;
		//$config->b_terrain_free
		return true;
	}

	function getBuildCellData($city_building, $building){
		$ret = '';

		$ret .= getImgHtml($building->img_url,"IMG ERR");

		$ret .= '';

		return $ret;
	}
	function getMoney(){

		//$sql = ""


		return $this->city->money;
	}

	function setMoney($new_value){

		global $db;

		$this->city->money = $new_value;

		$db->StoreRecord('city',$this->city);

		//$this->city->money;
	}

	function IncreaseMoney($value){
		$this->setMoney($this->getMoney() + $value);
	}

	private $MapData = '';

	function getMapData($refresh = false){

		if($this->MapData == '' || $refresh){
			$this->MapData =  $this->BuildGrid();
		}

		return $this->MapData;

	}

	function setMapData($data){
		$this->MapData = $data;
	}

	function UpdateCell($id, $SelectedTool, $no_test_act = false){

		global $db;

		$ret = array();

		debugging("tia= $SelectedTool->ti_action");

		if(hasValue($SelectedTool->ti_action && !$no_test_act)){
			switch($SelectedTool->ti_action){
				case "information":
					break;
				case "cleaner":
					debugging("will destruct");
					$ret = $this->DestroiExistingObjects($id, $SelectedTool);
					break;
				default :
					debugging("não deveria entrar aqui!");
					break;

			}
		}else{
			if($SelectedTool->price > $this->getMoney()){
				throw  new Exception(getString("no_suficient_money"));
			}

			$city_building = $this->DBGetCityBuilding($this->city->id, 0, 0, $id);

			$cbs = array();

			//Tratamento para building larger than 1x1
			if($SelectedTool->size_h > 1 || $SelectedTool->size_v > 1){

				$cnt_part = 1;
				//Esse bloco garante que não irá ser colocado construção fora dos limites do mapa
				for($v = 0; $v < $SelectedTool->size_v ; $v++){
					for($h = 0; $h < $SelectedTool->size_h ; $h++){
						$cb = $this->DBGetCityBuilding($this->city->id, $city_building->posX + $h, $city_building->posY + $v);
						if($cb == null){
							debugging("Não é possível contruír nesse local");
							throw  new Exception(getString("cant_build_here"));
						}else if($cb->free_to_build == 0){
							debugging("Não é possível contruír nesse local");
							throw  new Exception(getString("the_place_isnt_free"));
						}
						$cb->part = $cnt_part;
						$cb->id_parent = $city_building->id;
						$cb->id_building = $SelectedTool->id;
						$cnt_part++;
						$cbs[] = $cb; //Objetos a serem atualizados
					}
				}
				//TODO: Apagar todos que apontam para reg atual, ou qualquer um que aponte para algum
				//       cell que será sobreescrito.
			}else{

				if($city_building->free_to_build == 0 && $SelectedTool->ti_action != "cleaner"){
					debugging("Não é possível contruír nesse local");
					throw  new Exception(getString("the_place_isnt_free"));
				}


				$city_building->id_building = $SelectedTool->id;
				$city_building->id_parent = '';
				$city_building->part = 0;
				$cbs[] = $city_building;
				//				$this->DBUpdateCityBuilding($id,$SelectedTool->id);
			}

			foreach($cbs as $cb){
				$db->StoreRecord('city_building', $cb);
				$this->SaveCBToCache($cb);
				$ret[] = $cb->id;
			}

			$this->IncreaseMoney(-$SelectedTool->price);
			debugging("atualizado: $id");
			$this->setMapData('');

		}

		return $ret;
		//$CITY->c
	}

	function DestroiExistingObjects($id_city_building, $SelectedTool){

		global $db;

		$sql = "";
		$sql .= " select * from
		city_building
		where
		id = $id_city_building  or
		id_parent in (select id_parent from city_building where id = $id_city_building ) ";

		//$SelectedTool->ti_action = null;
		$ret = array();

		debugging($sql);
		if($rs = $db->getResultSet($sql)){

			while($o = $rs->FetchNextObj()){
				debugging("Update: $o->id");
				$this->UpdateCell($o->id, $SelectedTool, true);
				//debugging("Updated: $o->id");
				$ret[] = $o->id;
			}
		}

		return $ret;

	}

	//	function DBUpdateCityBuilding($id_city_building, $id_building, $part_number = 0, $id_parent = null){
	//
	//		global $db;
	//
	//		$sql = " update city_building set id_building = $id_building ";
	//		$sql .= " where id = $id_city_building ";
	//
	//		$db->ExecuteSql($sql);
	//
	//	}

	function DBGetCityInfo(){
		require_once_relative('lib/cron.php');

		global $db;

		$sql = getSqlCitySums($this->city->id);
			
		if($rs = $db->getResultSet($sql)){

			if($o = $rs->FetchNextObj()){
				return $o;
			}else{
				debugging("city info could'nt be loadeds");
			}
		}

	}

	function DBGetNextFreeXY(&$x, &$y){
		global $db;
		$sql = "
		
		select 
	max(posx) as max_posx,
	min(posx) as min_posx,
	max(posy) as max_posy,
	min(posy) as min_posy
from city
";


		if($rs = $db->getResultSet($sql)){

			if($o = $rs->FetchNextObj()){



				//Busca o menor valor absoluto de cada um
				if($o->max_posx >= abs($o->min_posx)){
					$x = $o->min_posx;
				}else{
					$x = $o->max_posx;
				}

				if($o->max_posy >= abs($o->min_posy)){
					$y = $o->min_posy;
				}else{
					$y = $o->max_posy;
				}


				do{

					if($x > $y){
						$y = $this->IncrementaAbsoluto($y);
					}else{
						$x = $this->IncrementaAbsoluto($x);
					}

				}while($this->ExistsCityPos($x, $y));

			}else{
				debugging("city info could'nt be loadeds");
			}
		}

	}

	function IncrementaAbsoluto($vl){

		if($vl >= 0){
			$vl++;
		}else{
			$vl--;
		}
		return $vl;

	}

	function ExistsCityPos($x, $y){

		global $db;

		$sql = "
		select count(*) as cnt from city where
		posx = $x and
		posy = $y
		";

		if($rs = $db->getResultSet($sql)){

			if($o = $rs->FetchNextObj()){

				if($o->cnt > 0){
					return true;
				}
			}
		}

		return false;

	}

	function setCityFromId($id){

		global $db;
		$sql = " select * from city where id = $id ";

		if($rs = $db->getResultSet($sql)){
			if($o = $rs->FetchNextObj()){
				$this->setCity($o);
				return $o;
			}else{
				return false;
			}
		}else{
			return false;

		}

	}

	function getSqlInsertsInitialCityBuilding(){


		//	$obj = new stdClass();
		//			$obj->id_city = $this->city->id;
		//
		//			$idx = array_rand($config->b_terrain_free,1);
		//
		//			$obj->id_building = $config->b_terrain_free[$idx];
		//			$obj->posX = $column;
		//			$obj->posY = $row;
		//			$obj->part = 0;
		//			$obj->dt_created = getCurrentDateTime();
		//			$obj->id_parent = null;
		//
		//			$sql = " insert into city_building (id_city, id_building, posX, posY, part, dt_created)
		//			values (
		//			'$obj->id_city',
		//			'$obj->id_building',
		//			'$obj->posX',
		//			'$obj->posY',
		//			'$obj->part', ";
		//
		//			$sql .= $db->getFormattedValue("dt_created", $obj->dt_created);
		//
		//			$sql .= ")";
		//
		//			$db->ExecuteSql($sql);
		//			$obj->id = $db->getLastInsertedId();

	}
}

?>