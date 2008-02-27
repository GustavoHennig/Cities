<?php
require_once dirname(__FILE__).'/../lib/config.php';
require_once_relative("map/City.class.php");

global $config, $CITY;


//Testa se o usuário está autenticado
debugging("testa sess usuáio");
if(!UserLogged()){
	debugging("usuário não autenticado");
	Redirect("login/index.php", getString("user_not_autenticated"),5);
}

//Testa a sessão
if(isset($CITY) && $CITY->isLoaded && isset($CITY->city)){
	echo getPageHeader(getString("creating_city"));
	echo '<script type="text/javascript" src="'. $config->www_root .'lib/ajaxlib.js"></script>';
	
	
	echo '<div id="msg_div">' . getString("please_wait_creating_city") . "</div>";
	echo '<BR/><BR/>';
	echo '<div id="msg_stats">' .''. "</div>";
	
	//while (@ob_end_flush());

	
	echo getLinkHtml("map/view.php",getString("start"));
	echo getPageFooter();
	
		echo '
	
	<script type="text/javascript">
		
		   setTimeout("refresh()", 200);
   
   	function refresh(){
   		var sp = document.getElementById(\'msg_stats\');
   		//var val;
   		//alert(sp.innerHTML);
   		getCityProgress("'.  $config->www_root . 'lib/progress.php", '. $CITY->city->id .', sp);
   		setTimeout("refresh()", 200);
	}
	
	function LoadMapData(){
		var ajax = CreateAjaxObj();
	    ajax.open("POST", "'.  $config->www_root . 'map/cityloadmapdata.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	    //ajax.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
		ajax.send("");
		//alert("fui rodado");
	}
	
	LoadMapData();
	
	</script>
	
	
	';
	//Redirect("map/view.php");
}else{
	Redirect("map/citylist.php");
}


?>