<?php
require '../vendor/autoload.php';

function sendMessage($message,$phone)
{
	$uri =  "https://api.wa.anant.io/v1/send";

	$instanceId = '458f01be-f075-4745-8639-f6632c7a3697';
	$authToken = '3a9344a4-600e-47b2-ab8a-4c42e64adda7';

	$response = \Httpful\Request::post($uri)
                    ->body([
                        'instanceId' => $instanceId,
                        'authToken' => $authToken,
						'to' => $phone,
						'channel' => 'whatsapp',
						'messageType' => 'TEXT',
						'message' => $message,
						'safeDelivery' => true
                            ], \Httpful\Mime::FORM)
                    ->send();
					
	return $response->body;					
}