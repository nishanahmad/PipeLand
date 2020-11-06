<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
session_start();
if(isset($_SESSION["user_name"]))
{
	$sqlDate = date("Y-m-d");
	$id = $_GET['id'];
	$qty = (int)$_GET['qty'];
	$delivered_by = (int)$_GET['driver'];
	$date = date('Y-m-d');

	$commitFlag = true;
	mysqli_autocommit($con, FALSE);

	$updateRequest = mysqli_query($con,"UPDATE sheets SET delivered_on ='$date' ,status ='delivered', delivered_by =$delivered_by, qty = $qty WHERE id=$id ");
	if(!$updateRequest)
		$commitFlag = false;
	
	
	$QuerySheetInHand = mysqli_query($con,"SELECT qty FROM sheets_in_hand WHERE user=$delivered_by ");
	$newQty = mysqli_fetch_array($QuerySheetInHand,MYSQLI_ASSOC)['qty'] - $qty;
	if($newQty < 0)
		$commitFlag = false;	
		
	else
	{
		$updateSheetInHand = mysqli_query($con,"UPDATE sheets_in_hand SET qty =qty - $qty WHERE user=$delivered_by ");
		if(!$updateSheetInHand)
			$commitFlag = false;			
	}		

	$queryFrom = mysqli_query($con,"SELECT qty FROM sheets_in_hand WHERE user=$delivered_by ");
	if(!$queryFrom)
		$commitFlag = false;		
	
	$fromStock = mysqli_fetch_array($queryFrom,MYSQLI_ASSOC)['qty'];	

	$transferred_on = date('Y-m-d H:i:s');
	$transferred_by = $_SESSION['user_id'];		

	$insertLogs = mysqli_query($con, "INSERT INTO transfer_logs (user_from, qty, transferred_on, transferred_by, fromStock, site) VALUES ('$delivered_by', '$qty', '$transferred_on', '$transferred_by', '$fromStock', '$id')");;
	if(!$insertLogs)
		$commitFlag = false;			

	if($commitFlag)
	{
		mysqli_commit($con);	
		header( "Location: requests.php" );
	}
	else
	{
		mysqli_rollback($con);
		header( "Location: requests.php?error=true" );		
	}
}
else
	header( "Location: ../index.php" );
?> 