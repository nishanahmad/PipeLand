<?php
	require '../../connect.php';
	session_start();
	if(!empty($_POST['ar']) && !empty($_POST['date']))
	{
		$arObjects = mysqli_query($con, "SELECT * FROM ar_details order by name ASC" ) or die(mysqli_error($con));	
		foreach($arObjects as $ar)
			$arNameMap[$ar['id']] = $ar['name'];
		
		$ar = $_POST['ar'];
		$date = date('Y-m-d',strtotime($_POST['date']));
		$checked_by = $_SESSION['user_id'];
		$checked_on = date('Y-m-d H:i:s');			
		
		$sql="INSERT INTO tally_day_check (date, ar, checked_by, checked_on)
			 VALUES
			 ('$date', '$ar', '$checked_by', '$checked_on')";

		$result = mysqli_query($con, $sql);				 		
		if($result)
		{
			echo $arNameMap[$ar];
		}
		else
		{
			echo false;	
		}
	}
