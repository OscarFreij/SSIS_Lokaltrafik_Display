<?php

// Require Container for functions and DB access.
require_once "Container.php";
$container = new Container();

// Logging config START //
$api_log_path = "../tmp/api.log";

if (!file_exists($api_log_path))
{
    file_put_contents($api_log_path, "Auto Generated log file for API Calls!\n");
}

error_reporting(2147483647);
ini_set('error_log', $api_log_path);
// Logging config END //

/*
 * API CALL AND RESPONSE PROCESSER
 * 1. Call DB and get request objects and start loop on them.
 * 2. Gather XML data for single request object.
 * 3. Save XML data to DB for later use by display.
 * When all requests are processed the script ends.
 */

error_log("======================================================");

if (isset($argv[1]))
{
    $data = $container->DB()->GetCallTimeRequests($argv[1]);
}
else
{
    $data = $container->DB()->GetCallTimeRequests("");
}

print_r($data);

if ($data != false)
{
    foreach ($data as $key => $value) {
        try
        {
            $responseData = $container->Functions()->GetXMLData($value['extId'],$value['attributes']);

            $container->DB()->StoreTimeTableData($value['id'], $container->Functions()->GenerateDepartureObjects($responseData));   
        }
        catch(Exception $e)
        {
            error_log("Unable to get XML data from api regarting callTime ".$value['id']);
        }
    }
}

error_log("======================================================");
exit;
