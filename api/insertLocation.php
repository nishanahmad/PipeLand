<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once 'database.php';
include_once 'location.php';
 
$database = new Database();
$db = $database->getConnection();
 
$location = new Location($db);
 
$data = json_decode(file_get_contents("php://input"));
 
if(!empty($data->fe) && !empty($data->accuracy))
{
	$location->fe = $data->fe;
    $location->accuracy = $data->accuracy;
 
    if($location->create())
	{
        http_response_code(201);
        echo json_encode(array("message" => "location request successfully created."));
    }
    else
	{
        http_response_code(503);
        echo json_encode(array("message" => mysqli_error($database->conn->error)));
    }	
}
else
{ 
	$newData = json_encode($data); 
    http_response_code(400);
    echo json_encode(array("message" => $newData));
}
?>