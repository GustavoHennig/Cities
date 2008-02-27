<?php



//array(1 => 'January', 'February','March');

class TableBuilder{
	
	
	private $html;
	
	function __construct(){
	$this->html = '';
	}
	
function getTableHeader($id, $columnsHeader, $NoShowHeader = false, $border = 0, $class = 'defaulttable'){
	
	$html = "";
	
	$html .= '<table id="'. $id .'" class="defaulttable" cellpadding="1" cellspacing="0" border="'. $border .'" >';  
        //style="border: thin solid #C0C0C0; width: 10%; height: 42px; ">

	if(!$NoShowHeader){
		$html .= '<tr>';
			foreach($columnsHeader as $col){
				$html .= '<td class="td_header_default"><b>';
				
				$html .= $col;
				
				$html .= '</b></td>';		
			}
		
		$html .= '</tr>';
	}
	
	$this->html .= $html;
	
}

function TableAddRow($values, $columnsClass = 'td_default'){
		$html = '<tr>';
			foreach($values as $col){
	
				$html .= '<td class="'. $columnsClass .'">';
				
				$html .= $col;
				
				$html .= '</td>';		
			}
		
		$html .= '</tr>';	
		$this->html .= $html;
}

function getHtmlData(){
	$this->html .= "</table>";
	return $this->html ;
}

}
?>