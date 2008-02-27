<?php

require_once dirname(__FILE__).'/../lib/config.php';
global $config;

if(!UserLogged()){
	debugging("usuário não autenticado");
	Redirect("login/index.php");
}

//Testa a sessão
if(!isset($CITY)   || !isset($CITY->isLoaded)  || !isset($CITY->city)){
	Redirect('map/view.php', getString('need_view_map'),3);
}else{
	debugging("CITY is in session.");
}

echo getPageHeader(getString('city_stats'));

echo "<div style='text-align: center;'>";

echo getString('city_stats');
$cityinfo = $CITY->DBGetCityInfo();

$str = '';

if(isset($cityinfo)){
	
	
	require_once_relative('lib/htmlbuildtable.php');
	$header = array("",getString('info'),getString('value'));
	
	$t = new TableBuilder();
	
	$t->getTableHeader("tb_stats", $header,false,1);
	
	
	//$t->TableAddRow(array("<b>". getString('total_power') . ":</b><br>" . getImgHtml("stats/torres_de_alta_tensao.jpg","x") ,$cityinfo->sum_power));
	AddItem($t,getString('total_power'),"torres_de_alta_tensao.jpg",$cityinfo->sum_power,"vmdown");
	
	//$str .= " <p><b>". getString('total_power') . ":</b>,$cityinfo->sum_power</p>";
	//$t->TableAddRow(array("<b>". getString('total_water') . ":</b><br>" . getImgHtml("stats/tratamento_agua.jpg","x"),$cityinfo->sum_water));
	AddItem($t,getString('total_water'),"tratamento_agua.jpg",$cityinfo->sum_water,"vmdown");
	//$t->TableAddRow(array("<b>". getString('total_housing') . ":</b>",$cityinfo->sum_housing));
	AddItem($t,getString('total_housing'),"gohome.png",$cityinfo->sum_housing,"");
	//$t->TableAddRow(array("<b>". getString('total_work') . ":</b>",$cityinfo->sum_work));
	AddItem($t,getString('total_work'),"parque_ind2.jpg",$cityinfo->sum_work,"");
	//$t->TableAddRow(array("<b>". getString('total_pollution') . ":</b>",$cityinfo->sum_pollution));
	AddItem($t,getString('total_pollution'),"poluicao.jpg",$cityinfo->sum_pollution,"vddown");
	
	//$t->TableAddRow(array("<b>". getString('total_income') . ":</b>",$cityinfo->sum_income));
	AddItem($t,getString('total_income'),"dinheiromais.jpg",$cityinfo->sum_income,"");
	
	
	UpdateCityFarFromStreet($CITY->city->id);
//	$t->TableAddRow(array("<b>". getString('lost_income') . ":</b>",DBGetLostIncome($CITY->city->id)));
	AddItem($t,getString('lost_income'),"dinheiromenos.jpg",DBGetLostIncome($CITY->city->id),"");
	
//	$t->TableAddRow(array("<b>". getString('total_expense') . ":</b>",$cityinfo->sum_expense));
	AddItem($t,getString('total_expense'),"dinheiromenos.jpg",$cityinfo->sum_expense,"");
	
	//$t->TableAddRow(array("<b>". getString('money') . ":</b>",$cityinfo->money));
	AddItem($t,getString('money'),"dinheiro.jpg",$cityinfo->money,"");
	//$t->TableAddRow(array("<b>". getString('citizens') . ":</b>",$cityinfo->citizens));
	
	AddItem($t,getString('citizens'),"desenho_pessoas.png",$cityinfo->citizens,"");
	
	

	echo $t->getHtmlData();
}

echo "</div>";

echo getPageFooter();


function AddItem(&$t, $description, $img, $value, $value_type){
	
	switch($value_type){
		case "vmdown":
			
			if($value == 0){
				$value = getImgHtml("16/null.png", "x") .' '. $value;
			}else if($value < 0){
				$value = getImgHtml("16/vmdown.png", "x") .' '. $value;
			}else if($value > 0){
				$value = getImgHtml("16/vdup.png", "x") .' '. $value;
			}
			break;
			
		case "vddown":
			if($value == 0){
				$value = getImgHtml("16/null.png", "x") .' '. $value;
			}else if($value < 0){
				$value = getImgHtml("16/vddown.png", "x") .' '. $value;
			}else if($value > 0){
				$value = getImgHtml("16/vmup.png", "x") .' '. $value;
			}
			
			
			break;
		default:
			break;
		
	}
	

	$t->TableAddRow(array(getImgHtml("stats/". $img, "x"), "<b>". $description . ":</b>", $value));
}


?>