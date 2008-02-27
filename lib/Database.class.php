<?php


class Database
{

	public $adodb;
	private $db_date_format = "Y-m-d h:i:s";
	
	function __construct() {
		
		define ('ADODB_ASSOC_CASE', 0); //Use lowercase fieldnames for ADODB_FETCH_ASSOC

		require_once ('adodb5/adodb.inc.php'); // inclui o arquivo da classe

		// instanciando a classe
		$this->adodb = &ADONewConnection('mysql'); # exemplos: 'mysql' ou 'postgres'
		//$this->adodb->debug = true; // coloca o debug como ativo

		// conectando no banco de dados
		$ok = $this->adodb->PConnect("localhost", 'root', '', 'cities');

		if(!$ok){
			require_once 'global.php';
			Redirect("lib/errorPage.php?err_msg=". 
				"Erro ao conectar a base de dados, provavelmente o sistema está sobrecarredado, tente novamente mais tarde.");
			die;
		}

		//http://phplens.com/adodb/reference.varibles.adodb_fetch_mode.html
    	$this->adodb->SetFetchMode(ADODB_FETCH_ASSOC);
    	
    	//Works on mysql an postgres
    	$this->adodb->Execute("SET NAMES 'utf8'");
    	
		//echo "<BR/>Construiu o bd<BR/>";
	}

	function getResultSet($sql){
		$rs = $this->adodb->Execute($sql);

		if (!$rs) {
			echo $this->adodb->ErrorMsg();

		}else{
			return $rs;
		}
	}

	function &ExecuteSql($sql){
		$ok = $this->adodb->Execute($sql);

		return $ok;

	}
	function RecordExists($table, $keyvalue, $tablekey = 'id'){
		
		$sql = " select * from $table where $tablekey = '$keyvalue' ";
		
		$rs = &$this->getResultSet($sql);
		
		if(!$rs){
			debugging("Erro ao buscar recordset", log_type_error);
			return false;	
		}
		
		return (!$rs->EOF);
		
//		while ($o = $rs->FetchNextObj()) { 
//             
//        }
		
		
	}
	
	function getLastInsertedId(){

		$sql = " SELECT LAST_INSERT_ID() as lid ";

		$rs = &$this->getResultSet($sql);
		
		if(!$rs){
			debugging("Error on get last id", log_type_error);
			return false;	
		}
//		
//		if ($arr = $rs->FetchNextObj()) {
//			
//		}
		
        if ($arr = $rs->FetchRow()) { 
            return $arr['lid'];
        } else{
        	return false;
        }

	}
	
	function getFieldValue($table, $tablekey, $keyvalue, $fieldname){
		$sql = " select $fieldname from $table where $tablekey = $keyvalue ";
		
		$rs = &$this->getResultSet($sql);
		
		if(!$rs){
			debugging("Erro ao buscar recordset", log_type_error);
			return false;	
		}
		
	
        if ($arr = $rs->FetchRow()) { 
            return $arr[$fieldname];
        } else{
        	return false;
        }

        
		
	}
	
	function StoreRecord($table, $obj){
		
		if (!isset($obj->id) || $obj->id == 0 || !$this->RecordExists($table, $obj->id, 'id')) {
			return $this->InsertRecord($table, $obj);
		}else{
			return $this->UpdateRecord($table, $obj);
		}
	}	

