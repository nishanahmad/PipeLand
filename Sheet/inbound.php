<?php
require '../connect.php';

$area=$_GET['area'];
$landmark=$_GET['landmark'];
$location=$_GET['location'];
$qty=$_GET['qty'];
$contactName=$_GET['contactName'];
$contactPhone=$_GET['contactPhone'];
$customerName=$_GET['customerName'];
$customerPhone=$_GET['customerPhone'];
$date=$_GET['date'];
$sqlDate = date("Y-m-d",strtotime($date));
$fe=$_GET['fe'];

$sql="INSERT INTO sheet_requests (date, area, location, landmark, qty, customerPhone, customerName, masonPhone, masonName, fe)
	 VALUES
	 ('$sqlDate', '$area', '$location', '$landmark', '$qty', '$customerPhone', '$customerName', '$contactPhone', '$contactName', '$fe')";

$result = mysqli_query($con, $sql) or die(mysqli_error($con));				 
	
header("Location:http://nas.force.com/NavigateSite?site=success");						
?>