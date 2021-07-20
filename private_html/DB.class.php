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

    public function GetCallTimeRequests($argument)
    {
        $t=time();
        $time = date("H:i:s",$t);
        $day = date("D",$t);
        $dayId = 0;
        switch ($day) {
            case 'Mon':
                $dayId = 1;
                break;

            case 'Tue':
                $dayId = 2;
                break;

            case 'Wed':
                $dayId = 3;
                break;

            case 'Thu':
                $dayId = 4;
                break;

            case 'Fri':
                $dayId = 5;
                break;

            case 'Sat':
                $dayId = 6;
                break;

            case 'Sun':
                $dayId = 7;
                break;
        
            default:
                $dayId = 0;
                break;
        }

        try 
        {
            error_log("Attempting to gather CallTime Requests.", 0);
            if ($argument == "force-interval")
            {
                $stmt = $this->pdo->prepare("SELECT callTime.id, stops.extId, stops.name, callTime.firstCall, callTime.lastCall, callTime.daysToCall, callTime.minutesBetweenCalls, callTime.attributes FROM callTime INNER JOIN stops ON callTime.stopId = stops.id WHERE (callTime.firstCall < '$time' AND callTime.lastCall > '$time')");
            }
            else if ($argument == "force-all")
            {
                $stmt = $this->pdo->prepare("SELECT callTime.id, stops.extId, stops.name, callTime.firstCall, callTime.lastCall, callTime.daysToCall, callTime.minutesBetweenCalls, callTime.attributes FROM callTime INNER JOIN stops ON callTime.stopId = stops.id");
            }
            else
            {
                $stmt = $this->pdo->prepare("SELECT callTime.id, stops.extId, stops.name, callTime.firstCall, callTime.lastCall, callTime.daysToCall, callTime.minutesBetweenCalls, callTime.attributes FROM callTime INNER JOIN stops ON callTime.stopId = stops.id WHERE (callTime.firstCall < '$time' AND callTime.lastCall > '$time') AND MOD(".date("i",$t).",callTime.minutesBetweenCalls) = 0;");
            }

            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $data = $stmt->fetchAll();
            $returnArray = array();

            error_log(sizeof($data)." CallTime Request/s gatherd.", 0);
            if (sizeof($data) != 0)
            {
                if ($argument != "force-all" && $argument != "force-date")
                {
                    error_log("ReturnArray is set to data returned from DB, scrubbed by current day.");
                    foreach ($data as $id => $row) {
                        $dayStart = substr($row['daysToCall'],0,1);
                        $dayStop = substr($row['daysToCall'],2,1);

                        if ((int)$dayStart <= (int)$dayId && (int)$dayId <= (int)$dayStop)
                        {
                            array_push($returnArray, $row);
                        }
                    }
                }
                else
                {
                    error_log("ReturnArray is set to data returned from DB.");
                    $returnArray = $data;
                }

                error_log(sizeof($returnArray)." CallTime Request/s valid and collected.", 0);
                return $returnArray;
            }
            else
            {
                error_log("Gatherd requests not enough for data call");
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

    public function GetTimeTableData($id)
    {
        try 
        {
            error_log("Attempting to gather XMLData from DB.", 0);
            $stmt = $this->pdo->prepare("SELECT timeTable.dateTime, timeTable.xmlData, timeTable.callId, callTime.title, stops.name FROM timeTable JOIN (callTime JOIN stops ON stops.id = callTime.stopId) ON callTime.id = timeTable.callId WHERE timeTable.callId = $id;");
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $data = $stmt->fetchAll();
            
            error_log("Timetable with id: ".$id." gatherd for callback.");
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