<?php
//ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../connect.php';
session_start();
if(isset($_SESSION["user_name"]))
{
	$month = (int)$_POST['month'];
	$year = (int)$_POST['year'];	
	
	foreach($_POST as $key => $value)
	{	
		$arr = explode("-",$key);
		$arId = (int)str_replace('_',' ',$arr[0]);
		
		if(isset($arr[1]))
		{
			if($arr[1] == 'target')
			{
				$sql="INSERT INTO target (target,ar_id,month,year,rate,payment_perc) VALUES ('$value','$arId','$month','$year',0,100)";
				$result = mysqli_query($con, $sql) or die(mysqli_error($con));				   
			}
			else if($arr[1] == 'rate')	
			{
				$sql="UPDATE target SET rate = '$value' WHERE ar_id = '$arId' AND month = '$month' AND year = '$year' ";
				$result = mysqli_query($con, $sql) or die(mysqli_error($con));				   			
			}	
			else if($arr[1] == 'pp')	
			{
				$sql="UPDATE target SET payment_perc = '$value' WHERE ar_id = '$arId' AND month = '$month' AND year = '$year' ";
				$result = mysqli_query($con, $sql) or die(mysqli_error($con));				   			
			}						
		}
	}
	
	header( "Location: list.php?" );

	mysqli_close($con); 
}
else
{
	header( "Location: ../index.php" );
}	
?> 