<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if(isset($_SESSION["user_name"]))
{
	require '../connect.php';
	require '../functions/sms.php';
	require 'updateUserDetails.php';
	if(count($_POST)>0) 
	{	

		$id = $_POST['id'];
		$result = mysqli_query($con,"SELECT * FROM nas_sale WHERE sales_id='$id'") or die(mysqli_error($con));	
		$oldSale= mysqli_fetch_array($result,MYSQLI_ASSOC);
		
		$sqlDate = date("Y-m-d", strtotime($_POST["entryDate"])); 
		$arId = $_POST['ar'];
		$truck = $_POST['truck_no'];
		$order_no = $_POST['order_no'];
		$product = $_POST['product'];
		$qty = $_POST['qty'];
		$discount = $_POST['bd'];	
		$remarks = $_POST['remarks'];
		$bill = $_POST['bill'];
		$customerName = $_POST['customerName'];
		$customerPhone = $_POST['customerPhone'];
		$address1 = $_POST['address1'];
		$entered_by = $_SESSION["user_name"];
		$entered_on = date('Y-m-d H:i:s');
		$sql = $_POST['sql'];
		$range = $_POST['range'];
			

		if(empty($discount))
			$discount = null;			
		if(empty($engId))
			$engId = null;	
		if(empty($order_no))
			$order_no = null;				
		
		
		$update = mysqli_query($con,"UPDATE nas_sale SET entry_date='$sqlDate', ar_id='$arId', truck_no='$truck',
											bill_no='$bill',order_no = ".var_export($order_no, true).",product='$product',qty='$qty',
											discount=".var_export($discount, true).",
											remarks='$remarks',address1='$address1',customer_name='$customerName', customer_phone='$customerPhone'
									 WHERE sales_id='$id'") or die(mysqli_error($con));
					
		$resultNew = mysqli_query($con,"SELECT * FROM nas_sale WHERE sales_id='$id'") or die(mysqli_error($con));	
		$newSale= mysqli_fetch_array($resultNew,MYSQLI_ASSOC);					

		updateUserDetails($oldSale,$newSale);
		
		$url = 'list.php?success&sql='.$sql.'&range='.$range;
		header( "Location: $url" );
	}																							
}
else
	header("Location:../index/home.php");
