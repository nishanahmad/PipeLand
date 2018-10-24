<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
session_start();
if(isset($_SESSION["user_name"]))
{
	$sqlDate = date("Y-m-d");
	$id = $_GET['id'];
	$qty = $_GET['qty'];
	$delivered_by = (int)$_SESSION['user_id'];

	$updateQuery = mysqli_query($con,"UPDATE sheet_requests SET status ='delivered', delivered_by ='$delivered_by' WHERE id=$id ") or die(mysqli_error($con));			 
	
	$requestQuery = mysqli_query($con,"SELECT * FROM sheet_requests WHERE id=$id ") or die(mysqli_error($con));			 
	$request=mysqli_fetch_assoc($requestQuery);
	$masonName = $request['masonName']; 
	$masonPhone = $request['masonPhone']; 
	$customerName = $request['customerName']; 
	$customerPhone = $request['customerPhone']; 
	$area = $request['area'].','.$request['location'].','.$request['landmark']; 
	
	
	$insert="INSERT INTO sheets (date, masonName, masonphone, customerName, customerPhone, qty, area, delivered_by)
		 VALUES
		 ('$sqlDate', '$masonName', '$masonPhone','$customerName', '$customerPhone', $qty, '$area', $delivered_by)";

	$result = mysqli_query($con, $insert) or die(mysqli_error($con));				 		

	header( "Location: index.php" );

}
else
	header( "Location: ../index.php" );
?> 