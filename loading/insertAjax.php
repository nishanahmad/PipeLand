<?php
header('Content-Type: application/json');

require '../connect.php';

$date = date('Y-m-d',strtotime($_POST['date']));
$time = date('H:i',strtotime($_POST['time']));
$truck = $_POST['truck'];
$product = $_POST['product'];
$qty = $_POST['qty'];

$searchQuery = "SELECT * FROM loading WHERE truck = $truck AND product = $product AND unbilled_qty >0";
$search = mysqli_query($con,$searchQuery);
if(mysqli_num_rows($search) > 0 )
{
	$response_array['status'] = 'error';
	$response_array['value'] = 'Unload pending for this truck. Please edit and modify the quantity';
}
else
{
	$insertQuery = "INSERT INTO loading (date, time, truck, product, qty, unbilled_qty)
					VALUES
					('$date', '$time', $truck, $product, $qty, $qty)";

	$insert = mysqli_query($con,$insertQuery);

	if($insert)
		$response_array['status'] = 'success';
	else
	{
		$response_array['status'] = 'error';
		$response_array['value'] = mysqli_error($con);
	}		
}
	
echo json_encode($response_array);

exit;