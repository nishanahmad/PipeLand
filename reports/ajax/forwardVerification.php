<?php
	require '../../connect.php';
	session_start();
	if(!empty($_POST['forwardId']))
	{		
		$saleId = $_POST['forwardId'];
		$remarks = $_POST['remarks'];
		$forwarded_by = $_SESSION['user_id'];
		$forwarded_on = date('Y-m-d H:i:s');		
		
		$insert ="INSERT INTO tally_check_forwards (sale, remarks, forwarded_by, forwarded_on)
				  VALUES
				 ('$saleId', '$remarks', '$forwarded_by', '$forwarded_on')";			
		$result = mysqli_query($con, $insert);				 			 

		if($result)
		{
			$checkSalequery = mysqli_query($con, "SELECT * FROM tally_sale_check WHERE sale = '$saleId'");			
			if(mysqli_num_rows($checkSalequery) > 0)
			{
				$updateSale ="UPDATE tally_sale_check SET status = 'UNLOCKED',unlocked_by = '$forwarded_by' WHERE sale = '$saleId'";
				$sale = mysqli_query($con, $updateSale);				
			}

			echo $saleId;
		}
		else
		{
			echo false;	
		}
	}
