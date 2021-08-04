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
        
        $data = $data."<h3>Data hämtad: $departureObject->collectionDateTime</h3>";
        $data = $data."</div>";
        $data = $data."<div id='departureList'>";
        $data = $data."<ul>";
        
        foreach ($departureObject->departures as $key => $value) {
            $timeDepartureBase = strtotime($value->date." ".$value->time);  
            if (isset($value->rtTime))
            {
                if ($timeDepartureBase < strtotime($value->rtDate." ".$value->rtTime))
                {
                    $timeDeparture = strtotime($value->rtDate." ".$value->rtTime);
                    $timeState = "late";
                }
                else
                {
                    $timeDeparture = $timeDepartureBase;
                    $timeState = "onTime";
                }
                  
            }
            else
            {
                $timeDeparture = $timeDepartureBase;
                $timeState = "onTime";
            }
            
            
            
            $dateTimeDepartureOriginal = date_create();
            date_timestamp_set($dateTimeDepartureOriginal, strtotime($value->date." ".$value->time));
            $dateTimeNow = new DateTime();
            $timeDiffMedium = $dateTimeNow->diff(new DateTime("@".strval(strtotime('-15 minutes', $timeDeparture))));
            $timeDiffShort =  $dateTimeNow->diff(new DateTime("@".strval(strtotime('-5 minutes', $timeDeparture))));;
            $timeDiffDeparture =  $dateTimeNow->diff(new DateTime("@".strval(strtotime('-5 minutes', $timeDeparture))));;
            $timeDiffLate =  $dateTimeDepartureOriginal->diff(new DateTime("@".strval($timeDeparture)));;
            
            //Debug stuff
            //$timeDiffDataArray = array( "timeDiffMedium" => $timeDiffMedium, "timeDiffShort" => $timeDiffShort, "timeDiffDeparture" => $timeDiffDeparture, "timeDiffLate" => $timeDiffLate);

            if ($timeDiffDeparture->invert == 1)
            {
                $state = "passed";
            }
            else if ($timeDiffShort->invert == 1)
            {
                $state = "high";
            }
            else if ($timeDiffMedium->invert == 1)
            {
                $state = "low";
            }
            else if ($timeDiffLate->invert == 1)
            {
                $state = "late";
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
                $data = $data."<li class='alert alert-low'>";
            }
            else if ($state == "high")
            {
                $data = $data."<li class='alert alert-high'>";
            }
            else if ($state == "passed")
            {
                $data = $data."<li class='time-passed'>";
            }
            
            $data = $data."<h2>$value->direction</h2>";
            
            $data = $data."<p>Avgår om: #</p>";
            
            $data = $data."<h2>$value->name</h2>";
            $data = $data."<p>Avgår: ";

            if ($timeState == "onTime")
            {
                $data = $data."<span class=''>$value->time</span>";    
            }
            else if ($timeState == "late")
            {
                $data = $data."<span class='obsolite'>$value->time</span>";
                $data = $data." <span class=''>$value->rtTime</span>";
            }
            
            $data = $data."</p>";

            $data = $data."</li>";

        }

        $data = $data."</ul>";
        $data = $data."</div>";

        return $data;
    }
}
?>