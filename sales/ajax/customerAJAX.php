<?php
header('Content-Type: application/json');

require '../../connect.php';

session_start();

$phone = $_POST['phone'];

$sales = mysqli_query($con,"SELECT customer_name,address1 FROM nas_sale WHERE customer_phone = $phone ORDER BY entry_date DESC LIMIT 1");

if(mysqli_num_rows($sales) > 0 )
{
	$response_array['status'] = 'success';
	foreach($sales as $sale)
	{
		$response_array['customer_name'] = $sale['customer_name'];
		$response_array['address'] = $sale['address1'];
	}
}
else
{
	$response_array['status'] = 'skip';
}

echo json_encode($response_array);
	
exit;