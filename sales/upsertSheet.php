<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
session_start();
if(isset($_SESSION["user_name"]))
{
	if(isset($_POST['id']))
		$id = $_POST['id'];
	$date = $_POST['sheetDate'];
	$sqlDate = date("Y-m-d", strtotime($date));
	$customer_name = $_POST['sheet_customer_name'];
	$customer_phone = $_POST['sheet_customer_phone'];
	$mason_name = $_POST['sheet_mason_name'];	
	$mason_phone = $_POST['sheet_mason_phone'];
	$bags = $_POST['sheet_bags'];
	$area = $_POST['sheet_area'];
	$driver_area = $_POST['driver_area'];
	$shop = $_POST['sheet_shop'];		
	$remarks = $_POST['sheet_remarks'];	
	$site = $_POST['site'];
	$truck = $_POST['sheet_truck'];
	$driver_name = $_POST['driver_name'];
	$driver_phone = $_POST['driver_phone'];
	$requested_by = $_SESSION['user_name'];
	$created_on = date('Y-m-d H:i:s');	
	$sql = $_POST['sheetSql'];
	$range = $_POST['sheetRange'];
	$delivery_by = $_POST['delivery'];
	
	//  FETCH DRIVER TO ASSIGN
	if($delivery_by == 'upn')
	{
		$driverQuery = mysqli_query($con, "SELECT driver FROM sheet_area WHERE id = $driver_area") or die(mysqli_error($con));
		$deliveryDriver = mysqli_fetch_array($driverQuery, MYSQLI_ASSOC)['driver'];
	}
	else
		$deliveryDriver = 31;

	
	
	if(isset($id))
	{
		$query = "UPDATE sheets SET date = '$sqlDate', customer_name = '$customer_name', customer_phone = '$customer_phone', 
								  mason_name = '$mason_name', mason_phone = '$mason_phone', bags = $bags, area = '$area', shop = '$shop', remarks = '$remarks',
								  truck = '$truck', driver = '$driver_name', phone = '$driver_phone', assigned_to = '$deliveryDriver', driver_area = '$driver_area'
				WHERE id = $id";
	}
	else
	{
		$query="INSERT INTO sheets (date, customer_name, customer_phone, mason_name, mason_phone, bags, area, shop, remarks, site, requested_by, status, created_on, truck, driver, phone, assigned_to, driver_area)
			 VALUES
			 ('$sqlDate', '$customer_name', '$customer_phone', '$mason_name', '$mason_phone', $bags, '$area', '$shop', '$remarks', $site , '$requested_by', 'requested', '$created_on', '$truck', '$driver_name', '$driver_phone', '$deliveryDriver', '$driver_area')";
	}

	$result = mysqli_query($con, $query) or die(mysqli_error($con));				 

	header( "Location: edit.php?sales_id=$site&sql=".$sql."&range=".$range);

}
else
	header( "Location: ../index/home.php" );
?>