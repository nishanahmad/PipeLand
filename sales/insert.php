<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
require 'updateHelper.php';

session_start();
if(isset($_SESSION["user_name"]))
{
	$date = $_POST['date'];
	$sqlDate = date("Y-m-d", strtotime($date));
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
	$userId = $_SESSION["user_id"];
	

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
			
	$sql="INSERT INTO nas_sale (entry_date, ar_id, eng_id, truck, godown, order_no, product, qty, discount, remarks, bill_no, customer_name, customer_phone, ar_direct, address1, entered_by, entered_on, locked)
		 VALUES
		 ('$sqlDate', '$arId', ".var_export($engId, true).", ".var_export($truck, true).", ".var_export($godown, true).", ".var_export($order_no, true).",'$product', '$qty',".var_export($discount, true).", '$remarks', '$bill', '$customerName', '$customerPhone', $ar_direct, '$address1', '$entered_by', '$entered_on', $locked)";

	$result = mysqli_query($con, $sql) or die(mysqli_error($con));
	
	$saleId = mysqli_insert_id($con);
	
	if(isset($_POST['clearHolding']))
	{
		foreach($_POST['clearHolding'] as $holding => $status)
		{
			if($status == 'true')
			{
				$updateQuery = "UPDATE holdings SET cleared_sale=$saleId,cleared_by = $userId WHERE id = $holding";
				$update = mysqli_query($con, $updateQuery) or die(mysqli_error($con));							
			}
		}
	}
	
	$newSaleSql = mysqli_query($con,"SELECT * FROM nas_sale WHERE deleted IS NULL AND sales_id = $saleId") or die(mysqli_error($con));
	$newSale = mysqli_fetch_array($newSaleSql, MYSQLI_ASSOC);
	clearPendingTruck(null,$newSale,$con);
	
	$sql = $_POST['sql'];
	$range = $_POST['range'];
		
	header('Location: list.php?success&sql='.$sql.'&range='.$range);
}
else
	header( "Location: ../index/home.php" );
?>