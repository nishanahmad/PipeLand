<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
session_start();
if(isset($_SESSION["user_name"]))
{
	$date = $_POST['date'];
	$sqlDate = date("Y-m-d", strtotime($date));
	$arId = $_POST['ar'];
	$category = $_POST['category'];
	$item = $_POST['item'];
	$qty = $_POST['qty'];
	$remarks = $_POST['remarks'];
	

	$sql="INSERT INTO gifts (date, ar_id, category, item, qty, remarks)
		 VALUES
		 ('$sqlDate', '$arId', '$category', '$item', '$qty', '$remarks')";

	$result = mysqli_query($con, $sql) or die(mysqli_error($con));				 

	header( "Location: new.php");

}
else
	header( "Location: ../index.php" );
?> 