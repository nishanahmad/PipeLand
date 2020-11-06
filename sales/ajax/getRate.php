<?php
	require '../../connect.php';
	
	if(!empty($_POST['date'])) 
	{
		$date = date('Y-m-d',strtotime($_POST['date']));
		$product = (int)$_POST['product'];
		
		$rateQuery = mysqli_query($con,"SELECT rate FROM rate WHERE date <= '$date' AND product = $product ORDER BY date DESC LIMIT 1") or die(mysqli_error($con));				 	 
		if(mysqli_num_rows($rateQuery)>0)
		{
			$rate = mysqli_fetch_array($rateQuery,MYSQLI_ASSOC) or die(mysqli_error($con));				 	 
			echo $rate['rate'];
		}
		else
		{
			echo null;			
		}
	}
