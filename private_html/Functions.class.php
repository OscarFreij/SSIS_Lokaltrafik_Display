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
            $response_xml_data = file_get_contents("https://api.resrobot.se/v2.1/departureBoard?accessId=".$container->Credentials()->GetAPICredentials()['key']."&id=".$externalId."&passlist=0".$extraAttributes);            
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

        $xml = simplexml_load_string($rawXML);

        $dateTimeNow = new DateTime();

        foreach ($xml->Departure as $key => $element) {
            $data = [];

            $data['direction'] = preg_replace("/ \(.*\)/", "", (string)$element->attributes()['direction']);
            $data['name'] = (string)$element->attributes()['name'];
            $data['collectionUnixTimeStamp'] = $dateTimeNow->getTimestamp();
            $data['unixTimeStamp'] = strtotime((string)$element->attributes()['date'] . " " . (string)$element->attributes()['time']);
            if (isset($element->attributes()['rtDate'])) {
                $data['rtunixTimeStamp'] = strtotime((string)$element->attributes()['rtDate'] . " " . (string)$element->attributes()['rtTime']);
            }

            array_push($arrayOfObjects, (object)$data);
        }
        return (object)$arrayOfObjects;
    }
}
?>
