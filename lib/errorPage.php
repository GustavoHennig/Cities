<?php


require_once dirname(__FILE__).'/config.php';
//global $config;

require_once_relative('lib/global.php');

echo getPageHeader("Error");

echo getRequestParameter("err_msg");

echo getPageFooter();



?>