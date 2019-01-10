<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
session_start();
if(isset($_SESSION["user_name"]))
{
	$arId = $_POST['id'];
	$name = $_POST['name'];
	$mobile = $_POST['mobile'];
	$shop = $_POST['shop'];
	$sap = $_POST['sap'];
	
	if(empty($mobile))
		$mobile = 'null';
	if(empty($sap))
		$sap = 'null';	
	
	if(empty($shop))
		$sql = "UPDATE ar_details SET name='$name',mobile=$mobile,sap_code=$sap,shop_name=NULL WHERE id=$arId";
	else
		$sql = "UPDATE ar_details SET name='$name',mobile=$mobile,sap_code=$sap,shop_name='$shop' WHERE id=$arId";
	
	$result = mysqli_query($con, $sql) or die(mysqli_error($con));				 

	header( "Location: view.php?id=".$arId );

}
else
	header( "Location:../index.php" );
?> 