<?php
header('Content-Type: application/json');

require '../connect.php';

$number = $_POST['number'];
$driver = $_POST['driver'];
$phone = $_POST['phone'];

$insertQuery = "INSERT INTO truck_details (number, driver, phone)
				VALUES
				('$number', '$driver', '$phone')";

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