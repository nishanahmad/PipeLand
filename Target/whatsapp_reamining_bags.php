<?php
require '../connect.php';
require 'sendMessage.php';

$month;
$year;
$targetArray = unserialize($_POST['input_name']);
//var_dump($targetArray);

foreach($targetArray as $target)
{
	$dateObj   = DateTime::createFromFormat('!m', $target['month']);
	$monthName = $dateObj->format('F');
	$month = $target['month'];
	$year = $target['year'];
	$balance = $target['target'] - $target['actual_sale'];
	if($target['whatsapp'] != null)
	{
		$phone = '91'.$target['whatsapp'];
		$text = 'Dear AR, Your balance to achieve your monthly target of '.$monthName.' '.$year.' is '.$balance.' bags. Achieve your target & earn special benefits - AR HELP';
		$status = sendMessage($text,$phone);
	}	
}

header("Location:list.php?success");
