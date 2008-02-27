<?php

require_once dirname(__FILE__).'/../lib/config.php';

class Toolbar{

	private $totColumns = 5;

	function setColumnsNumbers($totColumns){
		$this->totColumns = $totColumns;
	}

	private $cntItems = 0;
	private $htmlBuff = "";
	private $cntCols = 0;
	public $SelectedTool = null;

	function addToolItem($item, $new_group = false){

		global $config;

		if($new_group){
			if($this->cntCols > 0){
				$this->htmlBuff .= "</tr>";
				$this->cntCols = 0;	
			}
//			$this->htmlBuff .= "<tr>";
//			$this->htmlBuff .= "<td><hr/></td>";
//			$this->htmlBuff .= "</tr>";
		}
		if($this->cntCols == 0){
			$this->htmlBuff .= "<tr>";
		}

		$this->htmlBuff .= "	<td id='$item->ti_id'  class=\"clicable_cell\" onclick=\"return jsSelectTool(this.id)\"";
		
		if(isset($item->img_url_full)){
			$this->htmlBuff .= ' onmouseover="return overlib(\'<img src=&quot;'. $config->img_path . 'map/full/'.$item->img_url_full .'&quot; alt=&quot;No image&quot; />  \', WIDTH, 50, HEIGHT, 13, BGCOLOR, \'#C0C0C0\');" onmouseout="return nd();"';
		}
		
		$this->htmlBuff .= " >";
		
		$img_url_ico = (hasValue($item->ti_img_url) ? $item->ti_img_url : $item->img_url );
		
		$this->htmlBuff .= getImgHtml('map/16/'.$img_url_ico ,"IM_TB");

		
		
		
		//TODO: Carregar dados do botão

		$this->htmlBuff .= "	</td>";

		$this->cntCols ++;
		if($this->cntCols >= $this->totColumns){
			$this->htmlBuff .= "</tr>\n";
			$this->cntCols = 0;
		}

		$this->cntItems++;
	}

	function DBGetItems(){
		
		global $db;
		
		$sql = $this->getSqlForToolbarItem();

		//echo $sql;

		if($rs = $db->getResultSet($sql)){

			//Verify building type on 'building' table on database, must have 1.
			$last_bt = 1;
			
			while($o = $rs->FetchNextObj()){
				if($o->building_type != $last_bt){
					$last_bt = $o->building_type;
					$this->addToolItem($o, true);
				}else{
					$this->addToolItem($o);	
				}
				
			}
		}
	}


	function getToolbar(){
		$str = '';

		if($this->htmlBuff == ""){
			$this->DBGetItems();
		}

		$str .= '<table>';
		$str .= $this->htmlBuff;
		$str .= '</table>';
		return $str;

	}

function getSqlForToolbarItem($id = 0){
	
	require_once_relative('map/City.class.php');
	
	global $CITY;
	
	$sql = "
			select
		b.*,
		bt.description as descr_type,
		ti.id as ti_id,
		ifnull(ti.img_url, b.img_url) as ti_img_url,
		ti.action as ti_action,
		ifnull(ti.name, b.name) as ti_name
	from toolbar_item ti
	left join building b on
		ti.id_building = b.id and
		visible_on_level <= $CITY->Level
	left join building_type bt on
		ti.building_type = bt.id ";
	
	if($id != 0){
		$sql .= " where ti.id = $id ";
	}
	
	$sql .= " order by b.building_type, visible_on_level, price		 ";
	
	return $sql;
	
}
	function DBGetToolbarItem($id){

		global $db;
		
		$sql = $this->getSqlForToolbarItem($id) ;

		//echo $sql;

		if($rs = $db->getResultSet($sql)){
			if($o = $rs->FetchNextObj()){
				return $o;
			}
		}
		return null;
	}
	
	function &setSelectedTool($id){
		if(isset($id) && is_numeric($id)){
			$st = $this->DBGetToolbarItem($id);	
		}
		if(isset($st)){
			$this->SelectedTool = &$st;
			return $st;
		}else{
			debugging("Setting a wrong tool", log_type_error);	
			return null;
		}
	}
	
	function getSelectedToolInfo(){
		
		$str  = '';
		if(isset($this->SelectedTool)){
			
			$img_url_ico = (hasValue($this->SelectedTool->ti_img_url) ? $this->SelectedTool->ti_img_url : $this->SelectedTool->img_url );
			
			$str .= getImgHtml('map/16/'.$img_url_ico ,"IM_TB");
			$name = (hasValue($this->SelectedTool->ti_name) ? $this->SelectedTool->ti_name : $this->SelectedTool->name); 
			$str .= ' ' .$name. '<BR/>';
			if(isset($this->SelectedTool->price)){
				$str .= getString('costs').': ' .$this->SelectedTool->price . '<BR/>';
			}
		}else{
			$str .= getString('no_tool_selected');
		}
		return $str;
		
	}
}

?>