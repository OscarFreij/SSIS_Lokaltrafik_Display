<?php

    /*
    * This file should always be included in the index.php file.
    * It will also act as the only access point to the *.class.php files.
    */

require_once "DB.class.php";    
require_once "Functions.class.php";    
require_once "Credentials.class.php";


class Container
{
    private $db;
    private $functions;
    private $credentials;

    public function __construct()
    {
        // Container setup //
        date_default_timezone_set("Europe/Stockholm");
        error_reporting(2147483647);
        ini_set('error_log', "");
    }

    public function DB()
    {
        if ($this->db instanceof DB)
        {
            return $this->db;
        }
        else
        {
            return $this->db = new DB($this);
        }
    }

    public function Functions()
    {
        if ($this->functions instanceof Functions)
        {
            return $this->functions;
        }
        else
        {
            return $this->functions = new Functions($this);
        }
    }

    public function Credentials()
    {
        if ($this->credentials instanceof Credentials)
        {
            return $this->credentials;
        }
        else
        {
            return $this->credentials = new Credentials($this);
        }
    }
}

?>