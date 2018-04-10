<?php

if(isset($_POST['mobile_no']) && isset($_POST['message']))
{
	require_once('sms.lib.php');
	$mobile_no = $_POST['mobile_no'];
	$message = $_POST['message'];

	$sms = new Port();
	$sms->com = "COM5";
	$sms->buadRate = 115200;
	$sms->parity = 'N';
	$sms->data = 8;
	$sms->stop = 1;
	$sms->bits = 8;
	$sms->debugg = TRUE;
	$sms->init();
	$sms->openConnection();
	$sms->sendMessage($mobile_no,$message,1);
	$sms->closeConnection();
	echo $sms->message;
}

?>