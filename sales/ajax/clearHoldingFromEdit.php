<?php
header('Content-Type: application/json');

require '../../connect.php';

session_start();

$id = $_POST['id'];
$saleId = $_POST['saleId'];
$checked = $_POST['checked'];
$userId = $_SESSION["user_id"];

if($checked == 'true')
	$update = "UPDATE holdings SET cleared_sale = $saleId, cleared_by = $userId WHERE id = $id";
else	
	$update = "UPDATE holdings SET cleared_sale = null, cleared_by = null WHERE id = $id";

$result = mysqli_query($con,$update);

if($result)
	$response_array['status'] = 'success';
else
{
    $response_array['status'] = 'error';
	$response_array['value'] = mysqli_error($con);
}	
	
echo json_encode($response_array);

exit;