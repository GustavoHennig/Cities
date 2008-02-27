<?php


require_once dirname(__FILE__).'/../lib/config.php';
require_once_relative("map/City.class.php");
require_once_relative("map/Toolbar.class.php");

global $config, $CITY, $TOOLBAR, $USER, $SESSION;


//Testa se o usuário está autenticado
debugging("testa sess usuáio");
if(!UserLogged()){
	debugging("usuário não autenticado");
	Redirect("login/index.php", getString("user_not_autenticated"),5);
}

if($p_cityid = getRequestParameter("cityid")){
	$CITY->setCityFromId($p_cityid);
	$CITY->setSize($config->map_size);
	$CITY->isLoaded = true;
	$CITY->DBMakeCityBuildingCache();
//	if($CITY->new_city){
//		Redirect("map/citycreating.php");
//		die;
//	}
}

//Testa a sessão
if(!isset($CITY) || !isset($CITY->isLoaded)  || !isset($CITY->city)){
	
	Redirect("map/citylist.php", "Wrong Parameters", 2);
	die;
//	debugging("CITY created.");
//	require_once_relative("user/user.php");
//	$usercity = DBGetUserCity($USER);
//	if(!$usercity){
//		require_relative("map/cityedit.php");
//		die;
//	}else{
//		$CITY->setCity($usercity);
//		$CITY->setSize($config->map_size);
//		//$_SESSION['dbcity'] = $usercity;
//		$CITY->isLoaded = true;
//	}
}else{
	debugging("CITY is in session.");
}



if(!isset($TOOLBAR)){

	$TOOLBAR = new Toolbar();
	$TOOLBAR->DBGetItems();
}

//Instancia a classe para o Ajax
require_once_relative("lib/phplivex.php");
$ajax = new PHPLiveX();
$ajax->Export("SelectTool");
$ajax->Export("ClickOnMap");
$ajax->Export("UpdateMoney");
$ajax->Export("RefreshMapCell");


$map_data = $CITY->getMapData();
$toolbar_data = $TOOLBAR->getToolbar();
$selected_tool = $TOOLBAR->getSelectedToolInfo();

echo getPageHeader("Cities");
/*
echo '
	<!-- jsProgressBarHandler prerequisites : prototype.js -->
	<script type="text/javascript" src="'. $config->www_root .'lib/jsprogressbar/js/prototype/prototype.js"></script>

	<!-- jsProgressBarHandler core -->
	<script type="text/javascript" src="'. $config->www_root .'lib/jsprogressbar/js/bramus/jsProgressBarHandler.js"></script>
';
*/

$buttons = '
<input id="Button1" type="button" value="button" onclick="return jsSelectTool(this.id)" />
<input id="Button2" type="button" value="button" onclick="return jsClickOnMap(this.id)" />
';

//Print divs
//echo getDivEmpty("divheader",1,1,1000,100,$buttons);

//echo getDivEmpty("divupbar",1,101,1000,20, getFunctionLineData());

$pos_y_inicio = 181;
$pos_x_ini_rightbar = 745;
$largura_rightbar = 150;
$pos_y_selected_tool = 450;
$pos_y_cell_info = 550;

ob_start();


echo getDivEmpty("divmap", 10, $pos_y_inicio,700,600,$map_data);


echo getDivEmpty("divtoolbar", $pos_x_ini_rightbar , $pos_y_inicio, $largura_rightbar, $pos_y_selected_tool-$pos_y_inicio - 5, $toolbar_data);
echo getDivEmpty("divselectedtool", $pos_x_ini_rightbar, $pos_y_selected_tool, $largura_rightbar, $pos_y_cell_info - $pos_y_selected_tool - 5,$selected_tool );
echo getDivEmpty("divcellinfo", $pos_x_ini_rightbar, $pos_y_cell_info,  $largura_rightbar, 100);

ob_end_flush();

echo '<div id="listing" style="display: none; position: fixed; height: 20px; width: 80px; top: 5px; right: 5px; text-align: right;">Loading...</div>';


function SelectTool($toolid){
	global $TOOLBAR;
	//$toolitem =
	$TOOLBAR->setSelectedTool($toolid);
	return $TOOLBAR->getSelectedToolInfo();

}

function ClickOnMap($itemid){
	global $CITY, $TOOLBAR;
	if(isset($TOOLBAR->SelectedTool)){

		try{
			$ids = $CITY->UpdateCell($itemid, $TOOLBAR->SelectedTool);
		}catch(Exception $e){
			debugging($e->getMessage());
			ShowMessage($e->getMessage());
		}
	}

	$ret = '';

	foreach($ids as $id){
		$ret .= $id.',';
	}

	if(strlen($ret) > 1){
		$ret =  substr($ret,0,strlen($ret) - 1);
	}

	return $ret;//RefreshMapCell($itemid);
	//"UAIBU".$itemid;
}

function RefreshMapCell($cell_id){
	global $CITY;
	return $CITY->getCellData(0, 0, 1, $cell_id);
}

function UpdateMoney(){
	global $CITY;

	return $CITY->getMoney();
	//"UAIBU".$itemid;
}

function ShowMessage($msg){
	echo $msg;
}

echo '
<script language="javascript" type="text/javascript">
// <!CDATA[

function jsSelectTool(id) {
	SelectTool(id, {target: "divselectedtool", preload: "listing"});
}

function jsClickOnMap(id) {
	//	target: id, 
	var strIDs = ClickOnMap(id, {type: "r", preload: "listing"});
	//alert(strIDs);
	var vetIDs = strIDs.split(",");
	for( i in vetIDs ) {
		//alert(i);
    	jsRefreshMapCell( vetIDs[i] );
	}
	
	jsUpdateMoney();
}

function jsRefreshMapCell(id) {
	RefreshMapCell(id, {target: id, preload: "listing"});
}

function jsUpdateMoney(){
	UpdateMoney({target: "lbl_money"});
}

// ]]>
</script>


';

$ajax->Run();

echo "<BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/>";
echo "<BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/>";
echo getPageFooter();

?>