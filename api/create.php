<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once 'database.php';
include_once 'sheet.php';
 
$database = new Database();
$db = $database->getConnection();
 
$sheet = new Sheet($db);
 
$data = json_decode(file_get_contents("php://input"));
 
if(!empty($data->concDate) && !empty($data->bags) && !empty($data->area) && !empty($data->requested_by))
{
    $sheet->date = date('Y-m-d',strtotime($data->concDate));
	$sheet->customer_name = $data->customer_name;
    $sheet->customer_phone = $data->customer_phone;
	$sheet->mason_name = $data->mason_name;
    $sheet->mason_phone = $data->mason_phone;	
    $sheet->bags = $data->bags;
    $sheet->area = $data->area;
    $sheet->shop = $data->shop;
	$sheet->remarks = $data->remarks;
	$sheet->requested_by = $data->requested_by;
	$sheet->created_on = date('Y-m-d H:i:s');
 

    if($sheet->create())
	{
        http_response_code(201);
        echo json_encode(array("message" => "Sheet request successfully created."));
    }
    else
	{
        http_response_code(503);
        echo json_encode(array("message" => mysqli_error($database->conn->error)));
    }	
}
else
{ 
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create sheet. Data is incomplete."));
}
?>