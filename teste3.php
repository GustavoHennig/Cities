<?php

require 'lib/config.php';
global $db;

echo $db->l();


echo date_format(date_create(),"d/m/Y");




$rs = &$db->getResultSet(" select * from city where id = 6 ");

while ($o = $rs->FetchNextObj()) {
	//echo $o;
	echo gettype($o->dt_created);
	 echo "h $o->id";
	 $o->witdh = 30;
	 
	 $db->StoreRecord("city",$o);
}

?>