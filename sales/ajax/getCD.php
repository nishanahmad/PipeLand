<?php
	require '../../connect.php';
	
	if(!empty($_POST['client'])) 
	{
		$date = date('Y-m-d',strtotime($_POST['date']));
		$product = (int)$_POST['product'];
		$client = (int)$_POST['client'];
		
		$query = mysqli_query($con,"SELECT discount FROM discounts WHERE date <= '$date' AND product = $product AND client = $client AND type='Cash Discount' ORDER BY date DESC LIMIT 1") or die(mysqli_error($con));				 	 
		if(mysqli_num_rows($query)>0)
		{
			$discount = mysqli_fetch_array($query,MYSQLI_ASSOC) or die(mysqli_error($con));				 	 
			echo $discount['discount'];
		}
		else
		{
			echo null;			
		}
	}
