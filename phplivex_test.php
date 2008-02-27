
<?PHP

header("Content-Type: text/html; charset=utf-8");
require_once("phplivex.php");

# DB connection and table selecting codes
# There are 5 fields in the table. These are id (primary and auto increment), username, email, birthDate, phoneNumber.

session_start();

$_SESSION["GridPage"] = 1;
$_SESSION["GridOrder"] = "";

function getContent($page, $GridOrder = "id"){
	ob_start();
	
	if(!isset($_SESSION["GridOrder"])) $_SESSION["GridOrder"] = "id";
	else{
		if($GridOrder == $_SESSION["GridOrder"]) $_SESSION["GridOrder"] = $GridOrder . " DESC";
		else $_SESSION["GridOrder"] = $GridOrder;
	}
	
	if($page != "") $_SESSION["GridPage"] = $page;
	
	$rowsPerPage = 4;
	$sublimit = ($_SESSION["GridPage"] - 1) * $rowsPerPage;
	$total = mysql_fetch_array(mysql_query("SELECT Count(*) AS Count FROM plx_example"));
	$allCount = $total["Count"];
	
	$result = mysql_query("SELECT * FROM plx_example ORDER BY " . $_SESSION["GridOrder"] . " LIMIT $sublimit, " . $rowsPerPage);
	while($row = mysql_fetch_array($result)){
		foreach($row as $key => $val) $$key = stripslashes($val);
		echo '<table class="gridContent" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" width="150">
		'.$username.'</td><td align="center" width="190">'.$email.'</td><td align="center" width="100">'.$birthDate.'</td>
		<td align="center" width="100">'.$phoneNumber.'</td></tr></tbody></table>';
	}
	
	echo '<table class="gridContent" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td colspan="4" align="center">';
	
	if($_SESSION["GridPage"] > 1){ ?>
		<a href="javascript:changeGridPage('previous', {target: 'content', preload: 'listing'});">Previous</a>
	<?PHP }
	
	if($allCount <= $rowsPerPage) $limit = 0;
	elseif(($allCount % $rowsPerPage) == 0) $limit = ($allCount / $rowsPerPage) + 1;
	else $limit = ($allCount / $rowsPerPage) + 1;
	
	if($limit > 10 && $_SESSION["GridPage"] > 5){
		if($_SESSION["GridPage"] + 4 <= $limit){
			$start = $_SESSION["GridPage"] - 5;
			$end = $_SESSION["GridPage"] + 4;
		}else{
			$start = $limit - 9;
			$end = $limit;
		}
	}elseif($limit > 10){
		$start = 1;
		$end = 10;
	}else{
		$start = 1;
		$end = $limit;	
	}
	
	if($start > 1) echo "...&nbsp;";
	$start = ceil($start);
	$end = ceil($end);
	for($i=$start;$i<$end;$i++){
		if($i != $_SESSION["GridPage"]) $ext = 'href="javascript:' . "getContent('".$i."', {target: 'content', preload: 'listing'})" . '" style="text-decoration:none;"';
		else $ext = 'style="color:#FF0000; text-decoration:none;"';
		echo '<a' . $ext . '>' . $i . '</a>&nbsp;';
	}
	if($end < ceil($limit)) echo "...";
	if($_SESSION["GridPage"] < ($allCount / $rowsPerPage)){ ?>
		<a href="javascript:changeGridPage('next', {target: 'content', preload: 'listing'});">Next</a>
	<?PHP } ?>
	
	</td></tr>
	</tbody></table>
	
	<?PHP
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}

function changeGridPage($type){
	if($type == "previous"){
		return getContent($_SESSION["GridPage"] - 1);
	}elseif($type == "next"){
		return getContent($_SESSION["GridPage"] + 1);
	}
}

$plx = new PHPLiveX("getContent,changeGridPage");

?>

<style>
#grid {
	width: 540px;
	border: 2px outset #990000;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	background-color: #FFFCFC;
	margin-top: 5px;
}

#gridHead {
	border-bottom: 2px inset #990000;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #990000;
}

#gridHeaders {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	text-align: center;
}

#gridHeaders td{
	text-align: center;
	padding-left: 3px;
}

.gridContent {
	width: 540px;
	border: 2px outset #990000;
	border-top: none;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
</style>

<script language="javascript">
function GridHeader(type, header){
	if(type == "over"){
		header.style.backgroundColor = "#FFE8E8";
		header.style.cursor = "pointer";
	}else{
		header.style.backgroundColor = "#FFFFFF";
	}
}
</script>
<?php $plx->Run(); ?>

<table id="grid" align="center" cellpadding="0" cellspacing="0">
<tbody><tr><td colspan="4" id="gridHead">
<div style="position: relative;">
<div id="listing" style="display: none; position: absolute; width: 100%;" align="right">Loading...</div>
<div style="position: relative;" align="center">Data Grid</div>
</div>
</td></tr>

<tr id="gridHeaders">
<td onclick="getContent('', 'username', {target: 'content', preload: 'listing'});" onmouseover="GridHeader('over', this)" onmouseout="GridHeader('out', this)" width="150">User Name</td>
<td onclick="getContent('', 'email', {target: 'content', preload: 'listing'});" onmouseover="GridHeader('over', this)" onmouseout="GridHeader('out', this)" width="190">E-Mail Address</td>
<td onclick="getContent('', 'birthDate', {target: 'content', preload: 'listing'});" onmouseover="GridHeader('over', this)" onmouseout="GridHeader('out', this)" width="100">Birthdate</td>
<td onclick="getContent('', 'phoneNumber', {target: 'content', preload: 'listing'});" onmouseover="GridHeader('over', this)" onmouseout="GridHeader('out', this)" width="100">Phone Number</td></tr>
</tbody></table>
<div id="content"></div>

<script language="javascript">
getContent("1", "id", {target: 'content', preload: 'listing'});
</script>

