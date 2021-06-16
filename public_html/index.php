<?php

use function PHPSTORM_META\type;

include "../private_html/Container.php";
$container = new Container();

$dbReturn = $container->DB()->GetTimeTableData();

foreach ($dbReturn as $key => $value) {
    echo($value['callId']);
    $data = new DOMDocument();
    $data->createElementNS("https://api.resrobot.se/xsd?hafasRestDepartureBoard.xsd", "hafas_rest_v1");
    $XMLToLoad = str_replace("hafas_rest_v1","https://api.resrobot.se/xsd?hafasRestDepartureBoard.xsd",$value['xmlData']);
    $data->loadXML($XMLToLoad);
    //$data->createElementNS("https://api.resrobot.se/xsd?hafasRestDepartureBoard.xsd", "hafas_rest_v1");
    $xpath = new DOMXPath($data);
    $xpath->registerNamespace("xmlns", "https://api.resrobot.se/xsd?hafasRestDepartureBoard.xsd");
    

    $printData = $xpath->query("//xmlns:Departure");

    echo "<pre style='border-bottom: dotted black 8px; padding-bottom: 8px;'>";
    if ($printData == FALSE)
    {
        echo("ERROR: printData equal to FALSE");
    }
    else
    {
        foreach ($printData as $key => $value) {
            print_r($value);
        }
    }
    echo "</pre><br>";
}

exit;