<?php

require 'City.class.php';
require '../global/global.php';
require '../lib/htmlbuild.php';

$city  = new City();

$city->setSize(getConfig('map_size'));

echo getPageHeader();
echo "áéí";
echo $city->BuildGrid();
echo getPageFooter();

?>