<?php

class Credentials
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;    
    }

    public function GetAPICredentials()
    {
        $data = $this->ReadFileData();
        $this->apiCredentials = array("key" => $data->apiKey);
        return $this->apiCredentials;
    }

    public function GetDBCredentials()
    {
        $data = $this->ReadFileData();
        $this->dbCredentials = array("username" => $data->dbUsername, "password" => $data->dbPassword, "dbname" => $data->dbName);
        return $this->dbCredentials;
    }

    private function ReadFileData()
    {
        try
        {
            $file = fopen("../private_html/access.json", "r");
            $rawData = fread($file,filesize("../private_html/access.json"));
            fclose($file);
    
            $data = json_decode($rawData);
            return $data;
        } 
        catch (Exception $e)
        {
            throw new Exception("Error Gathering Credentials: ".$e->getMessage(), 1);
            return false;
        }

    }
}

?>