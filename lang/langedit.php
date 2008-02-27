

<?php



require_once dirname(__FILE__).'/../lib/config.php';
global $config;



$lang_key = getRequestParameter("lang_key");
$lang_value = getRequestParameter("lang_value");
$redir = getRequestParameter("redir");

if(!hasValue($redir)){
	$redirect =  $_SERVER['HTTP_REFERER'];
	if(!isset($redirect)){
		$redirect = 'login/index.php';
	}
}

if(hasValue($lang_key)){
	if(hasValue($lang_value)){
		SaveLangEntry($lang_key, $lang_value);
		Redirect($redir, getString('entry_saved'), 5);
	}else{

		echo getPageHeader(getString("editing_lang"));
		echo getLangEditForm($lang_key, $redirect);
		echo getPageFooter();

	}
}else{
	Redirect($redirect, getString('invalid_parameters'), 5);
}




function getLangEditForm($lang_key, $redir){

	require_once_relative ('edit/EditForm.class.php');

	$form = new EditForm();
	$form->setTitle(getString("lang_edit"));
	$form->setAction('langedit.php');

	$str = $form->getFormStart();

	$form->AddField("lang_key", getString("lang_key"), "lang_key", 'text', $lang_key, true);
	$form->AddField("lang_value", getString("translation"),"lang_value","text",$lang_key);
	$form->AddField("redir", "","redir","hidden",$redir);

	$form->AddToolItem(getString("save"));

	$str .= $form->getString();
	$str .= $form->getFormEnd();

	return $str;
}

function SaveLangEntry($lang_key, $lang_value){

	global $config;


	debugging("Saving lang entry...");


	$file = $config->local_root . 'lang\\' . $config->language . '.php';

	$str = file_get_contents($file);
	//Atenção, o arquivo origem deve estar em UTF-8
	//Os parâmetros enviados também deve estar em UTF-8
	//Assim, o arquivo será gravado em UTF-8 sem necessidade de conversão.
	
	//$str = utf8_decode($str);
	
	$str = substr($str,0,strlen($str) - 4);

	$str .= '$string[\''. $lang_key .'\'] = \''. $lang_value . '\';' . "\n";
	$str .= " \n";
	$str .= '?>';

	//$str = utf8_encode($str);

	if(file_exists($file . ".bak")){
		unlink($file . ".bak");
	}
	rename($file, $file . ".bak");

	file_put_contents($file, $str, FILE_APPEND );


}



?>

