<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
require '../functions/sms.php';
session_start();
if(isset($_SESSION["user_name"]))
{
	$date = $_POST['date'];
	$sqlDate = date("Y-m-d", strtotime($date));
	$arId = $_POST['ar'];
	$truck = $_POST['truck'];
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
	
	if(empty($discount))
		$discount = null;	
	if(empty($order_no))
		$order_no = null;	
	
	$sql="INSERT INTO nas_sale (entry_date, ar_id, truck_no, order_no, product, qty, discount, remarks, bill_no, customer_name, customer_phone, address1,entered_by,entered_on)
		 VALUES
		 ('$sqlDate', '$arId', '$truck', ".var_export($order_no, true).",'$product', '$qty',".var_export($discount, true).", '$remarks', '$bill', '$customerName', '$customerPhone', '$address1', '$entered_by', '$entered_on')";

	$result = mysqli_query($con, $sql) or die(mysqli_error($con));				 
	
	$sql = $_POST['sql'];
	$range = $_POST['range'];
		
	header('Location: list.php?success&sql='.$sql.'&range='.$range);

}
else
	header( "Location: ../index/home.php" );
?> 