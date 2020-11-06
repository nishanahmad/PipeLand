<?php
	require '../../connect.php';
	
	if(!empty($_POST['client'])) 
	{		
		$client = $_POST['client'];
		$query = mysqli_query($con,"SELECT type FROM ar_details WHERE id = '$client'") or die(mysqli_error($con));				 	 
		if(mysqli_num_rows($query)>0)
		{
			$ar = mysqli_fetch_array($query,MYSQLI_ASSOC) or die(mysqli_error($con));				 	 
			echo $ar['type'];
		}
		else
		{
			echo null;			
		}
	}
