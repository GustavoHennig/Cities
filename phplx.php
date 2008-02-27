<?PHP

header("Content-Type: text/html; charset=utf-8");
require_once("lib/phplivex.php");
if(!isset($_SESSION['bla'])){
	$_SESSION['bla'] = bla;
	echo 'bla blá  bl[a bl[a';
}
$ajax = new PHPLiveX;
$ajax->Export("function1");
$ajax->Export("function2");

function function1($arg){
        usleep(1000000);
        return $arg;
}

function function2(){
return ' <a href="toolbar.htm"><img alt="ii" src="kthemeimgr.png" /></a> ';
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>Untitled Page</title>
    <script language="javascript" type="text/javascript">
// <!CDATA[

function Button1_onclick() {
   // var v = document.getElementById('dt').innerHTML;
    //document.getElementById('dt').innerHTML = v + 'Fuck you!';
    function1('Hello World', {target: 'myAreas', preload: 'listing'});
    function2('', {target: 'cell12', preload: 'listing'});
    function2('', {target: 'cell32', preload: 'listing'});
 
 
}

// ]]>
</script>
</head>
<div id="listing" style="display: none; position: absolute; width: 100%;" align="right">Loading...</div>
<div id="myAreas">aa</div>

    <table cellpadding="0" cellspacing="0"  
        style="border: thin solid #C0C0C0; width: 10%; height: 42px; ">
        <tr>
            <td>
                <a href="toolbar.htm" id="p11" onclick="func(this);"><img alt="" src="kthememgr.png" /></a></td>
            <td>
                <a href="toolbar.htm"><img alt="" src="kthememgr.png" /></a></td>
            <td>
                <a href="toolbar.htm"><img alt="" src="kthememgr.png" /></a></td>
        </tr>
        <tr>
            <td id="cell12">
                <a href="toolbar.htm"><img alt="" src="kthememgr.png" /></a></td>
            <td>
                <a href="toolbar.htm"><img alt="" src="kthememgr.png" /></a></td>
            <td>
                <a href="toolbar.htm"><img alt="" src="kthememgr.png" /></a></td>
        </tr>
        <tr>
            <td>
                <a href"toolbar.htm"><img alt="" src="kthememgr.png" /></a></td>
            <td id="cell32">
                <a href"toolbar.htm"><img alt="" src="kthememgr.png" /></a></td>
            <td>
                <a href"toolbar.htm"><img alt="" src="kthememgr.png" /></a></td>
        </tr>
    </table>

<?php $ajax->Run(); ?>


<input id="Button1" type="button" value="button" onclick="return Button1_onclick()" /></p>
<input id='teste' type="text" value=""


<script type="text/javascript">

</script>

</body>
</html>