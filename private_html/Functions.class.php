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

        try
        {
            $response_xml_data = file_get_contents("https://api.resrobot.se/v2/departureBoard?key=".$container->Credentials()->GetAPICredentials()['key']."&id=".$externalId."&passlist=0".$extraAttributes);            
            return $response_xml_data;
        }
        catch (Exception $e)
        {
            error_log("ERROR: Unable to get data from API regarding externalId == $externalId");
            return false;
        }       
    }

    public function GenerateDepartureObjects($rawXML)
    {
        $arrayOfObjects = array();
        $data = new DOMDocument();
        $data->createElementNS("https://api.resrobot.se/xsd?hafasRestDepartureBoard.xsd", "hafas_rest_v1");
        $XMLToLoad = str_replace("hafas_rest_v1","https://api.resrobot.se/xsd?hafasRestDepartureBoard.xsd",$rawXML);
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
            $dateTimeNow = new DateTime();
            foreach ($printData as $key => $element) {
                
                $listOfValues = $xpath->query("./@direction | ./@name", $element);
                $objectData = array();

                $directionValue = $listOfValues[0]->value;
                $pos = strpos($directionValue,'(');
                if (is_numeric($pos))
                {
                    $objectData  += ["direction" => substr($directionValue, 0, $pos)];   
                }
                else
                {
                    $objectData  += ["direction" => $directionValue];
                }
                

                 
                $objectData  += ["name" => $listOfValues[1]->value]; 

                $objectData  += ["collectionUnixTimeStamp" => $dateTimeNow->getTimestamp()];  

                $listOfValues = $xpath->query("./@date | ./@time", $element);
                $objectData  += ["unixTimeStamp" => strtotime($listOfValues[0]->value." ".$listOfValues[1]->value)];  

                $listOfValues = $xpath->query("./@rtDate | ./@rtTime", $element);
                if (count($listOfValues) > 0)
                {
                    $objectData  += ["rtUnixTimeStamp" => strtotime($listOfValues[0]->value." ".$listOfValues[1]->value)];
                }
                $obj = (object)$objectData;
                array_push($arrayOfObjects, $obj);
            }
        }
        return (object)$arrayOfObjects;
    }
}
?>
