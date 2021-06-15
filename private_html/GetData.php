<?php

$api_log_path = "/var/www/SSIS_Lokaltrafik_Display/logs/api.log";

if (!file_exists($api_log_path))
{
    file_put_contents($api_log_path, "Auto Generated log file for API Calls!\n");
}

error_reporting(2147483647);
ini_set('error_log', $api_log_path);

include "Container.php";
$container = new Container();



$requestsToDo = $container->DB()->GetCallTimeRequests();

print_r($requestsToDo);

function GetXMLData($externalId, $extraAttributes)
{
    Global $container;

    $response_xml_data = file_get_contents("https://api.resrobot.se/v2/departureBoard?key=".$container->Credentials()->GetAPICredentials()['key']."&id=".$externalId."&passlist=0".$extraAttributes);
    return $response_xml_data;

    /*
    if($response_xml_data){
        $data = simplexml_load_string($response_xml_data);
        echo "<pre>"; print_r($data); exit;    
    }
    else
    {
        return false;
    }
    */
}