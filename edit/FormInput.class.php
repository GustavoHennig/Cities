<?php


class FormInput{
	

	public $fieldId;
	public $fieldName;
	public $type ;
	public $value;
	public $readonly;
	
	//function FormInput()
	//function Build()
	
	function getString(){
		
		$str = '';
		$str .= '<input id="'. $this->fieldId  .'"';	
		$str .= ' name="'. $this->fieldName .'"';
		$str .= ' type="'. $this->type .'"';
		$str .= ' value="'. $this->value .'"';
		
		if($this->type == 'submit'){
			$str .= ' class="buttons" ';
		}
		
		if($this->readonly){
			$str .= ' readonly="readonly" ';
		}
		$str .= '/>';
		
		return $str;
		
	}
}

?>