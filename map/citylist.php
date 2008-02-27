<?php




require_once dirname(__FILE__).'/../lib/config.php';
global $config, $db, $USER;


if(!UserLogged()){
	debugging("usuário não autenticado");
	Redirect("login/index.php", getString("user_not_autenticated"),3);
}


echo getPageHeader(getString("editing_lang"));


$sql = " select * from city where mayor_user = $USER->id ";


require_once_relative("lib/htmlbuildtable.php");

echo getLinkHtml("map/cityedit.php", getString("create_new_city"));

$header = array("",getString('info'),getString('value'));

$t = new TableBuilder();

$t->getTableHeader("tb_cities", $header,false,1);

if($rs = $db->getResultSet($sql)){
	while($o = $rs->FetchNextObj()){

		$t->TableAddRow(array(
		getLinkHtml("map/view.php?cityid=$o->id",$o->name),
		$o->description));
			
	}

}else{
	echo "Error on consult database.";

}

echo $t->getHtmlData();

echo getPageFooter();


?>