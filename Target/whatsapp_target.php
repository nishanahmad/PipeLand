<?php
require '../connect.php';
require 'sendMessage.php';

$month;
$year;
$targetArray = unserialize($_POST['input_name']);
foreach($targetArray as $target)
{
	$dateObj   = DateTime::createFromFormat('!m', $target['month']);
	$monthName = $dateObj->format('F');
	$month = $target['month'];
	$year = $target['year'];
	if($target['whatsapp'] != null)
	{
		$phone = '91'.$target['whatsapp'];
		$text = 'Dear AR, Ur '.$monthName.' Month Target is '.$target['target'].' Bags. Achieve Ur Target & Earn Full Lakshya Benefits - AR HELP';
		$status = sendMessage($text,$phone);
	}	
}

$statusSql = "INSERT INTO whatsapp_status (type, year, month) VALUES ('target', '$year', '$month')";
$insertStatus = mysqli_query($con, $statusSql) or die(mysqli_error($con));

header("Location:list.php?");

