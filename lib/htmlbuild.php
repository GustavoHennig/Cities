<?php

function getPageHeader($titulo = ''){

	global $config;

	$str = '
	<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>'.$titulo.'</title>
	<link href="'.$config->www_root.'css/theme.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="'.$config->www_root.'/lib/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
	</head>
	<body>
	<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
	<div id="divheader" style="text-align: center;">
	<img src="'.  $config->img_path .'header.jpg" alt="CITIES" />
	<p style="text-align: right;"> ';
	$str .= getFunctionLineData() . '
	</p>
		
	</div>
	<hr />

	';
//STICKY, MOUSEOFF
	return $str;

}

function getPageFooter(){

	$str = "<br/><br/><br/><br/><br/><br/><br/><br/>";
	$str .= "</body>";
	$str .= "</html>";
/*
	$str .= '
	<p style="text-align:center;font-size:80%;font-family:Verdana">
<br>
<span id="02eb4"><script type="text/javascript">var jsons_02eb4=null;var item_02eb4=-1;function handleJson_02eb4(mysa_json){if(jsons_02eb4==null){jsons_02eb4=mysa_json}item_02eb4++;if(jsons_02eb4.items[item_02eb4]==null){item_02eb4=0}var span=document.getElementById(jsons_02eb4.items[item_02eb4].span);span.innerHTML=jsons_02eb4.items[item_02eb4].code}</script><script type="text/javascript"src="http://www.free-web-hosting.biz/ads/mysa_output.php?callback=handleJson_02eb4&sid=02eb4&show_ad_group=2"></script></span>
<br>

Hosting is powered by <a href="http://www.free-web-hosting.biz" title="Free Web Hosting Service - 5gb free disk space!"><strong>Free Web Hosting</strong></a> | <a href="http://www.free-web-hosting.biz/donate.html" title="Link removal - Make a donation">Remove Links</a> | <a href="http://www.free-web-hosting.biz/advertise.html" title="Targeted Advertising">Advertise</a></p>
<script type="text/javascript" src="http://www.free-web-hosting.biz/fixstats.php"></script>
</body>
</html>
	';
*/
	return $str;
}


function getLinkHtml($url, $label, $align = null){
	global $config;

	$str = '';

	if($align != null){
		$str .= '<p style="text-align: '.$align.';">';
	}
	$str .= '<a href="'. $config->www_root . $url .'">'.$label.'</a>';

	if($align != null){
		$str .= '</p>';
	}

	return $str;
}

function getImgLink($url_link, $url_img, $alt){
	global $config;

	return '<a href="'. $config->www_root . $url_link .'">'.getImgHtml($url_img, $alt).'</a>';
}

function getImgHtml($url_img, $alt){

	global $config;
	return '<img src="'. $config->img_path .$url_img.'" alt="'.$alt.'" />';
}


function PrintAlert($text, $align = null){

	$str = '';

	//if($align != null){
	$str .= '<p style="text-align: '.$align.'; color: #800000">';
	//}
	$str .= $text;

	//if($align != null){
	$str .= '</p>';
	//}
	
	echo $str;
}


function getDivEmpty($id, $left = null, $top= null, $width= null, $height= null, $innerHtml = ''){
	$ret = '<div id="'.$id.'"
	style="';
	if(isset($height)){
		$ret .= ' height: '.$height.'px;';
	}
	if(isset($width)){
		$ret .= ' width: '.$width.'px;';
	}
	if(isset($top)){
		$ret .= ' top: '.$top.'px;';
	}
	
	if(isset($left)){
		$ret .= ' left: '.$left.'px;';
	}
	$ret .= 'position: absolute;">'. $innerHtml.'</div>';

	return $ret;
}


function getFunctionLineData(){

	global $USER, $CITY, $config;

	$ret= '';

	$logged = UserLogged(); 
	
	if($logged){
		$ret .= ''. $USER->name .' [<a href="'. $config->www_root. 'user/index.php?act=edit_user' .'">'. getString('edit_profile') .'</a>]';
		if($CITY->isLoaded){
			$ret .= ' | <a href="'.$config->www_root. 'map/citystats.php">'. getString('city_stats') .'</a>'; 
			$ret .= ' | '. getString('money'). ': <label id="lbl_money">'.$CITY->getMoney() .'</label>';
		}
		
	}else{
		$ret .= ' <a href="'. $config->www_root. 'user/index.php?act=new_user' .'">'. getString('register') .'</a>';
	}

	$ret .= ' | <a href="'. $config->www_root . 'index.php">'.getString('home').'</a>';
	
	if($logged){
	 	$ret .= ' | <a href="'. $config->www_root . 'user/index.php?act=logout">'.getString('exit').'</a> ';
	}
	return $ret;
}


?>