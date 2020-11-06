<?php 

require  '../connect.php';

$driver = $_GET['driver'];
$sheet = $_GET['sheet'];

$update = mysqli_query($con,"UPDATE sheets SET assigned_to='$driver' WHERE id='$sheet'") or die(mysqli_error($con));

if($driver == 0)
	$zeroUpdate = mysqli_query($con,"UPDATE sheets SET assign_order=0 WHERE id='$sheet'") or die(mysqli_error($con));
?>
