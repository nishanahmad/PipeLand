<?php
	require '../../connect.php';

	session_start();

	$id = $_POST['id'];
	
	$locked = mysqli_query($con, "SELECT * FROM tally_sale_check WHERE sale = $id");
	
	if( mysqli_num_rows($locked) > 0 )
	{
		$response_array['status'] = 'error';
		$response_array['value'] = 'Cannot delete the sale as tally verification is already done.';
	}
	else
	{
		$deleteForward = mysqli_query($con, "DELETE FROM tally_check_forwards WHERE sale = $id");
		
		$query = mysqli_query($con, "SELECT qty,remarks FROM nas_sale WHERE sales_id = $id");
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		$remarks = $row['remarks'].', Qty : '.$row['qty'];

		$user = $_SESSION["user_name"];
		$dateTime = date('Y-m-d H:i:s');	
		
		$sql="INSERT INTO sale_edits (sale_id, edited_on, edited_by, field, old_value, new_value)
			 VALUES
			 ($id, '$dateTime', '$user', 'DELETE', '', 'DELETED')";

		$insert = mysqli_query($con, $sql) or die(mysqli_error($con));
		
		$delete = mysqli_query($con, "UPDATE nas_sale SET qty =0, deleted = 1, remarks = '$remarks' WHERE sales_id = $id");
		if($delete)
		{
			$response_array['status'] = 'success';
		}
		else
		{
			$response_array['status'] = 'error';
			$response_array['value'] = mysqli_error($con);
		}				
	}

	echo json_encode($response_array);

	exit;	
?>