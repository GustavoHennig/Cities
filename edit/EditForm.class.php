<?php

require 'Form.class.php';
require 'FormInput.class.php';

/**
 * Classe para contruir um form de edição
 *
 */
class EditForm extends  Form {


	private $strInternalData = '';
	private $title = '';
	private $strToolbarData = '';
	
	function setTitle($title){
		$this->title = $title;
	}

	function AddField($fieldName,
	$fieldLabel,
	$fieldId,
	$type = 'text',
	$default_value = '',
	$readonly = false){

		$this->strInternalData .=  '<tr>
            <td>'. $fieldLabel . '</td>
            <td> ';

		$inp = new FormInput();

		$inp->fieldId = $fieldId;
		$inp->fieldName = $fieldName;
		$inp->type = $type;
		$inp->value = $default_value;
		$inp->readonly = $readonly;

		$this->strInternalData .= $inp->getString();

		$this->strInternalData .=  '   </td><td>
            </td>
        </tr>' ;

	}

	
	function AddToolItem($value, $id = 'btnSubmit'){
		//switch($action){
		//	case 'submit':
				
				$fi = new FormInput();
				$fi->fieldId = $id;
				$fi->fieldName = "Submit1";
				$fi->type = "submit";
				$fi->value = $value;
				
				
				$this->strToolbarData .= $fi->getString();
				
		//		break;
				
			
		//}
	}



	function getString(){

		$ret = '';
		
		$ret .= '<h1 style="text-align: center;">';
		$ret .= $this->title;
		$ret .= '</h1>';
//; height: 141px
		
		//style="width: 382px" 
		$ret .= '<table  align="center">';

		$ret .= $this->strInternalData;

		$ret .= '</table>';
			
		$ret .= ' <p style="text-align: center;"> ';
		$ret .= $this->strToolbarData;
 		$ret .= '</p>';
		return $ret;


	}


}


?>