<?php


class SqlBuilder {

	private $columns;
	private $values;

	private $table;
	//private String _sqlCached;


	function Reset() {

		unset($this->columns);
		unset($this->table);
		unset($this->values);
	}

	function __construct($table) {
		$this->table = $table;
		$columns =    new ArrayObject();
		$values =    new ArrayObject();
	}

	//    public void AddColumnValue(String column, Object value, EnuDataType tipo) {
	//        this.AddColumnValue(column, String.valueOf(value), tipo);
	//    }

	function AddColumnValue(String $column, Object $value, EnuDataType $tipo) {

		$vl;
		$aspas;



		if(tipo == EnuDataType.Texto || tipo == EnuDataType.Data){
			$aspas = "'";
		} else {
			$aspas =  "";
		}

		if (value == null) {
			$vl = " null ";
		} else {

			if(tipo == EnuDataType.Data){
				$df = new SimpleDateFormat(DBCon.FormatoDataHora);
				if(value instanceof  Date){
					//$dt = (Date)value;
					$value = df.format(dt);
				}else{

					/*
					 String ts = Global.getString(value);
					 try{
					 value =  df.format(df.parse(ts));
					 }catch(Exception err){
					 value = ts;
					 GlobalMessages.AddError(err);
					 }
					 */
				}

			}else{
				$value = Global.getSqlInjCleaner(Global.getString( value));
			}

			$vl = aspas + Global.getString(value) + aspas;
		}

		columns.add(column);
		values.add(vl);
	}

	function getSqlInsert() {

		$sql = " insert into " + _table + " (";

		$Start = true;
		foreach ($columns as $s) {

			if ($Start) {
				$sql .= $s + "";
				$Start = false;
			} else {
				$sql .= ", " + $s;
			}
		}
		$Start = true;
		$sql .= " ) values ( \n";
		foreach ($values as $s ) {
			if ($Start) {
				$sql .= $s + "";
				$Start = false;
			} else {
				$sql .= ", " + $s;
			}
		}
		$sql .= ");";

		return $sql;
	}

	function getSqlUpdate($where)  {


		if ($this->columns.size() != $this->values.size()) {
			throw new Exception("Nro de Colunas é diferente do nro valores.");
		}

		$sql = " update " + _table + " set ";

		$Start = true;
		for ($i = 0; $i < columns.size(); $i++) {
			if (Start) {
				$sql += columns.get($i) + " = " + values.get($i);
				$Start = false;
			} else {
				$sql += ", "+ columns.get($i) + " = " + values.get($i)  ;
			}
		}
		if(!where.equals("")){
			$sql += " where " +where;
		}

		return $sql;
	}

	//public PreparedStatement getPreparedStatement(){

	 
}


?>