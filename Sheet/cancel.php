<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
session_start();
if(isset($_SESSION["user_name"]))
{
	$sqlDate = date("Y-m-d");
	$id = $_GET['id'];
	$reason = $_GET['reason'];
	$closed_by = $_SESSION['user_id'];
	$closed_on = date("Y-m-d");

	$updateQuery = mysqli_query($con,"UPDATE sheets SET status ='cancelled',cancel_reason='$reason',closed_by = '$closed_by',closed_on='$closed_on' WHERE id=$id ") or die(mysqli_error($con));
	header( "Location: requests.php" );
}
?>	