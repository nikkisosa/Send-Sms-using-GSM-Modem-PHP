<?php

/**
* 
*/
class Port
{
	public $com = "COM5"; //default comport
	public $buadRate = 115200; //default buadRate
	public $parity = 'N'; //default parity
	public $data = 8; //default data
	public $stop = 1; //default stop
	public $bits = 8; //default bits
	private $output;
	private $retval;
	private $fp; //file opener
	public $debugg = FALSE; //show debugg 
	private $search; // search for valid buadRate
	public $message = '';
	function __construct()
	{
		
	}

	function init()
	{
		//list of valid BuadRate
		$validBuadRate = array(
			110    => 11,
            150    => 15,
            300    => 30,
            600    => 60,
            1200   => 12,
            2400   => 24,
            4800   => 48,
            9600   => 96,
            19200  => 19,
            38400  => 38400,
            57600  => 57600,
            115200 => 115200
		);
		$this->search = array_search($this->buadRate, $validBuadRate);
		if(!empty($this->search))
		{
			try {
				exec("MODE $this->com: BAUD=$this->buadRate PARITY=$this->parity DATA=$this->data STOP=$this->stop BITS=$this->bits", $this->output, $this->retval);
			} catch (Exception $e) {
				$this->debugg($e);
			}
		}
		else
		{
			if($this->debugg == TRUE)
			{
				trigger_error('Invalid buadRate.',E_USER_WARNING);
				exit();
			}
			
		}
		
		
	}

	function openConnection($mode = "r+")
	{
		try {
			$this->fp=fopen($this->com,$mode);
		} catch (Exception $e) {
			if($this->debugg == TRUE)
			{
				trigger_error($e,E_USER_ERROR);
				exit();
			}
		}
	}
	function closeConnection($waitingTime = 0.1)
	{
		if($this->fp)
		{
			sleep($waitingTime);
			fclose($this->fp);
		}
		else
		{
			if($this->debugg == TRUE)
			{
				throw new Exception("Connection must be open.", 1);
				exit();
			}
			
		}
	}

	function sendMessage($toAddress,$message,$waitingTime = 1)
	{
		if(empty($toAddress) || empty($message))
		{
			if($this->debugg == TRUE)
			{
				trigger_error("Invalid data",E_USER_NOTICE);
				exit();
			}
		}
		elseif (empty($toAddress) && empty($message)) 
		{
			if($this->debugg == TRUE)
			{
				trigger_error("Empty data",E_USER_NOTICE);
				exit();
			}
		}
		else
		{
			if($this->fp)
			{
				fputs($this->fp, "AT\n\r");
				sleep($waitingTime);
				fputs($this->fp, "AT+CMGF=1\n\r");
				sleep($waitingTime);
				fputs($this->fp, "AT+CMGS=\"$toAddress\"\n\r");
				sleep($waitingTime);
				fputs($this->fp, $message);
				sleep($waitingTime);
				fputs($this->fp, chr(26));
				$this->message = 'message sent!'; 
			}
			else
			{
				throw new Exception("Connection must be open. Message not sent!", 1);
				exit();
			}
		}
	}
}

?>