<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require 'updateHelper.php';
	
	if(count($_POST)>0) 
	{	

		$id = $_POST['id'];
		$result = mysqli_query($con,"SELECT * FROM nas_sale WHERE deleted IS NULL AND sales_id='$id'") or die(mysqli_error($con));	
		$oldSale= mysqli_fetch_array($result,MYSQLI_ASSOC);
		
		$sqlDate = date("Y-m-d", strtotime($_POST["entryDate"])); 
		$arId = $_POST['ar'];
		$engId = $_POST['engineer'];
		$truck = $_POST['truck'];
		$godown = $_POST['godown'];
		$order_no = $_POST['order_no'];
		$product = $_POST['product'];
		$qty = $_POST['qty'];
		$discount = $_POST['bd'];	
		$remarks = $_POST['remarks'];
		$bill = $_POST['bill'];
		$customerName = $_POST['customerName'];
		$customerPhone = $_POST['customerPhone'];
		if(isset($_POST['ar_direct']))
			$ar_direct = 1;
		else	
			$ar_direct = 0;		
		$address1 = $_POST['address1'];
		$entered_by = $_SESSION["user_name"];
		$entered_on = date('Y-m-d H:i:s');
		$sql = $_POST['sql'];
		$range = $_POST['range'];
		$total = $_POST['total'];
			

		if(empty($discount))
			$discount = null;			
		if(empty($engId))
			$engId = null;	
		if(empty($order_no))
			$order_no = null;				
		if(empty($truck))
			$truck = null;	
		if(empty($godown))
			$godown = null;	
		
		if( fnmatch("B*",$bill) || fnmatch("C*",$bill) || fnmatch("GB*",$bill) || fnmatch("GC*",$bill) || fnmatch("PB*",$bill) || fnmatch("PC*",$bill))
			$locked = 1;
		else	
			$locked = 0;
		
		$update = mysqli_query($con,"UPDATE nas_sale SET entry_date='$sqlDate', ar_id='$arId', eng_id = ".var_export($engId, true).", truck=".var_export($truck, true).",
											bill_no='$bill',order_no = ".var_export($order_no, true).",product='$product',qty='$qty',godown=".var_export($godown, true).",
											discount=".var_export($discount, true).",remarks='$remarks',address1='$address1',customer_name='$customerName', 
											customer_phone='$customerPhone',ar_direct=$ar_direct, locked = $locked
									 WHERE sales_id='$id'") or die(mysqli_error($con));
					
		$resultNew = mysqli_query($con,"SELECT * FROM nas_sale WHERE deleted IS NULL AND sales_id='$id'") or die(mysqli_error($con));	
		$newSale= mysqli_fetch_array($resultNew,MYSQLI_ASSOC);					

		updateUserDetails($oldSale,$newSale);
		clearPendingTruck($oldSale,$newSale,$con);
		
		if(billUpdatedCheck($oldSale,$newSale,$con))
			$url = 'list.php?success&sql='.$sql.'&range='.$range.'&total='.$total;
		else
			$url = 'list.php?success&sql='.$sql.'&range='.$range;
		
		header( "Location: $url" );
	}																							
}
else
	header("Location:../index/home.php");
