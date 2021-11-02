<?php
	require '../../connect.php';
	
	$sql = "SELECT * FROM nas_sale WHERE deleted IS NULL";
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
		$sql = $sql." AND ar_id = '$client'";
	}
	if(!empty($_POST['eng']))
	{
		$eng = $_POST['eng'];
		$sql = $sql." AND eng_id = '$eng'";
	}	
	if(!empty($_POST['phone']))
	{
		$phone = $_POST['phone'];
		$sql = $sql." AND customer_phone = '$phone'";
	}		
		
	$result=mysqli_query($con,$sql);
	$rowcount=mysqli_num_rows($result);
	
	if($rowcount <= 2000)
		echo $sql;
	else
		echo null;