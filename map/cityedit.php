<?php


require_once dirname(__FILE__).'/../lib/config.php';
global $config, $USER;

if (getRequestParameter("saving_city_data")){

	$city->id = getRequestParameter("id");
	$city->name = getRequestParameter("name");
	$city->description = getRequestParameter("description");
	$city->mayor_user = $USER->id;

	require_once_relative('map/City.class.php');
	
	$c = new City();
	$ret = $c->DBCreateCity($city);

	if(!is_numeric($ret) && is_string($ret)){
		echo $ret;
		echo getPageHeader('Nova cidade');
		echo getCityEditForm(null);
		echo getPageFooter();
	}else{
		if(is_numeric($ret)){
			PrintJS_CreateMapData($ret);
		}
		Redirect('map/citylist.php',getString('creating_city'),2);
	}
	
}else{
	echo getPageHeader('Nova cidade');
	echo getCityEditForm(null);
	echo getPageFooter();
	
	
}

function getCityEditForm($city){

	require_once_relative("edit/EditForm.class.php");
	
	$form = new EditForm();

	
	global $config;
	
	$id = '';
	$name = '';
	$description = '';
	
	$form->setAction($config->www_root .  'map/cityedit.php');
	if($city != null){
		$id = $city->id;
		$name = $city->name;
		$description = $city->description;

	}
	$form->setTitle(getString("new_city"));
	
	$form->AddField("name",getString("name"),"name",'text',$name);
	$form->AddField("description",getString("description"), "description",'text',$description);
	

	$form->AddField("saving_city_data","","saving_city_data","hidden","saving_city_data");
	$form->AddField("id","","id","hidden",$id);
	//);

	$form->AddToolItem("Gravar");
	
	$str = '';
	$str .= $form->getFormStart();
	$str .= $form->getString();
	$str .= $form->getFormEnd();
	return   $str;

}

function PrintJS_CreateMapData($idcity){
	
	
	global $config;
	
	echo '<script type="text/javascript" src="'. $config->www_root .'lib/ajaxlib.js"></script>';
	echo '
	<script type="text/javascript">
	
			function LoadMapData(){
		var ajax = CreateAjaxObj();
	    ajax.open("POST", "'.  $config->www_root . 'map/cityloadmapdata.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	    //ajax.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
		ajax.send("cityid='. $idcity .'");
		//alert("fui rodado");
	}
	
	LoadMapData();
	</script>
	';
		
}

?>