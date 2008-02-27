
<?php
function &get_instance_ref() {
    static $obj;

    echo "Objeto estatico: ";
    var_dump($obj);
    if (!isset($obj)) {
        // Assimila uma referencia a variavel estatica
        $obj = &new stdclass;
    }
    $obj->property++;
    return $obj;
}

function &get_instance_noref() {
    static $obj;

    echo "Objeto estatico: ";
    var_dump($obj);
    if (!isset($obj)) {
        // Assimila o objeto para a veriavel estatica
        $obj = new stdclass;
    }
    $obj->property++;
    return $obj;
}

$obj1 = get_instance_ref();
$still_obj1 = get_instance_ref();
echo "\n";
$obj2 = get_instance_noref();
$still_obj2 = get_instance_noref();

$date =  new DateTime();

$date = date_create('11/24/2009 20:18:3');
echo "<BR>";
echo date("d/m/Y h:i:s");
echo "<BR>";
echo date("d/m/Y h:i:s",strtotime('11/24/2009 20:18:3'));

echo "<BR>";


echo date_format($date,"d/m/Y h:i:s");

echo get_class($date);

?> 
