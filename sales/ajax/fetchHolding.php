<?php
header('Content-Type: application/json');

require '../../connect.php';

session_start();

$ar = $_POST['ar'];
$product = $_POST['product'];

$holdings = mysqli_query($con,"SELECT * FROM holdings WHERE product = $product AND ar = $ar AND cleared_sale IS NULL");

if(mysqli_num_rows($holdings) > 0 )
{
	$response_array['status'] = 'success';
	foreach($holdings as $holding)
		$response_array['holdings'][] = $holding;
}
else
{
	$response_array['status'] = 'skip';
}

echo json_encode($response_array);
	
exit;