<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../../connect.php';
include_once 'functions/avgRate.php';
include_once 'functions/nameMaps.php';

$productNameMap = getProductNamesMap($con);
$startDate = date("Y-m-d",strtotime($_GET['startDate']));
$endDate = date("Y-m-d",strtotime($_GET['endDate']));

$rate_arr=array();
$rate_arr["records"]=array();
foreach($productNameMap as $productId => $name)
{
	$avgRate = getAvgRate($productId,$startDate,$endDate,$con);

	$rate_item=array(
		"product" =>  $name,
		"rate" => $avgRate ,
	);
	array_push($rate_arr["records"], $rate_item);	
}

http_response_code(200); 
echo json_encode($rate_arr);