<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once 'database.php';
include_once 'sheet.php';
 
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$sheet = new Sheet($db);

$fe = $_GET['fe'];
$stmt = $sheet->read($fe);
$num = $stmt->rowCount();
 
if($num>0)
{ 
    $sheets_arr=array();
    $sheets_arr["records"]=array();
 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
        extract($row);
 
        $sheet_item=array(
            "id" => $id,
			"date" => date('d-m-Y',strtotime($date)),
            "customer_name" => $customer_name,
            "customer_phone" => $customer_phone,
            "mason_name" => $mason_name,
            "mason_phone" => $mason_phone,			
            "bags" => $bags,
			"area" => $area,
			"shop" => $shop,
			"remarks" => $remarks
        );
 
        array_push($sheets_arr["records"], $sheet_item);
    }
 
    http_response_code(200); 
    echo json_encode($sheets_arr);
}
else
{
    http_response_code(404);
    echo json_encode(array("message" => "No sheets found."));
}