<?php
	require '../../connect.php';

	$id = $_POST['id'];
	
	$locked = mysqli_query($con, "SELECT * FROM tally_sale_check WHERE sale = $id");
	
	if( mysqli_num_rows($locked) > 0 )
	{
		$response_array['status'] = 'error';
		$response_array['value'] = 'Cannot delete the sale as tally verification is already done.';
	}
	else
	{
		$delete = mysqli_query($con, "DELETE FROM nas_sale WHERE sales_id = $id");
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