<?php

class Functions
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;    
    }

    public function GetXMLData($externalId, $extraAttributes)
    {
        Global $container;

        $response_xml_data = file_get_contents("https://api.resrobot.se/v2/departureBoard?key=".$container->Credentials()->GetAPICredentials()['key']."&id=".$externalId."&passlist=0".$extraAttributes);
        return $response_xml_data;
    }

    public function GenerateDepartureObjects($departure)
    {
        $arrayOfObjects = array();
        $data = new DOMDocument();
        $data->createElementNS("https://api.resrobot.se/xsd?hafasRestDepartureBoard.xsd", "hafas_rest_v1");
        $XMLToLoad = str_replace("hafas_rest_v1","https://api.resrobot.se/xsd?hafasRestDepartureBoard.xsd",$departure['xmlData']);
        $data->loadXML($XMLToLoad);
        $xpath = new DOMXPath($data);
        $xpath->registerNamespace("xmlns", "https://api.resrobot.se/xsd?hafasRestDepartureBoard.xsd");
        
        $printData = $xpath->query("//xmlns:Departure");

        if ($printData == FALSE)
        {
            echo("ERROR: printData equal to FALSE");
        }
        else
        {
            foreach ($printData as $key => $element) {
                
                $listOfValues = $xpath->query("./@direction | ./@name | ./@date | ./@time | ./@rtDate | ./@rtTime", $element);
                
                $objectData = array();
                foreach ($listOfValues as $key2 => $value) {
                    $objectData += [$value->name => $value->value];
                }
                $obj = (object)$objectData;
                array_push($arrayOfObjects, $obj);
            }
        }
        return $returnObject = (object)["callTimeID"=>$departure['callId'], "collectionDateTime"=>$departure['dateTime'], "stationName"=>$departure['name'], "title"=>$departure['title'], "departures"=>$arrayOfObjects];
    }
}
?>