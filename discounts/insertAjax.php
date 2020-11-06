<?php
header('Content-Type: application/json');

require '../connect.php';

$date = date('Y-m-d',strtotime($_POST['date']));
$type = $_POST['type'];
$product = $_POST['product'];
$client = $_POST['client'];
$discount = $_POST['discount'];
$remarks = $_POST['remarks'];

if(empty($client))
	$client = null;	

$insertQuery = "INSERT INTO discounts (date, type, product, client, discount, remarks)
				VALUES
				('$date', '$type', '$product', ".var_export($client, true).", '$discount', '$remarks')";

$insert = mysqli_query($con,$insertQuery);

if($insert)
	$response_array['status'] = 'success';
else
{
    $response_array['status'] = 'error';
	$response_array['value'] = mysqli_error($con);
}	
	
echo json_encode($response_array);

exit;