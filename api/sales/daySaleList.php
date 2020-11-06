<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../database.php';
include_once '../../connect.php';
include_once 'sale.php';
include_once 'functions/nameMaps.php';
 
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$sale = new Sale($db);

$today = date("Y-m-d");
$stmt = $sale->getDailySales($today);
$num = $stmt->rowCount();
 
if($num>0)
{ 
	$arNamesMap = getArNamesMap($con);
	$productNameMap = getProductNamesMap($con);
    $sales_arr=array();
    $sales_arr["records"]=array();
 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
        extract($row);
 
        $sale_item=array(
            "sales_id" => $sales_id,
			"entry_date" => date('d-m-Y',strtotime($entry_date)),
            "product" => $productNameMap[$product],
            "qty" => $qty,
            "ar" => $arNamesMap[$ar_id]['name'],
			"shop" => $arNamesMap[$ar_id]['shop'],
            "eng_id" => $eng_id,			
            "return_bag" => $return_bag,
			"discount" => $discount,
			"remarks" => $remarks,
			"customer_name" => $customer_name,
			"customer_phone" => $customer_phone,
			"address1" => $address1,
			"address2" => $address2
        );
 
        array_push($sales_arr["records"], $sale_item);
    }
 
    http_response_code(200); 
    echo json_encode($sales_arr);
}
else
{
    http_response_code(404);
    echo json_encode(array("message" => "No Sales found."));
}