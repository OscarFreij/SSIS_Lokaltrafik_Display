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
            error_log("ERROR: printData equal to FALSE");
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

    public function GenerateDisplayHTML($departureObject)
    {
        return $this->GenerateDisplayHTML_NEW($departureObject);
        //return "<pre>".print_r($departureObject, true)."</pre>";
    }

    public function GenerateDisplayHTML_NEW($departureObject)
    {
        $data = "";

        $data = $data."<div id='title'>";
        if ($departureObject->title != "")
        {
            $data = $data."<h1>$departureObject->title</h1>";    
        }
        else
        {
            $data = $data."<h1>$departureObject->stationName</h1>";
        }
        
        $data = $data."<h3>Hämtad: $departureObject->collectionDateTime</h3>";
        $data = $data."</div>";
        $data = $data."<div id='departureList'>";
        $data = $data."<ul>";
        
        foreach ($departureObject->departures as $key => $value) {
            $timeDeparture = strtotime($value->date." ".$value->time);
            $timeMedium = strtotime('-15 minutes', $timeDeparture);
            $timeShort = strtotime('-5 minutes', $timeDeparture);
            $timeNow = time();

            if ($timeNow - $timeDeparture >= 0)
            {
                $state = "passed";
            }
            else if ($timeNow - $timeShort >= 0)
            {
                $state = "high";
            }
            else if ($timeNow - $timeMedium >= 0)
            {
                $state = "low";
            }
            else
            {
                $state = "none";
            }

            if ($state == "none")
            {
                $data = $data."<li>";
            }
            else if ($state == "low")
            {
                $data = $data."<li class='alert-low'>";
            }
            else if ($state == "high")
            {
                $data = $data."<li class='alert-high'>";
            }
            else if ($state == "passed")
            {
                $data = $data."<li class='time-passed'>";
            }

            $data = $data."<h2>$value->direction</h2>";
            $data = $data."<h2>$value->name</h2>";
            $data = $data."<p>Avgår: ";
            $data = $data."<span class=''>$value->time</span>";
            $data = $data."</p>";

            $data = $data."</li>";

        }

        $data = $data."</ul>";
        $data = $data."</div>";

        return $data;
    }
}
?>