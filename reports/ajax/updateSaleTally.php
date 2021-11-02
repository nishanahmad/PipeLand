<?php
	require '../../connect.php';
	session_start();
	if(!empty($_POST['saleId']))
	{		
		$saleId = $_POST['saleId'];
		$checked_by = $_SESSION['user_id'];
		$checked_on = date('Y-m-d H:i:s');			
		
		$query = mysqli_query($con, "SELECT * FROM tally_sale_check WHERE sale = '$saleId'");
		if(mysqli_num_rows($query) > 0)
		{			
			$update ="UPDATE tally_sale_check SET checked_by='$checked_by', checked_on='$checked_on', status = 'LOCKED' WHERE sale = '$saleId'";
			$result = mysqli_query($con, $update);				 			 			
		}			
		else
		{
			$insert ="INSERT INTO tally_sale_check (sale, checked_by, checked_on)
					  VALUES
					 ('$saleId', '$checked_by', '$checked_on')";			
			$result = mysqli_query($con, $insert);				 			 
		}
		
		if($result)
		{
			$checkForwardquery = mysqli_query($con, "SELECT * FROM tally_check_forwards WHERE sale = '$saleId' AND status = 1 ");			
			if(mysqli_num_rows($checkForwardquery) > 0)
			{
				$updateForward ="UPDATE tally_check_forwards SET status = 0 WHERE sale = '$saleId'";
				$forward = mysqli_query($con, $updateForward);				
			}

			echo $saleId;
		}
		else
		{
			echo false;
		}
	}
