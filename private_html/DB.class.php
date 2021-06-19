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

        try 
        {
            error_log("Attempting to gather CallTime Requests.", 0);
            $stmt = $this->pdo->prepare("SELECT callTime.id, stops.extId, stops.name, callTime.firstCall, callTime.lastCall, callTime.daysToCall, callTime.minutesBetweenCalls, callTime.attributes FROM callTime INNER JOIN stops ON callTime.stopId = stops.id WHERE (callTime.firstCall < '$time' AND callTime.lastCall > '$time') AND MOD(".date("i",$t).",callTime.minutesBetweenCalls) = 0;");
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $data = $stmt->fetchAll();
            
            error_log(sizeof($data)." CallTime Request/s gatherd.", 0);
            if (sizeof($data) != 0)
            {
                return $data;
            }
            else
            {
                return false;
            }
        }
        catch(PDOException $e)
        {
            throw new Exception("PDO CallTime Request Gathering Error: ".$e->getMessage(), 1);
            return false;
        }
    }

    public function StoreTimeTableData($callTimeId, $xmlData)
    {
        try
        {
            error_log("Attempting to update database with new xmlData. CallTimeId: $callTimeId", 0);
            $sql = "DELETE FROM timeTable WHERE timeTable.callId = $callTimeId;";
            $this->pdo->exec($sql);

            $sql = "INSERT INTO timeTable (timeTable.callId, timeTable.xmlData) VALUES ($callTimeId, '$xmlData');";
            $this->pdo->exec($sql);
            error_log("Database updated with new xmlData. CallTimeId: $callTimeId", 0);

            return true;
        }
        catch(PDOException $e)
        {
            throw new Exception("PDO TimeTable Update Error: ".$e->getMessage(), 1);
            return false;
        }
    }

    public function GetTimeTableData()
    {
        try 
        {
            error_log("Attempting to gather XMLData from DB.", 0);
            $stmt = $this->pdo->prepare("SELECT timeTable.dateTime, timeTable.xmlData, timeTable.callId, stops.name FROM timeTable JOIN (callTime JOIN stops ON stops.id = callTime.stopId) ON callTime.id = timeTable.callId;");
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $data = $stmt->fetchAll();
            
            error_log(sizeof($data)." timeTable/s gatherd..", 0);
            if (sizeof($data) != 0)
            {
                return $data;
            }
            else
            {
                return false;
            }
        }
        catch(PDOException $e)
        {
            throw new Exception("PDO TimeTable Gathering Error: ".$e->getMessage(), 1);
        }
    }
}

?>