	function UpdateRecord($table, $obj){
		if (! isset($obj->id) ) {
			debugging('No id in object to save');
			return false;
		}

		/// Check we are handling a proper $obj
		if (is_array($obj)) {
			debugging('Warning. Wrong call to update_record(). $obj must be an object. array found instead');
			$obj = (object)$obj;
		}

		$prefix = '';
		
		// Determine all the fields in the table
		if (!$columns = $this->adodb->MetaColumns($prefix . $table)) {
			debugging('Cannot determine table fields');
			return false;
		}
		$data = (array)$obj;


		// Pull out data matching these fields
		$ddd = array();
		foreach ($columns as $column) {
			if ($column->name <> 'id' and isset($data[$column->name]) ) {
				$ddd[$column->name] = $data[$column->name];
				// PostgreSQL bytea support
//				if ($CFG->dbfamily == 'postgres' && $column->type == 'bytea') {
//					$ddd[$column->name] = $this->$adodb->BlobEncode($ddd[$column->name]);
//				}
			}
		}

		// Construct SQL queries
		$numddd = count($ddd);
		$count = 0;
		$update = '';

		/// Only if we have fields to be updated (this will prevent both wrong updates +
		/// updates of only LOBs in Oracle
		if ($numddd) {
			foreach ($ddd as $key => $value) {
				$count++;
				$update .= strtolower ($key) .' = ';
				
				$update .= $this->getFormattedValue(strtolower ($key), $value);
				
				if ($count < $numddd) {
					$update .= ', ';
				}
			}

			if (!$rs = $this->adodb->Execute('UPDATE '. $prefix . $table .' SET '. $update .' WHERE id = \''. $obj->id .'\'')) {
				debugging($adodb->ErrorMsg() .'<br /><br />UPDATE '. $prefix . $table .' SET '. $update .' WHERE id = \''. $obj->id .'\'');
				if (!empty($CFG->dblogerror)) {
					
					error_log("SQL ".$adodb->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  UPDATE $CFG->prefix$table SET $update WHERE id = '$obj->id'");
				}
				return false;
			}
		}


		return true;
	}

	function InsertRecord($table, $obj){
		

		/// Check we are handling a proper $obj
		if (is_array($obj)) {
			debugging('Warning. Wrong call to update_record(). $obj must be an object. array found instead');
			$obj = (object)$obj;
		}

		$prefix = '';
		
		// Determine all the fields in the table
		if (!$columns = $this->adodb->MetaColumns($prefix . $table)) {
			debugging('Cannot determine table fields');
			return false;
		}
		$data = (array)$obj;


		// Pull out data matching these fields
		$ddd = array();
		foreach ($columns as $column) {
			if (isset($data[$column->name]) ) {
				$ddd[$column->name] = $data[$column->name];
				// PostgreSQL bytea support
//				if ($CFG->dbfamily == 'postgres' && $column->type == 'bytea') {
//					$ddd[$column->name] = $this->$adodb->BlobEncode($ddd[$column->name]);
//				}
			}
		}

		// Construct SQL queries
		$numddd = count($ddd);
		$count = 0;
		$insert_fields = '';
		$insert_values = '';

		/// Only if we have fields to be updated (this will prevent both wrong updates +
		/// updates of only LOBs in Oracle
		if ($numddd) {
			foreach ($ddd as $key => $value) {
				$key = strtolower ($key);
				$count++;
				if($key !== 'id' || $value != 0){
					$insert_fields .= $key; 
					$insert_values .= $this->getFormattedValue($key, $value);
					if ($count < $numddd) {
						$insert_fields .= ', ';
						$insert_values .= ', ';
					}
				}
			}

			$sql = ' INSERT INTO '. $prefix . $table . '(';
			$sql .= $insert_fields;
			$sql .= ') values ( '. $insert_values . ')';
			
			if (!$rs = $this->adodb->Execute($sql)) {
				debugging($this->adodb->ErrorMsg() .'<br /><br />'. $sql);
//				if (!empty($CFG->dblogerror)) {
//					
//					error_log("SQL ".$this->adodb->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  UPDATE $CFG->prefix$table SET $update WHERE id = '$obj->id'");
//				}
				return false;
			}
		}


		return true;
	}

	public function getFormattedValue($key, $value){
		
		$ret ="'";
		
		if(substr($key, 0, 3) === "dt_"){
//			if(get_class($value) !== 'DateTime'){
//				$value = date_create($value);
//			}
			
			require_once 'global.php';
			
			
			$ret .= getFormatedDate($value, $this->db_date_format);
			//date($this->db_date_format, )
			
			//$ret .= date_format($value, $this->db_date_format);
		}else{
			$ret .=  $value;
		}
		
		$ret .= "'";
		
		return $ret;
		
	}
	
	public function l(){
		return "s";
	}
	//print "<a href='http://127.0.0.1/adodb/teste2.php'>tgetete</a>";

	//	while ($o = $rs->FetchNextObj()) {
	//		print "$o->FIRSTNAME, $o->LASTNAME<BR>";
	//	}

}

?>