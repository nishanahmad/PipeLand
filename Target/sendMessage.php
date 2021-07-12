<?php
require '../vendor/autoload.php';

function sendMessage($text,$phone)
{
	$uri = "http://api.tally.messaging.bizbrain.in/api/v2/sendWAMessage?token=d25df4daa30fdd63fb08119080c643be&to=";
	$uri = $uri.$phone."&type=text&text=".urlencode($text);
	$response = \Httpful\Request::get($uri)->send();

	return $response->body->flag;
}