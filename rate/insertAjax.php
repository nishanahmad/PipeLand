<?php
header('Content-Type: application/json');

require '../connect.php';

$date = date('Y-m-d',strtotime($_POST['date']));
$product = $_POST['product'];
$rate = $_POST['rate'];

$insertQuery = "INSERT INTO rate (date, product, rate)
				VALUES
				('$date', '$product', '$rate')";

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