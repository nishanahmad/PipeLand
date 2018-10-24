<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
session_start();
if(isset($_SESSION["user_name"]))
{
	$sqlDate = date("Y-m-d");
	$id = $_GET['id'];
	$delivered_by = (int)$_SESSION['user_id'];

	$updateQuery = mysqli_query($con,"UPDATE sheet_requests SET status ='cancelled', delivered_by ='$delivered_by' WHERE id=$id ") or die(mysqli_error($con));
	header( "Location: index.php" );
}
?>	