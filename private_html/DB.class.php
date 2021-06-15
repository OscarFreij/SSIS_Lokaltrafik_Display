<?php

class DB
{
    private $container;
    private $pdo;

    public function __construct(Container $container)
    {
        $this->container = $container;    
        
        try 
        {
            $credentials = $this->container->Credentials()->GetDBCredentials();
            $dbname = $credentials['dbname'];
            $username = $credentials['username'];
            $password = $credentials['password'];

            $this->pdo = new PDO("mysql:host=localhost;dbname=$dbname",$username,$password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            error_log("PDO Connection Astablished", 0);
        }
        catch(PDOException $e)
        {
            throw new Exception("PDO Connection Error: ".$e->getMessage(), 1);
        }
    }

    public function GetCallTimeRequests()
    {
        date_default_timezone_set("Europe/Stockholm");
        $t=time();
        $time = date("h:i:s",$t);

        //$stmt = $this->pdo->prepare("SELECT stops.extId, stops.name, callTime.firstCall, callTime.lastCall, callTime.daysToCall, callTime.minutesBetweenCalls, callTime.attributes FROM callTime INNER JOIN stops ON callTime.stopId = stops.id WHERE callTime.firstCall < '$time' AND callTime.lastCall > '$time';");
        $stmt = $this->pdo->prepare("SELECT stops.extId, stops.name, callTime.firstCall, callTime.lastCall, callTime.daysToCall, callTime.minutesBetweenCalls, callTime.attributes FROM callTime INNER JOIN stops ON callTime.stopId = stops.id WHERE (callTime.firstCall < '$time' AND callTime.lastCall > '$time') AND MOD(".date("i",$t).",callTime.minutesBetweenCalls) = 0;");
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        return $data;
    }
}

?>