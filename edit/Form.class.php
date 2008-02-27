<?php

class Form{

	public $actionPath = '';
	public $formId = 'form1';

	function setAction($path){
		$this->actionPath =  $path;
	}
	function setId($id){
		$this->formId = $id;
		 
	}

	function getFormStart(){
		return "<form action='$this->actionPath' method='post'>";

	}
	function getFormEnd(){
		return "</form>";
	}

}

?>