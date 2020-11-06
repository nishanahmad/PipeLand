<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../connect.php';
session_start();
if(isset($_SESSION["user_name"]))
{
	if(!empty($_POST))
	{	
		$dateArray = explode(" to ",$_POST['jsDateString']);
		$year = $_POST['jsYear'];
		$month = $_POST['jsMonth'];
		$from = $dateArray[0];
		$to = $dateArray[1];
		$toString = $to.'-'.$month.'-'.$year;		
		$toDate = date("Y-m-d",strtotime($toString));	
		$fromString = $from.'-'.$month.'-'.$year;		
		$fromDate = date("Y-m-d",strtotime($fromString));		
	
		$achieved = $_POST['achieved'];
		$boost = $_POST['boost'];
		
		$insertQuery="INSERT INTO special_target_booster (fromDate, toDate, ifAchieved, boost)
			 VALUES
			 ('$fromDate', '$toDate', '$achieved', '$boost')";			

		$insert = mysqli_query($con, $insertQuery) or die(mysqli_error($con));				
		
		header( "Location: list.php");
	}	
}	