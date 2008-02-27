<?php

include('lib/adodb5/adodb.inc.php'); // inclui o arquivo da classe


global $db;


// executando uma query
$rs = $db->Execute('select * from mdl_user');

if (!$rs) {
        echo $db->ErrorMsg();
}

echo "deu certo!";

while ($o = $rs->FetchNextObj()) { 
            print "$o->FIRSTNAME, $o->LASTNAME<BR>"; 
        }
        


?>