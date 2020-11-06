<?php 

require  '../connect.php';

$date = date('Y-m-d',strtotime($_GET['date']));
$sheet = $_GET['sheet'];
$oldDriver = $_GET['oldDriver'];
$oldOrder = $_GET['oldOrder'];
$newDriver = $_GET['newDriver'];
$newOrder = $_GET['newOrder'];

if($oldDriver != $newDriver)
{
	if($oldDriver == 0)
	{
		$query1 = "UPDATE sheets SET assign_order=assign_order+1 WHERE assign_order >= '$newOrder' AND assigned_to='$newDriver' AND status='requested' AND date='$date'";
		$update1 = mysqli_query($con,$query1);
		if(!$update1)
		{
			$fp = fopen('query1.json', 'w');
			fwrite($fp, json_encode(mysqli_error($con)));
			fclose($fp);							
		}

		$query2 = "UPDATE sheets SET assign_order='$newOrder' WHERE id = '$sheet'";
		$update2 = mysqli_query($con,$query2);			
		if(!$update2)
		{
			$fp = fopen('query2.json', 'w');
			fwrite($fp, json_encode(mysqli_error($con)));
			fclose($fp);							
		}								
	}
	else
	{
		$query3 = "UPDATE sheets SET assign_order=assign_order-1 WHERE assign_order > '$oldOrder' AND assigned_to='$oldDriver' AND status='requested' AND date='$date'";
		$update3 = mysqli_query($con,$query3);
		if(!$update3)
		{
			$fp = fopen('query3.json', 'w');
			fwrite($fp, json_encode(mysqli_error($con)));
			fclose($fp);							
		}				
		
		if($newDriver != 0)
		{
			$query4 = "UPDATE sheets SET assign_order=assign_order+1 WHERE assign_order >= '$newOrder' AND assigned_to='$newDriver' AND status='requested' AND date='$date'";
			$update4 = mysqli_query($con,$query4);
			if(!$update4)
			{
				$fp = fopen('query4.json', 'w');
				fwrite($fp, json_encode(mysqli_error($con)));
				fclose($fp);
			}			

			$query5 = "UPDATE sheets SET assign_order='$newOrder' WHERE id = '$sheet'";
			$update5 = mysqli_query($con,$query5);			
			if(!$update5)
			{
				$fp = fopen('query5.json', 'w');
				fwrite($fp, json_encode(mysqli_error($con)));
				fclose($fp);							
			}						
		}
	}
}
else
{
	$new = mysqli_query($con,"SELECT id FROM sheets WHERE assign_order='$newOrder' AND date = '$date' AND assigned_to = '$newDriver'");
	$newId = mysqli_fetch_array($new,MYSQLI_ASSOC)['id'];
	
	$query6 = "UPDATE sheets SET assign_order = '$newOrder' WHERE assign_order='$oldOrder' AND date = '$date' AND assigned_to = '$newDriver'";
	$update6 = mysqli_query($con,$query6);			
	if(!$update6)
	{
		$fp = fopen('query6.json', 'w');
		fwrite($fp, json_encode(mysqli_error($con)));
		fclose($fp);							
	}						
	
	$query7 = "UPDATE sheets SET assign_order = '$oldOrder' WHERE id='$newId' ";
	$update7 = mysqli_query($con,$query7);			
	if(!$update7)
	{
		$fp = fopen('query7.json', 'w');
		fwrite($fp, json_encode(mysqli_error($con)));
		fclose($fp);							
	}							
}