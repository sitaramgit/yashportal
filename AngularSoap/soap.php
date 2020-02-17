<?php
// header('Content-Type: text/html; charset=utf-8');
$Server_Path = "http://app.theprintshop.co.za";
require_once('nusoap.php');
global $Server_Path;
global $client;
 
$client = new nusoap_client($Server_Path."/vtigerservice.php?service=customerportal", false, $proxy_host, $proxy_port, $proxy_username, $proxy_password,0,100);

//We have to overwrite the character set which was set in nusoap/lib/nusoap.php file (line 87)
$client->soap_defencoding = $default_charset;
// $client->soap_defencoding = "UTF-8";
?>