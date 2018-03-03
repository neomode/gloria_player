<?php
/**
 * PHP Template.
 */

class Communicator
{
    private $hostname;
    private $port;
    private $timeout;
    
    public function __construct($hostname, $port, $timeout)
    {
        $this->port = (int)$port;
        $this->hostname = $hostname;
        $this->timeout = (int)$timeout;
    }
    
    public function Send($command)
    {
        $returnData = "";
        $connector = fsockopen( $this->hostname,
                                $this->port,
                                $errnum,
                                $errstr,
                                $this->timeout);
                            
        if (!is_resource($connector)) {
            die("Error connecting to " . $this->hostname . ":" . $this->port);
        }
        else
        {
            fputs($connector, $command);
            $returnData = fgets($connector); 
        }
        fclose($connector);
        return $returnData;
    }
}
?>
