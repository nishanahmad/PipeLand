<?php
	require '../../connect.php';
	
	$sql = "SELECT * FROM nas_sale WHERE 1 = 1";
	if(!empty($_POST['startDate']))
	{
		$startDate = date('Y-m-d',strtotime($_POST['startDate']));
		$sql = $sql." AND entry_date >= '$startDate'";
	}		
	if(!empty($_POST['endDate']))
	{
		$endDate = date('Y-m-d',strtotime($_POST['endDate']));
		$sql = $sql." AND entry_date <= '$endDate'";
	}			
	if(!empty($_POST['product']))
	{
		$product = $_POST['product'];
		$sql = $sql." AND product = '$product'";		
	}
	if(!empty($_POST['client']))
	{
		$client = $_POST['client'];
		$sql = $sql." AND (ar_id = '$client' OR eng_id = '$client')";
	}
	if(!empty($_POST['phone']))
	{
		$phone = $_POST['phone'];
		$sql = $sql." AND customer_phone = '$phone'";
	}		
	
	if(isset($startDate) && isset($endDate))
	{
		if($startDate == $endDate && $startDate == date('Y-m-d'))
			$sql = $sql." ORDER BY bill_no ASC";		
	}
	
	$result=mysqli_query($con,$sql);
	$rowcount=mysqli_num_rows($result);
	
	if($rowcount <= 1000)
		echo $sql;
	else
		echo null;